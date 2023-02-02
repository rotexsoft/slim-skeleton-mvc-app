<?php
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// This file contains all route & middleware definitions & every other 
// modfication(s) you need to make to the $app object
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  * 
  * Do something with $routing_middleware below if needed by your application
  */
$routing_middleware = $app->addRoutingMiddleware(); 

///////////////////////////////////////////
// START: DEFINITION OF MVC ROUTE HANDLERS
///////////////////////////////////////////
$smvc_validate_method_name = function(
    \Psr\Http\Message\ServerRequestInterface $req,
    string $action_method
):void {
    
    $regex_4_valid_method_name = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    if( ! preg_match( $regex_4_valid_method_name, preg_quote($action_method, '/') ) ) {

        // A valid php class' method name starts with a letter or underscore,
        // followed by any number of letters, numbers, or underscores.

        // Make sure the controller name is a valid string usable as a class name
        // in php as defined in http://php.net/manual/en/language.oop5.basic.php
        // trigger 404 not found
        $log_message = "`".__FILE__."` on line ".__LINE__.": Bad action name `{$action_method}`.";
        
        throw new \Slim\Exception\HttpBadRequestException($req, $log_message);
    }  
};

$smvc_validate_method_exists_on_controller = function(
    \Psr\Http\Message\ServerRequestInterface $req,
    \SlimMvcTools\Controllers\BaseController $controller_obj,
    string $action_method
):void {
    
    if( !method_exists($controller_obj, $action_method) ) {
        
        $controller_class_name = get_class($controller_obj);

        // 404 Not Found: Action method does not exist in the controller object.
        $log_message = "`".__FILE__."` on line ".__LINE__
            .": The action method `{$action_method}` does not exist in class `{$controller_class_name}`.";
        
        throw new \Slim\Exception\HttpNotFoundException($req, $log_message);
    }
};

$smvc_default_route_handler =
function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Psr\Http\Message\ResponseInterface $response,
    $args
)
use ($app, $smvc_validate_method_name, $smvc_validate_method_exists_on_controller) {
    
    // No controller or action was specified, so use default controller and
    // invoke the default action on it.
    $default_action = SMVC_APP_DEFAULT_ACTION_NAME;
    $default_controller = SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME;

    $default_controller_parts = explode('\\', $default_controller);
    $default_controller_from_uri = \SlimMvcTools\Functions\Str\toDashes(array_pop($default_controller_parts));
    
    $smvc_validate_method_name($request, $default_action);

    // create default controller
    $default_controller_obj = 
        sMVC_CreateController(
            $app->getContainer(), $default_controller_from_uri, 
            \SlimMvcTools\Functions\Str\toDashes($default_action), 
            $request, $response
        );
    
    $smvc_validate_method_exists_on_controller(
        $request, $default_controller_obj, $default_action
    );

    $pre_action_response = $default_controller_obj->preAction();
    $default_controller_obj->setResponse( $pre_action_response );

    // invoke default action
    $action_result = $default_controller_obj->$default_action();

    if( is_string($action_result) ) {
        
        $response = $pre_action_response;
        // write the string in the response object as the response body
        $response->getBody()->write($action_result); 

    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; // the action returned a Response object
    }

    return $default_controller_obj->postAction($response);
};

$smvc_route_handler =
function(
    \Psr\Http\Message\ServerRequestInterface $req,
    \Psr\Http\Message\ResponseInterface $resp,
    $args
)
use ($app, $smvc_validate_method_name, $smvc_validate_method_exists_on_controller) {
    
    $container = $app->getContainer();
    $action_method = \SlimMvcTools\Functions\Str\dashesToCamel($args['action']);

    if( SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES ) {

        $action_method = sMVC_PrependAction2ActionMethodName($action_method);
    }

    // strip trailing forward slash
    $params_str = isset($args['parameters'])? rtrim($args['parameters'], '/') : '';

    // convert to array of parameters
    $params = empty($params_str) && mb_strlen($params_str, 'UTF-8') <= 0 ? [] : explode('/', $params_str);

    $smvc_validate_method_name($req, $action_method);

    $controller_obj = sMVC_CreateController($container, $args['controller'], $args['action'], $req, $resp);

    $smvc_validate_method_exists_on_controller(
        $req, $controller_obj, $action_method
    );
    
    $pre_action_response = $controller_obj->preAction();
    $controller_obj->setResponse( $pre_action_response );

    // execute the controller's action
    //$actn_res = call_user_func_array([$controller_obj, $action_method], $params);
    $actn_res = $controller_obj->$action_method(...$params);

    // If we got this far, that means that the action method was successfully
    // executed on the controller object.
    if( is_string($actn_res) ) {

        $resp = $pre_action_response;
        $resp->getBody()->write($actn_res); // write the string in the response object as the response body

    } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

        $resp = $actn_res; // the action returned a Response object
    }

    return $controller_obj->postAction($resp);
};

$smvc_controller_only_route_handler =
function (
    \Psr\Http\Message\ServerRequestInterface $req,
    \Psr\Http\Message\ResponseInterface $resp,
    $args
)
use ($app, $smvc_validate_method_name, $smvc_validate_method_exists_on_controller)
{
    // No action was specified, so invoke the default action on specified controller.
    $default_action = SMVC_APP_DEFAULT_ACTION_NAME;

    $smvc_validate_method_name($req, $default_action);
    
    $controller_object =
        sMVC_CreateController(
            $app->getContainer(), $args['controller'], \SlimMvcTools\Functions\Str\toDashes($default_action), $req, $resp
        );

    $smvc_validate_method_exists_on_controller(
        $req, $controller_object, $default_action
    );   

    $pre_action_response = $controller_object->preAction();
    $controller_object->setResponse( $pre_action_response );

    // invoke default action
    $actn_res = $controller_object->$default_action();

    if( is_string($actn_res) ) {

        $resp = $pre_action_response;
        // write the string in the response object as the response body
        $resp->getBody()->write($actn_res);

    } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

        $resp = $actn_res; // the action returned a Response object
    }

    $resp = $controller_object->postAction($resp);

    return $resp;
};
///////////////////////////////////////////
// END: DEFINITION OF MVC ROUTE HANDLERS
///////////////////////////////////////////

if( !SMVC_APP_USE_MVC_ROUTES ) {
    
    //Not using mvc routes. So at least define the default / route. 
    //You can change it for your app if desired
    $app->get('/', function($request, $response, $args)use ($app) {
        
        $prepend_action = !SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;
        $action = ($prepend_action) ? 'action-index' : 'index';
        $controller = new \SlimSkeletonMvcApp\Controllers\Hello($app->getContainer(), 'hello', $action, $request, $response);
        
        $pre_action_response = $controller->preAction();
        $controller->setResponse( $pre_action_response );

        //invoke default action
        $action_result = $controller->actionIndex();

        if( is_string($action_result) ) {

            $response = $pre_action_response;
            $response->getBody()->write($action_result); //write the string in the response object as the response body

        } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

            $response = $action_result; //the action returned a Response object
        }

        return $controller->postAction($response);
    });
}

/////////////////////////////
/////////////////////////////
// Start: Register mvc routes
/////////////////////////////
/////////////////////////////

if( SMVC_APP_USE_MVC_ROUTES ) {
    
    // default route
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/', 
        $smvc_default_route_handler
    );

    // controller with no action and params route handler
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}[/]', 
        $smvc_controller_only_route_handler
    );

    // controller with action and optional params route handler
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}/{action}[/{parameters:.+}]', 
        $smvc_route_handler
    );
    
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}/{action}/', 
        $smvc_route_handler
    ); //handle trailing slash
}
/////////////////////////////
// End: Register mvc routes
/////////////////////////////

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger  
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$error_middleware = $app->addErrorMiddleware(
    $app_settings['displayErrorDetails'],
    $app_settings['logErrors'], 
    $app_settings['logErrorDetails'],
    $container->get('logger')
);
$error_middleware->setDefaultErrorHandler(
    new $app_settings['error_handler_class'](
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $container->get('logger')
    )
);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->registerErrorRenderer(
    'text/html', 
    new $app_settings['html_renderer_class']($app_settings['error_template_file'])
);
$error_handler->setLogErrorRenderer(
    new $app_settings['log_renderer_class']()
);

if($app_settings['addContentLengthHeader']) {
    
    // Add any middleware which may modify the response body before adding the ContentLengthMiddleware
    $app->add(new \Slim\Middleware\ContentLengthMiddleware());
}

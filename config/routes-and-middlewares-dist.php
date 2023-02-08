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
  * Do something with $routing_middleware below if needed by your application
  */
$routing_middleware = $app->addRoutingMiddleware(); 

///////////////////////////////////////////
// START: DEFINITION OF MVC ROUTE HANDLER
///////////////////////////////////////////
$route_handler_obj = new \SlimMvcTools\MvcRouteHandler(
    $app,
    SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME,
    SMVC_APP_DEFAULT_ACTION_NAME,
    SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES
);

$smvc_route_handler =
function(
    \Psr\Http\Message\ServerRequestInterface $req,
    \Psr\Http\Message\ResponseInterface $resp,
    $args
)
use ($route_handler_obj) {
    
    return $route_handler_obj($req, $resp, $args);
};
///////////////////////////////////////////
// END: DEFINITION OF MVC ROUTE HANDLER
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
        $smvc_route_handler
    );

    // controller with no action and params route handler
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}[/]', 
        $smvc_route_handler
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

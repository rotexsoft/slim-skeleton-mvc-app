<?php
///////////////////////////////////////////////////////////////////////////////////
// This file contains all route & middleware definitions & every other 
// modfication(s) you need to make to the $app object.
// 
// Manually add the Slim\Middleware\OutputBufferingMiddleware to the $app object
// wherever you feel is necessary in this file. If you don't need it, don't add it.
// https://www.slimframework.com/docs/v4/middleware/output-buffering.html
// https://www.slimframework.com/docs/v4/start/upgrade.html#new-output-buffering-middleware
///////////////////////////////////////////////////////////////////////////////////

/**
  * The routing middleware should be added earlier than the ErrorMiddleware
  * Otherwise exceptions thrown from it will not be handled by the middleware
  * Do something with $routing_middleware below if needed by your application.
  * If you were using determineRouteBeforeAppMiddleware in Slim 3, you need 
  * to add the Middleware\RoutingMiddleware middleware to your application 
  * just before your call run() to maintain the previous behaviour (in this
  * case, at the end of this file before or after the addErrorMiddleware call).
  * https://www.slimframework.com/docs/v4/middleware/routing.html
  */
$routing_middleware = $app->addRoutingMiddleware();

////////////////////////////////////////////////////////////////////////////////
// Start: Register your own routes for your application here if needded.
//        They will override the mvc routes defined later on in this
//        file in situations where there have the same pattern.
//        SMVC_APP_USE_MVC_ROUTES being true or false will not have any
//        impact on the routes you define here.
////////////////////////////////////////////////////////////////////////////////

/**
 * Below is a good example of a route you can define which will override the 
 * '/{controller}[/]' mvc route which would normally map to executing the
 * default action on the \SlimSkeletonMvcApp\Controllers\Hello controller.

$app->get('/hello/', function (\Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Message\ResponseInterface $resp, $args) {
    
    $resp->getBody()->write("Hello!");
    return $resp;
});

*/

if( !SMVC_APP_USE_MVC_ROUTES ) {
    
    //Not using mvc routes. So at least define the default / route. 
    //You can change it for your app if desired
    $app->get('/', function($request, $response, $args) {
        $response->getBody()->write("Hello World!");
        return $response;
    });
}
 
////////////////////////////////////////////////////////////////////////////////
// End:  Register your own routes for your application here if needded.
////////////////////////////////////////////////////////////////////////////////

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

/////////////////////////////
// Start: Register mvc routes
/////////////////////////////
if( SMVC_APP_USE_MVC_ROUTES ) {
    
    $app->map($app_settings['mvc_routes_http_methods'], '/', $smvc_route_handler); // default route
    
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}[/]', 
        $smvc_route_handler
    ); // controller with no action and params route handler
    
    $app->map(
        $app_settings['mvc_routes_http_methods'], 
        '/{controller}/{action}[/{parameters:.+}]', 
        $smvc_route_handler
    ); // controller with action and optional params route handler
    
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
    $container->get(\SlimMvcTools\ContainerKeys::LOGGER)
);
$error_middleware->setDefaultErrorHandler(
    new $app_settings['error_handler_class'](
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $container->get(\SlimMvcTools\ContainerKeys::LOGGER)
    )
);
$error_handler = $error_middleware->getDefaultErrorHandler();

if($error_handler instanceof \SlimMvcTools\ErrorHandler) {
    
    // Inject the container into the error handler so you can pull stuff 
    // out of it that may be needed when handling errors
    $error_handler->setContainer($container);
}

/** @var \SlimMvcTools\HtmlErrorRenderer $html_error_renderer */
$html_error_renderer = new $app_settings['html_renderer_class']($app_settings['error_template_file']);
$html_error_renderer->setDefaultErrorTitle($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_text'));
$html_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\LogErrorRenderer $log_error_renderer */
$log_error_renderer = new $app_settings['log_renderer_class']();
$log_error_renderer->setDefaultErrorTitle($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_text'));
$log_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\JsonErrorRenderer $json_error_renderer */
$json_error_renderer = new $app_settings['json_renderer_class']();
$json_error_renderer->setDefaultErrorTitle($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_text'));
$json_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\XmlErrorRenderer $xml_error_renderer */
$xml_error_renderer = new $app_settings['xml_renderer_class']();
$xml_error_renderer->setDefaultErrorTitle($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_text'));
$xml_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_description'));

if($app_settings['displayErrorDetails']) {
    
    $html_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_detailed_description'));
    $log_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_detailed_description'));
    $json_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_detailed_description'));
    $xml_error_renderer->setDefaultErrorDescription($container->get(\SlimMvcTools\ContainerKeys::LOCALE_OBJ)->gettext('default_application_error_title_detailed_description'));
} 

$error_handler->registerErrorRenderer('text/html', $html_error_renderer);
$error_handler->registerErrorRenderer('text/plain', $log_error_renderer);
$error_handler->registerErrorRenderer('application/json', $json_error_renderer);
$error_handler->registerErrorRenderer('application/xml', $xml_error_renderer);
$error_handler->registerErrorRenderer('text/xml', $xml_error_renderer);

$error_handler->setLogErrorRenderer($log_error_renderer);
$error_handler->setDefaultErrorRenderer('text/html', $html_error_renderer);

// https://www.slimframework.com/docs/v4/objects/application.html#advanced-notices-and-warnings-handling
// Warnings and Notices are not caught by default. The code below causes your 
// application to display an error page when they happen.
$serverRequestCreator = \Slim\Factory\ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$displayErrorDetails = $app_settings['displayErrorDetails'];
$logErrors = $app_settings['logErrors'];
$logErrorDetails = $app_settings['logErrorDetails'];

$shutdownHandler = function() use ($request, $error_handler, $displayErrorDetails, $logErrors, $logErrorDetails) {
    
    $error = error_get_last();
    
    if (is_array($error)) {
        
        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorMessage = $error['message'];
        $errorType = $error['type'];
        $message = 'An error while processing your request. Please try again later.';

        if ($displayErrorDetails) {
            
            switch ($errorType) {
                case E_USER_ERROR:
                    $message = "FATAL ERROR: {$errorMessage} on line {$errorLine} in file {$errorFile}.";
                    break;

                case E_USER_WARNING:
                    $message = "WARNING: {$errorMessage}";
                    break;

                case E_USER_NOTICE:
                    $message = "NOTICE: {$errorMessage}";
                    break;

                default:
                    $message = "ERROR: {$errorMessage} on line {$errorLine} in file {$errorFile}.";
                    break;
            }
        } // if ($displayErrorDetails)

        $exception = new \Slim\Exception\HttpInternalServerErrorException($request, $message);
        $response = $error_handler->__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);

        if (ob_get_length()) {
          ob_clean();
        }

        $responseEmitter = new \Slim\ResponseEmitter();
        $responseEmitter->emit($response);
        
    } // if (is_array($error))
};
register_shutdown_function($shutdownHandler);

if($app_settings['addContentLengthHeader']) {
    
    // You may need to move this code up if the ContentLength calculation doesn't look right to you.
    // https://www.slimframework.com/docs/v4/start/upgrade.html#new-content-length-middleware
    $app->add(new \Slim\Middleware\ContentLengthMiddleware());
}

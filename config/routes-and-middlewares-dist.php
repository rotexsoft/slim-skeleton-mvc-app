<?php
use \SlimSkeletonMvcApp\AppSettingsKeys;

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
  * Do something with $routingMiddleware below if needed by your application.
  * If you were using determineRouteBeforeAppMiddleware in Slim 3, you need 
  * to add the Middleware\RoutingMiddleware middleware to your application 
  * just before your call run() to maintain the previous behaviour (in this
  * case, at the end of this file before or after the addErrorMiddleware call).
  * https://www.slimframework.com/docs/v4/middleware/routing.html
  */
$routingMiddleware = $app->addRoutingMiddleware();

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
$routeHandlerObject = new \SlimMvcTools\MvcRouteHandler(
    $app,
    SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME,
    SMVC_APP_DEFAULT_ACTION_NAME,
    SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES
);

$smvcRouteHandler =
function(
    \Psr\Http\Message\ServerRequestInterface $req,
    \Psr\Http\Message\ResponseInterface $resp,
    $args
) use ($routeHandlerObject) {
    
    return $routeHandlerObject($req, $resp, $args);
};
///////////////////////////////////////////
// END: DEFINITION OF MVC ROUTE HANDLER
///////////////////////////////////////////

/////////////////////////////
// Start: Register mvc routes
/////////////////////////////
if( SMVC_APP_USE_MVC_ROUTES ) {
    
    $app->map($appSettings[AppSettingsKeys::MVC_ROUTES_HTTP_METHODS], '/', $smvcRouteHandler); // default route
    
    $app->map(
        $appSettings[AppSettingsKeys::MVC_ROUTES_HTTP_METHODS],
        '/{controller}[/]',
        $smvcRouteHandler
    ); // controller with no action and params route handler
    
    $app->map(
        $appSettings[AppSettingsKeys::MVC_ROUTES_HTTP_METHODS],
        '/{controller}/{action}[/{parameters:.+}]',
        $smvcRouteHandler
    ); // controller with action and optional params route handler
    
    $app->map(
        $appSettings[AppSettingsKeys::MVC_ROUTES_HTTP_METHODS],
        '/{controller}/{action}/',
        $smvcRouteHandler
    ); //handle trailing slash
}
/////////////////////////////
// End: Register mvc routes
/////////////////////////////

$psrLogger = $container->get(\SlimSkeletonMvcApp\ContainerKeys::LOGGER);
$localeObject = $container->get(\SlimSkeletonMvcApp\ContainerKeys::LOCALE_OBJ);

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
$errorMiddleware = $app->addErrorMiddleware(
    $appSettings[AppSettingsKeys::DISPLAY_ERROR_DETAILS],
    $appSettings[AppSettingsKeys::LOG_ERRORS], 
    $appSettings[AppSettingsKeys::LOG_ERROR_DETAILS],
    $psrLogger
);
$errorMiddleware->setDefaultErrorHandler(
    new $appSettings[AppSettingsKeys::ERROR_HANDLER_CLASS](
        $app->getCallableResolver(),
        $app->getResponseFactory(),
        $psrLogger
    )
);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();

if($errorHandler instanceof \SlimMvcTools\ErrorHandler) {
    
    // Inject the container into the error handler so you can pull stuff 
    // out of it that may be needed when handling errors
    $errorHandler->setContainer($container);
}

/** @var \SlimMvcTools\HtmlErrorRenderer $htmlErrorRenderer */
$htmlErrorRenderer = new $appSettings[AppSettingsKeys::HTML_RENDERER_CLASS]($appSettings[AppSettingsKeys::ERROR_TEMPLATE_FILE_PATH]);
$htmlErrorRenderer->setDefaultErrorTitle($localeObject->gettext('default_application_error_title_text'));
$htmlErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\LogErrorRenderer $logErrorRenderer */
$logErrorRenderer = new $appSettings[AppSettingsKeys::LOG_RENDERER_CLASS]();
$logErrorRenderer->setDefaultErrorTitle($localeObject->gettext('default_application_error_title_text'));
$logErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\JsonErrorRenderer $jsonErrorRenderer */
$jsonErrorRenderer = new $appSettings[AppSettingsKeys::JSON_RENDERER_CLASS]();
$jsonErrorRenderer->setDefaultErrorTitle($localeObject->gettext('default_application_error_title_text'));
$jsonErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_description'));

/** @var \SlimMvcTools\XmlErrorRenderer $xmlErrorRenderer */
$xmlErrorRenderer = new $appSettings[AppSettingsKeys::XML_RENDERER_CLASS]();
$xmlErrorRenderer->setDefaultErrorTitle($localeObject->gettext('default_application_error_title_text'));
$xmlErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_description'));

if($appSettings[AppSettingsKeys::DISPLAY_ERROR_DETAILS]) {
    
    $htmlErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_detailed_description'));
    $logErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_detailed_description'));
    $jsonErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_detailed_description'));
    $xmlErrorRenderer->setDefaultErrorDescription($localeObject->gettext('default_application_error_title_detailed_description'));
} 

$errorHandler->registerErrorRenderer('text/html', $htmlErrorRenderer);
$errorHandler->registerErrorRenderer('text/plain', $logErrorRenderer);
$errorHandler->registerErrorRenderer('application/json', $jsonErrorRenderer);
$errorHandler->registerErrorRenderer('application/xml', $xmlErrorRenderer);
$errorHandler->registerErrorRenderer('text/xml', $xmlErrorRenderer);

$errorHandler->setLogErrorRenderer($logErrorRenderer);
$errorHandler->setDefaultErrorRenderer('text/html', $htmlErrorRenderer);

// https://www.slimframework.com/docs/v4/objects/application.html#advanced-notices-and-warnings-handling
// Warnings and Notices are not caught by default. The code below causes your 
// application to display an error page when they happen.
$serverRequestCreator = \Slim\Factory\ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();
$displayErrorDetails = $appSettings[AppSettingsKeys::DISPLAY_ERROR_DETAILS];
$logErrors = $appSettings[AppSettingsKeys::LOG_ERRORS];
$logErrorDetails = $appSettings[AppSettingsKeys::LOG_ERROR_DETAILS];

$shutdownHandler = function() use ($request, $errorHandler, $displayErrorDetails, $logErrors, $logErrorDetails) {
    
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
        $response = $errorHandler->__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);

        if (ob_get_length()) {
          ob_clean();
        }

        $responseEmitter = new \Slim\ResponseEmitter();
        $responseEmitter->emit($response);
        
    } // if (is_array($error))
};
register_shutdown_function($shutdownHandler);

if($appSettings[AppSettingsKeys::ADD_CONTENT_LENGTH_HEADER]) {
    
    // You may need to move this code up if the ContentLength calculation doesn't look right to you.
    // https://www.slimframework.com/docs/v4/start/upgrade.html#new-content-length-middleware
    $app->add(new \Slim\Middleware\ContentLengthMiddleware());
}

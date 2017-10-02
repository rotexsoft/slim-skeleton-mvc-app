<?php
require '../vendor/autoload.php';

define('S3MVC_APP_ENV_DEV', 'development');
define('S3MVC_APP_ENV_PRODUCTION', 'production');
define('S3MVC_APP_ENV_STAGING', 'staging');
define('S3MVC_APP_ENV_TESTING', 'testing');

define('S3MVC_APP_PUBLIC_PATH', dirname(__FILE__));
define('S3MVC_APP_ROOT_PATH', dirname(dirname(__FILE__)));

// If true, the mvc routes will be enabled. If false, then you must explicitly
// define all the routes for your application inside config/routes-and-middlewares.php
define('S3MVC_APP_USE_MVC_ROUTES', true);

// If true, the string `action` will be prepended to action method names (if the
// method name does not already start with the string `action`). The resulting 
// method name will be converted to camel case before being executed. 
// If false, then action method names will only be converted to camel 
// case before being executed. 
// NOTE: This setting does not apply to S3MVC_APP_DEFAULT_ACTION_NAME.
//       It only applies to the routes below:
//          '/{controller}/{action}[/{parameters:.+}]'
//          '/{controller}/{action}/'
define('S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES', false);

// This is used to create a controller object to handle the default / route. 
// Must be prefixed with the namespace if the controller class is in a namespace.
define('S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME', '\\Slim3MvcTools\\Controllers\\BaseController');

// This is the name of the action / method to be called on the default controller 
// to handle the default / route. This method should return a response string (ie. 
// valid html) or a PSR 7 response object containing valid html in its body.
// This default action / method should accept no arguments / parameters.
define('S3MVC_APP_DEFAULT_ACTION_NAME', 'actionIndex');

s3MVC_GetSuperGlobal(); // this method is first called here to ensure that $_SERVER, 
                        // $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV are 
                        // captured in their original state by the static $super_globals
                        // variable inside s3MVC_GetSuperGlobal(), before any other 
                        // library, framework, etc. accesses or modifies any of them.
                        // Subsequent calls to s3MVC_GetSuperGlobal(..) will return
                        // the stored values.
/**
 * 
 * This function detects which environment your web-app is running in 
 * (i.e. one of Production, Development, Staging or Testing).
 * 
 * NOTE: Make sure you edit /public/env.php to return one of S3MVC_APP_ENV_DEV, 
 *       S3MVC_APP_ENV_PRODUCTION, S3MVC_APP_ENV_STAGING or S3MVC_APP_ENV_TESTING 
 *       relevant to the environment you are installing your web-app.
 * 
 * @return string
 */
function s3MVC_GetCurrentAppEnvironment() {

    static $current_env;

    if( !$current_env ) {

        $root_dir = dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR;
        $current_env = include $root_dir.'config'. DIRECTORY_SEPARATOR.'env.php';
    }

    return $current_env;
}

function s3MVC_PrependAction2ActionMethodName($action_method_name) {
    
    if( strtolower( substr($action_method_name, 0, 6) ) !== "action"){
        
        $action_method_name = 'action'.  ucfirst($action_method_name);
    }
    
    return $action_method_name;
}

$s3mvc_root_dir = S3MVC_APP_ROOT_PATH. DIRECTORY_SEPARATOR;

// handle ini settings
require_once "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php';


$app_settings_file_path = "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php';

if( !file_exists($app_settings_file_path) ) {
    
    $app_settings_file_path_rel = '.'.DIRECTORY_SEPARATOR."config". DIRECTORY_SEPARATOR.'app-settings.php';
    $app_settings_dist_file_path_rel = '.'.DIRECTORY_SEPARATOR."config". DIRECTORY_SEPARATOR.'app-settings-dist.php';
    
    $app_settings_file_missing_error_page_content = <<<END
        <p>
            <strong>ERROR: `$app_settings_file_path_rel`</strong> not found!<br><br>
            Please copy <strong>`$app_settings_dist_file_path_rel`</strong> to 
            <strong>`$app_settings_file_path_rel`</strong> and configure 
            <strong>`$app_settings_file_path_rel`</strong> for your 
            application's current environment.
            <br>Goodbye!!!
        </p>
END;
    
    if( s3MVC_GetCurrentAppEnvironment() !== S3MVC_APP_ENV_DEV ) {
        
        // We are not in a dev environment.
        // Make error message to be sent to the client less detailed
        $app_settings_file_missing_error_page_content = <<<END
        <p>
            Please check server log file for details.
            <br>Goodbye!!!
        </p>
END;
    }
    
    $app_settings_file_missing_error_page = <<<END
<html>
    <head>
        <title>SlimPHP 3 Skeleton MVC App Error</title>
        <style>
            body{
                margin:0;
                padding:30px;
                font:14px/1.5 Helvetica,Arial,Verdana,sans-serif;
            }
            h1{
                margin:0;
                font-size:48px;
                font-weight:normal;
                line-height:48px;
            }
        </style>
    </head>
    <body>
        <h1>SlimPHP 3 Skeleton MVC App Configuration Error</h1>
        $app_settings_file_missing_error_page_content
    </body>
</html>
END;
    echo $app_settings_file_missing_error_page;
    
    $current_uri = \Slim\Http\Request::createFromEnvironment(new \Slim\Http\Environment(s3MVC_GetSuperGlobal('server')))->getUri()->__toString();
    
    // Write full message to log via error_log(...)
    // http://php.net/manual/en/function.error-log.php
    $log_message = "ERROR: [$current_uri] `$app_settings_file_path_rel` not found."
                 . " Please copy `$app_settings_dist_file_path_rel` to `$app_settings_file_path_rel` and"
                 . " configure `$app_settings_file_path_rel` for your application's current environment.";

    // error_log ( $log_message , 0 ) means message is sent to PHP's system logger, 
    // using the Operating System's system logging mechanism or a file, depending 
    // on what the error_log configuration directive is set to.
    if( @error_log ( $log_message , 0 ) === false ) {
        
        // last attempt to log
        error_log ( $log_message , 4 ); // message is sent directly to the SAPI logging handler.
    } 

    
    exit;
}

$app_settings = require_once $app_settings_file_path;

$app = new Slim\App($app_settings);
$container = $app->getContainer();

////////////////////////////////////////////////////////////////////////////////
// Start: Dependency Injection Configuration
//        Add objects to the dependency injection container
////////////////////////////////////////////////////////////////////////////////

require_once "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'dependencies.php';

////////////////////////////////////////////////////////////////////////////////
// End Dependency Injection Configuration
////////////////////////////////////////////////////////////////////////////////

$s3mvc_default_route_handler = 
function (
    \Psr\Http\Message\ServerRequestInterface $request, 
    \Psr\Http\Message\ResponseInterface $response, 
    $args
) 
//use ($app) 
{
    // NOTE: inside this function $this refers to the Slim app's container. 
    //       It is automatically bound to this closure by Slim 3 when any of
    //       $app->map or $app->get or $app->post, etc is called.
    
    // No controller or action was specified, so use default controller and 
    // invoke the default action on it.
    $default_action = S3MVC_APP_DEFAULT_ACTION_NAME;
    $default_controller = S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME;
    
    $default_controller_parts = explode('\\', $default_controller);
    $default_controller_from_uri = \Slim3MvcTools\Functions\Str\toDashes(array_pop($default_controller_parts));
    
    // create default controller
    $default_controller_obj = new $default_controller(
                                        $this,
                                        $default_controller_from_uri,
                                        \Slim3MvcTools\Functions\Str\toDashes($default_action),
                                        $request, $response
                                    );
    
    $pre_action_response = $default_controller_obj->preAction();
    $default_controller_obj->setResponse( $pre_action_response );
    
    // invoke default action
    $action_result = $default_controller_obj->$default_action();
    
    if( is_string($action_result) ) {
        
        $response = $pre_action_response;
        $response->getBody()->write($action_result); // write the string in the response object as the response body
        
    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; // the action returned a Response object
    }
    
    return $default_controller_obj->postAction($response);
};

$s3mvc_route_handler = 
function(
    \Psr\Http\Message\ServerRequestInterface $req, 
    \Psr\Http\Message\ResponseInterface $resp, 
    $args
) 
//use ($app) 
{
    // NOTE: inside this function $this refers to the Slim app's container. 
    //       It is automatically bound to this closure by Slim 3 when any of
    //       $app->map or $app->get or $app->post, etc is called.
    $container = $this;

    // Further enhancements:
    // Add an assoc array that contains allowed actions for a controller
    // $map = array('hello'=>'someothercontroller');

    $action_method = \Slim3MvcTools\Functions\Str\dashesToCamel($args['action']);

    if( S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES ) {

        $action_method = s3MVC_PrependAction2ActionMethodName($action_method);
    }

    // strip trailing forward slash
    $params_str = isset($args['parameters'])? rtrim($args['parameters'], '/') : '';

    // convert to array of parameters
    $params = empty($params_str) && mb_strlen($params_str, 'UTF-8') <= 0 ? [] : explode('/', $params_str);

    $regex_4_valid_method_name = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    if( ! preg_match( $regex_4_valid_method_name, preg_quote($action_method, '/') ) ) {

        // A valid php class' method name starts with a letter or underscore, 
        // followed by any number of letters, numbers, or underscores.

        // Make sure the controller name is a valid string usable as a class name
        // in php as defined in http://php.net/manual/en/language.oop5.basic.php
        // trigger 404 not found
        $notFoundHandler = $container->get('notFoundHandler');
        $log_message = "`".__FILE__."` on line ".__LINE__.": Bad action name `{$action_method}`.";
        
        // invoke the not found handler
        return $notFoundHandler($req, $resp, null, $log_message);
    }

    $controller_obj = s3MVC_CreateController($this, $args['controller'], $args['action'], $req, $resp);
    
    if( 
        $controller_obj instanceof \Slim3MvcTools\Controllers\BaseController
        && !method_exists($controller_obj, $action_method) 
    ) {
        $controller_class_name = get_class($controller_obj);

        // 404 Not Found: Action method does not exist in the controller object.
        $notFoundHandler = $container->get('notFoundHandler');
        $log_message = "`".__FILE__."` on line ".__LINE__
            .": The action method `{$action_method}` does not exist in class `{$controller_class_name}`.";
        
        // invoke the not found handler
        return $notFoundHandler($req, $resp, null, $log_message);

    } else if ( 
        $controller_obj instanceof \Slim3MvcTools\Controllers\BaseController 
    ) {
        $pre_action_response = $controller_obj->preAction();
        $controller_obj->setResponse( $pre_action_response );

        // execute the controller's action
        $actn_res = call_user_func_array([$controller_obj, $action_method], $params);

        // If we got this far, that means that the action method was successfully 
        // executed on the controller object.
        if( is_string($actn_res) ) {

            $resp = $pre_action_response;
            $resp->getBody()->write($actn_res); // write the string in the response object as the response body

        } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

            $resp = $actn_res; // the action returned a Response object
        }
        
        $resp = $controller_obj->postAction($resp);

    } else {

        // s3MVC_CreateController(..) returned a Response object containing a
        // not found page.
        $resp = $controller_obj;
    }

    return $resp;
};

$s3mvc_controller_only_route_handler =             
function (
    \Psr\Http\Message\ServerRequestInterface $req, 
    \Psr\Http\Message\ResponseInterface $resp, 
    $args
) 
//use ($app) 
{
    // NOTE: inside this function $this refers to the Slim app's container. 
    //       It is automatically bound to this closure by Slim 3 when any of
    //       $app->map or $app->get or $app->post, etc is called.

    // No action was specified, so invoke the default action on specified 
    // controller.
    $default_action = S3MVC_APP_DEFAULT_ACTION_NAME;

    // s3MVC_CreateController could return a Response object if $args['controller']
    // doesn't match any existing controller class.
    $controller_object = 
        s3MVC_CreateController(
            $this, $args['controller'], \Slim3MvcTools\Functions\Str\toDashes($default_action), $req, $resp
        );
        
    if( 
        $controller_object instanceof \Slim3MvcTools\Controllers\BaseController
        && !method_exists($controller_object, $default_action) 
    ) {
        $controller_class_name = get_class($controller_object);

        // 404 Not Found: Action method does not exist in the controller object.
        $notFoundHandler = $this->get('notFoundHandler');
        $log_message = "`".__FILE__."` on line ".__LINE__
            . ": The action method `{$default_action}` does not exist in class `{$controller_class_name}`.";

        // invoke the not found handler
        return $notFoundHandler($req, $resp, null, $log_message);
        
    }  else if ( 
        $controller_object instanceof \Slim3MvcTools\Controllers\BaseController 
    ) {
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
        
    } else {
        
        // s3MVC_CreateController(..) returned a Response object containing a
        // not found page.
        $resp = $controller_object;
    }
    
    return $resp;
};

////////////////////////////////////////////////////////////////////////////////
// Start: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

require_once "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'routes-and-middlewares.php';

////////////////////////////////////////////////////////////////////////////////
// End: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

/////////////////////////////
// Start: mvc routes
/////////////////////////////


if( S3MVC_APP_USE_MVC_ROUTES ) {
    
    // default route
    $app->map( ['GET', 'POST'], '/', $s3mvc_default_route_handler );

    // controller with no action and params route handler
    $app->map(['GET', 'POST'], '/{controller}[/]', $s3mvc_controller_only_route_handler);

    // controller with action and optional params route handler
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}[/{parameters:.+}]', $s3mvc_route_handler);
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}/', $s3mvc_route_handler);//handle trailing slash
}
/////////////////////////////
// End: mvc routes
/////////////////////////////

$app->run();

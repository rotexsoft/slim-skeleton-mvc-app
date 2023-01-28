<?php
require dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define('SMVC_APP_ENV_DEV', 'development');
define('SMVC_APP_ENV_PRODUCTION', 'production');
define('SMVC_APP_ENV_STAGING', 'staging');
define('SMVC_APP_ENV_TESTING', 'testing');

define('SMVC_APP_PUBLIC_PATH', dirname(__FILE__));
define('SMVC_APP_ROOT_PATH', dirname(dirname(__FILE__)));

// If true, the mvc routes will be enabled. If false, then you must explicitly
// define all the routes for your application inside config/routes-and-middlewares.php
define('SMVC_APP_USE_MVC_ROUTES', true);

// If true, the string `action` will be prepended to action method names (if the
// method name does not already start with the string `action`). The resulting
// method name will be converted to camel case before being executed.
// If false, then action method names will only be converted to camel
// case before being executed.
// NOTE: This setting does not apply to SMVC_APP_DEFAULT_ACTION_NAME.
//       It only applies to the routes below:
//          '/{controller}/{action}[/{parameters:.+}]'
//          '/{controller}/{action}/'
define('SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES', false);

// This is used to create a controller object to handle the default / route.
// Must be prefixed with the namespace if the controller class is in a namespace.
define('SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME', \SlimMvcTools\Controllers\BaseController::class);

// This is the name of the action / method to be called on the default controller
// to handle the default / route. This method should return a response string (ie.
// valid html) or a PSR 7 response object containing valid html in its body.
// This default action / method should accept no arguments / parameters.
define('SMVC_APP_DEFAULT_ACTION_NAME', 'actionIndex');

sMVC_GetSuperGlobal(); // this method is first called here to ensure that $_SERVER,
                        // $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV are
                        // captured in their original state by the static $super_globals
                        // variable inside sMVC_GetSuperGlobal(), before any other
                        // library, framework, etc. accesses or modifies any of them.
                        // Subsequent calls to sMVC_GetSuperGlobal(..) will return
                        // the stored values.

/**
 *
 * This function detects which environment your web-app is running in
 * (i.e. one of Production, Development, Staging or Testing).
 *
 * NOTE: Make sure you edit ../config/env.php to return one of SMVC_APP_ENV_DEV,
 *       SMVC_APP_ENV_PRODUCTION, SMVC_APP_ENV_STAGING or SMVC_APP_ENV_TESTING
 *       relevant to the environment you are installing your web-app.
 *
 * @return string
 */
function sMVC_GetCurrentAppEnvironment() {

    return sMVC_DoGetCurrentAppEnvironment(SMVC_APP_ROOT_PATH);
}


$smvc_root_dir = SMVC_APP_ROOT_PATH. DIRECTORY_SEPARATOR;

if( !file_exists("{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php') ) {

    $ini_settings_dist_file_path = "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings-dist.php';
    sMVC_DisplayAndLogFrameworkFileNotFoundError(
        'Missing Ini Settings Configuration File Error',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php',
        $ini_settings_dist_file_path,
        SMVC_APP_ROOT_PATH
    );
    exit;
}

// handle ini settings
require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php';


$app_settings_file_path = "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php';

if( !file_exists($app_settings_file_path) ) {

    $app_settings_dist_file_path = "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings-dist.php';
    sMVC_DisplayAndLogFrameworkFileNotFoundError(
        'Missing App Settings Configuration File Error',
        $app_settings_file_path,
        $app_settings_dist_file_path,
        SMVC_APP_ROOT_PATH
    );
    exit;
}

$app_settings = require_once $app_settings_file_path;

$app = new Slim\App($app_settings);
$container = $app->getContainer();

////////////////////////////////////////////////////////////////////////////////
// Start: Dependency Injection Configuration
//        Add objects to the dependency injection container
////////////////////////////////////////////////////////////////////////////////

require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'dependencies.php';

////////////////////////////////////////////////////////////////////////////////
// End Dependency Injection Configuration
////////////////////////////////////////////////////////////////////////////////

$smvc_default_route_handler =
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
    $default_action = SMVC_APP_DEFAULT_ACTION_NAME;
    $default_controller = SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME;

    $default_controller_parts = explode('\\', $default_controller);
    $default_controller_from_uri = \SlimMvcTools\Functions\Str\toDashes(array_pop($default_controller_parts));

    // create default controller
    $default_controller_obj = new $default_controller(
                                        $this,
                                        $default_controller_from_uri,
                                        \SlimMvcTools\Functions\Str\toDashes($default_action),
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

$smvc_route_handler =
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
    $action_method = \SlimMvcTools\Functions\Str\dashesToCamel($args['action']);

    if( SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES ) {

        $action_method = sMVC_PrependAction2ActionMethodName($action_method);
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

    $controller_obj = sMVC_CreateController($this, $args['controller'], $args['action'], $req, $resp);

    if(
        $controller_obj instanceof \SlimMvcTools\Controllers\BaseController
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
        $controller_obj instanceof \SlimMvcTools\Controllers\BaseController
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

        // sMVC_CreateController(..) returned a Response object containing a
        // not found page.
        $resp = $controller_obj;
    }

    return $resp;
};

$smvc_controller_only_route_handler =
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
    $default_action = SMVC_APP_DEFAULT_ACTION_NAME;

    // sMVC_CreateController could return a Response object if $args['controller']
    // doesn't match any existing controller class.
    $controller_object =
        sMVC_CreateController(
            $this, $args['controller'], \SlimMvcTools\Functions\Str\toDashes($default_action), $req, $resp
        );

    if(
        $controller_object instanceof \SlimMvcTools\Controllers\BaseController
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
        $controller_object instanceof \SlimMvcTools\Controllers\BaseController
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

        // sMVC_CreateController(..) returned a Response object containing a
        // not found page.
        $resp = $controller_object;
    }

    return $resp;
};

////////////////////////////////////////////////////////////////////////////////
// Start: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'routes-and-middlewares.php';

////////////////////////////////////////////////////////////////////////////////
// End: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

/////////////////////////////
// Start: mvc routes
/////////////////////////////

if( SMVC_APP_USE_MVC_ROUTES ) {

    // default route
    $app->map( ['GET', 'POST'], '/', $smvc_default_route_handler );

    // controller with no action and params route handler
    $app->map(['GET', 'POST'], '/{controller}[/]', $smvc_controller_only_route_handler);

    // controller with action and optional params route handler
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}[/{parameters:.+}]', $smvc_route_handler);
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}/', $smvc_route_handler);//handle trailing slash
}
/////////////////////////////
// End: mvc routes
/////////////////////////////

$app->run();

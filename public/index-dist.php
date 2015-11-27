<?php
require '../vendor/autoload.php';

define('S3MVC_APP_ENV_DEV', 'development');
define('S3MVC_APP_ENV_PRODUCTION', 'production');
define('S3MVC_APP_ENV_STAGING', 'staging');
define('S3MVC_APP_ENV_TESTING', 'testing');

define('S3MVC_APP_PUBLIC_PATH', dirname(__FILE__));
define('S3MVC_APP_ROOT_PATH', dirname(dirname(__FILE__)));

//If true, the mvc routes will be enabled. If false, then you must explicitly
//define all the routes for your application inside config/routes.php
define('S3MVC_APP_USE_MVC_ROUTES', false);

//If true, the string `action` will be prepended to action method names (if the
//method name does not already start with the string `action`). The resulting 
//method name will be converted to camel case before being executed. 
//If false, then action method names will only be converted to camel 
//case before being executed. 
//NOTE: This setting does not apply to S3MVC_APP_DEFAULT_ACTION_NAME.
//      It only applies to the routes below:
//          '/{controller}/{action}[/{parameters:.+}]'
//          '/{controller}/{action}/'
define('S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES', false);

//This is used to create a controller object to handle the default / route. 
//Must be prefixed with the name-space if the controller class is in a namespace.
define('S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME', '\\Slim3MvcTools\\Controllers\\BaseController');

//This is the name of the action / method to be called on the default controller 
//to handle the default / route. This method should return a response string (ie. 
//valid html) or a PSR 7 response object containing valid html in its body.
//This default action / method should accept no arguments / parameters.
define('S3MVC_APP_DEFAULT_ACTION_NAME', 'actionIndex');

s3MVC_GetSuperGlobal(); //this method is first called here to ensure that $_SERVER, 
                        //$_GET, $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV are 
                        //captured in their original state by the static $super_globals
                        //variable inside s3MVC_GetSuperGlobal(), before any other 
                        //library, framework, etc. accesses or modifies any of them.
                        //Subsequent calls to s3MVC_GetSuperGlobal(..) will return
                        //the stored values.
/**
 * 
 * This function detects which environment your web-app is running in 
 * (i.e. one of Production, Development, Staging or Testing).
 * 
 * NOTE: Make sure you rename /public/env-dist.php to /public/env.php and then
 *       return one of S3MVC_APP_ENV_DEV, S3MVC_APP_ENV_PRODUCTION, S3MVC_APP_ENV_STAGING or 
 *       S3MVC_APP_ENV_TESTING relevant to the environment you are installing your 
 *       web-app.
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

$s3mvc_root_dir = dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR;

//handle ini settings
require_once "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php';

$app = new Slim\App();
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
) {
    //NOTE: inside this function $this refers to $app. $app is automatically  
    //      bound to this closure by Slim 3 when $app->map is called.
    
    //No controller or action was specified, so use default controller and 
    //invoke the default action on it.
    $default_action = S3MVC_APP_DEFAULT_ACTION_NAME;
    $controller = S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME;
    
    //create default controller
    $default_controller_obj = new $controller($this, '', '');

    //invoke default action
    $action_result = $default_controller_obj->$default_action();
    
    if( is_string($action_result) ) {
        
        $response->getBody()->write($action_result); //write the string in the response object as the response body
        
    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; //the action returned a Response object
    }
    
    return $response;
};

$s3mvc_route_handler = 
    function(
        \Psr\Http\Message\ServerRequestInterface $req, 
        \Psr\Http\Message\ResponseInterface $resp, 
        $args
    ) {
        //NOTE: inside this function $this refers to $app. $app is automatically  
        //      bound to this closure by Slim 3 when $app->map is called.
        $container = $this->getContainer();
        $logger = $container->get('logger');

        //Further enhancements:
        //Add an assoc array that contains allowed actions for a controller
        //$map = array('hello'=>'someothercontroller');

        $action_method = \Slim3MvcTools\Functions\Str\dashesToCamel($args['action']);
        
        if( S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES ) {
            
            $action_method = s3MVC_PrependAction2ActionMethodName($action_method);
        }
        
        //strip trailing forward slash
        $params_str = 
                isset($args['parameters'])? rtrim($args['parameters'], '/') : '';
        
        //convert to array of parameters
        $params = empty($params_str)? [] : explode('/', $params_str);

        $regex_4_valid_method_name = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

        if( ! preg_match($regex_4_valid_method_name, $action_method) ) {

            //A valid php class' method name starts with a letter or underscore, 
            //followed by any number of letters, numbers, or underscores.

            //Make sure the controller name is a valid string usable as a class name
            //in php as defined in http://php.net/manual/en/language.oop5.basic.php
            //trigger 404 not found
            $logger->notice("`".__FILE__."` on line ".__LINE__.": Bad action name `{$action_method}`.");
            $notFoundHandler = $container->get('notFoundHandler');
            
            //invoke the not found handler
            return $notFoundHandler($req, $resp);
        }

        $controller_obj = s3MVC_CreateController(
                                $this, $args['controller'], $args['action'], 
                                $req, $resp
                            );

        if( 
            $controller_obj instanceof \Slim3MvcTools\Controllers\BaseController
            && !method_exists($controller_obj, $action_method) 
        ) {
            $controller_class_name = get_class($controller_obj);

            //404 Not Found: Action method does not exist in the controller object.
            $logger->notice("`".__FILE__."` on line ".__LINE__.": The action method `{$action_method}` does not exist in class `{$controller_class_name}`.");
            $notFoundHandler = $container->get('notFoundHandler');
            
            //invoke the not found handler
            return $notFoundHandler($req, $resp);
            
        } else if ( 
            $controller_obj instanceof \Slim3MvcTools\Controllers\BaseController 
        ) {
            //execute the controller's action
            $actn_res = call_user_func_array([$controller_obj, $action_method], $params);
            
            //If we got this far, that means that the action method was successfully 
            //executed on the controller object.
            if( is_string($actn_res) ) {

                $resp->getBody()->write($actn_res); //write the string in the response object as the response body

            } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

                $resp = $actn_res; //the action returned a Response object
            }
        } else {
            
            //s3MVC_CreateController(..) returned a Response object containing a
            //not found page.
            $resp = $controller_obj;
        }
        
        return $resp;
    };

$s3mvc_controller_only_route_handler =             
    function (
        \Psr\Http\Message\ServerRequestInterface $request, 
        \Psr\Http\Message\ResponseInterface $response, 
        $args
    ) {
        //NOTE: inside this function $this refers to $app. $app is automatically  
        //      bound to this closure by Slim 3 when $app->map is called.

        //No action was specified, so invoke the default action on specified 
        //controller.
        $default_action = S3MVC_APP_DEFAULT_ACTION_NAME;

        //s3MVC_CreateController could return a Response object if $args['controller']
        //doesn't match any existing controller class.
        $controller_object = 
            s3MVC_CreateController(
                $this, $args['controller'], '', $request, $response
            );

        //invoke default action
        $actn_res = 
            ($controller_object instanceof \Slim3MvcTools\Controllers\BaseController)
                ? $controller_object->$default_action() : $controller_object;

        if( is_string($actn_res) ) {

            //write the string in the response object as the response body
            $response->getBody()->write($actn_res);

        } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

            $response = $actn_res; //the action returned a Response object
        }

        return $response;
    };

////////////////////////////////////////////////////////////////////////////////
// Start: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

require_once "{$s3mvc_root_dir}config". DIRECTORY_SEPARATOR.'routes.php';

////////////////////////////////////////////////////////////////////////////////
// End: Load app specific routes
////////////////////////////////////////////////////////////////////////////////

/////////////////////////////
// Start: mvc routes
/////////////////////////////

//default route
if( S3MVC_APP_USE_MVC_ROUTES ) {
    
    $app->map( ['GET', 'POST'], '/', $s3mvc_default_route_handler );

    //controller with no action and params route handler
    $app->map(['GET', 'POST'], '/{controller}[/]', $s3mvc_controller_only_route_handler);

    //controller with action and optional params route handler
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}[/{parameters:.+}]', $s3mvc_route_handler);
    $app->map([ 'GET', 'POST' ], '/{controller}/{action}/', $s3mvc_route_handler);//handle trailing slash
}
/////////////////////////////
// End: mvc routes
/////////////////////////////

$app->run();

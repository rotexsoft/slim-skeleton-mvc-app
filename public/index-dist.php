<?php
require '../vendor/autoload.php';

define('S3MVC_APP_ENV_DEV', 'development');
define('S3MVC_APP_ENV_PRODUCTION', 'production');
define('S3MVC_APP_ENV_STAGING', 'staging');
define('S3MVC_APP_ENV_TESTING', 'testing');

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

$default_route_handler = 
function (
    \Psr\Http\Message\ServerRequestInterface $request, 
    \Psr\Http\Message\ResponseInterface $response, 
    $args
) {
    //NOTE: inside this function $this refers to $app. $app is automatically  
    //      bound to this closure by Slim 3 when $app->map is called.
    
    //No controller or action was specified, so use default controller and 
    //invoke the default action on it.
    $container = $this->getContainer();
    $action = $container->get('default_action_name');
    $controller = $container->get('default_controller_class_name');
    
    //create default controller
    $default_controller = new $controller($this, '', '');

    //invoke default action
    $action_result = $default_controller->$action();
    
    if( is_string($action_result) ) {
        
        $response->getBody()->write($action_result); //write the string in the response object as the response body
        
    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; //the action returned a Response object
    }
        
    return $response;
};

$mvc_route_handler = 
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

        $action_method = \Slim3MvcTools\dashesToCamel($args['action']);
        
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
        
        if( !method_exists($controller_obj, $action_method) ) {
            
            $controller_class_name = get_class($controller_obj);

            //404 Not Found: Action method does not exist in the controller object.
            $logger->notice("`".__FILE__."` on line ".__LINE__.": The action method `{$action_method}` does not exist in class `{$controller_class_name}`.");
            $notFoundHandler = $container->get('notFoundHandler');
            
            //invoke the not found handler
            return $notFoundHandler($req, $resp);
        }

        //line below prints the last time the current script (in this case index.php) was modified
        //echo "Last modified: " . date ("F d Y H:i:s.", getlastmod()). '<br>';

        //execute the controller's action
        $actn_res = call_user_func_array([$controller_obj, $action_method], $params);

        //If we got this far, that means that the action method was successfully 
        //executed on the controller object.
        if( is_string($actn_res) ) {

            $resp->getBody()->write($actn_res); //write the string in the response object as the response body

        } elseif ( $actn_res instanceof \Psr\Http\Message\ResponseInterface ) {

            $resp = $actn_res; //the action returned a Response object
        }

        return $resp;
    };

$mvc_controller_only_route_handler =             
    function (
        \Psr\Http\Message\ServerRequestInterface $request, 
        \Psr\Http\Message\ResponseInterface $response, 
        $args
    ) {
        //NOTE: inside this function $this refers to $app. $app is automatically  
        //      bound to this closure by Slim 3 when $app->map is called.

        //No action was specified, so invoke the default action on specified 
        //controller.
        $container = $this->getContainer();
        $action = $container->get('default_action_name');

        //create controller
        $controller_object = 
            s3MVC_CreateController(
                $this, $args['controller'], \Slim3MvcTools\camelToDashes($action), 
                $request, $response
            );

        //invoke default action
        $actn_res = $controller_object->$action();

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
$app->map( ['GET', 'POST'], '/', $default_route_handler );

//controller with no action and params route handler
$app->map(['GET', 'POST'], '/{controller}[/]', $mvc_controller_only_route_handler);

//controller with action and optional params route handler
$app->map([ 'GET', 'POST', 'PUT'], '/{controller}/{action}[/{parameters:.+}]', $mvc_route_handler);
$app->map([ 'GET', 'POST', 'PUT'], '/{controller}/{action}/', $mvc_route_handler);//handle trailing slash

/////////////////////////////
// End: mvc routes
/////////////////////////////

$app->run();

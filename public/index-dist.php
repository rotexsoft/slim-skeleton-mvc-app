<?php
require '../vendor/autoload.php';

define('S3MVC_APP_ENV_DEV', 'development');
define('S3MVC_APP_ENV_PRODUCTION', 'production');
define('S3MVC_APP_ENV_STAGING', 'staging');
define('S3MVC_APP_ENV_TESTING', 'testing');

s3MVC_GetSuperGlobal();//this method is first called here to ensure that $_SERVER 
                       //, $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV are 
                       //captured in their original state by the static $super_globals
                       //variable inside s3MVC_GetSuperGlobal(), before any other 
                       //library, framework, etc. accesses or modifies any of them.
                       //Subsequent calls to s3MVC_GetSuperGlobal(..) will return
                       //the stored values.

//handle ini settings
require_once '.'. DIRECTORY_SEPARATOR .'ini-settings.php';

/**
 * 
 * This function stores a snapshot of the following super globals $_SERVER, $_GET,
 * $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV and then returns the stored values
 * on subsequent calls. (In the case of $_SESSION, a reference to it is kept so that
 * modifying s3MVC_GetSuperGlobal('session') will also modify $_SESSION).
 * 
 * IT IS STRONGLY RECOMMENDED THAY YOU USE LIBRARIES LIKE aura/session 
 * (https://github.com/auraphp/Aura.Session) TO WORK WITH $_SESSION.
 * USING s3MVC_GetSuperGlobal('session') IS HIGHLY DISCOURAGED.
 * 
 * @param string $global_name the name (case-insensitive) of a any of the super 
 *                            globals mentioned above (excluding the $_). For 
 *                            example 'Post', 'pOst', etc.
 *                            s3MVC_GetSuperGlobal('get') === s3MVC_GetSuperGlobal('gEt'), etc.
 * 
 * @param string $key a key in the specified super global. For example $_GET['id']
 *                    is equivalent to s3MVC_GetSuperGlobal('get', 'id');
 * 
 * @param string $default_val the value to return if $key is not an actual key in
 *                            the specified super global.
 * 
 * @return mixed Returns an array containing all values in the specified super 
 *               global if $key and $default_val were not supplied. A value associated
 *               with a specific key in the specified super global is returned or the
 *               $default_val if the specific key is not found in the specified super 
 *               global (this happens when $global_name and $key are supplied;
 *               $default_val may be supplied too). If no parameters were supplied
 *               an array with the following keys 
 *              (`server`, `get`, `post`, `files`, `cookie`, `env` and `session`) 
 *              is returned (the corresponding values will be the value of the 
 *              super global associated with each key).
 * 
 */
function s3MVC_GetSuperGlobal($global_name='', $key='', $default_val='') {
    
    static $super_globals;
    
    if( !$super_globals ) {
        
        $super_globals = [];
        $super_globals['server'] = isset($_SERVER)? $_SERVER : []; //copy
        $super_globals['get'] = isset($_GET)? $_GET : []; //copy
        $super_globals['post'] = isset($_POST)? $_POST : []; //copy
        $super_globals['files'] = isset($_FILES)? $_FILES : []; //copy
        $super_globals['cookie'] = isset($_COOKIE)? $_COOKIE : []; //copy
        $super_globals['env'] = isset($_ENV)? $_ENV : []; //copy
        
        if(isset($_SESSION)) {
            
            $super_globals['session'] =& $_SESSION; //obtain a reference
            
        } else {
            
            $_SESSION = [];
            $super_globals['session'] =& $_SESSION; //obtain a reference
        }
    }
    
    if( empty($global_name) ) {
        
        //return everything
        return $super_globals;
    }
    
    //normalize the global name
    $global_name = strtolower($global_name);
    
    if( strpos($global_name, '$_') === 0 ) {
        
        $global_name = substr($global_name, 2);
    }
    
    if( empty($key) ) {
        
        //return everything for the specified global
        return array_key_exists($global_name, $super_globals)
                                    ? $super_globals[$global_name] : [];
    }
    
    //return value of the specified key in the specified global or the default value
    return array_key_exists($key, $super_globals[$global_name])
                                ? $super_globals[$global_name][$key] : $default_val;
}

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
    
    if(!$current_env) {
        
        $current_env = include '.'.DIRECTORY_SEPARATOR.'env.php';
    }
    
    return $current_env;
}

/**
 * 
 * Returns the base path segment of the URI.
 * It performs the same function as \Slim\Http\Uri::getBasePath()
 * You are strongly advised to use this function instead of 
 * \Slim\Http\Uri::getBasePath(), in order to ensure that your 
 * app will be compatible with other PSR-7 implementations because
 * \Slim\Http\Uri::getBasePath() is not a PSR-7 method.
 * 
 * @return string
 */
function s3MVC_GetBaseUrlPath() {
    
    static $server, $base_path, $has_been_computed;
    
    if( !$server ) {
        
        //copy / capture the super global only once
        $server = s3MVC_GetSuperGlobal('server');
    }
    
    if( !$base_path && !$has_been_computed ) {
        
        $base_path = '';
        $has_been_computed = true;
        $requestScriptName = parse_url($server['SCRIPT_NAME'], PHP_URL_PATH);
        $requestScriptDir = dirname($requestScriptName);
        $requestUri = parse_url($server['REQUEST_URI'], PHP_URL_PATH);
        
        if (stripos($requestUri, $requestScriptName) === 0) {

            $base_path = $requestScriptName;

        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {

            $base_path = $requestScriptDir;
        }
    }
    
    return $base_path;
}

//this function is only for debugging purposes
function echo_with_pre($v){ $v=(!is_string($v))?var_export($v, true):$v; echo "<pre>$v</pre>"; }

$app = new Slim\App();
$container = $app->getContainer();

////////////////////////////////////////////////////////////////////////////////
// Start: Dependency Injection Configuration
//        Add objects to the dependency injection container
////////////////////////////////////////////////////////////////////////////////

require_once '.'. DIRECTORY_SEPARATOR .'dependencies.php';

////////////////////////////////////////////////////////////////////////////////
// End Dependency Injection Configuration
////////////////////////////////////////////////////////////////////////////////

$default_route_handler = 
function (
    \Psr\Http\Message\ServerRequestInterface $request, 
    \Psr\Http\Message\ResponseInterface $response, 
    $args
) {
    //TODO: Make default action configurable via 
    //the dependency injection container.
    //Re-direct to default action
    $redirect_path = s3MVC_GetBaseUrlPath()
                    ."/base-controller/action-index";

    return $response->withHeader('Location', $redirect_path);
};

$mvc_route_handler = 
function(
    \Psr\Http\Message\ServerRequestInterface $request, 
    \Psr\Http\Message\ResponseInterface $response, 
    $args
) {
    //NOTE: inside this function $this refers to $app. $app is automatically  
    //      bound to this closure by Slim 3 when $app->map is called.
    $container = $this->getContainer();
    $logger = $container->get('logger');

    //Further enhancements:
    //Add an assoc array that contains allowed actions for a controller
    //$map = array('hello'=>'someothercontroller');
    
    $controller_class_name = \Slim3Mvc\OtherClasses\dashesToStudly($args['controller']);
    $action_method = \Slim3Mvc\OtherClasses\dashesToCamel($args['action']);
    $parameters_str = array_key_exists('parameters', $args)? rtrim($args['parameters'], '/') : '';//strip trailing forward slash
    $params = empty($parameters_str)? [] : explode('/', $parameters_str);//convert to array of parameters

    $regex_4_valid_class_or_method_name = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';
    
    if( !preg_match($regex_4_valid_class_or_method_name, $controller_class_name) ) {
        
        //A valid php class name starts with a letter or underscore, followed by 
        //any number of letters, numbers, or underscores.
         
        //Make sure the controller name is a valid string usable as a class name
        //in php as defined in http://php.net/manual/en/language.oop5.basic.php
        //trigger 404 not found
        $logger->notice("Bad controller name `{$controller_class_name}`");
        $notFoundHandler = $container->get('notFoundHandler');
        return $notFoundHandler($request, $response);//invoke the not found handler
    }
    
    if( ! preg_match($regex_4_valid_class_or_method_name, $action_method) ) {
        
        //A valid php class' method name starts with a letter or underscore, 
        //followed by any number of letters, numbers, or underscores.
         
        //Make sure the controller name is a valid string usable as a class name
        //in php as defined in http://php.net/manual/en/language.oop5.basic.php
        //trigger 404 not found
        $logger->notice("Bad action name `{$action_method}`.");
        $notFoundHandler = $container->get('notFoundHandler');
        return $notFoundHandler($request, $response);//invoke the not found handler
    }
    
    if( !class_exists($controller_class_name) ) {
        
        $namespace_4_controllers = $container->get('namespace_for_controllers');
        
        //try to prepend name space
        if( class_exists($namespace_4_controllers.$controller_class_name) ) {
            
            $controller_class_name = $namespace_4_controllers.$controller_class_name;
            
        } else {
            
            //404 Not Found: Controller class not found.
            $logger->notice("Class `{$controller_class_name}` does not exist.");
            $notFoundHandler = $container->get('notFoundHandler');
            return $notFoundHandler($request, $response);
        }
    }
    
    //Create the controller object
    $controller_object = 
        new $controller_class_name($this, $args['controller'], $args['action']);
    
    if( !method_exists($controller_object, $action_method) ) {
        
        //404 Not Found: Action method does not exist in the controller object.
        $logger->notice("The action method `{$action_method}` does not exist in class `{$controller_class_name}`.");
        $notFoundHandler = $container->get('notFoundHandler');
        return $notFoundHandler($request, $response);
    }
    
    //line below prints the last time the current script (in this case index.php) was modified
    //echo "Last modified: " . date ("F d Y H:i:s.", getlastmod()). '<br>';
    
    //execute the controller's action
    $action_result = call_user_func_array(array($controller_object, $action_method), $params);
    
    //If we got this far, that means that the action method was successfully 
    //executed on the controller object.
    if( is_string($action_result) ) {
        
        $response->getBody()->write($action_result); //write the string in the response object as the response body
        
    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; //the action returned a Response object
    }
    
    return $response;
};

$mvc_controller_only_route_handler =             
function (
    \Psr\Http\Message\ServerRequestInterface $request, 
    \Psr\Http\Message\ResponseInterface $response, 
    $args
) {
    //TODO: Make default action configurable via 
    //the dependency injection container.
    //Re-direct to default action
    $redirect_path = s3MVC_GetBaseUrlPath()."/{$args['controller']}/action-index";
    $original_path = s3MVC_GetBaseUrlPath().'/'.$request->getUri()->getPath();
    
    $scheme = $request->getUri()->getScheme();
    $authority = $request->getUri()->getAuthority();
    $host = ($scheme ? $scheme . ':' : '') . ($authority ? '//' . $authority : '');

    //log redirection
    $log_msg = "ROUTE WITH ONLY CONTROLLER REDIRECTING TO USE DEFAULT ACTION:"
             . " Redirecting from `{$host}$original_path` to `{$host}$redirect_path` ";
    
    $this->getContainer()->get('logger')->notice($log_msg);
    
    return $response->withHeader('Location', $redirect_path);
};

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

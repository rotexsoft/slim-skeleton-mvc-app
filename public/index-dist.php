<?php
require '../vendor/autoload.php';

define('APP_ENV_DEV', 'development');
define('APP_ENV_PROD', 'production');
define('APP_ENV_STAGING', 'staging');
define('APP_ENV_TEST', 'test');

function getCurrentAppEnvironment() {
    
    $server_name_in_request = $_SERVER['SERVER_NAME'];
    $request_uri_in_request = $_SERVER['REQUEST_URI'];
    
    if( $server_name_in_request === 'promis.cfs.nrcan.gc.ca' ) {
        
        return APP_ENV_PROD;
        
    } else if ( $server_name_in_request === 'cfsdev.nofc.cfs.nrcan.gc.ca' ) {
        
        if( strpos($request_uri_in_request, 'promis-staging') !== FALSE ) {
            
            return APP_ENV_STAGING;
        }
    }
    
    //Always default to dev
    return APP_ENV_DEV;
}

function echo_with_pre($var) {
    
    //this function is only for debugging purposes
    if( is_array($var) ) { $var = print_r($var, true); } echo "<pre>$var</pre>";
}

$app = new Slim\App();
$container = $app->getContainer();


////////////////////////////////////////////////////////////////////////////////
// Start: Dependency Injection Configuration
//        Add objects to the dependency injection container
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////
// Start configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

$container['logger'] = function ($c) {

    $opts = [
        'dateFormat'=>'Y-M-d g:i:s A', 
        'filename'=>'daily_log_'.date('Y_M_d').'.txt',
    ];
    $dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs' .DIRECTORY_SEPARATOR;
    
    return new Katzgrau\KLogger\Logger($dir, Psr\Log\LogLevel::DEBUG, $opts);
};

//Override the default 500 System Error Handler
$container['errorHandler'] = function ($c) {
    
    return function ($request, $response, $exception) use ($c) {
                
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = 'Something went wrong!<br>'. $exception->getMessage();
        $output_str = $layout_renderer->getAsString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        return $response->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write($output_str);
    };
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    
    return function ($request, $response) use ($c) {
        
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = 
            "Page not found: {$request->getUri()->getBaseUrl()}/{$request->getUri()->getPath()}";
            
        $output_str = $layout_renderer->getAsString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        $c['logger']->notice($layout_content); //log the not found message
        
        return $response->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write($output_str);
    };
};

//Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($c) {
    
    return function ($request, $response, $methods) use ($c) {
        
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = 'Method must be one of: ' . implode(', ', $methods);
        $output_str = $layout_renderer->getAsString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        return $response->withStatus(405)
                        ->withHeader('Allow', implode(', ', $methods))
                        ->withHeader('Content-type', 'text/html')
                        ->getBody()
                        ->write($output_str);
    };
};

//Change this to the namespcace for your web-app's controller classes or
//set it to an empty string if your controllers are in the default global
//namespace.
$container['namespace_for_controllers'] = '\\CfsSlim3\\Controllers\\';

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_layout_renderer']
    return new \CfsSlim3\OtherClasses\View([]);
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_view_renderer']
    return new \CfsSlim3\OtherClasses\View([]);
});

////////////////////////////////////////////////////////////////////////////////
// End configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

if( getCurrentAppEnvironment() === APP_ENV_DEV ) {
    
    //configuration specific to the development environment
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Aura.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    $container['aura_auth_factory'] = function ($c) {
        
        return new \Aura\Auth\AuthFactory($_COOKIE);
    };
    
    $container['aura_auth_object'] = function ($c) {
                
        return $c['aura_auth_factory']->newInstance();
    };
    
    $container['aura_auth_adapter_object'] = function ($c) {
        
        $server = 'ldap.server.org.ca';
        $cfs_ldap_adapter_specific_params = array(
            'filter'                        => '\w',
            'basedn'                        => 'DC=yada,DC=yada,DC=yada,DC=yada',
            'bindpw'                        => 'Pa$$w0rd',
            'limit'                         => array('dn'),
            'searchfilter'                  => 'somefilter',
            'successful_login_callback' 	=> function($login_timestamp_string) {
                                                    echo $login_timestamp_string.'<br>';
                                               }
        );
        $dnformat = 'ou=Company Name,dc=Department Name,cn=users';
        
        return new \Cfs\Authenticator\Adapter\CfsLdapAdapter(
                                            new \Aura\Auth\Phpfunc(),
                                            $server, 
                                            $dnformat,
                                            array(), 
                                            $cfs_ldap_adapter_specific_params
                                        );
    };
    
    $container['aura_login_service'] = function ($c) {
        
        $auth_factory = $c['aura_auth_factory'];
        $auth_adapter = $c['aura_auth_adapter_object'];
        return $auth_factory->newLoginService($auth_adapter);
    };
    
    $container['aura_logout_service'] = function ($c) {
        
        $auth_factory = $c['aura_auth_factory'];
        $auth_adapter = $c['aura_auth_adapter_object'];
        return $auth_factory->newLogoutService($auth_adapter);
    };
    
    $container['aura_resume_service'] = function ($c) {
        
        $auth_factory = $c['aura_auth_factory'];
        $auth_adapter = $c['aura_auth_adapter_object'];
        return $auth_factory->newResumeService($auth_adapter);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Aura.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////////////
// End Dependency Injection Configuration
////////////////////////////////////////////////////////////////////////////////

$app->map(['GET', 'POST'], '/', function ($request, $response, $args) {
    
    //TODO: Make default action configurable via the dependency injection container.
    //Re-direct to default action
    $redirect_path = $request->getUri()->getBasePath()."/base-controller/action-index";
    return $response->withHeader('Location', $redirect_path);
});

$app->map(['GET', 'POST'], '/{controller}/{action}[/{parameters:.+}]', function ($request, $response, $args) {

    //ServerRequestInterface $request, ResponseInterface $response 
    
    //NOTE: inside this function $this refers to $app. $app is automatically bound 
    //      to this closure by the Slim 3 when $app->map is called.
    $container_4_app = $this->getContainer();
    $logger = $container_4_app->get('logger');
    
    //Further enhancements:
    //Add an assoc array that contains allowed actions for a controller
    //$map = array('hello'=>'someothercontroller');
    
    $controller_class_name = \CfsSlim3\OtherClasses\dashesToStudly($args['controller']);
    $action_method = \CfsSlim3\OtherClasses\dashesToCamel($args['action']);
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
        $notFoundHandler = $container_4_app->get('notFoundHandler');
        return $notFoundHandler($request, $response);//invoke the not found handler
    }
    
    if( ! preg_match($regex_4_valid_class_or_method_name, $action_method) ) {
        
        //A valid php class' method name starts with a letter or underscore, 
        //followed by any number of letters, numbers, or underscores.
         
        //Make sure the controller name is a valid string usable as a class name
        //in php as defined in http://php.net/manual/en/language.oop5.basic.php
        //trigger 404 not found
        $logger->notice("Bad action name `{$action_method}`.");
        $notFoundHandler = $container_4_app->get('notFoundHandler');
        return $notFoundHandler($request, $response);//invoke the not found handler
    }
    
    if( !class_exists($controller_class_name) ) {
        
        $namespace_4_controllers = $container_4_app->get('namespace_for_controllers');
        
        //try to prepend name space
        if( class_exists($namespace_4_controllers.$controller_class_name) ) {
            
            $controller_class_name = $namespace_4_controllers.$controller_class_name;
            
        } else {
            
            //404 not Found
            $notFoundHandler = $container_4_app->get('notFoundHandler');
            return $notFoundHandler($request, $response);
        }
    }
    
    //Create the controller object
    $controller_object = 
        new $controller_class_name($this, $args['controller'], $args['action']);
    
    if( !method_exists($controller_object, $action_method) ) {
        
        //trigger 404 not found
        $notFoundHandler = $container_4_app->get('notFoundHandler');
        return $notFoundHandler($request, $response);
    }
    
    //line below prints the last time the current script (in this case index.php) was modified
    //echo "Last modified: " . date ("F d Y H:i:s.", getlastmod()). '<br>';
    
    //execute the controller's action
    $action_result = call_user_func_array(array($controller_object, $action_method), $params);

    if( is_string($action_result) ) {
        
        $response->getBody()->write($action_result); //write the string in the response object as the response body
        
    } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

        $response = $action_result; //the action returned a Response object
    }
    
    return $response;
});

$app->run();

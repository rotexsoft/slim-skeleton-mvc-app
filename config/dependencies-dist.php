<?php  //Dependencies

////////////////////////////////////////////////////////////////////////////////
// Start configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////
                                      
$container['logger'] = function () {

    $ds = DIRECTORY_SEPARATOR;
    $log_type = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
    $file = SMVC_APP_ROOT_PATH . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
    
    $adapter = new \Vespula\Log\Adapter\ErrorLog($log_type , $file);
    $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
    $adapter->setMinLevel(Psr\Log\LogLevel::DEBUG);
    $adapter->setDateFormat('Y-M-d g:i:s A');
    
    return new \Vespula\Log\Log('error-log', $adapter);
};

// this MUST be replaced with any subclass of \\SlimMvcTools\\Controllers\\BaseController
$container['errorHandlerClass'] = \SlimMvcTools\Controllers\HttpServerErrorController::class;

//Override the default 500 System Error Handler
$container['errorHandler'] = function ($c) {
    
    return function (
            \Psr\Http\Message\ServerRequestInterface $request, 
            \Psr\Http\Message\ResponseInterface $response, 
            \Exception $exception
          ) use ($c) {
        
        $errorHandlerClass = $c['errorHandlerClass'];
        $errorHandler = new $errorHandlerClass( $c, '', '', $request, $response);

        $response_from_pre_action = $errorHandler->preAction();
        
        // invoke the server error handler
        $action_response = $errorHandler->generateServerErrorResponse($exception, $request, $response_from_pre_action);
        
        return $errorHandler->postAction($action_response);
    };
};

// this MUST be replaced with any subclass of \\SlimMvcTools\\Controllers\\BaseController
$container['notFoundHandlerClass'] = \SlimMvcTools\Controllers\HttpNotFoundController::class;

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response,
                $_404_page_contents_str = null,
                $_404_page_additional_log_msg = null
            ) use ($c) {
 
        $notFoundHandlerClass = $c['notFoundHandlerClass'];
        $notFoundHandler = new $notFoundHandlerClass( $c, '', '', $request, $response);
        
        $notFoundHandler->setResponse( $notFoundHandler->preAction() );
        
        //invoke the not found handler
        $action_response = $notFoundHandler->actionHttpNotFound($_404_page_contents_str, $_404_page_additional_log_msg);
        
        return $notFoundHandler->postAction($action_response);
    };
};

// this MUST be replaced with any subclass of \\SlimMvcTools\\Controllers\\BaseController
$container['notAllowedHandlerClass'] = \SlimMvcTools\Controllers\HttpMethodNotAllowedController::class;

//Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response, 
                $methods
            ) use ($c) {
        
        $notAllowedHandlerClass = $c['notAllowedHandlerClass'];
        $notAllowedHandler = new $notAllowedHandlerClass( $c, '', '', $request, $response);

        $response_from_pre_action = $notAllowedHandler->preAction();
        
        // invoke the notAllowed handler
        $action_response = $notAllowedHandler->generateNotAllowedResponse($methods, $request, $response_from_pre_action);
        
        return $notAllowedHandler->postAction($action_response);
    };
};

//Add the namespcace(s) for your web-app's controller classes or leave it
//as is, if your controllers are in the default global namespace.
//The namespaces are searched in the order which they are added 
//to the array. It would make sense to add the namespaces for your
//application in the front part of these arrays so that if a controller class 
//exists in \SlimMvcTools\Controllers\ and / or \Slim3SkeletonMvcApp\Controllers\  
//and in your application's controller namespace(s) controllers
//in your application's namespaces are 
//Make sure you add the trailing slashes.
$container['namespaces_for_controllers'] = ['\\SlimMvcTools\\Controllers\\', '\\Slim3SkeletonMvcApp\\Controllers\\'];

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function () {
    
    //return a new instance on each access to $container['new_layout_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
    $layout_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_layout_files]);
    
    return $layout_renderer;
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function () {
    
    //return a new instance on each access to $container['new_view_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
    $view_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_view_files]);

    return $view_renderer;
});

////////////////////////////////////////////////////////////////////////////////
// End configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////
// Start Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////   

if( sMVC_GetCurrentAppEnvironment() === SMVC_APP_ENV_PRODUCTION ) {
    
    //configuration specific to the production environment
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////    
    $container['vespula_auth'] = function ($c) {
        
        //Optionally pass a maximum idle time and a time until the session 
        //expires (in seconds)
        $expire = 3600;
        $max_idle = 1200;
        $session = new \Vespula\Auth\Session\Session($max_idle, $expire, 'VESPULA_AUTH_DATA_'.SMVC_APP_ROOT_PATH);

        $bind_options = $c->get('settings')['bind_options'];

        $ldap_options = [
            LDAP_OPT_PROTOCOL_VERSION=>3
        ];
        
        $attributes = [
            'email',
            'givenname'
        ];

        $uri = $c->get('settings')['ldap_server_addr'];
        $dn = null;
        
        $adapter = new \Vespula\Auth\Adapter\Ldap(
                        $uri, $dn, $bind_options, $ldap_options, $attributes
                    );
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    
} else {
    
    //configuration specific to non-production environments
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    $container['vespula_auth'] = function () {
        
        $pdo = new \PDO(
                    'sqlite::memory:', 
                    null, 
                    null, 
                    [
                        PDO::ATTR_PERSISTENT => true, 
                        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
                    ]
                ); 
        
        $pass1 = password_hash('admin' , PASSWORD_DEFAULT);
        $pass2 = password_hash('root' , PASSWORD_DEFAULT);

        $sql = <<<SQL
DROP TABLE IF EXISTS "user_authentication_accounts";
CREATE TABLE user_authentication_accounts (
    username VARCHAR(255), password VARCHAR(255)
);
INSERT INTO "user_authentication_accounts" VALUES( 'admin', '$pass1' );
INSERT INTO "user_authentication_accounts" VALUES( 'root', '$pass2' );
SQL;
        $pdo->exec($sql); //add two default user accounts
        
        //Optionally pass a maximum idle time and a time until the session 
        //expires (in seconds)
        $expire = 3600;
        $max_idle = 1200;
        $session = new \Vespula\Auth\Session\Session($max_idle, $expire);
        
        $cols = ['username', 'password'];
        $from = 'user_authentication_accounts';
        $where = ''; //optional

        $adapter = new \Vespula\Auth\Adapter\Sql($pdo, $from, $cols, $where);
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////////
// End Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////  
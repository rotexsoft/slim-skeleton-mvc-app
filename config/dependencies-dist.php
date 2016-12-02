<?php  //Dependencies

////////////////////////////////////////////////////////////////////////////////
// Start configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////
                                      
$container['logger'] = function () {

    $ds = DIRECTORY_SEPARATOR;
    $log_type = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
    $file = S3MVC_APP_ROOT_PATH . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
    
    $adapter = new \Vespula\Log\Adapter\ErrorLog($log_type , $file);
    $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
    $adapter->setMinLevel(Psr\Log\LogLevel::DEBUG);
    $adapter->setDateFormat('Y-M-d g:i:s A');
    
    return new \Vespula\Log\Log($adapter);
};

// this can be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['errorHandlerClass'] = '\\Slim3MvcTools\\Controllers\\HttpServerErrorController';

//Override the default 500 System Error Handler
$container['errorHandler'] = function ($c) {
    
    return function (
            \Psr\Http\Message\ServerRequestInterface $request, 
            \Psr\Http\Message\ResponseInterface $response, 
            \Exception $exception
          ) use ($c) {
        
        $errorHandlerClass = $c['errorHandlerClass'];
        $errorHandler = new $errorHandlerClass( $c, '', '', $request, $response);

        $errorHandler->preAction();
        
        // invoke the server error handler
        $action_result = $errorHandler->generateServerErrorResponse($exception, $request, $response);
        $errorHandler->postAction();

        return $action_result;
    };
};

// this can be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['notFoundHandlerClass'] = '\\Slim3MvcTools\\Controllers\\HttpNotFoundController';

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response
            ) use ($c) {
 
        $notFoundHandlerClass = $c['notFoundHandlerClass'];
        $notFoundHandler = new $notFoundHandlerClass( $c, '', '', $request, $response);

        $notFoundHandler->preAction();
        
        //invoke the not found handler 
        $action_result = $notFoundHandler->actionHttpNotFound();
        $notFoundHandler->postAction();

        return $action_result;
    };
};

// this can be replaced with any subclass of \\Slim3MvcTools\\Controllers\\BaseController
$container['notAllowedHandlerClass'] = '\\Slim3MvcTools\\Controllers\\HttpMethodNotAllowedController';

//Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response, 
                $methods
            ) use ($c) {
        
        $notAllowedHandlerClass = $c['notAllowedHandlerClass'];
        $notAllowedHandler = new $notAllowedHandlerClass( $c, '', '', $request, $response);

        $notAllowedHandler->preAction();
        
        // invoke the notAllowed handler
        $action_result = $notAllowedHandler->generateNotAllowedResponse($methods, $request, $response);
        $notAllowedHandler->postAction();

        return $action_result;
    };
};

//Add the namespcace(s) for your web-app's controller classes or leave it
//as is, if your controllers are in the default global namespace.
//Make sure you add the trailing slashes.
$container['namespaces_for_controllers'] = ['\\Slim3MvcTools\\Controllers\\'];

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function () {
    
    //return a new instance on each access to $container['new_layout_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = S3MVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
    $layout_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_layout_files]);
    
    return $layout_renderer;
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function () {
    
    //return a new instance on each access to $container['new_view_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = S3MVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
    $view_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_view_files]);

    return $view_renderer;
});

////////////////////////////////////////////////////////////////////////////////
// End configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////
// Start Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////   

if( s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_PRODUCTION ) {
    
    //configuration specific to the production environment
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////    
    $container['vespula_auth'] = function () {
        
        //Optionally pass a maximum idle time and a time until the session 
        //expires (in seconds)
        $expire = 3600;
        $max_idle = 1200;
        $session = new \Vespula\Auth\Session\Session($max_idle, $expire);

        /*
         * `basedn`: The base dn to search through
         * `binddn`: The dn used to bind to
         * `bindpw`: A password used to bind to the server using the binddn
         * `filter`: A filter used to search for the user. Eg. samaccountname=%s
         */
        $bind_options = [
            'basedn' => 'OU=MyCompany,OU=Edmonton,OU=Alberta',
            'bindn'  => 'cn=%s,OU=Users,OU=MyCompany,OU=Edmonton,OU=Alberta',
            'bindpw' => 'Pa$$w0rd',
            'filter' => 'samaccountname=%s',
        ];

        $ldap_options = [
            LDAP_OPT_PROTOCOL_VERSION=>3
        ];
        
        $attributes = [
            'email',
            'givenname'
        ];

        $uri = 'ldap.server.org.ca';
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
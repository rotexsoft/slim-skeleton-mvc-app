<?php

//dependencies

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
    
    return function (
            \Psr\Http\Message\ServerRequestInterface $request, 
            \Psr\Http\Message\ResponseInterface $response, 
            \Exception $exception
          ) use ($c) {
                
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/site-layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = 'Something went wrong!';
        
        $exception_info = $exception->getMessage()
                        . ' on line '.$exception->getLine() 
                        . ' in `'.$exception->getFile().'`'
                        . '<br><br>'.$exception->getTraceAsString();
        
        if(s3MVC_GetCurrentAppEnvironment() !== S3MVC_APP_ENV_PRODUCTION) {
            
            //Append exception message if we are not in production.
            $layout_content .= '<br>'.$exception_info;
        }
        
        $output_str = $layout_renderer->renderAsString(
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        //log the error message
        $c['logger']->error(str_replace('<br>', PHP_EOL,"HTTP 500: $exception_info")); 
        
        $new_response = $response->withStatus(500)
                                 ->withHeader('Content-Type', 'text/html');
        
        $new_response->getBody()->write($output_str);
        
        return $new_response;
    };
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response
            ) use ($c) {
  
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/site-layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = "Page not found: ".$request->getUri()->__toString();
            
        $output_str = $layout_renderer->renderAsString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        $c['logger']->notice("HTTP 404: $layout_content"); //log the not found message
        
        $new_response = $response->withStatus(404)
                                 ->withHeader('Content-Type', 'text/html');
        
        $new_response->getBody()->write($output_str);
      
        return $new_response;
    };
};

//Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($c) {
    
    return function (
                \Psr\Http\Message\ServerRequestInterface $request, 
                \Psr\Http\Message\ResponseInterface $response, 
                $methods
            ) use ($c) {
        
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/site-layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $_405_message1 = 'Http method `'. strtoupper($request->getMethod())
                     . '` not allowed on the url `'.$request->getUri()->__toString() 
                     . '` ';
        $_405_message2 = 'HTTP Method must be one of: ' 
                         . implode( ' or ', array_map(function($val){ return "`$val`";}, $methods) );
        
        $layout_content = "$_405_message1<br>$_405_message2";
        $output_str = $layout_renderer->renderAsString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        $log_message = "$_405_message1. $_405_message2";
        
        $c['logger']->notice("HTTP 405: $log_message"); //log the message
        
        $new_response = $response->withStatus(405)
                                 ->withHeader('Allow', implode(', ', $methods))
                                 ->withHeader('Content-Type', 'text/html');
        
        $new_response->getBody()->write($output_str);
        
        return $new_response;
    };
};

//Add the namespcace(s) for your web-app's controller classes or leave it
//as is, if your controllers are in the default global namespace.
//Make sure you add the trailing slashes.
$container['namespaces_for_controllers'] = ['\\Slim3Mvc\\'];

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_layout_renderer']
    return new \Slim3Mvc\View([]);
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_view_renderer']
    return new \Slim3Mvc\View([]);
});

////////////////////////////////////////////////////////////////////////////////
// End configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////
// Start Aura.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////   

$container['aura_auth_factory'] = function ($c) {

    return new \Aura\Auth\AuthFactory(s3MVC_GetSuperGlobal('cookie'));
};

$container['aura_auth_object'] = function ($c) {

    return $c['aura_auth_factory']->newInstance();
};

if( s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_PRODUCTION ) {
    
    //configuration specific to the production environment
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Aura.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////    
    $container['aura_auth_adapter_object'] = function ($c) {
        
        $logger = $c['logger'];
        $server = 'ldap.server.org.ca';
        $ldap_adapter_specific_params = array(
            'filter'                        => '\w',
            'basedn'                        => 'DC=yada,DC=yada,DC=yada,DC=yada',
            'bindpw'                        => 'Pa$$w0rd',
            'limit'                         => array('dn'),
            'searchfilter'                  => 'somefilter',
            'successful_login_callback' 	=> function($login_timestamp_string) use ($logger) {
                                                    $logger->notice($login_timestamp_string);
                                               }
        );
        $dnformat = 'ou=Company Name,dc=Department Name,cn=users';
        
        return new \Cfs\Authenticator\Adapter\CfsLdapAdapter(
                                            new \Aura\Auth\Phpfunc(),
                                            $server, 
                                            $dnformat,
                                            array(), 
                                            $ldap_adapter_specific_params
                                        );
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Aura.Auth LDAP Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    
} else {
    
    //configuration specific to non-production environments
    
    ////////////////////////////////////////////////////////////////////////////
    // Start Aura.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    
    $container['aura_auth_adapter_object'] = function ($c) {
                
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
        //add two default user accounts
        $pdo->exec($sql);
        
        $hash = new \Aura\Auth\Verifier\PasswordVerifier(PASSWORD_DEFAULT);
        $from = 'user_authentication_accounts';
        $cols = ['username', 'password'];

        return $c['aura_auth_factory']->newPdoAdapter($pdo, $hash, $cols, $from);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Aura.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
}

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
// End Aura.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////  
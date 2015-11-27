<?php
//dependencies

////////////////////////////////////////////////////////////////////////////////
// Start configuration specific to all environments
////////////////////////////////////////////////////////////////////////////////

//If true, the mvc routes will be enabled. If false, then you must explicitly
//define the routes for your application inside config/routes.php
$container['use_mvc_routes'] = true;
                                      
$container['logger'] = function ($c) {

    $ds = DIRECTORY_SEPARATOR;
    $log_type = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
    $file = dirname(__DIR__) . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
    
    $adapter = new \Vespula\Log\Adapter\ErrorLog($log_type , $file);
    $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
    $adapter->setMinLevel(Psr\Log\LogLevel::DEBUG);
    $adapter->setDateFormat('Y-M-d g:i:s A');
    
    return new \Vespula\Log\Log($adapter);
};

//Override the default 500 System Error Handler
$container['errorHandler'] = function ($c) {
    
    return function (
            \Psr\Http\Message\ServerRequestInterface $request, 
            \Psr\Http\Message\ResponseInterface $response, 
            \Exception $exception
          ) use ($c) {
                
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = 'Something went wrong!';
        
        $exception_info = $exception->getMessage()
                        . ' on line '.$exception->getLine() 
                        . ' in `'.$exception->getFile().'`'
                        . '<br><br>'.$exception->getTraceAsString();
        
        if(s3MVC_GetCurrentAppEnvironment() !== S3MVC_APP_ENV_PRODUCTION) {
            
            //Append exception message if we are not in production.
            $layout_content .= '<br>'.nl2br($exception_info);
        }
        
        $output_str = $layout_renderer->renderToString(
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        try {
            
            //log the error message
            $c['logger']->error(str_replace('<br>', PHP_EOL, "HTTP 500: $exception_info"));

        } catch (\Exception $exc) {
            
            //do something else
            //echo $exc->getTraceAsString();
        }
        
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
  
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $layout_content = "Page not found: ".$request->getUri()->__toString();
            
        $output_str = $layout_renderer->renderToString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        try {
            
            //log the not found message
            $c['logger']->notice("HTTP 404: $layout_content");
            
        } catch (Exception $exc) {
            
            //do something else
            //echo $exc->getTraceAsString();
        }

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
        
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../src/layout-templates';
        
        $layout_renderer = $c['new_layout_renderer']; //get the view object for rendering layouts
        $layout_renderer->appendPath($path_2_layout_files);
        
        $_405_message1 = 'Http method `'. strtoupper($request->getMethod())
                     . '` not allowed on the url `'.$request->getUri()->__toString() 
                     . '` ';
        $_405_message2 = 'HTTP Method must be one of: ' 
                         . implode( ' or ', array_map(function($val){ return "`$val`";}, $methods) );
        
        $layout_content = "$_405_message1<br>$_405_message2";
        $output_str = $layout_renderer->renderToString( 
                            'main-template.php', 
                            ['content'=>$layout_content, 'request_obj'=>$request] 
                        );
        
        $log_message = "$_405_message1. $_405_message2";
        
        try {
            
            $c['logger']->notice("HTTP 405: $log_message"); //log the message
            
        } catch (\Exception $exc) {
            
            //do something else
            //echo $exc->getTraceAsString();
        }

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
$container['namespaces_for_controllers'] = ['\\Slim3MvcTools\\'];

//the `default_controller_class_name` is used to create a controller object to 
//handle the default / route. Must be prefixed with the name-space if 
//the controller class is in a namespace
$container['default_controller_class_name'] = '\Slim3MvcTools\BaseController';

//the `default_action_name` is the name of the action / method to be 
//called on the default controller to handle the default / route.
//This method should return a response string (ie. valid html) or a PSR 7
//response object containing valid html in its body.
//This default action / method should accept no arguments / parameters.
$container['default_action_name'] = 'actionIndex';

//Object for rendering layout files
$container['new_layout_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_layout_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = __DIR__."{$ds}..{$ds}src{$ds}layout-templates";
    $layout_renderer = new \Rotexsoft\Renderer();
    $layout_renderer->appendPath($path_2_layout_files);
    
    return $layout_renderer;
});

//Object for rendering view files
$container['new_view_renderer'] = $container->factory(function ($c) {
    
    //return a new instance on each access to $container['new_view_renderer']
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = __DIR__."{$ds}..{$ds}src{$ds}views{$ds}base";
    $view_renderer = new \Rotexsoft\Renderer();
    $view_renderer->appendPath($path_2_view_files);
    
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
    $container['vespula_auth'] = function ($c) {
        
        if( session_status() !== PHP_SESSION_ACTIVE ) { session_start(); }
        
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
    $container['vespula_auth'] = function ($c) {
        
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
        $from = 'user';
        $where = ''; //optional

        $adapter = new \Vespula\Auth\Adapter\Sql($pdo, $cols, $from, $where);
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
}
////////////////////////////////////////////////////////////////////////////
// End Vespula.Auth Authentication setup
////////////////////////////////////////////////////////////////////////////  
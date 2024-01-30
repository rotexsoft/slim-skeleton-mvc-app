<?php
use \SlimMvcTools\ContainerKeys,
    \SlimMvcTools\Controllers\BaseController;

////////////////////////////////////////////////////////////////////////////////
// Configure all the dependencies you'll need in your application in this file.
//
// Also call all the needed Setters on \Slim\Factory\AppFactory at the very end
// of this file right before the return statement in this file.
////////////////////////////////////////////////////////////////////////////////

// $container must be an instance of \Psr\Container\ContainerInterface
// It must be returned at the end of this file.
$container = new \SlimMvcTools\Container();
$container[ContainerKeys::APP_SETTINGS] = $app_settings;

// See https://learn.microsoft.com/en-us/cpp/c-runtime-library/language-strings?view=msvc-170
$container[ContainerKeys::DEFAULT_LOCALE] = 'en_US';
$container[ContainerKeys::VALID_LOCALES] = ['en_US', 'fr_CA']; // add more values for languages you will be supporting in your application
$container[ContainerKeys::LOCALE_OBJ] = function ($c) {

    // See https://packagist.org/packages/vespula/locale
    $ds = DIRECTORY_SEPARATOR;
    $locale_obj = new \Vespula\Locale\Locale($c[ContainerKeys::DEFAULT_LOCALE]);
    $path_2_locale_language_files = SMVC_APP_ROOT_PATH.$ds.'config'.$ds.'languages';        
    $locale_obj->load($path_2_locale_language_files); //load local entries for base controller
    
    // Try to update to previously selected language if stored in session
    if (
        session_status() === PHP_SESSION_ACTIVE
        && array_key_exists(BaseController::SESSN_PARAM_CURRENT_LOCALE_LANG, $_SESSION)
    ) {
        $locale_obj->setCode($_SESSION[BaseController::SESSN_PARAM_CURRENT_LOCALE_LANG]);
    }
    
    return $locale_obj;
};

$container[ContainerKeys::LOGGER] = function () {

    $ds = DIRECTORY_SEPARATOR;
    $log_type = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
    $file = SMVC_APP_ROOT_PATH . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
    $adapter = new \Vespula\Log\Adapter\ErrorLog($log_type , $file);
    $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
    $adapter->setMinLevel(Psr\Log\LogLevel::DEBUG);
    $adapter->setDateFormat('Y-M-d g:i:s A');
    
    return new \Vespula\Log\Log('error-log', $adapter);
};

//Add the namespcace(s) for your web-app's controller classes or leave it
//as is, if your controllers are in the default global namespace.
//The namespaces are searched in the order which they are added 
//to the array. It would make sense to add the namespaces for your
//application in the front part of these arrays so that if a controller class 
//exists in \SlimMvcTools\Controllers\ and / or \SlimSkeletonMvcApp\Controllers\  
//and in your application's controller namespace(s) controllers
//in your application's namespaces are 
//Make sure you add the trailing slashes.
$container[ContainerKeys::NAMESPACES_4_CONTROLLERS] = [
    '\\SlimMvcTools\\Controllers\\', 
    '\\SlimSkeletonMvcApp\\Controllers\\'
];

//Object for rendering layout files
$container[ContainerKeys::LAYOUT_RENDERER] 
    = $container->factory(function ($c) {
    
    // Return a new instance on each access to 
    // $container[ContainerKeys::LAYOUT_RENDERER]
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
    $layout_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_layout_files]);
    $layout_renderer->setVar('__localeObj', $c[ContainerKeys::LOCALE_OBJ]);
    
    return $layout_renderer;
});

//Object for rendering view files
$container[ContainerKeys::VIEW_RENDERER] 
    = $container->factory(function ($c) {
    
    // Return a new instance on each access to 
    // $container[ContainerKeys::VIEW_RENDERER]
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
    $view_renderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$path_2_view_files]);
    $view_renderer->setVar('__localeObj', $c[ContainerKeys::LOCALE_OBJ]);

    return $view_renderer;
});

////////////////////////////////////////////////////////////////////////////
// Start Vespula.Auth PDO Authentication setup
// 
// You should use a proper database like mysql or postgres or other
// adapters like LDAP for performing authentication in your applications.
// 
// \SlimMvcTools\Controllers\BaseController->actionLogin will work out of 
// the box with any properly configured \Vespula\Auth\Adapter\* instance.
$container[ContainerKeys::VESPULA_AUTH] = function () {

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

////////////////////////////////////////////////////////////////////////////
// Call all the needed Setters on \Slim\Factory\AppFactory below here before
// AppFactory::create() is called in index.php
////////////////////////////////////////////////////////////////////////////
\Slim\Factory\AppFactory::setContainer($container);

return $container;

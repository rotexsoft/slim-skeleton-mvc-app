<?php
use \SlimSkeletonMvcApp\ContainerKeys,
    \SlimSkeletonMvcApp\AppSettingsKeys,
    \SlimMvcTools\Controllers\BaseController,
    \Psr\Container\ContainerInterface;

return function(array $appSettings): ContainerInterface {
    
    ////////////////////////////////////////////////////////////////////////////////
    // Configure all the dependencies you'll need in your app in this function.
    // 
    // You can replace the default \SlimMvcTools\Container with any other 
    // implementation of the \Psr\Container\ContainerInterface and configure 
    // it to contain all the default items defined in this function for a 
    // new application instance for this framework.
    ////////////////////////////////////////////////////////////////////////////////

    // $container must be an instance of \Psr\Container\ContainerInterface
    // It must be returned at the end of this file.
    // See https://github.com/silexphp/Pimple for documentation on how to properly
    // use \SlimMvcTools\Container which extends \Pimple\Container
    $container = new \SlimMvcTools\Container();
    $container[ContainerKeys::APP_SETTINGS] = $appSettings;

    // See https://learn.microsoft.com/en-us/cpp/c-runtime-library/language-strings?view=msvc-170
    $container[ContainerKeys::DEFAULT_LOCALE] = 'en_US';
    $container[ContainerKeys::VALID_LOCALES] = ['en_US', 'fr_CA']; // add more values for languages you will be supporting in your application
    $container[ContainerKeys::LOCALE_OBJ] = function (ContainerInterface $c) { // An object managing localized strings

        // See https://packagist.org/packages/vespula/locale
        $ds = DIRECTORY_SEPARATOR;
        $localeObj = new \Vespula\Locale\Locale($c[ContainerKeys::DEFAULT_LOCALE]);
        $pathToLocaleLanguageFiles = SMVC_APP_ROOT_PATH.$ds.'config'.$ds.'languages';        
        $localeObj->load($pathToLocaleLanguageFiles); //load local entries for base controller

        if(session_status() !== \PHP_SESSION_ACTIVE) {

            // Try to start or resume existing session

            $sessionOptions = $c->get(ContainerKeys::APP_SETTINGS)[AppSettingsKeys::SESSION_START_OPTIONS];

            if(isset($sessionOptions['name'])) {

                ////////////////////////////////////////////////////////////////
                // Set the session name first
                // https://www.php.net/manual/en/function.session-start.php
                //      To use a named session, call session_name() before 
                //      calling session_start(). 
                // 
                // https://www.php.net/manual/en/function.session-name.php
                ////////////////////////////////////////////////////////////////
                session_name($sessionOptions['name']);
            }

            session_start($sessionOptions);
        }

        // Try to update to previously selected language if stored in session
        if (
            session_status() === \PHP_SESSION_ACTIVE
            && array_key_exists(BaseController::SESSN_PARAM_CURRENT_LOCALE_LANG, $_SESSION)
        ) {
            $localeObj->setCode($_SESSION[BaseController::SESSN_PARAM_CURRENT_LOCALE_LANG]);
        }

        return $localeObj;
    };

    // A PSR 3 / PSR Log Compliant logger
    $container[ContainerKeys::LOGGER] = function (ContainerInterface $c) {

        // See https://packagist.org/packages/vespula/log
        $ds = DIRECTORY_SEPARATOR;
        $logType = \Vespula\Log\Adapter\ErrorLog::TYPE_FILE;
        $file = SMVC_APP_ROOT_PATH . "{$ds}logs{$ds}daily_log_" . date('Y_M_d') . '.txt';
        $adapter = new \Vespula\Log\Adapter\ErrorLog($logType , $file);
        $adapter->setMessageFormat('[{timestamp}] [{level}] {message}');
        $adapter->setMinLevel(\Psr\Log\LogLevel::DEBUG);
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

    // Object for rendering layout files
    $container[ContainerKeys::LAYOUT_RENDERER]  = $container->factory(function (ContainerInterface $c) {

        // See https://github.com/rotexsoft/file-renderer
        // Return a new instance on each access to 
        // $container[ContainerKeys::LAYOUT_RENDERER]
        $ds = DIRECTORY_SEPARATOR;
        $pathToLayoutFiles = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
        $layoutRenderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$pathToLayoutFiles]);
        $layoutRenderer->setVar('__localeObj', $c[ContainerKeys::LOCALE_OBJ]);

        return $layoutRenderer;
    });

    // Object for rendering view files
    $container[ContainerKeys::VIEW_RENDERER] = $container->factory(function (ContainerInterface $c) {

        // See https://github.com/rotexsoft/file-renderer
        // Return a new instance on each access to 
        // $container[ContainerKeys::VIEW_RENDERER]
        $ds = DIRECTORY_SEPARATOR;
        $pathToViewFiles = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
        $viewRenderer = new \Rotexsoft\FileRenderer\Renderer('', [], [$pathToViewFiles]);
        $viewRenderer->setVar('__localeObj', $c[ContainerKeys::LOCALE_OBJ]);

        return $viewRenderer;
    });

    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth PDO Authentication setup
    // 
    // You should use a proper database like mysql or postgres or other
    // adapters like LDAP for performing authentication in your applications.
    // 
    // \SlimMvcTools\Controllers\BaseController->actionLogin will work out of 
    // the box with any properly configured \Vespula\Auth\Adapter\* instance.
    $container[ContainerKeys::VESPULA_AUTH] = function (ContainerInterface $c) {

        // See https://packagist.org/packages/vespula/auth
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
        $maxIdle = 1200;
        $sessionStartSettings = $c->get(ContainerKeys::APP_SETTINGS)[AppSettingsKeys::SESSION_START_OPTIONS];
        $session = new \Vespula\Auth\Session\Session($maxIdle, $expire, null, $sessionStartSettings);

        $cols = ['username', 'password'];
        $from = 'user_authentication_accounts';
        $where = ''; //optional
        $adapter = new \Vespula\Auth\Adapter\Sql($pdo, $from, $cols, $where);

        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////

    // New PSR 7 Request Object
    $container[ContainerKeys::NEW_REQUEST_OBJECT]  = $container->factory(function (ContainerInterface $c) {

        $serverRequestCreator = \Slim\Factory\ServerRequestCreatorFactory::create();
        return $serverRequestCreator->createServerRequestFromGlobals();
    });

    // New PSR 7 Response Object
    $container[ContainerKeys::NEW_RESPONSE_OBJECT]  = $container->factory(function (ContainerInterface $c) {

        $responseFactory = \Slim\Factory\AppFactory::determineResponseFactory();
        return $responseFactory->createResponse();
    });

    ////////////////////////////////////////////////////////////////////////////
    // Call all the needed Setters on \Slim\Factory\AppFactory below here before
    // AppFactory::create() is called in index.php
    ////////////////////////////////////////////////////////////////////////////
    \Slim\Factory\AppFactory::setContainer($container);

    return $container;
};

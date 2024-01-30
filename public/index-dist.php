<?php
declare(strict_types=1);

require dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

////////////////////////////////////////////////////////////////////////////////
// This is the bootstrap file for rotexsoft/slim-skeleton-mvc-app
// 
// You SHOULD NOT be modifying the contents of this file (index.php).
// 
// You should instead configure your application by modifying these files:
// 
// 1. app-settings.php: Returns an associative array of settings for your application.
//                      Put app specific settings like, db credentials in this file.
//                      
//                      This file should not be committed to your source control repo 
//                      (e.g. Git). A new copy of this file with default values will
//                      be generated for your application when you run composer update
//                      or install (if the file does not exist).
// 
// 2. dependencies.php Returns an instance of \Psr\Container\ContainerInterface of
//                     your choosing that would be bound to the \Slim\App instance 
//                     created by \Slim\Factory\AppFactory later on in this file.
//                     
//                     By default, an instance of \SlimMvcTools\Container is returned
//                     (which is an extension of Pimple https://github.com/silexphp/Pimple).
//                     
//                     Configure all the dependencies you'll need in your application 
//                     in dependencies.php. Also call all the needed Setters (if any) 
//                     on \Slim\Factory\AppFactory in dependencies.php.
//                     
//                     You can safely & should commit dependencies.php to your source 
//                     control repo (e.g. Git). Sensitive app settings can be injected 
//                     into dependencies.php via the $app_settings variable, you would 
//                     never need to directly store sensitive credentials in dependencies.php.
// 
// 3. env.php          Returns any one of SMVC_APP_ENV_DEV, SMVC_APP_ENV_PRODUCTION, 
//                     SMVC_APP_ENV_STAGING or SMVC_APP_ENV_TESTING. It basically
//                     represents the mode your application is running in.
//                     
//                     Use sMVC_GetCurrentAppEnvironment() to get the value
//                     returned by env.php wherever you need it all over your
//                     application.
//                     
//                     This file should not be committed to your source control repo
//                     (e.g. Git). A new copy of this file with default value of 
//                     SMVC_APP_ENV_DEV will be generated for your application 
//                     when you run composer update or install (if the file does 
//                     not exist).
// 
// 4. ini-settings.php Make all the ini_set calls for your application here. 
//                     You could still call ini_set in other parts of your
//                     application, like inside controller methods, etc.
//                     It is better you make all your ini_set calls in
//                     ini-settings.php if possible.
//                     
//                     You can add environment specific ini_set calls to app-settings.php
// 
//                     You can safely & should commit ini-settings.php to your source control 
//                     repo (e.g. Git). 
// 
// 5. routes-and-middlewares.php Contains route definitions for your app.
//                               
//                               rotexsoft/slim-skeleton-mvc-app's MVC routes
//                               are also defined in routes-and-middlewares.php
//                               
//                               It is recommended that you also make modififications
//                               to the $app object inside routes-and-middlewares.php
//                               if you need to do so for your application. The $app
//                               object is available inside routes-and-middlewares.php.
//                               
//                               You can safely & should commit routes-and-middlewares.php 
//                               to your source control repo (e.g. Git).
//                               
// 6. languages/[en_US.php...fr_CA.php] Each locale file in the languages folder is meant
//                                      to contain language specific translations for text
//                                      to be displayed in specific languages in an application
////////////////////////////////////////////////////////////////////////////////
use Slim\Factory\AppFactory;

define('SMVC_APP_ENV_DEV', 'development');
define('SMVC_APP_ENV_PRODUCTION', 'production');
define('SMVC_APP_ENV_STAGING', 'staging');
define('SMVC_APP_ENV_TESTING', 'testing');

define('SMVC_APP_PUBLIC_PATH', dirname(__FILE__));
define('SMVC_APP_ROOT_PATH', dirname(dirname(__FILE__)));

sMVC_GetSuperGlobal();  // this method is first called here to ensure that $_SERVER,
                        // $_GET, $_POST, $_FILES, $_COOKIE, $_SESSION & $_ENV are
                        // captured in their original state by the static $super_globals
                        // variable inside sMVC_GetSuperGlobal(), before any other
                        // library, framework, etc. accesses or modifies any of them.
                        // Subsequent calls to sMVC_GetSuperGlobal(..) will return
                        // the stored values.

/**
 * This function detects which environment your web-app is running in
 * (i.e. one of Production, Development, Staging or Testing).
 *
 * NOTE: Make sure you edit ../config/env.php to return one of SMVC_APP_ENV_DEV,
 *       SMVC_APP_ENV_PRODUCTION, SMVC_APP_ENV_STAGING or SMVC_APP_ENV_TESTING
 *       relevant to the environment you are installing your web-app.
 */
function sMVC_GetCurrentAppEnvironment(): string {

    return sMVC_DoGetCurrentAppEnvironment(SMVC_APP_ROOT_PATH);
}

$smvc_root_dir = SMVC_APP_ROOT_PATH . DIRECTORY_SEPARATOR;
$smvc_files_to_check = [
    [
        'Missing Ini Settings Configuration File Error',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings-dist.php',
        SMVC_APP_ROOT_PATH,
    ],
    [
        'Missing App Settings Configuration File Error',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings-dist.php',
        SMVC_APP_ROOT_PATH,
    ],
    [
        'Missing Dependencies File Error',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'dependencies.php',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'dependencies-dist.php',
        SMVC_APP_ROOT_PATH, 
    ],
    [
        'Missing Routes and Middlewares File Error',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'routes-and-middlewares.php',
        "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'routes-and-middlewares-dist.php',
        SMVC_APP_ROOT_PATH
    ],
];

foreach ($smvc_files_to_check as $smvc_file_to_check) {
    
    if( !file_exists($smvc_file_to_check[1]) ) {

        sMVC_DisplayAndLogFrameworkFileNotFoundError(...$smvc_file_to_check);
        exit;
    } // if( !file_exists($smvc_file_to_check[1]) )
} // foreach ($smvc_files_to_check as $smvc_file_to_check) 

////////////////////////////////////////////////////////
// load ini settings for your app from ini-settings.php
require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'ini-settings.php';

///////////////////////////////////////////////
// load app settings from app-settings.php
$app_settings = require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php';

// If true, the mvc routes will be enabled. If false, then you must explicitly
// define all the routes for your application inside config/routes-and-middlewares.php
define( 'SMVC_APP_USE_MVC_ROUTES', ((bool)$app_settings['use_mvc_routes']) );

// If true, the string `action` will be prepended to action method name (if the
// method name does not already start with the string `action`). The resulting
// method name will be converted to camel case before being executed.
// If false, then action method names will only be converted to camel
// case before being executed.
// NOTE: This setting only applies to the MVC routes below if 
//       $app_settings['use_mvc_routes'] === true:
//          '/'
//          '/{controller}[/]'
//          '/{controller}/{action}[/{parameters:.+}]'
//          '/{controller}/{action}/'
define( 'SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES', ((bool)$app_settings['auto_prepend_action_to_action_method_names']) );

// This is used to create a controller object to handle the default / route.
// Must be prefixed with the namespace if the controller class is in a namespace.
if(is_string($app_settings['default_controller_class_name']) && $app_settings['default_controller_class_name'] !== '' ) {
    
    define('SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME', $app_settings['default_controller_class_name']);
    
} else {
    
    echo 'The value associated with `default_controller_class_name` in `' 
       . "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php`'
       . " must be a non-empty string value. Please edit the file with an"
       . " appropriate value. Goodbye!";
    exit;
}

// This is the name of the action / method to be called on the default controller
// to handle the default / route. This method should return a response string (ie.
// valid html) or a PSR 7 response object containing valid html in its body.
// This default action / method should accept no arguments / parameters.
if(is_string($app_settings['default_action_name']) && $app_settings['default_action_name'] !== '' ) {
    
    define('SMVC_APP_DEFAULT_ACTION_NAME', $app_settings['default_action_name']);
    
} else {
    
    echo 'The value associated with `default_action_name` in `' 
       . "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'app-settings.php`'
       . " must be a non-empty string value. Please edit the file with an"
       . " appropriate value. Goodbye!";
    exit;
}

////////////////////////////////////////////////////////////////////////////////
// Load Dependency Injection Container
$container = require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'dependencies.php';

$app = AppFactory::create();
$app->setBasePath($app_settings['app_base_path']);

////////////////////////////////////////////////////////////////////////////////
// Load app specific and slim mvc route definitions.
// It is recommended that you modify the $app object inside 
// routes-and-middlewares.php if you need to do so for your application.
require_once "{$smvc_root_dir}config". DIRECTORY_SEPARATOR.'routes-and-middlewares.php';

$app->run(); // Finally run the app

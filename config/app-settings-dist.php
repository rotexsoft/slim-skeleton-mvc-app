<?php
////////////////////////////////////////////////////////////////////////////////
// ./config/app-settings-dist.php is used to generate ./config/app-settings.php 
// when setting up your app in a new environment.
// 
// When you run composer install or update, composer will create a new 
// ./config/app-settings.php file (if it doesn't already exist) by copying 
// ./config/app-settings-dist.php.
// 
// ./config/app-settings-dist.php is expected to be committed into version control, 
// so just put placeholder/dummy values for sensitive settings like db credentials 
// in ./config/app-settings-dist.php.
// 
// You should NEVER commit ./config/app-settings.php (which will contain the real 
// values of all settings specific to an environment your app will run in) into 
// version control, since it's expected to contain sensitive information like db 
// passwords, etc.
////////////////////////////////////////////////////////////////////////////////

return [
    ////////////////////////////////////////////////////////////////////////////
    //
    //  Put environment specific settings below.
    //  You can access the settings via your app's container
    //  object (e.g. $c) like this: $c->get(\SlimMvcTools\ContainerKeys::APP_SETTINGS)['specific_setting_1']
    //  where `specific_setting_1` can be replaced with the actual setting name.
    // 
    ////////////////////////////////////////////////////////////////////////////
    
    ///////////////////////////////
    // Slim PHP Related Settings
    //////////////////////////////
    'displayErrorDetails' => (sMVC_GetCurrentAppEnvironment() !== \SlimMvcTools\AppEnvironments::PRODUCTION), // should be always false in production
    'logErrors' => true,
    'logErrorDetails' => true,
    'addContentLengthHeader' => (sMVC_GetCurrentAppEnvironment() === \SlimMvcTools\AppEnvironments::PRODUCTION), // should be always true in production
    /////////////////////////////////////
    // End of Slim PHP Related Settings
    /////////////////////////////////////

    /////////////////////////////////////////////
    // Your App's Environment Specific Settings
    /////////////////////////////////////////////
    'app_base_path' => '', // https://www.slimframework.com/docs/v4/start/web-servers.html#run-from-a-sub-directory
    'error_template_file'=> SMVC_APP_ROOT_PATH. DIRECTORY_SEPARATOR 
                            . 'src' . DIRECTORY_SEPARATOR 
                            . 'layout-templates' . DIRECTORY_SEPARATOR 
                            . 'error-template.html',
    'use_mvc_routes' => true,
    'mvc_routes_http_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'auto_prepend_action_to_action_method_names' => false,
    'default_controller_class_name' => \SlimMvcTools\Controllers\BaseController::class,
    'default_action_name' => 'actionIndex',
    
    'error_handler_class' => \SlimSkeletonMvcApp\AppErrorHandler::class,
    
    'html_renderer_class' => \SlimMvcTools\HtmlErrorRenderer::class,
    'json_renderer_class' => \SlimMvcTools\JsonErrorRenderer::class,
    'log_renderer_class'  => \SlimMvcTools\LogErrorRenderer::class,
    'xml_renderer_class' => \SlimMvcTools\XmlErrorRenderer::class,
    
    ////////////////////////////////////////////////////////////////////////////
    // Options for PHP's session_start https://www.php.net/manual/en/function.session-start.php
    // See https://www.php.net/session.configuration for more info about these options.
    // 
    // You can pass these options to any part of your code that calls session_start
    // \SlimMvcTools\Controllers\BaseController uses these options in all calls to
    // session_start within it.
    // 
    // Alternatively, you could configure all these options except read_and_close
    // in ./config/ini-settings.php by calling ini_set for each option you want 
    // to configure.
    // 
    // This setting is optional and you don't need to configure it if you don't
    // really need to.
    ////////////////////////////////////////////////////////////////////////////
    'session_start_options' => [
//        "cache_expire" => "180",
//        "cache_limiter" => "nocache",
//        "cookie_domain" => "",
//        "cookie_httponly" => "0",
//        "cookie_lifetime" => "0",
//        "cookie_path" => "/",
//        "cookie_samesite" => "",
//        "cookie_secure" => "0",
//        "gc_divisor" => "100",
//        "gc_maxlifetime" => "1440",
//        "gc_probability" => "1",
//        "lazy_write" => "1",
//        "name" => "PHPSESSID",
//        "read_and_close" => "0",
//        "referer_check" => "",
//        "save_handler" => "files",
//        "save_path" => "",
//        "serialize_handler" => "php",
//        "sid_bits_per_character" => "4",
//        "sid_length" => "32",
//        "trans_sid_hosts" => $_SERVER['HTTP_HOST'],
//        "trans_sid_tags" => "a=href,area=href,frame=src,form=",
//        "use_cookies" => "1",
//        "use_only_cookies" => "1",
//        "use_strict_mode" => "0",
//        "use_trans_sid" => "0",  
    ],
    
    ////////////////////////////////////////////////////////////////////////////
    // add other stuff like DB credentials, api keys, etc below
    ////////////////////////////////////////////////////////////////////////////
    
    
    
    ////////////////////////////////////////////////////
    // End of Your App's Environment Specific Settings
    ////////////////////////////////////////////////////
];

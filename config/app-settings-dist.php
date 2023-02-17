<?php
// Copy this file to ./config/app-settings.php when setting up your app in a new environment
// You should not commit ./config/app-settings.php into version control, since it's expected
// to contain sensitive information like database passwords, etc.
return [
    ///////////////////////////////
    // Slim PHP Related Settings
    //////////////////////////////
    'displayErrorDetails' => false,
    'logErrors' => false,
    'logErrorDetails' => false,
    'addContentLengthHeader' => true,

    /////////////////////////////////////
    // End of Slim PHP Related Settings
    /////////////////////////////////////

    /////////////////////////////////////////////
    // Your App's Environment Specific Settings
    /////////////////////////////////////////////
    'app_base_path' => '',
    'error_template_file'=> SMVC_APP_ROOT_PATH. DIRECTORY_SEPARATOR 
                            . 'src' . DIRECTORY_SEPARATOR 
                            . 'layout-templates' . DIRECTORY_SEPARATOR 
                            . 'error-template.php',
    'use_mvc_routes' => true,
    'mvc_routes_http_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'auto_prepend_action_to_action_method_names' => false,
    'default_controller_class_name' => \SlimMvcTools\Controllers\BaseController::class,
    'default_action_name' => 'actionIndex',
    
    'error_handler_class' => \SlimMvcTools\ErrorHandler::class,
    'html_renderer_class' => \SlimMvcTools\HtmlErrorRenderer::class,
    'log_renderer_class'  => \SlimMvcTools\LogErrorRenderer::class,

    //////////////////////////////////////////////////////////////////////////////
    //
    //  Put environment specific settings below.
    //  You can access the settings via your app's container
    //  object (e.g. $c) like this: $c->get('settings')['specific_setting_1']
    //  where `specific_setting_1` can be replaced with the actual setting name
    //  e.g. like the `bind_options` setting name below.
    // 
    //////////////////////////////////////////////////////////////////////////////

    /*
     * `basedn`: The base dn to search through
     * `binddn`: The dn used to bind to
     * `bindpw`: A password used to bind to the server using the binddn
     * `filter`: A filter used to search for the user. Eg. samaccountname=%s
     */
    'bind_options' => [
        'basedn' => 'OU=MyCompany,OU=Edmonton,OU=Alberta',
        'bindn'  => 'cn=%s,OU=Users,OU=MyCompany,OU=Edmonton,OU=Alberta',
        'bindpw' => 'Pa$$w0rd',
        'filter' => 'samaccountname=%s',
    ],

    'ldap_server_addr' => 'ldap.server.org.ca',

    ////////////////////////////////////////////////////
    // End of Your App's Environment Specific Settings
    ////////////////////////////////////////////////////
];

<?php
// Copy this file to ./config/app-settings.php when setting up your app in a new environment
// You should not commit ./config/app-settings.php into version control, since it's expected
// to contain sensitive information like database passwords, etc.
return [
    
    // the settings array below will be accessible in your app's container $c via
    // $c['settings'] or $c->get('settings') and will also be accessible in the
    // container object inside ./config/dependencies.php
    
    'settings' => [
        ///////////////////////////////
        // Slim PHP Related Settings
        //////////////////////////////
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
        /////////////////////////////////////
        // End of Slim PHP Related Settings
        /////////////////////////////////////
        
        /////////////////////////////////////////////
        // Your App's Environment Specific Settings
        /////////////////////////////////////////////

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
    ]
];
<?php
/**
 * Set your ini settings here
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);
ini_set('date.timezone', 'America/Edmonton');

if( sMVC_GetCurrentAppEnvironment() !== SMVC_APP_ENV_DEV ) {
    
    ini_set('error_reporting',  E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED  & ~E_WARNING);
}

set_error_handler(function ($severity, $message, $file, $line) {
    
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting, so ignore it
        return;
    }
    
    // convert php errors to exception
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

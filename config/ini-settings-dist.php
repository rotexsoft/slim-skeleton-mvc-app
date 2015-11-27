<?php
/**
 * Set your ini settings here
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', true);
ini_set('date.timezone', 'America/Edmonton');

if( s3MVC_GetCurrentAppEnvironment() !== S3MVC_APP_ENV_DEV ) {
    
    ini_set('error_reporting',  E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED  & ~E_WARNING);
}
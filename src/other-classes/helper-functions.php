<?php
namespace Slim3Mvc\OtherClasses;

/**
 * 
 * Returns "foo-bar-baz" as "fooBarBaz".
 * 
 * @param string $str The dashed word.
 * 
 * @return string The word in camel-caps.
 * 
 */
function dashesToCamel($str)
{
    $str = ucwords(str_replace('-', ' ', $str));
    $str = str_replace(' ', '', $str);
    $str[0] = strtolower($str[0]);
    return $str;
}

/**
 * 
 * Returns "foo-bar-baz" as "FooBarBaz".
 * 
 * @param string $str The dashed word.
 * 
 * @return string The word in studly-caps.
 * 
 */
function dashesToStudly($str)
{
    $str = dashesToCamel($str);
    $str[0] = strtoupper($str[0]);
    return $str;
}
    
/**
 * 
 * Returns "foo_bar_baz" as "fooBarBaz".
 * 
 * @param string $str The underscore word.
 * 
 * @return string The word in camel-caps.
 * 
 */
function underToCamel($str)
{
    $str = ucwords(str_replace('_', ' ', $str));
    $str = str_replace(' ', '', $str);
    $str[0] = strtolower($str[0]);
    return $str;
}

/**
 * 
 * Returns "foo_bar_baz" as "FooBarBaz".
 * 
 * @param string $str The underscore word.
 * 
 * @return string The word in studly-caps.
 * 
 */
function underToStudly($str)
{
    $str = underToCamel($str);
    $str[0] = strtoupper($str[0]);
    return $str;
}
    
/**
 * 
 * Returns any string, converted to using dashes with only lowercase 
 * alphanumerics.
 * 
 * @param string $str The string to convert.
 * 
 * @return string The converted string.
 * 
 */
function toDashes($str)
{
    $str = preg_replace('/[^a-z0-9 _-]/i', '', $str);
    $str = camelToDashes($str);
    $str = preg_replace('/[ _-]+/', '-', $str);
    return $str;
}

/**
 * 
 * Returns "camelCapsWord" and "CamelCapsWord" as "Camel_Caps_Word".
 * 
 * @param string $str The camel-caps word.
 * 
 * @return string The word with underscores in place of camel caps.
 * 
 */
function camelToUnder($str)
{
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = str_replace(' ', '_', ucwords($str));
    return $str;
}

/**
 * 
 * Returns "camelCapsWord" and "CamelCapsWord" as "camel-caps-word".
 * 
 * @param string $str The camel-caps word.
 * 
 * @return string The word with dashes in place of camel caps.
 * 
 */
function camelToDashes($str)
{
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = str_replace(' ', '-', ucwords($str));
    return strtolower($str);
}


function dumpAuthinfo(\Aura\Auth\Auth $auth) {

    return 'Initial Login Time: '. $auth->getFirstActive().PHP_EOL
         . 'Time Now: ' . $auth->getLastActive().PHP_EOL
         . 'Login Status: ' . $auth->getStatus().PHP_EOL
         . 'Logged in Person\'s Username: ' . $auth->getUserName().PHP_EOL
         . 'Logged in User\'s Data: ' . PHP_EOL . print_r($auth->getUserData(), true);
}
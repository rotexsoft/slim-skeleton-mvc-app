<?php
$ds = DIRECTORY_SEPARATOR;
require_once __DIR__.DIRECTORY_SEPARATOR."..{$ds}vendor{$ds}rotexsoft{$ds}slim3-skeleton-mvc-tools{$ds}src{$ds}functions{$ds}str-helpers.php";

echo \Slim3MvcTools\color_4_console(
        "Running post composer installation tasks for Slim3Mvc ........".PHP_EOL.PHP_EOL, 
        "green", 
        "black"
    );

$raw_config_src_folder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config';
$config_src_folder = realpath($raw_config_src_folder).DIRECTORY_SEPARATOR;

$raw_public_src_folder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public';
$public_src_folder = realpath($raw_public_src_folder).DIRECTORY_SEPARATOR;

echo \Slim3MvcTools\color_4_console(
        "Copying `{$config_src_folder}dependencies-dist.php` to `{$config_src_folder}dependencies.php` ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( @copy("{$config_src_folder}dependencies-dist.php", "{$config_src_folder}dependencies.php") ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully Copied! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not copy `{$raw_config_src_folder}{$ds}dependencies-dist.php` to `{$raw_config_src_folder}{$ds}dependencies.php` ! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}
sleep(1);

echo \Slim3MvcTools\color_4_console(
        "Copying `{$config_src_folder}env-dist.php` to `{$config_src_folder}env.php` ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( @copy("{$config_src_folder}env-dist.php", "{$config_src_folder}env.php") ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully Copied! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not copy `{$raw_config_src_folder}{$ds}env-dist.php` to `{$raw_config_src_folder}{$ds}env.php` ! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}
sleep(1);

echo \Slim3MvcTools\color_4_console(
        "Copying `{$config_src_folder}ini-settings-dist.php` to `{$config_src_folder}ini-settings.php` ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( @copy("{$config_src_folder}ini-settings-dist.php", "{$config_src_folder}ini-settings.php") ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully Copied! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not copy `{$raw_config_src_folder}{$ds}ini-settings-dist.php` to `{$raw_config_src_folder}{$ds}ini-settings.php` ! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}
sleep(1);

echo \Slim3MvcTools\color_4_console(
        "Copying `{$config_src_folder}routes-dist.php` to `{$config_src_folder}routes.php` ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( @copy("{$config_src_folder}routes-dist.php", "{$config_src_folder}routes.php") ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully Copied! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not copy `{$raw_config_src_folder}{$ds}routes-dist.php` to `{$raw_config_src_folder}{$ds}routes.php` ! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}
sleep(1);

echo \Slim3MvcTools\color_4_console(
        "Copying `{$public_src_folder}index-dist.php` to `{$public_src_folder}index.php` ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( @copy("{$public_src_folder}index-dist.php", "{$public_src_folder}index.php") ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully Copied! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not copy `{$raw_public_src_folder}{$ds}index-dist.php` to `{$raw_public_src_folder}{$ds}index.php` ! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}

sleep(1);

$logs_folder = __DIR__."{$ds}..{$ds}logs";
echo \Slim3MvcTools\color_4_console(
        "Making `{$logs_folder}` writable ....".PHP_EOL, 
        "green", 
        "black"
    );
        
if( chmod($logs_folder, 0777) ) {
    
    echo \Slim3MvcTools\color_4_console(
            "Successfully made `{$logs_folder}` writable! ".PHP_EOL.PHP_EOL, 
            "green", 
            "black"
        );

} else {
    
    echo \Slim3MvcTools\color_4_console(
            "Error: Could not make `{$logs_folder}` writable!! ".PHP_EOL.PHP_EOL, 
            "red", 
            "black"
        );
}

<?php
$ds = DIRECTORY_SEPARATOR;
require_once __DIR__.DIRECTORY_SEPARATOR."..{$ds}vendor{$ds}rotexsoft{$ds}slim3-skeleton-mvc-tools{$ds}src{$ds}functions{$ds}str-helpers.php";

printInfo( "Running post composer create-project tasks for Slim3Mvc ........".PHP_EOL );

$raw_root_folder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
$root_folder = realpath($raw_root_folder).DIRECTORY_SEPARATOR;

$raw_config_src_folder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config';
$config_src_folder = realpath($raw_config_src_folder).DIRECTORY_SEPARATOR;

$raw_public_src_folder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public';
$public_src_folder = realpath($raw_public_src_folder).DIRECTORY_SEPARATOR;

printInfo( "Moving `{$config_src_folder}dependencies-dist.php` to `{$config_src_folder}dependencies.php` ...." );
        
if( @rename("{$config_src_folder}dependencies-dist.php", "{$config_src_folder}dependencies.php") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_config_src_folder}{$ds}dependencies-dist.php` to `{$raw_config_src_folder}{$ds}dependencies.php` ! ".PHP_EOL );
}
sleep(1);

printInfo( "Moving `{$config_src_folder}env-dist.php` to `{$config_src_folder}env.php` ...." );
        
if( @rename("{$config_src_folder}env-dist.php", "{$config_src_folder}env.php") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_config_src_folder}{$ds}env-dist.php` to `{$raw_config_src_folder}{$ds}env.php` ! ".PHP_EOL );
}
sleep(1);

printInfo( "Moving `{$config_src_folder}ini-settings-dist.php` to `{$config_src_folder}ini-settings.php` ...." );
        
if( @rename("{$config_src_folder}ini-settings-dist.php", "{$config_src_folder}ini-settings.php") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_config_src_folder}{$ds}ini-settings-dist.php` to `{$raw_config_src_folder}{$ds}ini-settings.php` ! ".PHP_EOL );
}
sleep(1);

printInfo( "Moving `{$config_src_folder}routes-dist.php` to `{$config_src_folder}routes.php` ...." );
        
if( @rename("{$config_src_folder}routes-dist.php", "{$config_src_folder}routes.php") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_config_src_folder}{$ds}routes-dist.php` to `{$raw_config_src_folder}{$ds}routes.php` ! ".PHP_EOL );
}
sleep(1);

printInfo( "Moving `{$public_src_folder}index-dist.php` to `{$public_src_folder}index.php` ...." );
        
if( @rename("{$public_src_folder}index-dist.php", "{$public_src_folder}index.php") ) {
    
    printInfo("Successfully Moved! ".PHP_EOL);

} else {
    
    printError( "Could not move `{$raw_public_src_folder}{$ds}index-dist.php` to `{$raw_public_src_folder}{$ds}index.php` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Moving `{$raw_root_folder}.gitignore-dist` to `{$raw_root_folder}.gitignore` ...." );
        
if( @rename("{$root_folder}.gitignore-dist", "{$root_folder}.gitignore") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_root_folder}.gitignore-dist` to `{$raw_root_folder}.gitignore` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Moving `{$raw_root_folder}README-dist.md` to `{$raw_root_folder}README.md` ...." );
        
if( @rename("{$root_folder}README-dist.md", "{$root_folder}README.md") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_root_folder}README-dist.md` to `{$raw_root_folder}README.md` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Moving `{$raw_root_folder}composer-dist.json` to `{$raw_root_folder}composer.json` ...." );
        
if( @rename("{$root_folder}composer-dist.json", "{$root_folder}composer.json") ) {
    
    printInfo( "Successfully Moved! ".PHP_EOL );

} else {
    
    printError( "Could not move `{$raw_root_folder}composer-dist.json` to `{$raw_root_folder}composer.json` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Deleting `{$raw_root_folder}slim3-psr7.png` ....".PHP_EOL );
        
if( @unlink("{$root_folder}slim3-psr7.png") ) {
    
    printInfo( "Successfully Deleted! ".PHP_EOL );

} else {
    
    printError( "Could not delete `{$raw_root_folder}slim3-psr7.png` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Deleting `{$raw_root_folder}slim3-psr7.pub` ....".PHP_EOL );
        
if( @unlink("{$root_folder}slim3-psr7.pub") ) {
    
    printInfo( "Successfully Deleted! ".PHP_EOL );

} else {
    
    printError( "Could not delete `{$raw_root_folder}slim3-psr7.pub` ! ".PHP_EOL );
}

sleep(1);

////////////////////////////////////////////////////////////
printInfo( "Deleting `{$raw_root_folder}phpunit.xml.dist` ....".PHP_EOL );
        
if( @unlink("{$root_folder}phpunit.xml.dist") ) {
    
    printInfo( "Successfully Deleted! ".PHP_EOL  );

} else {
    
    printError("Could not delete `{$raw_root_folder}phpunit.xml.dist` ! ".PHP_EOL);
}

sleep(1);

////////////////////////////////////////////////////////////////////////////////
$logs_folder = __DIR__."{$ds}..{$ds}logs";
printInfo("Making `{$logs_folder}` writable ....".PHP_EOL);
        
if( chmod($logs_folder, 0777) ) {
    
    printInfo( "Successfully made `{$logs_folder}` writable! ".PHP_EOL );

} else {
    
    printError("Could not make `{$logs_folder}` writable!! ".PHP_EOL);
}

function printError($str, $append_new_line = true) {
    
    echo \Slim3MvcTools\Functions\Str\color_4_console( "ERROR: $str", "red",  "black");
    
    if( $append_new_line ) {
        
        echo PHP_EOL;
    }
}

function printInfo($str, $append_new_line = true) {
    
    echo \Slim3MvcTools\Functions\Str\color_4_console( $str, "green",  "black");
    
    if( $append_new_line ) {
        
        echo PHP_EOL;
    }
}

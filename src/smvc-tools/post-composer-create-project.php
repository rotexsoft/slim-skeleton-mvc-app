<?php
require_once __DIR__. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR
            . "vendor" . DIRECTORY_SEPARATOR . "rotexsoft" . DIRECTORY_SEPARATOR
            . "slim-skeleton-mvc-tools" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR
            . "functions" . DIRECTORY_SEPARATOR . "str-helpers.php";

class SMVC_PostComposerCreateHandler {

    public static function exec(){

        $ds = DIRECTORY_SEPARATOR;
        static::printInfo( "Running post composer create-project tasks for SlimMvc ........".PHP_EOL );

        $raw_root_folder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $root_folder = realpath($raw_root_folder).DIRECTORY_SEPARATOR;

        $raw_config_src_folder = $raw_root_folder.'config';
        $config_src_folder = realpath($raw_config_src_folder).DIRECTORY_SEPARATOR;

        $raw_public_src_folder = $raw_root_folder.'public';
        $public_src_folder = realpath($raw_public_src_folder).DIRECTORY_SEPARATOR;
        
        $files_to_copy = [
            "{$config_src_folder}app-settings-dist.php" => "{$config_src_folder}app-settings.php",
            "{$config_src_folder}env-dist.php" => "{$config_src_folder}env.php",
        ];
        
        foreach($files_to_copy as $from => $to) {
            
            static::printInfo( "Trying to copy `{$from}` to `{$to}` ...." );

            if( copy($from, $to) ) {

                static::printInfo( "Successfully Copied!".PHP_EOL );

            } else {

                static::printError( "Could not copy `{$from}` to `{$to}`!".PHP_EOL );
            }
            
            sleep(1);
        }
        
        $files_to_rename = [
            "{$config_src_folder}dependencies-dist.php" => "{$config_src_folder}dependencies.php",
            "{$config_src_folder}ini-settings-dist.php" => "{$config_src_folder}ini-settings.php",
            "{$config_src_folder}routes-and-middlewares-dist.php" => "{$config_src_folder}routes-and-middlewares.php",
            "{$public_src_folder}index-dist.php" => "{$public_src_folder}index.php",
            "{$root_folder}.gitignore-dist" => "{$root_folder}.gitignore",
            "{$root_folder}README-dist.md" => "{$root_folder}README.md",
            "{$root_folder}composer-dist.json" => "{$root_folder}composer.json", 
        ];
        
        foreach($files_to_rename as $from => $to) {
            
            static::printInfo( "Trying to move `{$from}` to `{$to}` ...." );

            if( rename($from, $to) ) {

                static::printInfo( "Successfully Moved!".PHP_EOL );

            } else {

                static::printError( "Could not move `{$from}` to `{$to}`!".PHP_EOL );
            }
            
            sleep(1);
        }
        
        ////////////////////////////////////////////////////////////////////////
        // delete folders not needed in skeleton app
        ////////////////////////////////////////////////////////////////////////
        $folders_to_delete = [
            "{$root_folder}documentation",
            "{$root_folder}.github",
        ];

        foreach ($folders_to_delete as $folder_to_delete) {
            
            static::printInfo( "Deleting `{$folder_to_delete}` ....".PHP_EOL );
            
            if( static::rrmdir("{$folder_to_delete}") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$folder_to_delete}`!".PHP_EOL);
            }
            
            sleep(1);
        }

        ////////////////////////////////////////////////////////////////////////
        // delete files not needed in skeleton app
        ////////////////////////////////////////////////////////////////////////        
        $files_to_delete = [
            "{$root_folder}slim3-psr7.png",
            "{$root_folder}TODO.md",
            "{$root_folder}slim3-psr7.pub",
            "{$root_folder}index.php-overview.docx",
            "{$root_folder}index.php-overview.png",
            "{$root_folder}phpunit.xml.dist",
        ];
        
        foreach ($files_to_delete as $file_to_delete) {
            
            static::printInfo( "Trying to delete `{$file_to_delete}` ....".PHP_EOL );

            if( !file_exists($file_to_delete) || unlink($file_to_delete) ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$file_to_delete}`!".PHP_EOL);
            }

            sleep(1);
        }

        ////////////////////////////////////////////////////////////////////////////////
        $logs_folder = $root_folder."logs";
        static::printInfo("Making `{$logs_folder}` writable ....".PHP_EOL);

        if( chmod($logs_folder, 0777) ) {

            static::printInfo( "Successfully made `{$logs_folder}` writable!".PHP_EOL );

        } else {

            static::printError("Could not make `{$logs_folder}` writable!!".PHP_EOL);
        }

        ////////////////////////////////////////////////////////////////////////////////
        //Interactive Part: Ask for user input
        ////////////////////////////////////////////////////////////////////////////////
        
        // $response = static::readFromLine("Do you want to use the Zurb Foundation front-end framework (which includes jQuery) that ships with SlimPHP 4 Skeleton MVC package? (Y/N)");
    }

    public static function printError($str, $append_new_line = true) {

        echo \SlimMvcTools\Functions\Str\color_4_console( "ERROR: $str", "red",  "black");

        if( $append_new_line ) {

            echo PHP_EOL;
        }
    }

    public static function printInfo($str, $append_new_line = true) {

        echo \SlimMvcTools\Functions\Str\color_4_console( $str, "green",  "black");

        if( $append_new_line ) {

            echo PHP_EOL;
        }
    }

    public static function readFromLine( $prompt = '' ) {

        echo $prompt;
        return trim(rtrim( fgets( STDIN ), PHP_EOL ));
    }

    public static function rrmdir($src) {

        if( strlen($src) <=0 || !is_dir($src) ) {

            return false;
        }

        $dir = opendir($src);

        while(false !== ( $file = readdir($dir)) ) {

            if (( $file != '.' ) && ( $file != '..' )) {

                $full = $src . '/' . $file;

                if ( is_dir($full) ) {

                    static::rrmdir($full);

                } else {

                    unlink($full);
                }
            }
        }

        closedir($dir);

        return rmdir($src);
    }
}

<?php
require_once __DIR__. DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR 
            . "vendor" . DIRECTORY_SEPARATOR . "rotexsoft" . DIRECTORY_SEPARATOR 
            . "slim3-skeleton-mvc-tools" . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR 
            . "functions" . DIRECTORY_SEPARATOR . "str-helpers.php";

class S3MVC_PostComposerCreateHandler {

    public static function exec(){

        $ds = DIRECTORY_SEPARATOR;
        static::printInfo( "Running post composer create-project tasks for Slim3Mvc ........".PHP_EOL );

        $raw_root_folder = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $root_folder = realpath($raw_root_folder).DIRECTORY_SEPARATOR;

        $raw_config_src_folder = $raw_root_folder.'config';
        $config_src_folder = realpath($raw_config_src_folder).DIRECTORY_SEPARATOR;

        $raw_public_src_folder = $raw_root_folder.'public';
        $public_src_folder = realpath($raw_public_src_folder).DIRECTORY_SEPARATOR;

        static::printInfo( "Moving `{$config_src_folder}app-settings-dist.php` to `{$config_src_folder}app-settings.php` ...." );

        if( @rename("{$config_src_folder}app-settings-dist.php", "{$config_src_folder}app-settings.php") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_config_src_folder}{$ds}app-settings-dist.php` to `{$raw_config_src_folder}{$ds}app-settings.php`!".PHP_EOL );
        }
        sleep(1);

        static::printInfo( "Moving `{$config_src_folder}dependencies-dist.php` to `{$config_src_folder}dependencies.php` ...." );

        if( @rename("{$config_src_folder}dependencies-dist.php", "{$config_src_folder}dependencies.php") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_config_src_folder}{$ds}dependencies-dist.php` to `{$raw_config_src_folder}{$ds}dependencies.php`!".PHP_EOL );
        }
        sleep(1);

        static::printInfo( "Moving `{$config_src_folder}env-dist.php` to `{$config_src_folder}env.php` ...." );

        if( @rename("{$config_src_folder}env-dist.php", "{$config_src_folder}env.php") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_config_src_folder}{$ds}env-dist.php` to `{$raw_config_src_folder}{$ds}env.php`!".PHP_EOL );
        }
        sleep(1);

        static::printInfo( "Moving `{$config_src_folder}ini-settings-dist.php` to `{$config_src_folder}ini-settings.php` ...." );

        if( @rename("{$config_src_folder}ini-settings-dist.php", "{$config_src_folder}ini-settings.php") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_config_src_folder}{$ds}ini-settings-dist.php` to `{$raw_config_src_folder}{$ds}ini-settings.php`!".PHP_EOL );
        }
        sleep(1);

        static::printInfo( "Moving `{$config_src_folder}routes-dist.php` to `{$config_src_folder}routes.php` ...." );

        if( @rename("{$config_src_folder}routes-dist.php", "{$config_src_folder}routes.php") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_config_src_folder}{$ds}routes-dist.php` to `{$raw_config_src_folder}{$ds}routes.php`!".PHP_EOL );
        }
        sleep(1);

        static::printInfo( "Moving `{$public_src_folder}index-dist.php` to `{$public_src_folder}index.php` ...." );

        if( @rename("{$public_src_folder}index-dist.php", "{$public_src_folder}index.php") ) {

            static::printInfo("Successfully Moved!".PHP_EOL);

        } else {

            static::printError( "Could not move `{$raw_public_src_folder}{$ds}index-dist.php` to `{$raw_public_src_folder}{$ds}index.php`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Moving `{$raw_root_folder}.gitignore-dist` to `{$raw_root_folder}.gitignore` ...." );

        if( @rename("{$root_folder}.gitignore-dist", "{$root_folder}.gitignore") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_root_folder}.gitignore-dist` to `{$raw_root_folder}.gitignore`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Moving `{$raw_root_folder}README-dist.md` to `{$raw_root_folder}README.md` ...." );

        if( @rename("{$root_folder}README-dist.md", "{$root_folder}README.md") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_root_folder}README-dist.md` to `{$raw_root_folder}README.md`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Moving `{$raw_root_folder}composer-dist.json` to `{$raw_root_folder}composer.json` ...." );

        if( @rename("{$root_folder}composer-dist.json", "{$root_folder}composer.json") ) {

            static::printInfo( "Successfully Moved!".PHP_EOL );

        } else {

            static::printError( "Could not move `{$raw_root_folder}composer-dist.json` to `{$raw_root_folder}composer.json`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Deleting `{$raw_root_folder}slim3-psr7.png` ....".PHP_EOL );

        if( @unlink("{$root_folder}slim3-psr7.png") ) {

            static::printInfo( "Successfully Deleted!".PHP_EOL );

        } else {

            static::printError( "Could not delete `{$raw_root_folder}slim3-psr7.png`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Deleting `{$raw_root_folder}slim3-psr7.pub` ....".PHP_EOL );

        if( @unlink("{$root_folder}slim3-psr7.pub") ) {

            static::printInfo( "Successfully Deleted!".PHP_EOL );

        } else {

            static::printError( "Could not delete `{$raw_root_folder}slim3-psr7.pub`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Deleting `{$raw_root_folder}index.php-overview.docx` ....".PHP_EOL );

        if( @unlink("{$root_folder}index.php-overview.docx") ) {

            static::printInfo( "Successfully Deleted!".PHP_EOL );

        } else {

            static::printError( "Could not delete `{$raw_root_folder}index.php-overview.docx`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Deleting `{$raw_root_folder}index.php-overview.png` ....".PHP_EOL );

        if( @unlink("{$root_folder}index.php-overview.png") ) {

            static::printInfo( "Successfully Deleted!".PHP_EOL );

        } else {

            static::printError( "Could not delete `{$raw_root_folder}index.php-overview.png`!".PHP_EOL );
        }

        sleep(1);

        ////////////////////////////////////////////////////////////
        static::printInfo( "Deleting `{$raw_root_folder}phpunit.xml.dist` ....".PHP_EOL );

        if( @unlink("{$root_folder}phpunit.xml.dist") ) {

            static::printInfo( "Successfully Deleted!".PHP_EOL  );

        } else {

            static::printError("Could not delete `{$raw_root_folder}phpunit.xml.dist`!".PHP_EOL);
        }

        sleep(1);

        ////////////////////////////////////////////////////////////////////////////////
        $logs_folder = $root_folder."logs";
        static::printInfo("Making `{$logs_folder}` writable ....".PHP_EOL);

        if( chmod($logs_folder, 0777) ) {

            static::printInfo( "Successfully made `{$logs_folder}` writable!".PHP_EOL );

        } else {

            static::printError("Could not make `{$logs_folder}` writable!!".PHP_EOL);
        }

        ////////////////////////////////////////////////////////////////////////////////
        //Interactive Part: Ask if user wants foundation template
        ////////////////////////////////////////////////////////////////////////////////
        $response = static::readFromLine("Do you want to use the Zurb Foundation CSS/JS framework that ships with SlimPHP 3 Skeleton MVC package? (Y/N)");

        if ( strtoupper(trim($response)) === 'N' ) {

            ////////////////////////////////////////////////////////////
            // delete src/layout-templates/main-template.php
            static::printInfo( PHP_EOL."Deleting `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template.php` ....".PHP_EOL );

            $main_template_folder = "{$root_folder}src{$ds}layout-templates{$ds}";

            if( @unlink("{$main_template_folder}main-template.php") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template.php`!".PHP_EOL);
            }

            sleep(1);

            ///////////////////////////////////////////////////////////////////////////////////////
            // move main-template-no-foundation.php into main-template.php in src/layout-templates
            static::printInfo( "Moving `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template-no-foundation.php` to `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template.php` ....".PHP_EOL );

            if( @rename("{$main_template_folder}main-template-no-foundation.php", "{$main_template_folder}main-template.php") ) {

                static::printInfo( "Successfully Moved!".PHP_EOL  );

            } else {

                static::printError("Could not move `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template-no-foundation.php` to `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template.php`!".PHP_EOL);
            }

            sleep(1);

            ////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////
            // delete src/views/base/login.php
            static::printInfo( "Deleting `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login.php` ....".PHP_EOL );

            $base_view_folder = "{$root_folder}src{$ds}views{$ds}base{$ds}";

            if( @unlink("{$base_view_folder}login.php") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login.php`!".PHP_EOL);
            }

            sleep(1);

            ///////////////////////////////////////////////////////////////////////////////////////
            // move login-no-foundation.php into login.php  in src/views/base
            static::printInfo( "Moving `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login-no-foundation.php` to `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login.php` ....".PHP_EOL );

            if( @rename("{$base_view_folder}login-no-foundation.php", "{$base_view_folder}login.php") ) {

                static::printInfo( "Successfully Moved!".PHP_EOL  );

            } else {

                static::printError("Could not move `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login-no-foundation.php` to `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login.php`!".PHP_EOL);
            }

            sleep(1);

            ////////////////////////////////////////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////////////////////////////////////////

            ////////////////////////////////////////////////////////////
            // delete public/css/foundation
            static::printInfo( "Deleting `{$raw_root_folder}public{$ds}css{$ds}foundation` ....".PHP_EOL );

            $base_foundation_css_folder = "{$root_folder}public{$ds}css{$ds}foundation";

            if( static::rrmdir("{$base_foundation_css_folder}") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}public{$ds}css{$ds}foundation`!".PHP_EOL);
            }

            sleep(1);

            ////////////////////////////////////////////////////////////
            // delete public/js/foundation
            static::printInfo( "Deleting `{$raw_root_folder}public{$ds}js{$ds}foundation` ....".PHP_EOL );

            $base_foundation_js_folder = "{$root_folder}public{$ds}js{$ds}foundation";

            if( static::rrmdir("{$base_foundation_js_folder}") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}public{$ds}js{$ds}foundation`!".PHP_EOL);
            }

            sleep(1);

            static::printInfo( PHP_EOL . "Successfully Disabled Zurb Foundation CSS/JS framework!" . PHP_EOL );
            sleep(1);

        } else {

            ///////////////////////////////////////////////////////////////
            // delete src/layout-templates/main-template-no-foundation.php
            static::printInfo( PHP_EOL."Deleting `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template-no-foundation.php` ....".PHP_EOL );

            $main_template_folder = "{$root_folder}src{$ds}layout-templates{$ds}";

            if( @unlink("{$main_template_folder}main-template-no-foundation.php") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}src{$ds}layout-templates{$ds}main-template-no-foundation.php`!".PHP_EOL);
            }

            sleep(1);

            ///////////////////////////////////////////////////////////////
            // delete src/views/base/login-no-foundation.php
            static::printInfo( PHP_EOL."Deleting `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login-no-foundation.php` ....".PHP_EOL );

            $base_view_folder = "{$root_folder}src{$ds}views{$ds}base{$ds}";

            if( @unlink("{$base_view_folder}login-no-foundation.php") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}src{$ds}views{$ds}base{$ds}login-no-foundation.php`!".PHP_EOL);
            }

            sleep(1);

            ///////////////////////////////////////////////////////////////
            // delete public/css/app.css
            static::printInfo( PHP_EOL."Deleting `{$raw_root_folder}public{$ds}css{$ds}app.css` ....".PHP_EOL );

            $base_public_css_folder = "{$root_folder}public{$ds}css{$ds}";

            if( @unlink("{$base_public_css_folder}app.css") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}public{$ds}css{$ds}app.css`!".PHP_EOL);
            }

            sleep(1);

            ///////////////////////////////////////////////////////////////
            // delete public/js/app.js
            static::printInfo( PHP_EOL."Deleting `{$raw_root_folder}public{$ds}js{$ds}app.js` ....".PHP_EOL );

            $base_public_js_folder = "{$root_folder}public{$ds}js{$ds}";

            if( @unlink("{$base_public_js_folder}app.js") ) {

                static::printInfo( "Successfully Deleted!".PHP_EOL  );

            } else {

                static::printError("Could not delete `{$raw_root_folder}public{$ds}js{$ds}app.js`!".PHP_EOL);
            }

            sleep(1);

            static::printInfo( PHP_EOL . "Successfully Enabled Zurb Foundation CSS/JS framework!" . PHP_EOL );
            sleep(1);
        }
    }


     public static function printError($str, $append_new_line = true) {

        echo \Slim3MvcTools\Functions\Str\color_4_console( "ERROR: $str", "red",  "black");

        if( $append_new_line ) {

            echo PHP_EOL;
        }
    }
    
    public static function printInfo($str, $append_new_line = true) {

        echo \Slim3MvcTools\Functions\Str\color_4_console( $str, "green",  "black");

        if( $append_new_line ) {

            echo PHP_EOL;
        }
    }
    
    public static function readFromLine( $prompt = '' ) {
        
        echo $prompt;
        return trim(rtrim( fgets( STDIN ), PHP_EOL ));
    }
    
    public static function rrmdir($src) {
        
        if( strlen($src) <=0 || !is_dir($src) ) { return false; }
        
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
        
        return @rmdir($src);
    }
}

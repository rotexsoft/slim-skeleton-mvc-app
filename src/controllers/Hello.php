<?php
declare(strict_types=1);

namespace SlimSkeletonMvcApp\Controllers;

use \Psr\Container\ContainerInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Description of Hello
 *
 * @author Rotimi Ade
 */
class Hello extends \SlimMvcTools\Controllers\BaseController
{
    public function __construct(
        ContainerInterface $container, 
        string $controller_name_from_uri, 
        string $action_name_from_uri, 
        ServerRequestInterface $req, 
        ResponseInterface $res     
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
    }
    
    public function actionIndex(): ResponseInterface|string {

        //using a string here directly instead of a view
        $view_str = 'Hello@actionIndex: Controller Action Method Content Goes Here!';
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionWorld($name, $another_param): string {
        
        //get the contents of the view first
        $view_str = $this->renderView('world.php', ['name'=>$name, 'params'=>$another_param]);
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionThere($first_name, $last_name): string {

        $view_str = "Hello There {$first_name}, {$last_name}<br>";
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
    
    public function actionForceHttpFourxxOrFivexx($http_code=400): string {
        
        $allowed_codes = [400, 401, 403, 404, 405, 410, 500, 501];
        
        if(\in_array($http_code, $allowed_codes)) {
            
            $method = "forceHttp{$http_code}";
            $message = "Forced HTTP {$http_code}";
            $this->$method($message);
        }
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=> "Could not force http response with code: `{$http_code}`"] );
    }
}

<?php
declare(strict_types=1);

namespace SlimSkeletonMvcApp\Controllers;

use \Psr\Container\ContainerInterface,
    \Psr\Http\Message\ServerRequestInterface,
    \Psr\Http\Message\ResponseInterface;

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
    
    public function actionIndex(): string {

        //using a string here directly instead of a view
        $view_str = 'Hello@actionIndex: Controller Action Method Content Goes Here!';
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
    
   /**
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function actionWorld($name, $another_param) {
        
        //get the contents of the view first
        $view_str = $this->renderView('world.php', ['name'=>$name, 'params'=>$another_param]);
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
    
   /**
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function actionThere($first_name, $last_name) {

        $view_str = "Hello There $first_name, $last_name<br>";
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str] );
    }
}

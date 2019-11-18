<?php
namespace Slim3SkeletonMvcApp\Controllers;

/**
 * Description of Hello
 *
 * @author aadegbam
 */
class Hello extends \Slim3MvcTools\Controllers\BaseController
{
    public function __construct(
        \Interop\Container\ContainerInterface $container, $controller_name_from_uri, $action_name_from_uri, 
        \Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Message\ResponseInterface $res     
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
    }
    
    public function actionIndex() {

        //using a string here directly instead of a view
        $view_str = 'Hello@actionIndex: Controller Action Method Content Goes Here!';
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
    
    public function actionWorld($name, $another_param) {
        
        //get the contents of the view first
        $view_str = $this->renderView('world.php', ['name'=>$name, 'params'=>$another_param]);
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
    
    public function actionThere($first_name, $last_name) {

        $view_str = "Hello There $first_name, $last_name<br>";
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
}


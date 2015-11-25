<?php

/**
 * Description of Hello
 *
 * @author aadegbam
 */
class Hello extends \Slim3MvcTools\BaseController
{
    public function __construct(\Slim\App $app, $controller_name_from_uri, $action_name_from_uri) {
        
        parent::__construct($app, $controller_name_from_uri, $action_name_from_uri);
        
        //Prepend view folder for this controller. 
        //It takes precedence over the view folder for the base controller. 
        $path_2_view_files = __DIR__.DIRECTORY_SEPARATOR.'../views/hello';
        $this->view_renderer->prependPath($path_2_view_files);
    }
    
    public function actionIndex() {

        return 'Hello@actionIndex: Controller Action Method Content Goes Here!';
    }
    
    public function world($name, $another_param) {
        
        //get the contents of the view first
        $view_str = $this->renderView('world.php', ['name'=>$name, 'params'=>$another_param]);
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
}
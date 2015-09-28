<?php

/**
 * Description of Hello
 *
 * @author aadegbam
 */
class Hello extends \Slim3Mvc\Controllers\BaseController
{
    
    public function __construct(\Slim\App $app, $controller_name_from_uri, $action_name_from_uri) {
        
        parent::__construct($app, $controller_name_from_uri, $action_name_from_uri);
        
        //Prepend view folder for this controller. 
        //It takes precedence over the view folder for the base controller. 
        $path_2_view_files = __DIR__.DIRECTORY_SEPARATOR.'../views-4-controller-actions/hello';
        $this->view_renderer->prependPath($path_2_view_files);
    }
    
    public function actionIndex() {

        return 'Content Goes Here';
    }
    
    public function world($name, $another_param) {
        
        //echo $name, ' ', $another_param;
        
        //get the contents of the view first
        $view_str = $this->getViewAsString('world.php', ['name'=>$name, 'params'=>$another_param]);
        $layout_data = ['content'=>$view_str, 'request_obj'=>$this->app->request];
        
        return $this->getLayoutAsString( 'main-template.php', $layout_data );
    }
}
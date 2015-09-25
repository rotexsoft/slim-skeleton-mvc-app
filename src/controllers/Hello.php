<?php

/**
 * Description of Hello
 *
 * @author aadegbam
 */
class Hello extends \CfsSlim3\Controllers\BaseController
{
    
    public function world($name, $another_param) {
        
        //echo $name, ' ', $another_param;
        
        //get the contents of the view first
        $view_str = $this->getViewAsString('default-content.php');
        $layout_data = ['content'=>$view_str, 'request_obj'=>$this->app->request];
        
        return $this->getLayoutAsString( 'main-template.php', $layout_data );
    }
}
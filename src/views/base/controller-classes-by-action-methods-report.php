<?php
use function \SlimMvcTools\Functions\Str\camelToDashes;

// Function to check string starting 
// with given substring 
$functionStrStartsWith = function (string $string, string $startString) {
    $string_len = strlen($string);
    $start_len = strlen($startString);
    return ($string_len >= $start_len) 
           && (substr($string, 0, $start_len) === $startString); 
};

// first traverse the src directory and include all *.php files and then get declared classes
$src_path  = SMVC_APP_ROOT_PATH.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
$Directory = new RecursiveDirectoryIterator($src_path);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

// include the php files so that classes contained in them will be declared
foreach ($Regex as $file) {

    //echo $file[0] . PHP_EOL;
    include_once $file[0];
}

$reflection_methods_map = [];
$declared_classes = get_declared_classes();

$controller_classes = array_filter(
    $declared_classes,
    function($currentClassName) {
        return is_subclass_of($currentClassName, \SlimMvcTools\Controllers\BaseController::class)
               || is_a($currentClassName, \SlimMvcTools\Controllers\BaseController::class, true);
    }   
); // get an array of the name of the classes that are instances of \SlimMvcTools\Controllers\BaseController

$action_methods_by_controller_class_name = [];

foreach($controller_classes as $className) {
    
    $rfclass = new \ReflectionClass($className);
    $rfclassMethodObjs = $rfclass->getMethods(\ReflectionMethod::IS_PUBLIC);
    
    $action_methods_by_controller_class_name[$className] = array_filter(
        $rfclassMethodObjs, 
        function(\ReflectionMethod $current_method)use($functionStrStartsWith, $rfclass, &$reflection_methods_map, $className, $onlyPublicMethodsPrefixedWithAction) {

            $is_action_method_defined_in_class = 
                $current_method->getFileName() === $rfclass->getFileName() // make sure it's not an inherited or trait method
                && 
                (
                    (
                        $onlyPublicMethodsPrefixedWithAction
                        && $functionStrStartsWith($current_method->getName(), 'action')
                    )
                    || (!$onlyPublicMethodsPrefixedWithAction)
                );

            if( $is_action_method_defined_in_class ) {

                $reflection_methods_map[$className.':'.$current_method->getName()] = $current_method;
            }

            return $is_action_method_defined_in_class;
        }  
    );
}

// At this point, $action_methods_by_controller_class_name contains an array
// whose keys are class names and the corresponding values are arrays of
// \ReflectionMethod associated with each class.

// We are going to transform $action_methods_by_controller_class_name to an array
// whose keys are class names and the corresponding values are arrays of method 
// names associated with each class.

foreach($action_methods_by_controller_class_name as $className => $methods) {
    
    if(is_array($methods) && count($methods) > 0) {
        
        foreach($methods as $key=>$method) {
            
            $action_methods_by_controller_class_name[$className][$key] = $method->getName();
        }
        
        asort($action_methods_by_controller_class_name[$className], \SORT_REGULAR);
    }
}

ksort($action_methods_by_controller_class_name);

$dataToRender = [];

if ( count($action_methods_by_controller_class_name) > 0 ) {
    
    foreach ( $action_methods_by_controller_class_name  as $controller_class_name=>$action_methods ) {
        
        foreach ( $action_methods as $action_method ) {
            
            $ref_meth_obj = $reflection_methods_map["{$controller_class_name}:{$action_method}"];
            $route = camelToDashes($ref_meth_obj->getDeclaringClass()->getShortName())
                . "/" 
                . 
                (
                    $onlyPublicMethodsPrefixedWithAction
                        ? camelToDashes(str_replace('action', '', $ref_meth_obj->getName()))
                        : camelToDashes($ref_meth_obj->getName())
                );
            
            foreach ($ref_meth_obj->getParameters() as $parameter) {
                
                if( $parameter->isOptional() ) {
                    
                    $route .= '[/';
                    
                } else {
                    
                    $route .= '/';
                }
                
                $route .= $parameter->getName();
                
                if( $parameter->isDefaultValueAvailable() ) {
                    
                    $route .= '='. var_export($parameter->getDefaultValue(), true);
                }
                
                if( $parameter->isOptional() ) { $route .= ']'; }
            }
            
            $dataToRender[] = [
                'controlller'   => $controller_class_name,
                'action'        => $action_method,
                'route'         => $route,
            ];
        } // foreach ( $action_methods as $action_method )
    } // foreach ( $action_methods_by_controller_class_name  as $controller_class_name=>$action_methods )
}//if ( $action_methods_by_controller_class_name->count() > 0 )

?>
            <h1>App Routes</h1>
            <table>
                <thead>
                    <tr>
                        <th>Controller Class Name</th>
                        <th>Action Method Name</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dataToRender as $data): ?>
                        <tr>
                            <td><?= $this->escapeHtml($data['controlller']); ?></td>
                            <td><?= $this->escapeHtml($data['action']); ?></td>
                            <td><?= $this->escapeHtml($data['route']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

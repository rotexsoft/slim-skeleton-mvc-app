<?php
namespace Slim3Mvc;

/**
 * 
 * Class for rendereing the contents of a php file.
 *
 * @author aadegbam
 */
class View
{
    /**
     * 
     * Path(s) to folder(s) containing (*.php) files to be rendered via this class.
     *
     * @var string|array
     *  
     */
    protected $possible_paths_to_file;
    
    /**
     * 
     * For use ONLY in $this->renderAsString(..)
     * 
     * To avoid confilict when extract($data) is called in $this->renderAsString(..)
     * Referencing $this->found_path rather than $found_path inside 
     * $this->renderAsString(..) allows for the existence of $data['found_path'] 
     * which will be extracted to $found_path.
     * 
     * @var string
     */
    protected $found_path = '';


    /**
     * 
     * @param array $possible_paths_to_file
     */
    public function __construct( array $possible_paths_to_file ) {
        
        $this->possible_paths_to_file = $possible_paths_to_file;
    }

    /**
     * 
     * @param string $path
     */
    public function appendPath( $path ) {
        
        $this->possible_paths_to_file[] = $path;
    }

    /**
     * 
     * @param string $path
     */
    public function prependPath( $path ) {
        
        array_unshift($this->possible_paths_to_file, $path);
    }
    
    /**
     * 
     * @param string $number_of_paths_2_remove
     */
    public function removeFirstNPaths($number_of_paths_2_remove) {
        
        if( is_numeric($number_of_paths_2_remove) ) {
            
            while ( 
                $number_of_paths_2_remove > 0  
                && count($this->possible_paths_to_file) > 0 
            ) {
                array_shift($this->possible_paths_to_file);
                $number_of_paths_2_remove--;
            }
        }
    }
    
    /**
     * 
     * @param string $number_of_paths_2_remove
     */
    public function removeLastNPaths($number_of_paths_2_remove) {
        
        if( is_numeric($number_of_paths_2_remove) ) {
            
            while ( 
                $number_of_paths_2_remove > 0  
                && count($this->possible_paths_to_file) > 0 
            ) {
                array_pop($this->possible_paths_to_file);
                $number_of_paths_2_remove--;
            }
        }
    }
    
    /**
     * 
     * WARNING $data CANNOT CONTAIN $data['this']. It will be unset by this 
     * method in order to prevent overwritting $this.
     * 
     * @param string $file_name
     * @param array $data
     * @return string
     * @throws \Slim3Mvc\ViewFileNotFoundException
     */
    public function renderAsString( $file_name, array $data = [] ) {

        $this->found_path = '';
        
        foreach ($this->possible_paths_to_file as $possible_path) {
            
            if( file_exists( rtrim($possible_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $file_name ) ) {
                
                $this->found_path = rtrim($possible_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $file_name;
                break;
            }
        }
        
        if( empty($this->found_path) ) {
            
            //the file does not exist in any of the possible paths supplied
            $msg = "ERROR: Could not load the file named `$file_name` "
                    . "from any of the paths below:"
                    . PHP_EOL . implode(PHP_EOL, $this->possible_paths_to_file) . PHP_EOL
                    . PHP_EOL . get_class($this) . '::' . __FUNCTION__ . '(...).' 
                    . PHP_EOL;
            
            throw new ViewFileNotFoundException($msg);
        }
        
        
        // prevent overwriting $this
        unset($data['this']);
        
        extract($data);
        ob_start();
        
        require $this->found_path;
       
        return ob_get_clean();
    }
}

class ViewFileNotFoundException extends \Exception { }
<?php
namespace Slim3Mvc;

/**
 * 
 * Class for rendereing the contents of a php file.
 *
 * @author Rotimi Adegbamigbe
 * 
 */
class View
{
    /**
     * 
     * Path(s) to directorie(s) containing (*.php) files to be rendered via this class.
     *
     * @var string|array
     *  
     */
    protected $possible_paths_to_file;

    /**
     * 
     * @param array $file_paths An array of path(s) to directorie(s) containing 
     *                          (*.php) files to be rendered via this class.
     * 
     */
    public function __construct( array $file_paths ) {
        
        $this->possible_paths_to_file = $file_paths;
    }

    /**
     * 
     * Returns the value of the `possible_paths_to_file` property of an instance
     * of this class.
     * 
     * @return array the array of path(s) to directorie(s) containing (*.php) 
     *               files to be rendered via this class.
     * 
     */
    public function getPossiblePathsToFile() {
        
        return $this->possible_paths_to_file;
    }
    
    /**
     * 
     * Add a path as the last element of the array of path(s) to directorie(s) 
     * containing (*.php) files to be rendered via this class.
     * 
     * @param string $path
     * 
     */
    public function appendPath( $path ) {
        
        $this->possible_paths_to_file[] = $path;
    }

    /**
     * 
     * Add a path as the first element of the array of path(s) to directorie(s) 
     * containing (*.php) files to be rendered via this class.
     * 
     * @param string $path
     * 
     */
    public function prependPath( $path ) {
        
        array_unshift($this->possible_paths_to_file, $path);
    }
    
    /**
     * 
     * Removes the first `n` elements in the array of path(s) to directorie(s) 
     * containing (*.php) files to be rendered via this class.
     * 
     * @param string $number_of_paths_2_remove
     * 
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
     * Removes the last `n` elements in the array of path(s) to directorie(s) 
     * containing (*.php) files to be rendered via this class.
     * 
     * @param string $number_of_paths_2_remove
     * 
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
	 * Captures and returns the output that is generated when a php file is included. 
     * 
     * @param string $file_name Name of php file to be included (with/without
     *                          the directory path). If the directory path is 
     *                          included & the file exists, it is included and
     *                          the output is returned. If the directory path is 
     *                          not included, the file will be searched for in the 
     *                          list of directories registered in $this->possible_paths_to_file.
     *                          
     * @param array $data Array of data to be extracted to make local variables.
     * 
     * @return string the output that is generated when $file_name is included
     * 
     * @throws \Slim3Mvc\ViewFileNotFoundException
     * 
     */
    public function renderAsString( $file_name, array $data = [] ) {

        $located_file = file_exists($file_name) ? $file_name : false;
        
        if( $located_file === false ) {
            
            //$file_name is not an existent file on its own. Search for it in 
            //the list of paths registered in $this->possible_paths_to_file.
            
            foreach ( $this->possible_paths_to_file as $possible_path ) {

                if( file_exists( rtrim($possible_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $file_name ) ) {

                    $located_file = rtrim($possible_path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $file_name;
                    break;
                }
            }
        }
        
        if( $located_file === false ) {
            
            //the file does not exist in any of the registered possible paths
            $msg = "ERROR: Could not load the file named `$file_name` "
                . "from any of the paths below:"
                . PHP_EOL . implode(PHP_EOL, $this->possible_paths_to_file) . PHP_EOL
                . PHP_EOL . get_class($this) . '::' . __FUNCTION__ . '(...).' 
                . PHP_EOL;
            
            throw new ViewFileNotFoundException($msg);
        }
        
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        ////
        //// Deliberately not specifying parameters in the anonymous function's 
        //// definition signature below in order to avoid having any explicit 
        //// variable(s) defined inside the anonymous function. Rather, the 
        //// parameters are being accessed via func_get_arg() and not even 
        //// assigned to any local variable(s) inside the function.
        ////
        //// This way we need not worry about any variable(s) being overwritten
        //// when extract(..) is called within the anonymous function.
        ////  
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
		$render_view = function()
		{
            //func_get_arg(0): the name of the file to be included whose output 
            //                 is to be captured and returned
            
            //func_get_arg(1): the data array from which to extract variables
            
            //Extract variables from the data array which may be needed in the
            //view file to be included below.
			extract(func_get_arg(1), EXTR_REFS);

			// Capture the view output
			ob_start();

            // Load the view within the current scope
            include func_get_arg(0);

			// Get the captured output and close the buffer
			return ob_get_clean();
		};
        
		return $render_view($located_file, $data);
    }
}

class ViewFileNotFoundException extends \Exception { }
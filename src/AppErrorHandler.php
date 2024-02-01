<?php
declare(strict_types=1);

namespace SlimSkeletonMvcApp;

use \Psr\Http\Message\ServerRequestInterface;

/**
 * Description of AppErrorHandler
 *
 * @author rotimi
 */
class AppErrorHandler extends \SlimMvcTools\ErrorHandler {
    
    public function __invoke(ServerRequestInterface $request, \Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): \Psr\Http\Message\ResponseInterface {
        
        $response = parent::__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);
        
        if($this->container !== null) {
            
            // Do some stuff with the container here, like pull out a mailer 
            // object and send out notification emails about the current error 
            // before finally displaying the error page.
            
            if($exception instanceof \Slim\Exception\HttpNotFoundException) {
                
                // do some 404 specific processing here
                
            } elseif ($exception instanceof \Slim\Exception\HttpMethodNotAllowedException) {
                
                // do some 405 specific processing here
                
            } // .... on and on and on for other Http*Exception instances
        }
        
        return $response;
    }
}

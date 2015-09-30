<?php
namespace Slim3Mvc\Controllers;

use \Psr\Http\Message\ServerRequestInterface,
    \Psr\Http\Message\ResponseInterface;

/**
 * 
 * Description of BaseController
 *
 * @author Rotimi Adegbamigbe
 * 
 */
class BaseController
{
    /**
     *
     * A Slim App object containing, Request, Response and other Environment
     * information for each request sent to this controller or any of its
     * sub-classes.
     * 
     * @var \Slim\Slim
     * 
     */
    protected $app;

    /**
     * 
     * View object for rendering layout files. 
     *
     * @var \Slim3Mvc\OtherClasses\View
     *  
     */
    protected $layout_renderer;
    
    /**
     * 
     * View object for rendering view files associated with controller actions. 
     *
     * @var \Slim3Mvc\OtherClasses\View
     *  
     */
    protected $view_renderer;
        
    /**
     *
     * The action section of the url. 
     * 
     * Eg. http://localhost/slim3-skeleton-mvc-app/public/base-controller/action-index
     * will result in $this->action_name_from_uri === 'action-index'
     * 
     * @var string 
     * 
     */
    public $action_name_from_uri;
    
    /**
     *
     * The controller section of the url. 
     * 
     * Eg. http://localhost/slim3-skeleton-mvc-app/public/base-controller/action-index
     * will result in $this->controller_name_from_uri === 'base-controller'
     * 
     * @var string 
     * 
     */
    public $controller_name_from_uri;

    /**
     * 
     * 
     * 
     * @param \Slim\App $app
     * @param string $controller_name_from_uri
     * @param string $action_name_from_uri
     * 
     */
    public function __construct(\Slim\App $app, $controller_name_from_uri, $action_name_from_uri) {
        
        $this->app = $app;
        $this->action_name_from_uri = $action_name_from_uri;
        $this->controller_name_from_uri = $controller_name_from_uri;
        
        $path_2_layout_files = __DIR__.DIRECTORY_SEPARATOR.'../layout-templates';
        $this->layout_renderer = $this->app->getContainer()->get('new_layout_renderer');
        $this->layout_renderer->appendPath($path_2_layout_files);
        
        $path_2_view_files = __DIR__.DIRECTORY_SEPARATOR.'../views-4-controller-actions/base';
        $this->view_renderer = $this->app->getContainer()->get('new_view_renderer');
        $this->view_renderer->appendPath($path_2_view_files);
    }
    
    /**
     * 
     * Executes a PHP file and returns its output as a string. This file is 
     * supposed to contain the layout template of your site.
     * 
     * @param string $file_name name of the file (including extension eg. `read.php`) 
     *                          containing valid php to be executed and returned as
     *                          string.
     * @param array $data an array of data to be passed to the layout file. Each
     *                    key in this array is automatically converted to php
     *                    variables accessible in the layout file. 
     *                    Eg. passing ['content'=>'yabadabadoo'] to this method 
     *                    will result in a variable named $content (with a 
     *                    value of 'yabadabadoo') being available in the layout
     *                    file (i.e. the file named $file_name).
     * @return string
     * 
     */
    public function renderLayout( $file_name, array $data = ['content'=>''] ) {
        
        return $this->layout_renderer->getAsString($file_name, $data);
    }
    
    /**
     * 
     * Executes a PHP file and returns its output as a string. This file is 
     * supposed to contain the output markup (usually html) for the current 
     * controller action method being executed.
     * 
     * @param string $file_name name of the file (including extension eg. `read.php`) 
     *                          containing valid php to be executed and returned as
     *                          string.
     * @param array $data an array of data to be passed to the view file. Each
     *                    key in this array is automatically converted to php
     *                    variables accessible in the view file. 
     *                    Eg. passing ['content'=>'yabadabadoo'] to this method 
     *                    will result in a variable named $content (with a 
     *                    value of 'yabadabadoo') being available in the view
     *                    file (i.e. the file named $file_name).
     * @return string
     * 
     */
    public function renderView( $file_name, array $data = [] ) {

        return $this->view_renderer->getAsString($file_name, $data);
    }
    
    public function actionIndex() {

        //get the contents of the view first
        $view_str = $this->renderView('index.php');
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
    
    public function actionDefaultTemplateContent() {

        //get the contents of the view first
        $view_str = $this->renderView('default-content.php');
        
        return $this->renderLayout( 'main-template.php', ['content'=>$view_str] );
    }
    
    public function actionLogin() {

        $success_redirect_path = '';
        $using_default_redirect = false;
        $request_obj = $this->app->getContainer()->get('request');
        
        if( 
            strtoupper($request_obj->getMethod()) === 'GET' 
            && !empty(s3MVC_GetSuperGlobal('get', 'login_redirect_path'))
        ) {
            //TODO: SANITIZATION: should sanitize the value from $_GET below 
            $success_redirect_path = s3MVC_GetSuperGlobal('get', 'login_redirect_path');
        }
        
        if( empty($success_redirect_path) ) {
            
            $using_default_redirect = true;
            $success_redirect_path = "{$this->controller_name_from_uri}/action-login-status";
        }
        
        $data_4_login_view = [
                            'controller_object'=>$this,
                            'error_message' => ''
                        ];
        
        if( strtoupper($request_obj->getMethod()) === 'GET' ) {

            //show login form
            $view_str = $this->renderView('login.php', $data_4_login_view);
            return $this->renderLayout('main-template.php', ['content'=>$view_str]);
            
        } else {
            
            //this is a POST request, process login
            $username = s3MVC_GetSuperGlobal('post', 'username');//TODO: SANITIZATION: should sanitize this value
            $password = s3MVC_GetSuperGlobal('post', 'password');//TODO: SANITIZATION: should sanitize this value
            
            $auth = $this->app->getContainer()->get('aura_auth_object');
            $login_service = $this->app->getContainer()->get('aura_login_service');
            $loggedin_successfully = false;
            
            try {

                $login_service->login(
                    $auth, [ 'username'=>$username, 'password'=>$password ]
                );
                
                $msg = "You are now logged into a new session. <br>";
                $loggedin_successfully = true;

            } catch (\Aura\Auth\Exception\UsernameMissing $e) {

                $msg = "The 'username' field is missing or empty. <br>";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\PasswordMissing $e) {

                $msg = "The 'password' field is missing or empty. <br>";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\UsernameNotFound $e) {

                $msg = "The username you entered was not found. <br>";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\MultipleMatches $e) {

                $msg = "There is more than one account with that username. <br>";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\PasswordIncorrect $e) {

                $msg = "The password you entered was incorrect. <br>";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\ConnectionFailed $e) {

                $msg = "Cound not connect to IMAP or LDAP server.";
                $msg .= " This could be because the username or password was wrong,";
                $msg .= " or because the the connect operation itself failed in some way. <br>";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\BindFailed $e) {

                $msg = "Cound not bind to LDAP server.";
                $msg .= "This could be because the username or password was wrong,";
                $msg .= " or because the the bind operation itself failed in some way. <br>";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Exception $e) {

                $msg = "Invalid login details. Please try again. <br>";
            }
            
            $msg .= nl2br(\Slim3Mvc\OtherClasses\dumpAuthinfo($auth));

            if( $loggedin_successfully ) {
                                
                //redirect to desired or default destination
                if($using_default_redirect) {
                    
                    $success_redirect_path .= '/1'; //will lead to
                                                    //$this->actionLoginStatus('1')
                    $success_redirect_path .= '?login_status='. rawurlencode($msg);
                }
                
                if( strpos($success_redirect_path, s3MVC_GetBaseUrlPath()) === false ) {
                    
                    //prepend base path
                    $success_redirect_path = 
                        s3MVC_GetBaseUrlPath().'/'.ltrim($success_redirect_path, '/');
                }
                
                //re-direct
                return $this->app
                            ->getContainer()
                            ->get('response')
                            ->withHeader('Location', $success_redirect_path);
            } else {
                
                //re-display login form with error messages
                $data_4_login_view['error_message'] = $msg;
                $view_str = $this->renderView('login.php', $data_4_login_view);
                
                return $this->renderLayout('main-template.php', ['content'=>$view_str]);
            }
        }
    }
    
    /**
     * 
     * 
     * 
     */
    public function actionLogout() {
        
        $msg = '';
        $auth = $this->app->getContainer()->get('aura_auth_object');
        $logout_service = $this->app->getContainer()->get('aura_logout_service');
        $logout_service->logout($auth);

        if ($auth->isAnon()) {

            $msg = "You are now logged out. <br>";

        } else if ($auth->isValid()) {

            $msg = "Something went wrong; you are still logged in. <br>";
            
        } else {
            
            $msg = "Something went wrong; but you are not logged in. <br>";
        }

        $msg .= nl2br(\Slim3Mvc\OtherClasses\dumpAuthinfo($auth));
        
        $redirect_path = s3MVC_GetBaseUrlPath()
                        . "/{$this->controller_name_from_uri}/action-login-status"
                        . '?login_status='. rawurlencode($msg);
        //re-direct
        return $this->app->getContainer()
                         ->get('response')
                         ->withHeader('Location', $redirect_path);
    }
    
    /**
     * 
     * 
     * 
     */
    public function actionLoginStatus($is_logged_in=false) {

        $msg = '';
        $request_obj = $this->app->getContainer()->get('request');
        
        if( strtoupper($request_obj->getMethod()) === 'GET' ) {
        
            //TODO: SANITIZATION: the status message was passed via get; should sanitize this value
            $get_array = s3MVC_GetSuperGlobal('get');
            $message_in_get = array_key_exists('login_status', $get_array);
            $msg = ($message_in_get) ? $get_array['login_status'] : $msg;
            
        } else {
            
            //TODO: SANITIZATION: the status message was passed via get; should sanitize this value
            $msg = s3MVC_GetSuperGlobal('post', 'login_status');
        }
        
        $view_str = $this->renderView('login-status.php', ['message'=>$msg, 'is_logged_in'=>$is_logged_in, 'controller_object'=>$this]);
        return $this->renderLayout('main-template.php', ['content'=>$view_str]);
    }
    
    /**
     * 
     * 
     * 
     */
    public function actionCheckLoginStatus() {

        $msg = '';
        $auth = $this->app->getContainer()->get('aura_auth_object');
        $resume_service = $this->app->getContainer()->get('aura_resume_service');
        $resume_service->resume($auth);

        switch (true) {
            
            case $auth->isAnon():
                $msg = "You are not logged in. <br>";
                break;
            case $auth->isIdle():
                $msg = "Your session was idle for too long. Please log in again. <br>";
                break;
            case $auth->isExpired():
                $msg = "Your session has expired. Please log in again. <br>";
                break;
            case $auth->isValid():
                $msg = "You are still logged in. <br>";
                break;
            default:
                $msg =  "You have an unknown status. <br>";
                break;
        }

        $msg .= nl2br(\Slim3Mvc\OtherClasses\dumpAuthinfo($auth));
        
        $redirect_path = 
            s3MVC_GetBaseUrlPath()
                . "/{$this->controller_name_from_uri}/action-login-status"
                . ( $auth->isValid() ? '/1' : '')
                . '?login_status='. rawurlencode($msg);
        
        //re-direct
        return $this->app->getContainer()->get('response')->withHeader('Location', $redirect_path);
    }

    /**
     * 
     * Force 404 notFound from within action methods in your controller.
     * For example if a database record could not be retrieved, you can force a
     * notFound response.
     * 
     * @param ServerRequestInterface $req a request object
     * @param ResponseInterface $res a response object
     * @param string $_404_page_content a string containing the html to display as the 404 page.
     *                                  If this string contains a value other than the default value,
     *                                  render it as the 404 page
     * 
     * @return ResponseInterface a response object with the 404 status and 
     *                           appropriate body (eg the html showing the 404 message)
     */
    protected function notFound(ServerRequestInterface $req, ResponseInterface $res, $_404_page_content='Page Not Found') {
        
        $not_found_handler = $this->app->getContainer()->get('notFoundHandler');
        
        if( is_callable($not_found_handler) && $_404_page_content === 'Page Not Found') {
            
            return $not_found_handler($req, $res);    
        } 
        
        //404 handler could not be retrieved from the container
        //manually set the 404
        $new_res = $res->withStatus(404);
        $new_res->getBody()->write($_404_page_content);
        
        return $new_res;
    }
}
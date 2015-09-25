<?php
namespace CfsSlim3\Controllers;

/**
 * Description of BaseController
 *
 * @author Rotimi Adegbamigbe
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
     * @var \CfsSlim3\OtherClasses\View
     *  
     */
    protected $layout_renderer;
    
    /**
     * 
     * View object for rendering view files associated with controller actions. 
     *
     * @var \CfsSlim3\OtherClasses\View
     *  
     */
    protected $view_renderer;
        
    /**
     *
     * 
     * 
     * @var string 
     * 
     */
    public $action_name_from_uri;
    
    /**
     *
     * 
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
     * @param type $controller_name_from_uri
     * @param type $action_name_from_uri
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
     * 
     * 
     * @param type $file_name
     * @param array $data
     * 
     * @return type
     * 
     */
    public function getLayoutAsString( $file_name, array $data = [] ) {
        
        return $this->layout_renderer->getAsString($file_name, $data);
    }
    
    /**
     * 
     * 
     * 
     * @param type $file_name
     * @param array $data
     * 
     * @return type
     * 
     */
    public function getViewAsString( $file_name, array $data = [] ) {

        return $this->view_renderer->getAsString($file_name, $data);
    }
    
    /**
     * 
     * 
     * 
     */
    public function getBasePath() {
        
        return $this->app->getContainer()->get('request')->getUri()->getBasePath();
    }
    
    public function actionIndex() {
                
        //get the contents of the view first
        $view_str = $this->getViewAsString('index.php');
        $layout_data = ['content'=>$view_str, 'request_obj'=>$this->app->request];
        
        return $this->getLayoutAsString( 'main-template.php', $layout_data );
    }
    
    /**
     * 
     * 
     * 
     */
    public function actionLogin() {
        
        $success_redirect_path = '';
        $using_default_redirect = false;
        $request_obj = $this->app->getContainer()->get('request');
        
        if( 
            $request_obj->isGet() 
            && array_key_exists('login_redirect_path', $_GET)
        ) {
            //should sanitize the value from $_GET below 
            $success_redirect_path = $_GET['login_redirect_path'];
        }
        
        if( empty($success_redirect_path) ) {
            
            $using_default_redirect = true;
            $success_redirect_path = "{$this->controller_name_from_uri}/action-login-status";
        }
        
        $data_4_login_view = [
                            'controller_object'=>$this, 
                            'redirect_path'=>$success_redirect_path,
                            'error_message' => ''
                        ];
        
        if( $request_obj->isGet() ) {
            
            //show login form
            $view_str = $this->getViewAsString('login.php', $data_4_login_view);
            return $this->getLayoutAsString('main-template.php', ['content'=>$view_str, 'request_obj'=>$request_obj]);
            
        } else {
            
            //this is a POST request, process login
            $username = $_POST['username'];//should sanitize this value
            $password = $_POST['password'];//should sanitize this value
            
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
                $msg .= " or because the the connect operation itself failed in some way. ";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\BindFailed $e) {

                $msg = "Cound not bind to LDAP server.";
                $msg .= "This could be because the username or password was wrong,";
                $msg .= " or because the the bind operation itself failed in some way. ";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Exception $e) {

                $msg = "Invalid login details. Please try again. <br>";
            }
            
            $msg .= nl2br(\CfsSlim3\OtherClasses\dumpAuthinfo($auth));

            if( $loggedin_successfully ) {
                                
                //redirect to desired or default destination
                if($using_default_redirect) {
                    
                    $success_redirect_path .= '/1'; //will lead to
                                                    //$this->actionLoginStatus('1')
                    $success_redirect_path .= '?login_status='. rawurlencode($msg);
                }
                
                if( strpos($success_redirect_path, $this->getBasePath()) === false ) {
                    
                    $success_redirect_path = 
                        $this->getBasePath().'/'.ltrim($success_redirect_path, '/');
                }
                
                //re-direct
                return $this->app->getContainer()->get('response')->withHeader('Location', $success_redirect_path);
                
            } else {
                
                //re-display login form with error messages
                $data_4_login_view['error_message'] = $msg;
                $view_str = $this->getViewAsString('login.php', $data_4_login_view);
                return $this->getLayoutAsString('main-template.php', ['content'=>$view_str, 'request_obj'=>$request_obj]);
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

        $msg .= nl2br(\CfsSlim3\OtherClasses\dumpAuthinfo($auth));
        
        $redirect_path = $this->getBasePath()
                        . "/{$this->controller_name_from_uri}/action-login-status"
                        . '?login_status='. rawurlencode($msg);
        
        //re-direct
        return $this->app->getContainer()->get('response')->withHeader('Location', $redirect_path);
    }
    
    /**
     * 
     * 
     * 
     */
    public function actionLoginStatus($is_logged_in=false) {

        $msg = '';
        $request_obj = $this->app->getContainer()->get('request');
        
        if( $request_obj->isGet() ) {
        
            //the status message was passed via get; should sanitize this value
            $message_in_get = isset($_GET) && array_key_exists('login_status', $_GET);
            $msg = ($message_in_get) ? $_GET['login_status'] : $msg;
            
        } else {
            
            //the status message was passed via get; should sanitize this value
            $message_in_post = isset($_POST) && array_key_exists('login_status', $_POST);
            $msg = ($message_in_post) ? $_POST['login_status'] : $msg;
        }
        
        $view_str = $this->getViewAsString('login-status.php', ['message'=>$msg, 'is_logged_in'=>$is_logged_in, 'controller_object'=>$this]);
        return $this->getLayoutAsString('main-template.php', ['content'=>$view_str, 'request_obj'=>$request_obj]);
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

        $msg .= nl2br(\CfsSlim3\OtherClasses\dumpAuthinfo($auth));
        
        $redirect_path = 
            $this->getBasePath()
                 . "/{$this->controller_name_from_uri}/action-login-status"
                 . ( $auth->isValid() ? '/1' : '')
                 . '?login_status='. rawurlencode($msg);
        
        //re-direct
        return $this->app->getContainer()->get('response')->withHeader('Location', $redirect_path);
    }
}
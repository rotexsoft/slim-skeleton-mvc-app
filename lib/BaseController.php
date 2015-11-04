<?php
namespace Slim3Mvc;

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
     * @var \Slim3Mvc\View
     *  
     */
    protected $layout_renderer;
    
    /**
     * 
     * View object for rendering view files associated with controller actions. 
     *
     * @var \Slim3Mvc\View
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

    //////////////////////////////////
    // Session Parameter keys
    //////////////////////////////////
    const SESSN_PARAM_LOGIN_REDIRECT = 'login_redirect_path';
    
    //////////////////////////////////
    // Get Parameter keys
    //////////////////////////////////
    
    
    //////////////////////////////////
    // Post Parameter keys
    //////////////////////////////////
    
    
    //////////////////////////////////
    // Get and Post Parameter keys
    //////////////////////////////////
    
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
        
        $ds = DIRECTORY_SEPARATOR;
        
        $path_2_layout_files = __DIR__."{$ds}..{$ds}src{$ds}site-layout-templates";
        $this->layout_renderer = $this->app->getContainer()->get('new_layout_renderer');
        $this->layout_renderer->appendPath($path_2_layout_files);
        
        $path_2_view_files = __DIR__."{$ds}..{$ds}src{$ds}views{$ds}base";
        $this->view_renderer = $this->app->getContainer()->get('new_view_renderer');
        $this->view_renderer->appendPath($path_2_view_files);
        
        //try to resume the session
        $auth = $this->app->getContainer()->get('aura_auth_object');
        $resume_service = $this->app->getContainer()->get('aura_resume_service');
        $resume_service->resume($auth);
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
    public function renderLayout( $file_name, array $data = ['content'=>'Content should be placed here!'] ) {
        
        return $this->layout_renderer->renderAsString($file_name, $data);
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

        return $this->view_renderer->renderAsString($file_name, $data);
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
            session_status() === PHP_SESSION_ACTIVE && isset($_SESSION)
            && array_key_exists(static::SESSN_PARAM_LOGIN_REDIRECT, $_SESSION)
        ) {
            //there is an active session with a redirect url stored in it
            $success_redirect_path = $_SESSION[static::SESSN_PARAM_LOGIN_REDIRECT];
        }
        
        if( empty($success_redirect_path) ) {
            
            $using_default_redirect = true;
            $success_redirect_path = "{$this->controller_name_from_uri}/action-login-status";
        }
        
        $data_4_login_view = [
                            'controller_object'=>$this,
                            'error_message' => '',
                            'username' => '',
                            'password' => ''
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
                
                //The call to login above did not throw an Exception, this means
                //that the login was successful
                $msg = "You are now logged into a new session.";
                $loggedin_successfully = true;
                
                if( 
                    !$using_default_redirect 
                    && session_status() === PHP_SESSION_ACTIVE && isset($_SESSION)
                    && array_key_exists(static::SESSN_PARAM_LOGIN_REDIRECT, $_SESSION)
                ) { 
                    //redirect url must have been read from the session
                    //remove it from the session, since the login was 
                    //successful and we have already read the value.
                    unset($_SESSION[static::SESSN_PARAM_LOGIN_REDIRECT]);
                }

            } catch (\Aura\Auth\Exception\UsernameMissing $e) {

                $msg = "The 'username' field is missing or empty.";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\PasswordMissing $e) {

                $msg = "The 'password' field is missing or empty.";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\UsernameNotFound $e) {

                $msg = "The username you entered was not found.";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\MultipleMatches $e) {

                $msg = "There is more than one account with that username.";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\PasswordIncorrect $e) {

                $msg = "The password you entered was incorrect.";
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\ConnectionFailed $e) {

                $msg = "Cound not connect to IMAP or LDAP server.";
                $msg .= " This could be because the username or password was wrong,";
                $msg .= " or because the the connect operation itself failed in some way.";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Aura\Auth\Exception\BindFailed $e) {

                $msg = "Cound not bind to LDAP server.";
                $msg .= "This could be because the username or password was wrong,";
                $msg .= " or because the the bind operation itself failed in some way.";
                //$msg .= $e->getMessage();
                //throw new \Exception();

            } catch (\Exception $e) {

                $msg = "Invalid login details. Please try again.";
            }
            
            if( s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_DEV ) {
                
                $msg .= '<br>'.nl2br(\Slim3Mvc\dumpAuthinfo($auth));
            }

            if( $loggedin_successfully ) {
                
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
                $data_4_login_view['username'] = $username;
                $data_4_login_view['password'] = $password;
                $view_str = $this->renderView('login.php', $data_4_login_view);
                
                return $this->renderLayout('main-template.php', ['content'=>$view_str]);
            }
        }
    }
    
    /**
     * 
     * @param mixed $show_status_on_completion any value that evaluates to true or false.
     *                                         When the value is true, the user will be 
     *                                         redirected to actionLoginStatus(). When it
     *                                         is false, the user will be redirected to 
     *                                         actionLogin()
     * @return type
     */
    public function actionLogout($show_status_on_completion = false) {
        
        $auth = $this->app->getContainer()->get('aura_auth_object');
        $logout_service = $this->app->getContainer()->get('aura_logout_service');
        $logout_service->logout($auth);
                
        $actn = ($show_status_on_completion) ? 'action-login-status' : 'action-login';
        
        $redirect_path = 
            s3MVC_GetBaseUrlPath() . "/{$this->controller_name_from_uri}/{$actn}";
 
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
    public function actionLoginStatus() {

        $msg = '';
            
        //Just get the current login status
        $auth = $this->app->getContainer()->get('aura_auth_object');

        switch (true) {

            case $auth->isAnon():
                $msg = "You are not logged in.";
                break;
            
            case $auth->isIdle():
                $msg = "Your session was idle for too long. Please log in again.";
                break;
            
            case $auth->isExpired():
                $msg = "Your session has expired. Please log in again.";
                break;
            
            case $auth->isValid():
                $msg = "You are still logged in.";
                break;
            
            default:
                $msg =  "You have an unknown status.";
                break;
        }

        if( s3MVC_GetCurrentAppEnvironment() === S3MVC_APP_ENV_DEV ) {

            $msg .= '<br>'.nl2br(\Slim3Mvc\dumpAuthinfo($auth));
        }

        $view_str = $this->renderView('login-status.php', ['message'=>$msg, 'is_logged_in'=>$this->isLoggedIn(), 'controller_object'=>$this]);
        return $this->renderLayout('main-template.php', ['content'=>$view_str]);
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
        $not_found_response = $res->withStatus(404);
        $not_found_response->getBody()->write($_404_page_content);
        
        return $not_found_response;
    }
    
    public function isLoggedIn() {
        
        return ($this->app->getContainer()->get('aura_auth_object')->isValid() === true);
    }
    
    /**
     * 
     * Return a response object (an instance of \Psr\Http\Message\ResponseInterface)
     * if the user is not logged in (The url the user is currently accessing will be
     * stored in $_SESSION with the key `static::SESSN_PARAM_LOGIN_REDIRECT`. Upon
     * a successful login, the user will be redirected back to the current url in
     * $this->actionLogin().
     * 
     * False is returned if the user is logged in and there is no need to redirect to 
     * the login page. 
     * 
     * @return boolean|\Psr\Http\Message\ResponseInterface
     * 
     */
    protected function getResponseObjForLoginRedirection() {
        
        if( !$this->isLoggedIn() ) {
            
            $uri = $this->app->request->getUri();
            $base_path = s3MVC_GetBaseUrlPath();
            $path = $uri->getPath();
            $query = $uri->getQuery();
            $frag = $uri->getFragment();
            $path = $base_path . '/' . ltrim($path, '/');
            
            $current_url = 
                    $path . ($query ? '?'.$query :'') . ($frag ? '#'.$frag :'');
            
            $redirect_path = 
                s3MVC_GetBaseUrlPath()
                    . "/{$this->controller_name_from_uri}/action-login";
                    
            if( session_status() !== PHP_SESSION_ACTIVE ) {
                
                //start a new session
                session_start();
            }

            //store current url in session
            $_SESSION[static::SESSN_PARAM_LOGIN_REDIRECT] = $current_url;

            return $this->app->getContainer()
                             ->get('response')
                             ->withHeader('Location', $redirect_path);
        }
        
        return false;
    }
}
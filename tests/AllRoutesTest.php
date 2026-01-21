<?php
declare(strict_types=1);

namespace SlimSkeletonMvcApp\Tests;

use \GuzzleHttp\Client;

/**
 * Description of AllRoutesTest
 *
 * @author rotimi
 */
class AllRoutesTest extends \PHPUnit\Framework\TestCase {
    
    protected static string $process_id;
    protected static int $web_server_port = 8080;
    protected static string $web_server_host = 'localhost';
    protected static string $web_server_docroot = "./public";
    
    protected static function clearOldSessionFiles(): void {
        
        $session_path = \dirname(__DIR__).DIRECTORY_SEPARATOR.'tmp/session';
        
        foreach (\glob("{$session_path}/sess_*") as $filename) {
            
            \unlink($filename); //echo "$filename size " . filesize($filename) . "\n";
        }
    }
    
    protected static function startWebserver(): void {
        
        static::clearOldSessionFiles();
        
        // Command that starts the built-in web server
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            static::$web_server_host,
            static::$web_server_port,
            static::$web_server_docroot
        );

        // Execute the command and store the process ID
        $output = [];
        exec($command, $output);
        self::$process_id = ''. $output[0];
        

        echo sprintf(
            '%s - Web server started on %s:%d with PID %d',
            date('r'),
            static::$web_server_host,
            static::$web_server_port,
            self::$process_id
        ) . PHP_EOL . PHP_EOL;

        sleep(5); //wait for server to get going
    }
    
    protected static function stopWebserver(): void {
        
        echo PHP_EOL . PHP_EOL . sprintf('%s - Killing builtin PHP webserver process with ID %s', \date('r'), static::$process_id) . PHP_EOL;
        exec('kill ' . static::$process_id);
    }
    
    public static function setUpBeforeClass():void {
        
        static::$web_server_host = (getenv('WEBSERVER_HOST') !== false) ? getenv('WEBSERVER_HOST') : static::$web_server_host;
        static::$web_server_port = (getenv('WEBSERVER_PORT') !== false) ? ((int)getenv('WEBSERVER_PORT')) : static::$web_server_port;
        static::$web_server_docroot = (getenv('WEBSERVER_DOCROOT') !== false) ? getenv('WEBSERVER_DOCROOT') : static::$web_server_docroot;
        
        static::startWebserver();
    }

    public static function tearDownAfterClass():void {
        
        static::stopWebserver();
    }
    
    public function test404() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/non-existent-path");
        
        self::assertEquals(404, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<div><strong>Type:</strong> Slim\Exception\HttpNotFoundException</div><div><strong>", $response_body);
        self::assertStringContainsString("Class `NonExistentPath` does not exist.", $response_body);
        $this->assertErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    public function testHttpFourxxAndFivexx() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $http_codes = [400, 401, 403, 405, 410, 500, 501];
        
        foreach($http_codes as $http_code) {
        
            $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/hello/action-force-http-fourxx-or-fivexx/{$http_code}");

            self::assertEquals($http_code, $response->getStatusCode());

            // The html page returned
            $response_body = ((string)$response->getBody());

            self::assertStringContainsString("Forced HTTP {$http_code}", $response_body);
            $this->assertErrorLayoutIsPresentInResponseBody($response_body);
        } // foreach($http_codes as $http_code)
    }
    
    public function testBaseControllerActionIndex() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-index");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString('<h4><strong>Below are the default links that are available in your application:</strong></h4>', $response_body);
        self::assertStringContainsString('<h4><strong>A little bit about Controllers and MVC:</strong></h4>', $response_body);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    public function testBaseControllerActionLogin_WithGetRequests() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body);
        self::assertStringContainsString('<span>User Name: </span>', $response_body);
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $response_body);
        
        self::assertStringContainsString('<span>Password: </span>', $response_body);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $response_body);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
        
        ///////////////////////////////////////////////////////////////////////////
        // Toggle Language to French & check the login form returned is as expected
        ///////////////////////////////////////////////////////////////////////////
        
        $response2 = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login?selected_lang=fr_CA");
        
        self::assertEquals(200, $response2->getStatusCode());
        
        // The html page returned
        $response_body2 = ((string)$response2->getBody());
        
        self::assertStringNotContainsString("<h1>Welcome to Your New Site</h1>", $response_body2);
        self::assertStringContainsString("<h1>Bienvenue sur votre nouveau site</h1>", $response_body2);
        
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body2);
        
        self::assertStringNotContainsString('<span>User Name: </span>', $response_body2);
        self::assertStringContainsString("<span>Nom d'utilisateur: </span>", $response_body2);
        
        self::assertStringNotContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $response_body2);
        self::assertStringContainsString('<input type="text" name="username" placeholder="Nom&#x20;d&#x27;utilisateur" value="">', $response_body2);
        
        self::assertStringNotContainsString('<span>Password: </span>', $response_body2);
        self::assertStringContainsString('<span>Mot de passe: </span>', $response_body2);
        
        self::assertStringNotContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $response_body2);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Mot&#x20;de&#x20;passe" value="">', $response_body2);
        
        
        self::assertStringNotContainsString('<input type="submit" value="Login">', $response_body2);
        self::assertStringContainsString('<input type="submit" value="Se&#x20;connecter">', $response_body2);
        
        self::assertStringNotContainsString('Copyright no one at all. Go to town. </p>', $response_body2);
        self::assertStringContainsString('Copyright personne du tout. Aller en ville. </p>', $response_body2);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body2);
        
        
        // Toggle Language Back to English
        $response3 = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login?selected_lang=en_US");
        
        $response_body3 = ((string)$response3->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body3);
        self::assertStringNotContainsString("<h1>Bienvenue sur votre nouveau site</h1>", $response_body3);
        
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body3);
        
        self::assertStringContainsString('<span>User Name: </span>', $response_body3);
        self::assertStringNotContainsString("<span>Nom d'utilisateur: </span>", $response_body3);
        
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $response_body3);
        self::assertStringNotContainsString('<input type="text" name="username" placeholder="Nom&#x20;d&#x27;utilisateur" value="">', $response_body3);
        
        self::assertStringContainsString('<span>Password: </span>', $response_body3);
        self::assertStringNotContainsString('<span>Mot de passe: </span>', $response_body3);
        
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $response_body3);
        self::assertStringNotContainsString('<input type="password" name="password" autocomplete="off" placeholder="Mot&#x20;de&#x20;passe" value="">', $response_body3);
        
        
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body3);
        self::assertStringNotContainsString('<input type="submit" value="Se&#x20;connecter">', $response_body3);
        
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body3);
        self::assertStringNotContainsString('Copyright personne du tout. Aller en ville. </p>', $response_body3);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body3);
    }
    
    public function testBaseControllerActionLogin_WithPostRequests() {
        
        // We enable cookies here so login can work properly
        $client = new Client(['http_errors' => false, 'cookies' => true,]);
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        
        // Start by requesting a page that requires being logged in to view it
        // which will trigger a redirect to the login page.
        
        $not_auto_redirected_response = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/1/0",
            ['allow_redirects' => false]
        );
        
        self::assertEquals(302, $not_auto_redirected_response->getStatusCode());
        self::assertTrue($not_auto_redirected_response->hasHeader('Location'));
        self::assertEquals(
            "/base-controller/action-login", 
            $not_auto_redirected_response->getHeaderLine('Location')
        ); // make sure we are being redirected to the login page
        
        // The request below will be auto-redirected since we didn't specify
        // ['allow_redirects' => false]
        $response = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/1/0"
        );
        
        self::assertEquals(200, $response->getStatusCode());
        
        /////////////////////////////////////////////////////////////////////////
        // Request to /base-controller/action-routes/1/0 which requires the user
        // to be logged in will have caused a redirect to get the login form
        // verify that login form is what got returned in the response's body
        /////////////////////////////////////////////////////////////////////////
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body);
        self::assertStringNotContainsString('id="login-form-errors"', $response_body);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body);
        self::assertStringContainsString('<span>User Name: </span>', $response_body);
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $response_body);
        
        self::assertStringContainsString('<span>Password: </span>', $response_body);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $response_body);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body);
        
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
        
        ////////////////////////////////////////////////
        // Scenario 1. Blank user name & Blank Password
        ////////////////////////////////////////////////
        $response2 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => '',
                'password' => '',
            ]
        ]);
        
        // login form should be returned with error(s)
        $response_body2 = ((string)$response2->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body2);
        self::assertStringContainsString('id="login-form-errors"', $response_body2);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body2);
        self::assertStringContainsString('<span>User Name: </span>', $response_body2);
        self::assertStringContainsString('<span>Password: </span>', $response_body2);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body2);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body2);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body2);
        
        ////////////////////////////////////////////////
        // 2. Blank user name & non-Blank Password
        ////////////////////////////////////////////////
        $response3 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => '',
                'password' => 'ya',
            ]
        ]);
        
        // login form should be returned with error(s)
        $response_body3 = ((string)$response3->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body3);
        self::assertStringContainsString('id="login-form-errors"', $response_body3);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body3);
        self::assertStringContainsString('<span>User Name: </span>', $response_body3);
        self::assertStringContainsString('<span>Password: </span>', $response_body3);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body3);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body3);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body3);
        
        ////////////////////////////////////////////////
        // 3. non-Blank user name & Blank Password
        ////////////////////////////////////////////////
        $response4 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => 'ya',
                'password' => '',
            ]
        ]);
        
        // login form should be returned with error(s)
        $response_body4 = ((string)$response4->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body4);
        self::assertStringContainsString('id="login-form-errors"', $response_body4);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body4);
        self::assertStringContainsString('<span>User Name: </span>', $response_body4);
        self::assertStringContainsString('<span>Password: </span>', $response_body4);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body4);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body4);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body4);
        
        ////////////////////////////////////////////////
        // 4-a. wrong user pass combo
        ////////////////////////////////////////////////
        $response5 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => 'ya',
                'password' => 'yo',
            ]
        ]);
        
        // login form should be returned with error(s)
        $response_body5 = ((string)$response5->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $response_body5);
        self::assertStringContainsString('id="login-form-errors"', $response_body5);
        self::assertStringContainsString('ERROR_NO_ROWS', $response_body5);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $response_body5);
        self::assertStringContainsString('<span>User Name: </span>', $response_body5);
        self::assertStringContainsString('<span>Password: </span>', $response_body5);
        self::assertStringContainsString('<input type="submit" value="Login">', $response_body5);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $response_body5);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body5);
        
        ////////////////////////////////////////////////////////////////////////
        // 4-b. correct user pass combo
        // Login succeeded with correct username and password. 
        // Should redirect back to /base-controller/action-routes/1/0
        ////////////////////////////////////////////////////////////////////////
        $response6 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);
        
        // Should contain the /base-controller/action-routes/1/0 page
        $response_body6 = ((string)$response6->getBody());
        
        self::assertStringContainsString('<style>', $response_body6);
        self::assertStringContainsString('</style>', $response_body6);
        self::assertStringContainsString('#routes-table {', $response_body6);
        self::assertStringContainsString('font-family: Arial, Helvetica, sans-serif;', $response_body6);
        self::assertStringContainsString('border-collapse: collapse;', $response_body6);
        self::assertStringContainsString('width: 100%;', $response_body6);
        self::assertStringContainsString('}', $response_body6);
        self::assertStringContainsString('#routes-table td, #routes-table th {', $response_body6);
        self::assertStringContainsString('border: 1px solid #ddd;', $response_body6);
        self::assertStringContainsString('padding: 8px;', $response_body6);
        self::assertStringContainsString('#routes-table tr:nth-child(even){background-color: #f2f2f2;}', $response_body6);
        self::assertStringContainsString('#routes-table tr:hover {background-color: #ddd;}', $response_body6);
        self::assertStringContainsString('#routes-table th {', $response_body6);
        self::assertStringContainsString('padding-top: 12px;', $response_body6);
        self::assertStringContainsString('padding-bottom: 12px;', $response_body6);
        self::assertStringContainsString('text-align: left;', $response_body6);
        self::assertStringContainsString('background-color: #04AA6D;', $response_body6);
        self::assertStringContainsString('color: white;', $response_body6);
        self::assertStringContainsString('<h1 style="padding-bottom: 0.5em;">App Routes</h1>', $response_body6);
        self::assertStringContainsString('<table id="routes-table">', $response_body6);
        self::assertStringContainsString('</table>', $response_body6);
        self::assertStringContainsString('<thead>', $response_body6);
        self::assertStringContainsString('</thead>', $response_body6);
        self::assertStringContainsString('<tr>', $response_body6);
        self::assertStringContainsString('</tr>', $response_body6);
        self::assertStringContainsString('<th>Controller Class Name</th>', $response_body6);
        self::assertStringContainsString('<th>Action Method Name</th>', $response_body6);
        self::assertStringContainsString('<th>Route</th>', $response_body6);
        self::assertStringContainsString('<td>SlimMvcTools\Controllers\BaseController</td>', $response_body6);
        self::assertStringContainsString('<td>actionIndex</td>', $response_body6);
        self::assertStringContainsString('<td>base-controller/action-index</td>', $response_body6);
        self::assertStringContainsString('<td>actionLogin</td>', $response_body6);
        self::assertStringContainsString('<td>base-controller/action-login</td>', $response_body6);
        self::assertStringContainsString('<td>actionLoginStatus</td>', $response_body6);
        self::assertStringContainsString('<td>base-controller/action-login-status</td>', $response_body6);
        self::assertStringContainsString('<td>actionLogout</td>', $response_body6);
        self::assertStringContainsString('<td>base-controller/action-logout[/show_status_on_completion=false]</td>', $response_body6);
        self::assertStringContainsString('<td>actionRoutes</td>', $response_body6);
        self::assertStringContainsString('<td>base-controller/action-routes[/onlyPublicMethodsPrefixedWithAction=true][/stripActionPrefixFromMethodName=true]</td>', $response_body6);
        self::assertStringContainsString('<td>SlimSkeletonMvcApp\Controllers\Hello</td>', $response_body6);
        self::assertStringContainsString('<td>actionForceHttpFourxxOrFivexx</td>', $response_body6);
        self::assertStringContainsString('<td>hello/action-force-http-fourxx-or-fivexx[/http_code=400]</td>', $response_body6);
        self::assertStringContainsString('<td>hello/action-index</td>', $response_body6);
        self::assertStringContainsString('<td>actionThere</td>', $response_body6);
        self::assertStringContainsString('<td>hello/action-there/first_name/last_name</td>', $response_body6);
        self::assertStringContainsString('<td>actionWorld</td>', $response_body6);
        self::assertStringContainsString('<td>hello/action-world/name/another_param</td>', $response_body6);
        self::assertStringContainsString('<tbody>', $response_body6);
        self::assertStringContainsString('</tbody>', $response_body6);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body6);
        
        ////////////////////////////////////////////////////////////////////////
        // Now that we are logged in, browsing to the login page should lead to
        // a form with only a logout button being displayed
        ////////////////////////////////////////////////////////////////////////
        $response7 = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login");
        
        self::assertEquals(200, $response7->getStatusCode());
        
        // The html page returned
        $response_body7 = ((string)$response7->getBody());
        
        self::assertStringContainsString('<form action="/base-controller/action-logout/0" method="post">', $response_body7);
        self::assertStringContainsString('<input type="submit" value="Logout">', $response_body7);
        self::assertStringContainsString('</form>', $response_body7);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body7);
    }
    
    public function testBaseControllerActionLoginStatus() {
        
        // We enable cookies here so login can work properly
        $client = new Client(['http_errors' => false, 'cookies' => true,]);
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        
        // Stop the webserver & start it again to clear the existing session &
        // cookie data, etc. We want a fresh environment
        static::stopWebserver();
        static::startWebserver();
        
        // Since we haven't logged in yet, /base-controller/action-login-status
        // should only show auth info (which is anonymous) and a link to the
        // login page
        $response = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-login-status"
        );
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        // Test that the auth info is present in the response
        self::assertStringContainsString('You are not logged in.', $response_body);
        self::assertStringContainsString('Login Status: ', $response_body);
        self::assertStringContainsString("Logged in Person's Username: ", $response_body);
        self::assertStringContainsString("Logged in User's Data: ", $response_body);
        
        // Test that the non-logged-in section of the 
        // /base-controller/action-login-status is present in this response
        self::assertStringContainsString(
            '<p> <a href="/base-controller/action-login">Login</a> </p>', 
            $response_body
        );
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    public function testBaseControllerActionLogout() {
        
        // We enable cookies here so login can work properly
        $client = new Client(['http_errors' => false, 'cookies' => true,]);
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        
        // Stop the webserver & start it again to clear the existing session &
        // cookie data, etc. We want a fresh environment
        static::stopWebserver();
        static::startWebserver();
        
        // Since we haven't logged in yet, /base-controller/action-login-status
        // should only show auth info (which is anonymous) and a link to the
        // login page
        $response = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-login-status"
        );
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        // Test that the auth info is present in the response
        self::assertStringContainsString('You are not logged in.', $response_body);
        
        ///////////////////////////////////////////////////////////////////////
        // Now login, the successful login will redirect to
        // /base-controller/action-index
        ///////////////////////////////////////////////////////////////////////
        $response2 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);
        
        // Should contain the /base-controller/action-login-status page
        $response_body2 = ((string)$response2->getBody());

        // Test that index page was returned
        self::assertStringContainsString('<h4><strong>Below are the default links that are available in your application:</strong></h4>', $response_body2);
        self::assertStringContainsString('<h4><strong>A little bit about Controllers and MVC:</strong></h4>', $response_body2);
        
        ////////////////////////////////////////////////////////////////////////
        // Now go to /base-controller/action-logout/1 which should log the user
        // out and redirect to the /base-controller/action-login-status page
        ////////////////////////////////////////////////////////////////////////
        $response3 = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-logout/1"
        );
        
        self::assertEquals(200, $response3->getStatusCode());
        
        // The html page returned
        $response_body3 = ((string)$response3->getBody());
        
        // Test that returned status page indicates that user has been logged out
        self::assertStringContainsString('You are not logged in.', $response_body3);
    }
    
    public function testBaseControllerActionRoutes() {
        
        // We enable cookies here so login can work properly
        $client = new Client(['http_errors' => false, 'cookies' => true,]);
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        
        // Stop the webserver & start it again to clear the existing session &
        // cookie data, etc. We want a fresh environment
        static::stopWebserver();
        static::startWebserver();
        
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        // Test /base-controller/action-routes/0/0
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        
        // Since we haven't logged in yet, /base-controller/action-routes/0/0
        // should redirect to the login page
        $response = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/0/0"
        );
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        // Test that login form is present in response
        self::assertStringContainsString(
            '<form action="/base-controller/action-login" method="post">', 
            $response_body
        );
        
        ///////////////////////////////////////////////////////////////////////
        // Now login, the successful login will redirect back to
        // /base-controller/action-routes/0/0
        ///////////////////////////////////////////////////////////////////////
        $response2 = $client->request('POST', "http://{$web_server_host}:{$web_server_port}/base-controller/action-login", [
            'form_params' => [
                'username' => 'admin',
                'password' => 'admin',
            ]
        ]);
        
        // Should contain the /base-controller/action-routes/0/0 page
        $response_body2 = ((string)$response2->getBody());
        
        $expected_output1 = <<<HTML
        <div>
            <div>
                <style>
    #routes-table {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #routes-table td, #routes-table th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #routes-table tr:nth-child(even){background-color: #f2f2f2;}

    #routes-table tr:hover {background-color: #ddd;}

    #routes-table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04AA6D;
      color: white;
    }
</style>

            <h1 style="padding-bottom: 0.5em;">App Routes</h1>
            <table id="routes-table">
                <thead>
                    <tr>
                        <th>Controller Class Name</th>
                        <th>Action Method Name</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>__construct</td>
                            <td>base-controller/__construct/container/controller_name_from_uri/action_name_from_uri/request/response</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionIndex</td>
                            <td>base-controller/action-index</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogin</td>
                            <td>base-controller/action-login</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLoginStatus</td>
                            <td>base-controller/action-login-status</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogout</td>
                            <td>base-controller/action-logout[/show_status_on_completion=false]</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionRoutes</td>
                            <td>base-controller/action-routes[/onlyPublicMethodsPrefixedWithAction=true][/stripActionPrefixFromMethodName=true]</td>
                        </tr>
HTML;
        self::assertStringContainsString($expected_output1, $response_body2);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body2);
        
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        // Test /base-controller/action-routes/0/1 [We are already logged in]
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        
        $response3 = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/0/1"
        );
        
        self::assertEquals(200, $response3->getStatusCode());
        
        // Should contain the /base-controller/action-routes/0/1 page
        $response_body3 = ((string)$response3->getBody());

        $expected_output2 = <<<HTML
        <div>
            <div>
                <style>
    #routes-table {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #routes-table td, #routes-table th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #routes-table tr:nth-child(even){background-color: #f2f2f2;}

    #routes-table tr:hover {background-color: #ddd;}

    #routes-table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04AA6D;
      color: white;
    }
</style>

            <h1 style="padding-bottom: 0.5em;">App Routes</h1>
            <table id="routes-table">
                <thead>
                    <tr>
                        <th>Controller Class Name</th>
                        <th>Action Method Name</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>__construct</td>
                            <td>base-controller/__construct/container/controller_name_from_uri/action_name_from_uri/request/response</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionIndex</td>
                            <td>base-controller/index</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogin</td>
                            <td>base-controller/login</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLoginStatus</td>
                            <td>base-controller/login-status</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogout</td>
                            <td>base-controller/logout[/show_status_on_completion=false]</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionRoutes</td>
                            <td>base-controller/routes[/onlyPublicMethodsPrefixedWithAction=true][/stripActionPrefixFromMethodName=true]</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>forceHttp400</td>
                            <td>base-controller/force-http400/message[/request=NULL]</td>
                        </tr>
HTML;
        self::assertStringContainsString($expected_output2, $response_body3);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body3);
        
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        // Test /base-controller/action-routes/1/0 [We are already logged in]
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        
        $response4 = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/1/0"
        );
        
        self::assertEquals(200, $response4->getStatusCode());
        
        // Should contain the /base-controller/action-routes/1/0 page
        $response_body4 = ((string)$response4->getBody());

        $expected_output3 = <<<HTML
        <div>
            <div>
                <style>
    #routes-table {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #routes-table td, #routes-table th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #routes-table tr:nth-child(even){background-color: #f2f2f2;}

    #routes-table tr:hover {background-color: #ddd;}

    #routes-table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04AA6D;
      color: white;
    }
</style>

            <h1 style="padding-bottom: 0.5em;">App Routes</h1>
            <table id="routes-table">
                <thead>
                    <tr>
                        <th>Controller Class Name</th>
                        <th>Action Method Name</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionIndex</td>
                            <td>base-controller/action-index</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogin</td>
                            <td>base-controller/action-login</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLoginStatus</td>
                            <td>base-controller/action-login-status</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogout</td>
                            <td>base-controller/action-logout[/show_status_on_completion=false]</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionRoutes</td>
                            <td>base-controller/action-routes[/onlyPublicMethodsPrefixedWithAction=true][/stripActionPrefixFromMethodName=true]</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionForceHttpFourxxOrFivexx</td>
                            <td>hello/action-force-http-fourxx-or-fivexx[/http_code=400]</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionIndex</td>
                            <td>hello/action-index</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionThere</td>
                            <td>hello/action-there/first_name/last_name</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionWorld</td>
                            <td>hello/action-world/name/another_param</td>
                        </tr>
                                    </tbody>
            </table>
            </div>
        </div>
HTML;
        self::assertStringContainsString($expected_output3, $response_body4);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body4);
        
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        // Test /base-controller/action-routes/1/1 [We are already logged in]
        ////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////
        
        $response5 = $client->request(
            "GET", 
            "http://{$web_server_host}:{$web_server_port}/base-controller/action-routes/1/1"
        );
        
        self::assertEquals(200, $response5->getStatusCode());
        
        // Should contain the /base-controller/action-routes/1/1 page
        $response_body5 = ((string)$response5->getBody());

        $expected_output4 = <<<HTML
        <div>
            <div>
                <style>
    #routes-table {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #routes-table td, #routes-table th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #routes-table tr:nth-child(even){background-color: #f2f2f2;}

    #routes-table tr:hover {background-color: #ddd;}

    #routes-table th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04AA6D;
      color: white;
    }
</style>

            <h1 style="padding-bottom: 0.5em;">App Routes</h1>
            <table id="routes-table">
                <thead>
                    <tr>
                        <th>Controller Class Name</th>
                        <th>Action Method Name</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionIndex</td>
                            <td>base-controller/index</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogin</td>
                            <td>base-controller/login</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLoginStatus</td>
                            <td>base-controller/login-status</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionLogout</td>
                            <td>base-controller/logout[/show_status_on_completion=false]</td>
                        </tr>
                                            <tr>
                            <td>SlimMvcTools\Controllers\BaseController</td>
                            <td>actionRoutes</td>
                            <td>base-controller/routes[/onlyPublicMethodsPrefixedWithAction=true][/stripActionPrefixFromMethodName=true]</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionForceHttpFourxxOrFivexx</td>
                            <td>hello/force-http-fourxx-or-fivexx[/http_code=400]</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionIndex</td>
                            <td>hello/index</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionThere</td>
                            <td>hello/there/first_name/last_name</td>
                        </tr>
                                            <tr>
                            <td>SlimSkeletonMvcApp\Controllers\Hello</td>
                            <td>actionWorld</td>
                            <td>hello/world/name/another_param</td>
                        </tr>
                                    </tbody>
            </table>
            </div>
        </div>
HTML;
        self::assertStringContainsString($expected_output4, $response_body5);
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body5);
    }
    
    public function testHelloActionIndex() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/hello/action-index");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<div>", $response_body);
        self::assertStringContainsString(
            "Hello@actionIndex: Controller Action Method Content Goes Here!", 
            $response_body
        );
        self::assertStringContainsString("</div>", $response_body);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    public function testHelloActionThere() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/hello/action-there/john/doe");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<div>", $response_body);
        self::assertStringContainsString(
            "Hello There john, doe<br>", 
            $response_body
        );
        self::assertStringContainsString("</div>", $response_body);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    public function testHelloActionWorld() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/hello/action-world/john/doe");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $response_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<div>", $response_body);
        self::assertStringContainsString(
            "Hello Controller: Hello john!<br>", 
            $response_body
        );
        self::assertStringContainsString(
            "Other Parameter: `doe`", 
            $response_body
        );
        self::assertStringContainsString("</div>", $response_body);
        
        $this->assertNonErrorLayoutIsPresentInResponseBody($response_body);
    }
    
    protected function assertErrorLayoutIsPresentInResponseBody(string $response_body): void {
        
        self::assertStringContainsString('<!doctype html>', $response_body);
        self::assertStringContainsString('<html lang="en">', $response_body);
        self::assertStringContainsString('</html>', $response_body);
        self::assertStringContainsString('<head>', $response_body);
        self::assertStringContainsString('</head>', $response_body);
        self::assertStringContainsString('<meta charset="utf-8">', $response_body);
        self::assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1">', $response_body);
        self::assertStringContainsString('<title>', $response_body);
        self::assertStringContainsString('</title>', $response_body);
        self::assertStringContainsString('<style>', $response_body);
        self::assertStringContainsString('body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif}', $response_body);
        self::assertStringContainsString('h1{margin:0;font-size:48px;font-weight:normal;line-height:48px}', $response_body);
        self::assertStringContainsString('strong{display:inline-block;width:65px}', $response_body);
        self::assertStringContainsString('<body>', $response_body);
        self::assertStringContainsString('</body>', $response_body);
        self::assertStringContainsString('<a href="#" onclick="window.history.go(-1)">&lt;&lt; Go Back</a>', $response_body);
    }
    
    protected function assertNonErrorLayoutIsPresentInResponseBody(string $response_body) {
        
        self::assertStringContainsString('<!doctype html>', $response_body);
        self::assertStringContainsString('<html class="no-js" lang="en">', $response_body);
        self::assertStringContainsString('</html>', $response_body);
        self::assertStringContainsString('<head>', $response_body);
        self::assertStringContainsString('</head>', $response_body);
        self::assertStringContainsString('<meta charset="utf-8" />', $response_body);
        self::assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1.0" />', $response_body);
        self::assertStringContainsString('<title>Site Title Goes Here</title>', $response_body);
        self::assertStringContainsString('<link rel="stylesheet" href="/css/app.css" />', $response_body);
        self::assertStringContainsString('<body>', $response_body);
        self::assertStringContainsString('</body>', $response_body);
        self::assertStringContainsString('<div>', $response_body);
        
        self::assertStringContainsString('<a href="https://github.com/rotexsoft/slim-skeleton-mvc-app">', $response_body);
        self::assertStringContainsString('</a>', $response_body);
        
        self::assertStringContainsString('</div>', $response_body);
        self::assertStringContainsString('<ul style="padding-left: 0;">', $response_body);
        self::assertStringContainsString('</ul>', $response_body);
        self::assertStringContainsString('<li style="display: inline;">', $response_body);
        self::assertStringContainsString('</li>', $response_body);
        self::assertStringContainsString('<a href="/">', $response_body);
        self::assertStringContainsString('</a>&nbsp;', $response_body);
        self::assertStringContainsString('/action-routes">', $response_body);
        self::assertStringContainsString('?selected_lang=en_US">', $response_body);
        self::assertStringContainsString('?selected_lang=fr_CA">', $response_body);
        self::assertStringContainsString('/action-log', $response_body);
        self::assertStringContainsString('<h1>', $response_body);
        self::assertStringContainsString('</h1>', $response_body);
        self::assertStringContainsString('<p>', $response_body);
        self::assertStringContainsString('</p>', $response_body);
        self::assertStringContainsString('<a href="https://github.com/rotexsoft/slim-skeleton-mvc-app">', $response_body);
        self::assertStringContainsString('<br>', $response_body);
        self::assertStringContainsString('<footer>', $response_body);
        self::assertStringContainsString('</footer>', $response_body);
        self::assertStringContainsString('<hr/>', $response_body);
        self::assertStringContainsString('<p>&copy;', $response_body);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $response_body);
    }
}

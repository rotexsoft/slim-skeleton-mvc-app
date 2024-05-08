<?php
declare(strict_types=1);

namespace SlimSkeletonMvcApp\Tests;

use \GuzzleHttp\Client;

/**
 * Description of AllRoutesTest
 *
 * @author rotimi
 */
#[CoversClass(\SlimSkeletonMvcApp\Controllers\Hello::class)]
class AllRoutesTest extends \PHPUnit\Framework\TestCase {
    
    protected static string $process_id;
    protected static int $web_server_port = 8080;
    protected static string $web_server_host = 'localhost';
    protected static string $web_server_docroot = "./public";
    
    public static function setUpBeforeClass():void {
        
        static::$web_server_host = (getenv('WEBSERVER_HOST') !== false) ? getenv('WEBSERVER_HOST') : static::$web_server_host;
        static::$web_server_port = (getenv('WEBSERVER_PORT') !== false) ? ((int)getenv('WEBSERVER_PORT')) : static::$web_server_port;
        static::$web_server_docroot = (getenv('WEBSERVER_DOCROOT') !== false) ? getenv('WEBSERVER_DOCROOT') : static::$web_server_docroot;
        
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

    public static function tearDownAfterClass():void {
        
        echo PHP_EOL . PHP_EOL . sprintf('%s - Killing builtin PHP webserver process with ID %s', \date('r'), static::$process_id) . PHP_EOL;
        exec('kill ' . static::$process_id);
    }
    
    public function test404() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/non-existent-path");
        
        self::assertEquals(404, $response->getStatusCode());
        
        // The html page returned
        $reponse_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<div><strong>Type:</strong> Slim\Exception\HttpNotFoundException</div><div><strong>", $reponse_body);
        self::assertStringContainsString("Class `NonExistentPath` does not exist.", $reponse_body);
        self::assertStringContainsString('<a href="#" onclick="window.history.go(-1)">Go Back</a>', $reponse_body);
    }
    
    public function testHttp4xxAnd5xx() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $http_codes = [400, 401, 403, 405, 410, 500, 501];
        
        foreach($http_codes as $http_code) {
        
            $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/hello/action-force-http-4xx-or-5xx/{$http_code}");

            self::assertEquals($http_code, $response->getStatusCode());

            // The html page returned
            $reponse_body = ((string)$response->getBody());

            self::assertStringContainsString("Forced HTTP {$http_code}", $reponse_body);
            self::assertStringContainsString('<a href="#" onclick="window.history.go(-1)">Go Back</a>', $reponse_body);
            
        } // foreach($http_codes as $http_code)
    }
    
    public function testBaseControllerActionIndex() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-index");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $reponse_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body);
        self::assertStringContainsString("SlimPHP 4 Skeleton MVC App.", $reponse_body);
        self::assertStringContainsString('<h4><strong>Below are the default links that are available in your application:</strong></h4>', $reponse_body);
        self::assertStringContainsString('<h4><strong>A little bit about Controllers and MVC:</strong></h4>', $reponse_body);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body);
    }
    
    public function testBaseControllerActionLogin_WithGetRequests() {
        
        $client = new Client(['http_errors' => false]);
        
        $web_server_host = static::$web_server_host;
        $web_server_port = static::$web_server_port;
        $response = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login");
        
        self::assertEquals(200, $response->getStatusCode());
        
        // The html page returned
        $reponse_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body);
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $reponse_body);
        
        self::assertStringContainsString('<span>Password: </span>', $reponse_body);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $reponse_body);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body);
        
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body);
        
        ///////////////////////////////////////////////////////////////////////////
        // Toggle Language to French & check the login form returned is as expected
        ///////////////////////////////////////////////////////////////////////////
        
        $response2 = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login?selected_lang=fr_CA");
        
        self::assertEquals(200, $response2->getStatusCode());
        
        // The html page returned
        $reponse_body2 = ((string)$response2->getBody());
        
        self::assertStringNotContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body2);
        self::assertStringContainsString("<h1>Bienvenue sur votre nouveau site</h1>", $reponse_body2);
        
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body2);
        
        self::assertStringNotContainsString('<span>User Name: </span>', $reponse_body2);
        self::assertStringContainsString("<span>Nom d'utilisateur: </span>", $reponse_body2);
        
        self::assertStringNotContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $reponse_body2);
        self::assertStringContainsString('<input type="text" name="username" placeholder="Nom&#x20;d&#x27;utilisateur" value="">', $reponse_body2);
        
        self::assertStringNotContainsString('<span>Password: </span>', $reponse_body2);
        self::assertStringContainsString('<span>Mot de passe: </span>', $reponse_body2);
        
        self::assertStringNotContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $reponse_body2);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Mot&#x20;de&#x20;passe" value="">', $reponse_body2);
        
        
        self::assertStringNotContainsString('<input type="submit" value="Login">', $reponse_body2);
        self::assertStringContainsString('<input type="submit" value="Se&#x20;connecter">', $reponse_body2);
        
        self::assertStringNotContainsString('Copyright no one at all. Go to town. </p>', $reponse_body2);
        self::assertStringContainsString('Copyright personne du tout. Aller en ville. </p>', $reponse_body2);
        
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body2);
        
        
        // Toggle Language Back to English
        $response3 = $client->request("GET", "http://{$web_server_host}:{$web_server_port}/base-controller/action-login?selected_lang=en_US");
        
        $reponse_body3 = ((string)$response3->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body3);
        self::assertStringNotContainsString("<h1>Bienvenue sur votre nouveau site</h1>", $reponse_body3);
        
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body3);
        
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body3);
        self::assertStringNotContainsString("<span>Nom d'utilisateur: </span>", $reponse_body3);
        
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $reponse_body3);
        self::assertStringNotContainsString('<input type="text" name="username" placeholder="Nom&#x20;d&#x27;utilisateur" value="">', $reponse_body3);
        
        self::assertStringContainsString('<span>Password: </span>', $reponse_body3);
        self::assertStringNotContainsString('<span>Mot de passe: </span>', $reponse_body3);
        
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $reponse_body3);
        self::assertStringNotContainsString('<input type="password" name="password" autocomplete="off" placeholder="Mot&#x20;de&#x20;passe" value="">', $reponse_body3);
        
        
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body3);
        self::assertStringNotContainsString('<input type="submit" value="Se&#x20;connecter">', $reponse_body3);
        
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body3);
        self::assertStringNotContainsString('Copyright personne du tout. Aller en ville. </p>', $reponse_body3);
        
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body3);
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
        $reponse_body = ((string)$response->getBody());
        
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body);
        self::assertStringNotContainsString('id="login-form-errors"', $reponse_body);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body);
        self::assertStringContainsString('<input type="text" name="username" placeholder="User&#x20;Name" value="">', $reponse_body);
        
        self::assertStringContainsString('<span>Password: </span>', $reponse_body);
        self::assertStringContainsString('<input type="password" name="password" autocomplete="off" placeholder="Password" value="">', $reponse_body);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body);
        
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body);
        
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
        $reponse_body2 = ((string)$response2->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body2);
        self::assertStringContainsString('id="login-form-errors"', $reponse_body2);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body2);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body2);
        self::assertStringContainsString('<span>Password: </span>', $reponse_body2);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body2);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body2);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body2);
        
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
        $reponse_body3 = ((string)$response3->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body3);
        self::assertStringContainsString('id="login-form-errors"', $reponse_body3);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body3);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body3);
        self::assertStringContainsString('<span>Password: </span>', $reponse_body3);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body3);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body3);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body3);
        
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
        $reponse_body4 = ((string)$response4->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body4);
        self::assertStringContainsString('id="login-form-errors"', $reponse_body4);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body4);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body4);
        self::assertStringContainsString('<span>Password: </span>', $reponse_body4);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body4);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body4);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body4);
        
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
        $reponse_body5 = ((string)$response5->getBody());
        self::assertStringContainsString("<h1>Welcome to Your New Site</h1>", $reponse_body5);
        self::assertStringContainsString('id="login-form-errors"', $reponse_body5);
        self::assertStringContainsString('ERROR_NO_ROWS', $reponse_body5);
        self::assertStringContainsString('<form action="/base-controller/action-login" method="post">', $reponse_body5);
        self::assertStringContainsString('<span>User Name: </span>', $reponse_body5);
        self::assertStringContainsString('<span>Password: </span>', $reponse_body5);
        self::assertStringContainsString('<input type="submit" value="Login">', $reponse_body5);
        self::assertStringContainsString('Copyright no one at all. Go to town. </p>', $reponse_body5);
        self::assertStringContainsString('<script src="/js/app.js"></script>', $reponse_body5);
        
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
        $reponse_body6 = ((string)$response6->getBody());
        
var_dump($reponse_body6);
    }
}

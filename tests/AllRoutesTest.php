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
        static::$web_server_docroot = (getenv('WEBSERVER_DOCROOT') !== false) ? getenv('WEBSERVER_DOCROOT') : dirname(__DIR__).DIRECTORY_SEPARATOR."public";
        
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
    }
}

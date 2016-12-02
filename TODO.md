* In index.php and documentation put a section where the settings can be configured and then passed to the Slim App class' constructor. For example:

```php
<?php
    $config = [];
    $config['httpVersion'] = "1.1";
    $config['responseChunkSize'] = 4096;
    $config['outputBuffering'] = "append";
    $config['displayErrorDetails'] = true;
    $config['determineRouteBeforeAppMiddleware'] = false;
    $app = new \Slim\App(["settings" => $config]);
```

* Look into adding documentation about using $request->getQueryParams() to get at GET parameters as opposed to $_GET

* Look into adding documentation about using $request->getParsedBody() to get at POST parameters as opposed to $_POST
    
* in index.php look into adding a fallback exception handler via set_exception_handler to catch Exceptions that the errorHandler 
in the dependencies file can't catch. 
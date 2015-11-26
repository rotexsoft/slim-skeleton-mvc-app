<?php

// Define project specific routes using $app which is an instance of Slim

/*
For example:

$app->get('/foo/bar', function($request, $response, $args) {
    $controller = new \Project\Controllers\Index($this, $request, $response);
    return $controller->index();
});

*/

if( !$app->getContainer()->get('use_mvc_routes') ) {
    
    //define the default / route
    $app->get('/', function($request, $response, $args) {
        $controller = new Hello($this, 'hello', 'action-index');
        return $controller->actionIndex();
    });
}
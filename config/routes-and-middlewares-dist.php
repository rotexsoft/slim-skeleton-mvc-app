<?php
////////////////////////////////////////////////////////////////////////////////
// Define project specific midddle-wares using $app which is an instance of Slim
////////////////////////////////////////////////////////////////////////////////

/*
For example:

$app->add(function (\Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Message\ResponseInterface $res, $next) {

    $res->getBody()->write('in Middleware before current route handler<br>');
    
    $new_response = $next($req, $res); // this will eventually execute the route handler
                                       // for the matched route for the current request.
                                       // This is where the controller is instantiated 
                                       // and the appropriate controller method is 
                                       // invoked with / without parameters.
                                                
    $new_response->getBody()->write('in Middleware after current route handler<br>');
    
    return $new_response;
});

*/

//////////////////////////////////////////////////////////////////////////
// Define project specific routes using $app which is an instance of Slim
//////////////////////////////////////////////////////////////////////////

/*
For example:

$app->get('/foo/bar', function($request, $response, $args) {
    $controller = new \Project\Controllers\Index($this, $request, $response);
    return $controller->index();
});

*/

if( !S3MVC_APP_USE_MVC_ROUTES ) {
    
    //Not using mvc routes. So at least define the default / route. 
    //You can change it for your app if desired
    $app->get('/', function($request, $response, $args) {
        
        $prepend_action = !S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;
        $action = ($prepend_action) ? 'action-index' : 'index';
        $controller = new \Slim3SkeletonMvcApp\Controllers\Hello($this, 'hello', $action, $request, $response);
        
        $pre_action_response = $controller->preAction();
        $controller->setResponse( $pre_action_response );

        //invoke default action
        $action_result = $controller->actionIndex();

        if( is_string($action_result) ) {

            $response = $pre_action_response;
            $response->getBody()->write($action_result); //write the string in the response object as the response body

        } elseif ( $action_result instanceof \Psr\Http\Message\ResponseInterface ) {

            $response = $action_result; //the action returned a Response object
        }

        return $controller->postAction($response);
        
    });
}

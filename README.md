# CFS Slim PHP 3 Skeleton App

This is a template web-application (powered by SlimPHP 3), that can be extended to build more complex web applications.

## Installation
* Clone or download source files from https://bitbucket.org/cfsweb/cfs-slim-skeleton-app
* Make sure you have composer installed and then run
> `composer install`
* Copy /public/index-dist.php to /public/index.php
* Change the permission on the *logs* folder. Make it writable by the web-server process. 
* Browse to the public folder via your browser (eg. http://localhost/cfs-slim-skeleton-app/public/). You should see a default page.

## Configuration
* To use LDAP autehntication, you will need to update the values for the *$server*, *'basedn'*, *'bindpw'*, *'searchfilter'* and '*$dnformat*' in the *'aura_auth_adapter_object'* entry in the dependency injection container.
> You can optionally change the default *'successful_login_callback'* callback.


Action methods in Controller classes MUST either return a string (i.e. containing the output to display to the client)
or an instance of Psr\Http\Message\ResponseInterface (e.g. $response, that has the output to be displayed to the client, 
injected into it via $response->getBody()->write($data) );


## Documentation for Components Used
* SlimPHP 3 http://www.slimframework.com/docs/

* Logger https://github.com/katzgrau/KLogger

* Authentication https://bitbucket.org/cfsweb/cfs-authenticator

* Database ORM package http://rotexsoft.github.io/leanorm/

* See http://pimple.sensiolabs.org/ for more information on how the dependency injection container used by *SlimPHP 3* works.
 
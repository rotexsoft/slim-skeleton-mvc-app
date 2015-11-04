# SlimPHP 3 Skeleton MVC App

This is a template web-application (powered by SlimPHP 3), that can be extended to build more complex web applications.

It introduces the Model-View-Controller structure to a SlimPHP3 web-application.

## Installation
* Clone or download source files from https://github.com/rotexsoft/slim3-skeleton-mvc-app

* Make sure you have composer installed and then run

  > `composer install`

* Copy `/public/index-dist.php` to `/public/index.php`

* Copy `/public/ini-settings-dist.php` to `/public/ini-settings.php`

  > You should override php ini settings via call(s) to `ini_set(..., ...)` in `/config/ini-settings.php`

* You should add dependencies to the dependency injection container in `/config/dependencies.php`

* Change the permission on the **logs** folder. Make it writable by the web-server process. 

* To setup an environment for your web-app, simply copy `/public/env-dist.php` to `/public/env.php` and edit `/public/env.php` to return one of **S3MVC_APP_ENV_DEV**, **S3MVC_APP_ENV_PRODUCTION**, **S3MVC_APP_ENV_STAGING** or **S3MVC_APP_ENV_TESTING** relevant to the environment you are installing your web-app.

* Browse to the public folder via your browser (eg. `http://localhost/slim3-skeleton-mvc-app/public/`). You should see a default page. Note that `http://yourserver.com/slim3-skeleton-mvc-app/public/` should be changed to match your server setup.

## Configuration
* To use LDAP autehntication, you will need to update the values for the *$server*, *'basedn'*, *'bindpw'*, *'searchfilter'* and '*$dnformat*' in the *'aura_auth_adapter_object'* entry in the dependency injection container.
> You can optionally change the default *'successful_login_callback'* callback.


* Below are the urls to controller & controller - actions / methods that are viewable by default. Note that `http://yourserver.com/slim3-skeleton-mvc-app/public/` should be changed to match your server setup.
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-check-login-status`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-default-template-content`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-index`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-login`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-logout`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/base-controller/action-login-status/[1]`
    * [1] means parameter is optional
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-check-login-status`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-default-template-content`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-index`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-login`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-logout`
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/action-login-status/[1]`
    * [1] means parameter is optional
  * `http://yourserver.com/slim3-skeleton-mvc-app/public/hello/world/{name}/{another_parameter}`
    * you can do stuff like hello/world/john/doe

Action methods in Controller classes MUST either return a string (i.e. containing the output to display to the client)
or an instance of Psr\Http\Message\ResponseInterface (e.g. $response, that has the output to be displayed to the client, 
injected into it via $response->getBody()->write($data) );


## Documentation for Components Used
* SlimPHP 3 http://www.slimframework.com/docs/

* Logger https://github.com/katzgrau/KLogger (a PSR-3 compliant logger)

* Authentication https://bitbucket.org/cfsweb/cfs-authenticator

* See http://pimple.sensiolabs.org/ for more information on how the dependency injection container used by *SlimPHP 3* works.

## SlimPHP 3's Implementation of PSR-7

![Class Diagram of SlimPHP 3's Implementation of PSR-7](slim3-psr7.png)

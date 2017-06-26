# SlimPHP 3 Skeleton MVC App

This is a template web-application (powered by [SlimPHP 3](https://www.slimframework.com/)), that can be extended to build more complex web applications.

While it is not necessary to have expert understanding of the SlimPHP 3 framework (https://www.slimframework.com/docs/) in order
to build web-applications with this template application framework, such understanding would help in maximizing the capabilities
of this template application framework.

## Features / Benefits of using the Slim MVC Framework
* It adds the Model-View-Controller structure to your web-application. Actually, really more of Controller-View functionality with a **model** folder provided for housing your Model classes (leaving you with the responsibility of choosing whichever ORM / Database Access Library suits your needs or are most comfortable with)

* Provides a skeleton folder / directory structure for your application:
```
./path/to/newly/created/app
|-- config/
|   |-- app-settings.php
|   |-- dependencies.php
|   |-- env.php
|   |-- ini-settings.php
|   `-- routes-and-middlewares.php
|
|-- logs/
|
|-- public/
|   |-- css/
|   |-- images/
|   |-- js/
|   `-- index.php
|
|-- src/
|   |-- controllers/
|   |-- layout-templates/
|   |-- models/
|   `-- views/
|
|-- tests/
|
|-- tmp/
|
|-- vendor/
|
|-- .gitignore
|-- composer.json
|-- composer.lock
`-- README.md
```

* Provides some helper functions like **s3MVC_UriToString(\Psr\Http\Message\UriInterface $uri)**,
**s3MVC_addQueryStrParamToUri(\Psr\Http\Message\UriInterface $uri, $param_name, $param_value)**, etc. in the default global namespace and a few string helper functions in the **`Slim3MvcTools\Functions\Str`** namespace

* Provides an automatic routing scheme for mapping request urls to methods in Controller classes that are sub-classes of **`Slim3MvcTools\Controllers\BaseController`**. You don't need to define any routes for your application if you adhere to using Controllers that are compatible with the routing scheme
	* Also supports operating using only pure Slim PHP3 functionality (i.e. you can manually / explicitly define all or some of the routes (each of which may or may not make use of Controller classes as route handlers) and middle-wares for your application. You can also disable the automatic routing scheme described above if you plan to manually define all your routes. Manually / explicitly defined routes will override automatic routes with the same path definition)

* Ships whith a BaseController class (i.e. **`Slim3MvcTools\Controllers\BaseController`**) that provides methods for authentication, generating HTTP 404, 405 and 500 response objects and methods for rendering **php** view and layout files using the light-weight and easily extensible [Rotexsoft\FileRenderer\Renderer](https://github.com/rotexsoft/file-renderer) class

* Provides a command-line script for creating Controller classes (that extend **`Slim3MvcTools\Controllers\BaseController`** or any of its descendants). 
	* **`./vendor/bin/s3mvc-create-controller`** on **`*nix-like`** Oses and **`.\vendor\bin\s3mvc-create-controller.bat`** on **`Windows`**
        * NOTE: **`./vendor/bin/s3mvc-create-controller-wizard`** is the interactive version of **`./vendor/bin/s3mvc-create-controller`**

* Ships with a very minimal amount of composer / packagists dependencies (all of which are used by **`Slim3MvcTools\Controllers\BaseController`**) in order to allow you to include only additional dependencies that suit the specific needs of your application. Thereby reducing the possibility of having unused / unneeded dependencies in your application

* Optionally ships with the Zurb Foundation front-end framework (http://foundation.zurb.com/) and jQuery or no front-end framework

* Strives to adhere strictly to the **PSR-7 HTTP messages** and **container-interop/container-interop** interfaces, in order to make it easy to use different implementations of the PSR-7 request and response objects and **container-interop/container-interop** compliant containers

## Requirements

* PHP 5.5+
* Pdo sqlite (3) extension for Authentication in non-production environments
* Composer (https://getcomposer.org)

## Documentation

* [Quick Start Guide](documentation/QUICKSTART.md)
* [MVC Functionality](documentation/MVCFUNCTIONALITY.md)
* [Real World Usage: Creating a Movie Catalog application](documentation/MOVIE_CATALOG_APP_WALK_THROUGH.md)
* All command-line examples assume you have changed directory to the root folder of your newly created application.
* Please submit an issue or a pull request if you find any issues with the documentation.

## Issues

* Please submit an issue or a pull request if you find any problems with this skeleton app.
* If you are suggesting an enhancement please create an issue first so that it can be deliberated upon, before going on to submit a pull request.

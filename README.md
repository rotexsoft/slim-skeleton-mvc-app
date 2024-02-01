# SlimPHP 4 Skeleton MVC App

[![Run PHP Tests and Code Quality Tools](https://github.com/rotexsoft/slim-skeleton-mvc-app/actions/workflows/php.yml/badge.svg)](https://github.com/rotexsoft/slim-skeleton-mvc-app/actions/workflows/php.yml) &nbsp; 
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
![GitHub](https://img.shields.io/github/license/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
[![Coverage Status](https://coveralls.io/repos/github/rotexsoft/slim-skeleton-mvc-app/badge.svg?branch=master)](https://coveralls.io/github/rotexsoft/slim-skeleton-mvc-app?branch=master) &nbsp; 
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
![Packagist Downloads](https://img.shields.io/packagist/dt/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
![GitHub top language](https://img.shields.io/github/languages/top/rotexsoft/slim-skeleton-mvc-app) &nbsp;
![Packagist PHP Version Support (specify version)](https://img.shields.io/packagist/php-v/rotexsoft/slim-skeleton-mvc-app/dev-master) &nbsp; 
![GitHub commits since latest release (by date)](https://img.shields.io/github/commits-since/rotexsoft/slim-skeleton-mvc-app/latest) &nbsp; 
![GitHub last commit](https://img.shields.io/github/last-commit/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
![GitHub Release Date](https://img.shields.io/github/release-date/rotexsoft/slim-skeleton-mvc-app) &nbsp; 
<a href="https://libraries.io/github/rotexsoft/slim-skeleton-mvc-app">
    <img alt="Libraries.io dependency status for GitHub repo" src="https://img.shields.io/librariesio/github/rotexsoft/slim-skeleton-mvc-app">
</a>


> The documentation for the version of this framework based on SlimPHP 3 can be found [here](https://github.com/rotexsoft/slim-skeleton-mvc-app/tree/slim-3-edition).


This is a template web-application (powered by [SlimPHP 4](https://www.slimframework.com/)), that can be extended to build more complex web applications.

While it is not necessary to have expert understanding of the SlimPHP 4 framework (https://www.slimframework.com/docs/v4/) in order
to build web-applications with this template application framework, such understanding would help in maximizing the capabilities
of this template application framework.

## Features / Benefits of using the Slim MVC Framework
* It adds the Model-View-Controller structure to your web-application. Actually, really more of Controller-View functionality with a **model** folder provided for housing your Model classes (leaving you with the responsibility of choosing whichever ORM / Database Access Library suits your needs or are most comfortable with)

* Provides a skeleton folder / directory structure for your application:
```
./path/to/newly/created/app
|-- config/
    |-- languages/
|       |-- en_US.php
|       `-- fr_CA.php
|   |-- app-settings.php
|   |-- app-settings-dist.php
|   |-- dependencies.php
|   |-- env.php
|   |-- env-dist.php
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
|   |-- views/
|   `-- AppErrorHandler.php
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

* Provides some helper functions like **sMVC_UriToString(\Psr\Http\Message\UriInterface $uri)**,
**sMVC_addQueryStrParamToUri(\Psr\Http\Message\UriInterface $uri, $param_name, $param_value)**, etc. in the default global namespace and a few string helper functions in the **`SlimMvcTools\Functions\Str`** namespace

* Provides an automatic routing scheme for mapping request urls to methods in Controller classes that are sub-classes of **`SlimMvcTools\Controllers\BaseController`**. You don't need to define any routes for your application if you adhere to using Controllers that are compatible with the routing scheme
	* Also supports operating using only pure Slim PHP functionality (i.e. you can manually / explicitly define all or some of the routes (each of which may or may not make use of Controller classes as route handlers) and middle-wares for your application. You can also disable the automatic routing scheme described above if you plan to manually define all your routes. Manually / explicitly defined routes will override automatic routes with the same path definition)

* Ships whith a BaseController class (i.e. **`SlimMvcTools\Controllers\BaseController`**) that provides methods for authentication (login, logout & checking authentication status), listing all methods in all controller classes in an application that can be automatically routed to if the automatic MVC routing is enabled, forcing HTTP 400, 401, 403, 404, 405, 410, 500 & 501 responses and methods for rendering **php** view and layout files using the light-weight and easily extensible [Rotexsoft\FileRenderer\Renderer](https://github.com/rotexsoft/file-renderer) class. Take a look at the class for a full list of methods that can be used in your applications.

* Provides a command-line script for creating Controller classes (that extend **`SlimMvcTools\Controllers\BaseController`** or any of its descendants). 
	* **`./vendor/bin/smvc-create-controller`** on **`*nix-like`** Oses and **`.\vendor\bin\smvc-create-controller.bat`** on **`Windows`**
        * NOTE: **`./vendor/bin/smvc-create-controller-wizard`** is the interactive version of **`./vendor/bin/smvc-create-controller`**

* Ships with a very minimal amount of composer / packagist dependencies (all of which are used by **`SlimMvcTools\Controllers\BaseController`**) in order to allow you to include only additional dependencies that suit the specific needs of your application. Thereby reducing the possibility of having unused / unneeded dependencies in your application. The following packages are used in this framework:
    * [Slim Framework Version 4](https://github.com/slimphp/Slim/tree/4.x): for routing http requests to action methods in Controller classes (or other callables acceptable by Slim for responding to requests that match defined routes) and sending back http responses to clients
    * [File Renderer](https://github.com/rotexsoft/file-renderer): for rendering php layout and view files (which basically contain a mix of php, html, css & Javascript) which will be sent back as html in http response bodies to clients. Plain Text, Json, Xml & other non-html responses can be emitted directly from Controller Action methods (they don't need this renderer)
    * [Vespula Auth](https://packagist.org/packages/vespula/auth): a lightweight authentication package that is used by the default login & logout actions in the BaseController to authenticate users. An in-memory sqlite db is used for authentication by default, you should switch to a proper relational db like Mysql (which this package can also autheticate against)
    * [Vespula Locale](https://packagist.org/packages/vespula/locale): another light-weight package for managing localized strings in this framework. Things like error messages during login are managed using an instance of this package to display those messages in the desired language selected by the user. US English & Canadian French are the two languages this framework supports out of the box. Other locale files can be added to the **./config/languages** folder and their locale code(s) should be added to the container (**$container[ContainerKeys::VALID_LOCALES]**) and a link for that language should be added to the layout template with the appropriate query string so that users that click on it can set that language as their desired language when interacting with an application built with this framework
    * [Vespula Log](https://packagist.org/packages/vespula/log): a PSR-3 Logger that is used throughout this framework for logging various events (like  successful login & logout events, etc.). It's lighter than other packages like Monolog and pretty decent.
    * [Pimple](https://packagist.org/packages/pimple/pimple): a simple Dependency Injection Container that is used to power [\SlimMvcTools\Container](https://github.com/rotexsoft/slim-skeleton-mvc-tools/blob/master/src/Container.php) (the [PSR-11](https://packagist.org/packages/psr/container) compliant container that ships with this framework)
    * [Nyholm PSR7](https://packagist.org/packages/nyholm/psr7): A fast PHP7 implementation of PSR-7 injected into SlimPHP by this framework
    * [Nyholm PSR7 Server](https://packagist.org/packages/nyholm/psr7-server): Helper classes to handle PSR-7 server requests, also used by SlimPHP within this framework

* Strives to adhere strictly to the **PSR-7 HTTP messages** and **psr/container** interfaces, in order to make it easy to use different implementations of the PSR-7 request and response objects and **psr/container** compliant containers

## Requirements

* PHP 7.2+ (for version 3.X) or PHP 7.4+ (for version 4.X)
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

# Real World Usage: Creating a Movie Catalog application

In this tutorial, we will be creating a simple application for managing a list of movies. 
This app will be built from scratch using the 
[SlimPHP 3 Skeleton MVC App](https://github.com/rotexsoft/slim3-skeleton-mvc-app/) 
framework. This tutorial assumes you are working in a Linux environment (you may 
have to tweak some of the commands if you are using Windows).

The only additional composer packages that will be used in this tutorial are the 
[Leanorm](https://packagist.org/packages/rotexsoft/leanorm) package (A light-weight, 
PHP data access library) for implementing the model layer of the app and the 
[Slim Flash](https://packagist.org/packages/slim/flash) package for displaying
flash messages.
The sql provided in this walk through was written to work with MySQL, but can be 
easily modified to work with PostgreSQL and SQLite.

Features like Input Data Validation, Cross-site Request Forgery prevention, 
User Management, 
access control and the likes are left out of this tutorial since there are a variety 
of ways and packages that can be used to implement these features.

First, we create the new app by running the command below:

```
composer create-project -n -s dev rotexsoft/slim3-skeleton-mvc-app movie-catalog
```

I entered **`Y`** when the prompt below appeared during the creation of the project
signifying that I want to use the Zurb Foundation CSS/JS framework that comes with
the skeleton app.

```
Do you want to use the Zurb Foundation front-end framework (which includes jQuery) 
that ships with SlimPHP 3 Skeleton MVC package? (Y/N)
```

At this point, the new app will be in a folder / directory named 
`movie-catalog`. You should change into the folder by running the command below:

```
cd movie-catalog
```

Now, let's test our app by running the command below and navigating to 
http://localhost:8888 in your browser (you should see a welcome page):
```
php -S 0.0.0.0:8888 -t public
```

Once we've verified that the welcome page is being displayed, we are sure that 
our app was successfully installed. Now we can stop the php development server
we started in the last command above by pressing the **`ctrl`** and **`c`** keys
together.

Next, we need to create the database and database tables for our app by 
running the sql commands below (in whatever client program we use to issue commands
to our MySQL server):

```sql
CREATE DATABASE `movie_catalog`; 
USE `movie_catalog`; 

CREATE TABLE `movie_listings`( 
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, 
    `title` TEXT NOT NULL, 
    `release_year` VARCHAR(4) NOT NULL, 
    `genre` VARCHAR(255), 
    `duration_in_minutes` INT, 
    `mpaa_rating` VARCHAR(255), 
    `record_creration_date` DATETIME NOT NULL, 
    `record_last_modification_date` DATETIME NOT NULL, 
    PRIMARY KEY (`id`) 
); 

CREATE TABLE user_authentication_accounts (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, 
    `username` VARCHAR(255), 
    `password` VARCHAR(255),
    `record_creration_date` DATETIME NOT NULL, 
    `record_last_modification_date` DATETIME NOT NULL, 
    PRIMARY KEY (`id`) 
);
```

We just created the database for our app with two tables: **`movie_listings`** 
(for storing data for each of the movies we want in our catalog) and 
**`user_authentication_accounts`** (for storing usernames and password hashes of
users that can login to our app).

Next we are going to create some controller classes for our application by running 
the commands below:

```
./vendor/bin/s3mvc-create-controller -c movie-catalog-base -p "./src" -n "MovieCatalog\Controllers" -e "\Slim3MvcTools\Controllers\BaseController"
./vendor/bin/s3mvc-create-controller -c users -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
./vendor/bin/s3mvc-create-controller -c movie-listings -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
./vendor/bin/s3mvc-create-controller -c http-not-allowed-not-found-server-error-handler -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
composer dumpautoload -o
``` 

We should now have the following files and folders in our application:

* **./src/controllers/MovieCatalogBase.php:** containing the
**`\MovieCatalog\Controllers\MovieCatalogBase`** class which is a direct sub-class
of **`\Slim3MvcTools\Controllers\BaseController`**. This class will serve as the 
base controller class in our application which all other controllers will extend.
All logic common to all other controllers in our application should be implemented here.

    * **./src/views/movie-catalog-base/:** is the folder where view files for 
    **\MovieCatalog\Controllers\MovieCatalogBase** should be placed. A default
    **index.php** will be in this folder and is used in the default **actionIndex()**
    in **\MovieCatalog\Controllers\MovieCatalogBase**. Classes that extend 
    **\MovieCatalog\Controllers\MovieCatalogBase** will also be able to 
    access views in this folder when the **renderView()** method is called
    inside such controller classes.


* **./src/controllers/MovieListings.php:** containing the
**`\MovieCatalog\Controllers\MovieListings`** class which is a sub-class of
**`\MovieCatalog\Controllers\MovieCatalogBase`**. This controller class will 
contain action methods to list all, view each, add, edit and delete movie 
listings in our application.

    * **./src/views/movie-listings/:** is the folder where view files for 
    **\MovieCatalog\Controllers\MovieListings** should be placed. A default
    **index.php** will be in this folder and is used in the default 
    **actionIndex()** in **\MovieCatalog\Controllers\MovieListings**. 
    When the **renderView('index.php')** method is called from within 
    **\MovieCatalog\Controllers\MovieListings**, 
    **./src/views/movie-listings/index.php** would be used 
    instead of **./src/views/movie-catalog-base/index.php** because it
    is in the controller's own view folder. If there were no **index.php**
    in **./src/views/movie-listings/**, **./src/views/movie-catalog-base/index.php**
    would be used when the **renderView('index.php')** method is called from within
    **\MovieCatalog\Controllers\MovieListings**.


* **./src/controllers/HttpNotAllowedNotFoundServerErrorHandler.php:** containing the
**`\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler`** class which is a sub-class of
**`\MovieCatalog\Controllers\MovieCatalogBase`**. This controller class will 
be used for handling HTTP `404`, `405` and `500` errors in our application. 

    * **./src/views/http-not-allowed-not-found-server-error-handler/:** is the 
    folder where view files for **\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler** 
    should be placed. A default **index.php** will be in this folder and is used 
    in the default **actionIndex()** in **\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler**. 
    The default **actionIndex()** in 
    **\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler** is
    really not needed, since this controller is only meant to handle the 
    earlier mentioned HTTP errors.

    * Edit the **./config/dependecies.php** by assigning a value of 
    **`'\\MovieCatalog\\Controllers\\HttpNotAllowedNotFoundServerErrorHandler'`** 
    to **$container['errorHandlerClass']**, **$container['notFoundHandlerClass']** and
    **$container['notAllowedHandlerClass']** to delegate 
    `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler` as the 
    handler for HTTP 404, 405 and 500 errors in our application. The default 
    handlers for HTTP 404, 405 and 500 errors are the 

        * `\Slim3MvcTools\Controllers\HttpServerErrorController`, 

        * `\Slim3MvcTools\Controllers\HttpNotFoundController` and

        * `\Slim3MvcTools\Controllers\HttpMethodNotAllowedController`

    classes which are direct sub-classes of `\Slim3MvcTools\Controllers\BaseController`. 
    These default handlers will not be able to take advantage of the `preAction()` and 
    `postAction(..)` implementations in `\MovieCatalog\Controllers\MovieCatalogBase`,
    that's why we are making `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler`
    our HTTP 404, 405 and 500 error handler.
        * Also note that the methods below can be overriden in 
        `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler` in
        order to change the how 404, 405 and 500 errors are actually handled:
            - `\Slim3MvcTools\Controllers\BaseController::generateNotAllowedResponse(array $methods, ServerRequestInterface $req=null, ResponseInterface $res=null)`

            - `\Slim3MvcTools\Controllers\BaseController::generateNotFoundResponse(ServerRequestInterface $req=null, ResponseInterface $res=null, $_404_page_content=null, $_404_additional_log_message=null)` 

            - `\Slim3MvcTools\Controllers\BaseController::generateServerErrorResponse(\Exception $exception, ServerRequestInterface $req=null, ResponseInterface $res=null)`
        
        the `\Slim3MvcTools\Controllers\BaseController` implementations of these
        methods would be used by `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler`
        if they are not overriden inside `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler`.


* **./src/controllers/Users.php:** containing the
**`\MovieCatalog\Controllers\Users`** class which is a sub-class of
**`\MovieCatalog\Controllers\MovieCatalogBase`**. This controller class will 
contain action methods to manage users (i.e. list all, view each, add, edit 
and delete) that can login to our application.

    * **./src/views/users/:** is the folder where view files for 
    **\MovieCatalog\Controllers\Users** should be placed. A default
    **index.php** will be in this folder and is used in the default 
    **actionIndex()** in **\MovieCatalog\Controllers\Users**. 
    When the **renderView('index.php')** method is called from within 
    **\MovieCatalog\Controllers\Users**, 
    **./src/views/users/index.php** would be used 
    instead of **./src/views/movie-catalog-base/index.php** because it
    is in the controller's own view folder. If there were no **index.php**
    in **./src/views/users/**, **./src/views/movie-catalog-base/index.php**
    would be used when the **renderView('index.php')** method is called from 
    within **\MovieCatalog\Controllers\Users**.

Now we edit the dependencies file (i.e. **`./config/dependencies.php`**) by 
assigning a value of **`'\\MovieCatalog\\Controllers\\HttpNotAllowedNotFoundServerErrorHandler'`** 
to **$container['errorHandlerClass']**, **$container['notFoundHandlerClass']** and
**$container['notAllowedHandlerClass']** in order to make the 
`\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler` class the 
handler for HTTP 404, 405 and 500 errors in our application. 

Next, we add the value (**`'\\MovieCatalog\\Controllers\\'`**) to the 
**$container['namespaces_for_controllers']** array in the dependencies file.
This would be used by the MVC routing mechanism when trying to create an instance
of the controller class (whose name was extracted from the request url).

We then go on to edit **`./public/index.php`** by assigning the value of **`true`** 
to `S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES` and the value of 
**`'\\MovieCatalog\\Controllers\\MovieListings'`** to 
`S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME`. 

The first edit eliminates the need to prepend `action-` to action method names 
in the request url and the second edit makes 
**`\MovieCatalog\Controllers\MovieListings'`** the default controller for our app. 

Since `S3MVC_APP_DEFAULT_ACTION_NAME` has a default value of **`'actionIndex'`**, 
this means that the **`'actionIndex'`** method in 
**`\MovieCatalog\Controllers\MovieListings'`** would be used to handle the 
**`/`** route in our application.

Now let's run our php development server again:

```
php -S 0.0.0.0:8888 -t public
```

Browse to http://localhost:8888/movie-catalog-base or http://localhost:8888/movie-catalog-base/index 
and you should see the output below:
```
You have successfully executed MovieCatalog\Controllers\MovieCatalogBase::actionIndex()
This is the default view for MovieCatalog\Controllers\MovieCatalogBase::actionIndex().
```

Next, browse to http://localhost:8888/movie-listings or http://localhost:8888/movie-listings/index 
and you should see the output below:
```
You have successfully executed MovieCatalog\Controllers\MovieListings::actionIndex()
This is the default view for MovieCatalog\Controllers\MovieListings::actionIndex().
```

Next, browse to http://localhost:8888/users or http://localhost:8888/users/index 
and you should see the output below:
```
You have successfully executed MovieCatalog\Controllers\Users::actionIndex()
This is the default view for MovieCatalog\Controllers\Users::actionIndex().
```

Then, browse to http://localhost:8888/http-not-allowed-not-found-server-error-handler 
or http://localhost:8888/http-not-allowed-not-found-server-error-handler/index 
and you should see the output below:
```
You have successfully executed MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler::actionIndex()
This is the default view for MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler::actionIndex(). 
```

If browsing to all the links above resulted in the corresponding output, then all
our controllers have been correctly setup and we are now ready to start implementing
methods in our controllers. You can stop the php development server for now.

Now, we need to install the two composer packages (ie. 
[Leanorm](https://packagist.org/packages/rotexsoft/leanorm) and 
[Slim Flash](https://packagist.org/packages/slim/flash)) we are going to use
for creating our model classes and manage flash messaging in our app. Run the 
commands below to install these packages:
```
composer require slim/flash
composer require rotexsoft/leanorm
```

We then go on to register a `Slim Flash` object in the container in our dependencies
file by adding the code below to **`./config/dependencies.php`**:
```php
$container['slim_flash'] = function () {

    if ( session_status() !== PHP_SESSION_ACTIVE ) { 
        
        // Start PHP session if not already started
        session_start();
    }
    
    return new \Slim\Flash\Messages();
};
```

Next, add the classes below to the **`./src/models/`** folder:

**BaseCollection.php**

```php
<?php

class BaseCollection extends \LeanOrm\Model\Collection {
    //put your code here
}
```

**BaseModel.php**

```php
<?php

class BaseModel extends \LeanOrm\Model {

    public function __construct(
        $dsn='', $uname='', $paswd='', array $pdo_driver_opts=[], array $ext_opts=[]
    ) {
        parent::__construct($dsn, $uname, $paswd, $pdo_driver_opts, $ext_opts);
        
        $col_names = $this->getTableColNames();
        
        if( in_array('record_creration_date', $col_names) ) {
        
            $this->_created_timestamp_column_name = 'record_creration_date';
        }
        
        if( in_array('record_last_modification_date', $col_names) ) {
        
            $this->_updated_timestamp_column_name = 'record_last_modification_date';
        }
    }
}
```
**BaseRecord.php**

```php
<?php

class BaseRecord extends \LeanOrm\Model\Record {

    public function getDateCreated() {
        
        $col = $this->getModel()->getCreatedTimestampColumnName();
        
        if( in_array($col, $this->getModel()->getTableColNames()) ) {
            
            return date('M j, Y g:i a T', strtotime($this->$col));
        }
        
        return '';
    }
    
    public function getLastModfiedDate() {
        
        $col = $this->getModel()->getUpdatedTimestampColumnName();
        
        if( in_array($col, $this->getModel()->getTableColNames()) ) {
            
            return date('M j, Y g:i a T', strtotime($this->$col));
        }
        
        return '';
    }
}
```

We then go on to register a Model objects (for the `movie_listings` and 
`user_authentication_accounts` database tables) in the container in our 
dependencies file by adding the code below to **`./config/dependencies.php`**
(**NOTE:** you should update `$container['db_dsn']`, `$container['db_uname']`, 
`$container['db_passwd']` to suit your database server):

```php
$container['db_dsn'] = 'mysql:host=server-name;dbname=movie_catalog';
$container['db_uname'] = 'user_name';
$container['db_passwd'] = 'pass';

$container['movie_listings_model'] = function ($c) {

    $model = new \BaseModel (
        $c['db_dsn'], $c['db_uname'], $c['db_passwd'],
        [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'],
        [
            'primary_col' => 'id',
            'table_name' => 'movie_listings',

            //If not set, \LeanOrm\Model\Collection will be used by default
            'collection_class_name' => 'BaseCollection',

            //If not set, \LeanOrm\Model\Record will be used by default 
            'record_class_name' => 'BaseRecord',
        ]
    );

    return $model;
};

$container['users_model'] = function ($c) {

    $model = new \BaseModel (
        $c['db_dsn'], $c['db_uname'], $c['db_passwd'],
        [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'],
        [
            'primary_col' => 'id',
            'table_name' => 'user_authentication_accounts',

            //If not set, \LeanOrm\Model\Collection will be used by default
            'collection_class_name' => 'BaseCollection',

            //If not set, \LeanOrm\Model\Record will be used by default 
            'record_class_name' => 'BaseRecord',
        ]
    );

    return $model;
};
```

Now, we run the command below to allow composer's autoloader to be able to find
the `BaseCollection`, `BaseModel` and `BaseRecord` classes we just added to our
application:

```
composer dumpautoload -o
```
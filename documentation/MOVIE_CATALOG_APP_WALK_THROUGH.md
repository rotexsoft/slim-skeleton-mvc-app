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

Next we are going to create some controller classes for our app by running 
the commands below:

```
./vendor/bin/s3mvc-create-controller -c movie-catalog-base -p "./src" -n "MovieCatalog\Controllers" -e "\Slim3MvcTools\Controllers\BaseController"
./vendor/bin/s3mvc-create-controller -c users -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
./vendor/bin/s3mvc-create-controller -c movie-listings -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
./vendor/bin/s3mvc-create-controller -c http-not-allowed-not-found-server-error-handler -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
composer dumpautoload -o
``` 

We should now have the following files and folders in our app:

* **./src/controllers/MovieCatalogBase.php:** containing the
**`\MovieCatalog\Controllers\MovieCatalogBase`** class which is a direct sub-class
of **`\Slim3MvcTools\Controllers\BaseController`**. This class will serve as the 
base controller class in our app which all other controllers will extend.
All logic common to all other controllers in our app should be implemented here.

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
listings in our app.

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
be used for handling HTTP `404`, `405` and `500` errors in our app. 

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
    handler for HTTP 404, 405 and 500 errors in our app. The default 
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
and delete) that can login to our app.

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
handler for HTTP 404, 405 and 500 errors in our app. 

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
**`/`** route in our app.

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
            
            // this column will be automatically updated 
            // when a new record is saved to the database
            $this->_created_timestamp_column_name = 'record_creration_date';
        }
        
        if( in_array('record_last_modification_date', $col_names) ) {
        
            // this column will be automatically updated 
            // when a record (new or existent) is saved to the database
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
app:

```
composer dumpautoload -o
```


Next, we configure the `Vespula.Auth PDO Authentication setup` section of our
dependencies file (**`./config/dependencies.php`**) to authenticate against the 
`user_authentication_accounts` table in our MySQL `movie_catalog` database by updating 
`$container['vespula_auth']` in the `Vespula.Auth PDO Authentication setup` section 
with the code below:

```php
    ////////////////////////////////////////////////////////////////////////////
    // Start Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
    $container['vespula_auth'] = function ($c) {

        $pdo = new \PDO(
            $c['db_dsn'], 
            $c['db_uname'], 
            $c['db_passwd'], 
            [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]
        );
        
        //Optionally pass a maximum idle time and a time until the session 
        //expires (in seconds)
        $expire = 3600;
        $max_idle = 1200;
        $session = new \Vespula\Auth\Session\Session($max_idle, $expire);
        
        $cols = ['username', 'password'];
        $from = 'user_authentication_accounts';
        $where = ''; //optional

        $adapter = new \Vespula\Auth\Adapter\Sql($pdo, $from, $cols, $where);
        
        return new \Vespula\Auth\Auth($adapter, $session);
    };
    ////////////////////////////////////////////////////////////////////////////
    // End Vespula.Auth PDO Authentication setup
    ////////////////////////////////////////////////////////////////////////////
```

Next we create an action method named `actionInitUsers` in the 
`\MovieCatalog\Controllers\Users` class to create a user with the username 
`admin` and password `admin` (if and only if the `user_authentication_accounts` 
table contains no data). It will be accessible via http://localhost:8888/users/init-users.
Below is the method:

```php
    public function actionInitUsers() {
        
        $view_str = ''; // will hold output to be injected into 
                        // the site layout file (i.e. 
                        // `./src/layout-templates/main-template.php`)
                        // when $this->renderLayout(...) is called
        
        $model_obj = $this->container->get('users_model');
        $num_existing_users = $model_obj->fetchValue(['cols'=>['count(*)']]);
        
        if( !is_numeric($num_existing_users) ) {
            
            // no need to add entries for the `record_creration_date`
            // and `record_last_modification_date` fields in the 
            // $user_data array below
            $user_data = [
                'username' => 'admin', 
                'password' => password_hash('admin' , PASSWORD_DEFAULT)
            ];
            $user_record = $model_obj->createNewRecord($user_data);
            
            if( $user_record->save() !== false ) {
                
                $view_str = 'First user successfully initialized!';
                
            } else {
                
                $view_str = 'Error: could not create first user!';
            }
            
        } else if( 
            is_numeric($num_existing_users) 
            && ((int)$num_existing_users) > 0 
        ) {
            $view_str = 'First user already initialized!';
            
        } else {
            
            $view_str = 'Error: could not initialize first user!';
        }
        
        // The 'content' key in the array below will be available in
        // `./src/layout-templates/main-template.php` as $content
        // 
        // Note: $this->layout_template_file_name has a value of
        //       'main-template.php'
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

**actionInitUsers()** above checks if there is no data in the `user_authentication_accounts` 
table and if there is none, it then proceeds to insert a row of data into the table with a 
`username` value of **`admin`** and `password` value of **`admin`** (note that it's the 
hashed form of the password that is stored in the `user_authentication_accounts` table).
If there is data in the `user_authentication_accounts` table, the method just sets a
message to be displayed.

All we now need to do to ensure we have a user with the username **`admin`** in 
our app is to browse to http://localhost:8888/users/init-users. After this,
we can login to our app with a `username` of **`admin`** and a `password` of
**`admin`**. We can login via any controller with the path **`<controller_name>/login`** 
in our url, where **`<controller_name>`** can be substitued with the controller 
names of any of the controllers we have created in our app. 

We can even create a manual route **/init-users** in **`./config/routes-and-middlewares.php`** 
that redirects to http://localhost:8888/users/init-users. So we can use a shorter
url http://localhost:8888/init-users to accomplish the same goal of creating the
**`admin`** user. 

Just add the code below to **`./config/routes-and-middlewares.php`**
and http://localhost:8888/init-users will become active:

```php
$app->get(
        
    '/init-users',
        
    function(
        \Psr\Http\Message\ServerRequestInterface $request, 
        \Psr\Http\Message\ResponseInterface $response, 
        $args
    ) {
        return $response->withStatus(301)
                        ->withHeader('Location', '/users/init-users');
    }
);
```

Below are the login urls currently available in our app (they are all calling 
**`\Slim3MvcTools\Controllers\BaseController::actionLogin()`**):

- **http://localhost:8888/base-controller/login :** logging in via `\Slim3MvcTools\Controllers\BaseController` 
- **http://localhost:8888/hello/login :** logging in via `\Slim3SkeletonMvcApp\Controllers\Hello` (a sample controller that ships with each new [SlimPHP 3 Skeleton MVC App](https://github.com/rotexsoft/slim3-skeleton-mvc-app/) app) 
- **http://localhost:8888/http-not-allowed-not-found-server-error-handler/login :** logging in via `\MovieCatalog\Controllers\HttpNotAllowedNotFoundServerErrorHandler` 
- **http://localhost:8888/movie-catalog-base/login :** logging in via `\MovieCatalog\Controllers\MovieCatalogBase` 
- **http://localhost:8888/movie-listings/login :** logging in via `\MovieCatalog\Controllers\MovieListings` 
- **http://localhost:8888/users/login :** logging in via `\MovieCatalog\Controllers\Users` 

Below is a list of all the features we will be implementing

* **\MovieCatalog\Controllers\Users** controller

    - List all users via **`actionIndex()`** (will be located at http://localhost:8888/users/ or http://localhost:8888/users/index)
    - View a single user via **`actionView($id)`** (will be located at http://localhost:8888/users/view/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)
    - Add the first user (i.e. the **`admin`** user) via **`actionInitUsers()`** **[Already Implemented]** (located at http://localhost:8888/users/init-users [a SlimPHP 3 Skeleton mvc route] or http://localhost:8888/init-users [a manally defined route])
    - Add a new user via **`actionAdd()`** (will be located at http://localhost:8888/users/add)
    - Edit an existing user via **`actionEdit($id)`** (will be located at http://localhost:8888/users/edit/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)
    - Delete a specific user via **`actionDelete($id)`** (will be located at http://localhost:8888/users/delete/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)

* **\MovieCatalog\Controllers\MovieListings** controller

    - List all movies via **`actionIndex()`**  (will be located at http://localhost:8888/movie-listings/ or http://localhost:8888/movie-listings/index)
    - View a single movie via **`actionView($id)`** (will be located at http://localhost:8888/movie-listings/view/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)
    - Add a new movie via **`actionAdd()`** (will be located at http://localhost:8888/movie-listings/add)
    - Edit an existing movie via **`actionEdit($id)`** (will be located at http://localhost:8888/movie-listings/edit/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)
    - Delete a specific movie via **`actionDelete($id)`** (will be located at http://localhost:8888/movie-listings/delete/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 3 Skeleton mvc routing mechanism)

Next we edit our site's layout template (**`./src/layout-templates/main-template.php`**) 
to contain links to all the features we will be implementing.

**`./src/layout-templates/main-template.php`** before edit:
```php
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Site Title Goes Here</title>
        <link rel="stylesheet" href="<?php echo s3MVC_MakeLink('/css/foundation/foundation.css'); ?>" />
    </head>
    <body>
        <div class="row">
            <div class="small-12 columns">
                <ul class="menu" style="padding-left: 0;">
                    <li><a href="#">Section 1</a></li>
                    <li><a href="#">Section 2</a></li>
                    <li><a href="#">Section 3</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="small-12 columns">
                <h1>Welcome to Your New Site</h1>
                <p>This site is powered by the <a href="https://github.com/rotexsoft/slim3-skeleton-mvc-app">SlimPHP 3 Skeleton MVC App Micro-Framework</a> based on SlimPHP 3. It also ships with the <a href="http://foundation.zurb.com/">Foundation</a> UI framework. Everything you need to know about using the Foundation UI framework can be found <a href="http://foundation.zurb.com/docs">here</a>.</p>
            </div>
        </div>

        <div class="row">    
            <div class="small-12 columns">
                <?php echo $content; ?>                
            </div>
        </div>

        <footer class="row">
            <div class="small-12 columns">
                <hr/>
                <div class="row">
                    <div class="small-6 columns">
                        <p>© Copyright no one at all. Go to town.</p>
                    </div>
                </div>
            </div> 
        </footer>

        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/jquery.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/what-input.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/foundation.min.js'); ?>"></script>
        <script> $(document).foundation(); </script>
    </body>
</html>
```

**`./src/layout-templates/main-template.php`** after edit:
```php
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Da Numba 1 Movie Catalog App</title>
        <link rel="stylesheet" href="<?php echo s3MVC_MakeLink('/css/foundation/foundation.css'); ?>" />

        <style>
            /* style for menu items */
            ul.menu li.active-link,
            ul.menu li.active-link a{
                color: #fff;
            }
            ul.menu li.active-link{
                background-color: #2e8acd;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="top-bar">
                <div class="top-bar-left">
                    <ul class="dropdown menu" data-dropdown-menu>
                        <li class="menu-text">Da Numba 1 Movie Catalog App</li>
                        <li <?php isset($controller_name_from_uri) && makeMenuItemActive('movie-listings', $controller_name_from_uri); ?> >
                            <a href="<?php echo s3MVC_MakeLink("movie-listings"); ?>">
                                Home
                            </a>
                        </li>
                        <li <?php isset($controller_name_from_uri) && makeMenuItemActive('users', $controller_name_from_uri); ?> >
                            <a href="<?php echo s3MVC_MakeLink("users"); ?>">Manage Users</a>
                            <ul class="menu vertical">
                                <li><a href="<?php echo s3MVC_MakeLink("users/add"); ?>">Add New User</a></li>
                            </ul>
                        </li>
                    </ul> <!-- <ul class="dropdown menu" data-dropdown-menu> -->
                </div> <!-- <div class="top-bar-left"> -->

                <div class="top-bar-right">
                    <ul class="menu">
                        <li><input type="search" placeholder="Search"></li>
                        <li><button type="button" class="button">Search</button></li>
                        <li>&nbsp;</li>
                        <li>
                            <?php
                                if( !isset($controller_name_from_uri) ) {

                                    $controller_name_from_uri = 'movie-listings';
                                }

                                $login_action_path = s3MVC_MakeLink("/{$controller_name_from_uri}/login");
                                $logout_action_path = s3MVC_MakeLink("/{$controller_name_from_uri}/logout");
                            ?>
                            <?php if( isset($is_logged_in) && $is_logged_in ): ?>

                                <strong style="color: #7f8fa4;">Logged in as <?php echo isset($logged_in_users_username) ? $logged_in_users_username : ''; ?></strong>
                                <a class="button" href="<?php echo $logout_action_path; ?>">
                                    <strong>Log Out</strong>
                                </a>

                            <?php else: ?>

                                <a class="button" href="<?php echo $login_action_path; ?>">
                                    <strong>Log in</strong>
                                </a>

                            <?php endif; ?>
                        </li>
                    </ul> <!-- <ul class="menu"> -->
                </div> <!-- <div class="top-bar-right"> -->
            </div> <!-- <div class="top-bar"> -->
        </div> <!-- <div class="row"> -->

        <?php if( isset($last_flash_message) && $last_flash_message !== null  ): ?>

            <?php $last_flash_message_css_class = isset($last_flash_message_css_class)? $last_flash_message_css_class : ''; ?>

            <div class="row" style="margin-top: 1em;">
                <div class="callout <?php echo $last_flash_message_css_class; echo is_array($last_flash_message)? '' : ' text-center'; ?>"  data-closable>
                    <button class="close-button" data-close>&times;</button>
                    <p>
                        <?php if( is_array($last_flash_message) ): ?>
                            
                            <ul>
                            <?php foreach($last_flash_message as $curr_flash_msg): ?>
                        
                                <li><?php echo $curr_flash_msg; ?></li>
                        
                            <?php endforeach; // foreach($last_flash_message as $curr_flash_msg): ?>
                            </ul>
                        <?php else: ?>
                            <?php echo $last_flash_message; ?>
                        <?php endif; // if( is_array($last_flash_message) ): ?>
                    </p>
                </div> <!-- <div class="callout <?php echo $last_flash_message_css_class; echo is_array($last_flash_message)? '' : ' text-center'; ?>"  data-closable> -->
            </div> <!-- <div class="row" style="margin-top: 1em;"> -->

        <?php endif; //if( $last_flash_message !== null )?>

        <div class="row" style="margin-top: 1em;">
            <div class="small-12 columns">
                <?php echo $content; ?>
            </div>
        </div>

        <footer class="row">
            <div class="small-12 columns">
                <hr/>
                <div class="row">
                    <div class="small-6 columns">
                        <p>Copyright &copy; <?php echo date('Y'); ?>. Da Numba 1 Movie Catalog App.</p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/jquery.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/what-input.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/foundation.min.js'); ?>"></script>
        <script> $(document).foundation(); </script>
    </body>
</html>

<?php
function makeMenuItemActive($links_controller_name, $controller_name_from_uri) {

    if( trim($controller_name_from_uri) === trim($links_controller_name) ) {

        echo 'class="active-link"';

    } else { echo ''; }
}
```

Our layout template (**`./src/layout-templates/main-template.php`**) is now fully
configured. If you look closely at the edited template file, you will notice the
following **php** variables:

- **$controller_name_from_uri**
- **$action_name_from_uri** (this variable is not actually present in the template but we will be setting it for consistency sake)
- **$is_logged_in**
- **$logged_in_users_username**
- **$last_flash_message**
- **$last_flash_message_css_class**
- **$content**

We need to inject these variables into our layout template via the **renderLayout**
method in our controller. We will override the **renderLayout** method in 
**`\MovieCatalog\Controllers\MovieCatalogBase`** (since it's the base controller
for our app) like so:

```php
    
    public function renderLayout($file_name, array $data=[]) {
        
        //layout vars        
        $this->layout_renderer->setVar('action_name_from_uri', $this->action_name_from_uri);
        $this->layout_renderer->setVar('controller_name_from_uri', $this->controller_name_from_uri);
        $this->layout_renderer->setVar('is_logged_in', $this->isLoggedIn());
        $this->layout_renderer->setVar('logged_in_users_username', '');
        
        if ( $this->isLoggedIn() ) {

            $logged_in_username = $this->container->get('vespula_auth')->getUsername();
            $this->layout_renderer->setVar('logged_in_users_username', $logged_in_username);
        }
        
        $this->layout_renderer->setVar('last_flash_message', $this->getLastFlashMessage());
        $this->layout_renderer->setVar('last_flash_message_css_class', $this->getLastFlashMessageCssClass());

        if ( !isset($this->layout_renderer->content) ) {

            $this->layout_renderer->setVar('content', 'Content Goes Here!');
        }
        
        // Note that items in $data with the same key name as any of the layout
        // variables above (e.g. if $data === ['action_name_from_uri' => 'new-action-name-from-uri' ])
        // will overwrite the values set by the corresponding call to 
        // $this->layout_renderer->setVar above. In the earlier example.
        // $action_name_from_uri will have a value of 'new-action-name-from-uri'
        // in the layout template since $data['action_name_from_uri'] (in the call 
        // to parent::renderLayout($file_name, $data) below) will overwrite
        // the effect of the earlier call above to 
        // $this->layout_renderer->setVar('action_name_from_uri', $this->action_name_from_uri);
        return parent::renderLayout($file_name, $data);
    }
```

We are going to add the following methods to **`\MovieCatalog\Controllers\MovieCatalogBase`** 
for managing flash messages: 
```php
    protected function setErrorFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('alert');
    }

    protected function setSuccessFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('success');
    }

    protected function setWarningFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('warning');
    }
    
    protected function setFlashMessage($msg) {

        $msg_key = 'curr_msg';
        $this->container->get('slim_flash')->addMessage($msg_key, $msg);
    }

    protected function getLastFlashMessage() {

        $msg_key = 'curr_msg';
        $messages = $this->container->get('slim_flash')->getMessage($msg_key);

        if( is_array($messages) && count($messages) === 1 ) {
            
            $messages = array_pop($messages);
        }
        return $messages;
    }

    protected function setFlashMessageCssClass($css_class) {

        $msg_key = 'curr_msg_css_class';
        $this->container->get('slim_flash')->addMessage($msg_key, $css_class);
    }

    protected function getLastFlashMessageCssClass() {

        $msg_key = 'curr_msg_css_class';
        $messages = $this->container->get('slim_flash')->getMessage($msg_key);
        
        if( is_array($messages) && count($messages) > 0 ) {
            
            $messages = array_pop($messages);
        }
        return $messages;
    }
```




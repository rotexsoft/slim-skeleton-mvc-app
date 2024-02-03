# Real World Usage: Creating a Movie Catalog application

In this tutorial, we will be creating a simple application for managing a list of movies. 
This app will be built from scratch using the 
[SlimPHP 4 Skeleton MVC App](https://github.com/rotexsoft/slim-skeleton-mvc-app/) 
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

The source files for the fully developed app illustrated in this walk-through is located 
at https://github.com/rotexsoft/movie-catalog-web-app . You can download the files or 
clone the repository to follow along if you wish.

First, we create the new app by running the command below:

```
composer create-project -n -s dev rotexsoft/slim-skeleton-mvc-app movie-catalog
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

> **NOTE:** you may get a **session_start()** error if the folder configured for storing
php sessions is not writable by the php webserver started in the command above.
This can be fixed by setting the value of the php configuration option **session.save_path**
to a path to a folder that the built-in php webserver can write to. This configuration
option should be set inside **./movie-catalog/config/ini-settings.php**.
> By default, **'session.save_path'** has the value of **'./movie-catalog/tmp/session'** in **./movie-catalog/config/ini-settings.php**

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
    `record_creation_date` DATETIME NOT NULL, 
    `record_last_modification_date` DATETIME NOT NULL, 
    PRIMARY KEY (`id`) 
); 

CREATE TABLE user_authentication_accounts (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, 
    `username` VARCHAR(255), 
    `password` VARCHAR(255),
    `record_creation_date` DATETIME NOT NULL, 
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
./vendor/bin/smvc-create-controller -c movie-catalog-base -p "./src" -n "MovieCatalog\Controllers" -e "\SlimMvcTools\Controllers\BaseController"
./vendor/bin/smvc-create-controller -c users -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
./vendor/bin/smvc-create-controller -c movie-listings -p "./src" -n "MovieCatalog\Controllers" -e "\MovieCatalog\Controllers\MovieCatalogBase"
``` 

We should now have the following files and folders in our app:

* **./src/controllers/MovieCatalogBase.php:** containing the
**`\MovieCatalog\Controllers\MovieCatalogBase`** class which is a direct sub-class
of **`\SlimMvcTools\Controllers\BaseController`**. This class will serve as the 
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
    in **./src/views/movie-listings/**, then **./src/views/movie-catalog-base/index.php**
    would be used when the **renderView('index.php')** method is called from within
    **\MovieCatalog\Controllers\MovieListings**.


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
    in **./src/views/users/**, then **./src/views/movie-catalog-base/index.php**
    would be used when the **renderView('index.php')** method is called from 
    within **\MovieCatalog\Controllers\Users**.

Next, we add the value (**`'\\MovieCatalog\\Controllers\\'`**) to the 
**$container[\SlimMvcTools\ContainerKeys::NAMESPACES_4_CONTROLLERS]** array in the dependencies file.
This would be used by the MVC routing mechanism when trying to create an instance
of the controller class (whose name was extracted from the request url).

We then go on to edit **`./config/app-settings.php`** by assigning the value of **`true`** 
to `auto_prepend_action_to_action_method_names ` and the value of 
**`\MovieCatalog\Controllers\MovieListings::class`** to 
`default_controller_class_name`. 

The first edit eliminates the need to prepend `action-` to action method names 
in the request url and the second edit makes 
**`\MovieCatalog\Controllers\MovieListings'`** the default controller for our app. 

Since `default_action_name` has a default value of **`'actionIndex'`**, 
this means that the **`'actionIndex'`** method in 
**`\MovieCatalog\Controllers\MovieListings'`** would be used to handle the 
**`/`** route in our app.

Now let's run our php development server again:

```
php -S 0.0.0.0:8888 -t public
```

Browse to http://localhost:8888/movie-catalog-base or 
http://localhost:8888/movie-catalog-base/index and you should see the output below:

```
You have successfully executed MovieCatalog\Controllers\MovieCatalogBase::actionIndex()
This is the default view for MovieCatalog\Controllers\MovieCatalogBase::actionIndex().
```

Next, browse to http://localhost:8888/movie-listings or 
http://localhost:8888/movie-listings/index and you should see the output below:

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

If browsing to all the links above resulted in the corresponding output, then all
our controllers have been correctly setup and we are now ready to start implementing
methods in our controllers. You can stop the php development server for now.

Now, we need to install the two composer packages (i.e. 
[Leanorm](https://packagist.org/packages/rotexsoft/leanorm) and 
[Slim Flash](https://packagist.org/packages/slim/flash)) we are going to use
for creating our model classes and manage flash messaging in our app. Run the 
commands below to install these packages:
```
composer require slim/flash
composer require rotexsoft/leanorm
composer require --dev rotexsoft/leanorm-cli
```

We then go on to register a `Slim Flash` object in the container in our dependencies
file by adding the code below to **`./config/dependencies.php`**:
```php
$container[\Slim\Flash\Messages::class] = function () {

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
namespace MovieCatalog\Models\Collections;

class BaseCollection extends \LeanOrm\Model\Collection {
    //put your code here
}
```

**BaseModel.php**

```php
<?php
namespace MovieCatalog\Models;

namespace MovieCatalog\Models;

class BaseModel extends \LeanOrm\Model {

    public function __construct(
        string $dsn = '', 
        string $username = '', 
        string $passwd = '', 
         array $pdo_driver_opts = [], 
        string $primary_col_name = '', 
        string $table_name = ''
    ) {
        parent::__construct($dsn, $username, $passwd, $pdo_driver_opts, $primary_col_name, $table_name);
    }
}
```
**BaseRecord.php**

```php
<?php
namespace MovieCatalog\Models\Records;

class BaseRecord extends \LeanOrm\Model\Record {

    public function getDateCreated(): string {
        
        $col = $this->getModel()->getCreatedTimestampColumnName();
        
        if( in_array($col, $this->getModel()->getTableColNames()) ) {
            
            return date('M j, Y g:i a T', strtotime($this->$col));
        }
        
        return '';
    }
    
    public function getLastModfiedDate(): string {
        
        $col = $this->getModel()->getUpdatedTimestampColumnName();
        
        if( in_array($col, $this->getModel()->getTableColNames()) ) {
            
            return date('M j, Y g:i a T', strtotime($this->$col));
        }
        
        return '';
    }
}
```

Now we use the leanorm-cli tool to generate model classes for our application. We need to create a config file for that by running the command below

```
cp ./vendor/rotexsoft/leanorm-cli/sample-config.php ./leanorm-cli-config.php
```

Edit **./leanorm-cli-config.php** to contain the db connection info and these other values:

* **directory:** `./src/models`
* **namespace:** `MovieCatalog\\Models`
* **collection_class_to_extend:** `'\\' . \MovieCatalog\Models\Collections\BaseCollection::class`
* **model_class_to_extend:** `'\\' . \MovieCatalog\Models\BaseModel::class`
* **record_class_to_extend:** `'\\' . \MovieCatalog\Models\Records\BaseRecord::class`
* **created_timestamp_column_name:** `record_creation_date`
* **updated_timestamp_column_name:** `record_last_modification_date`
* **store_table_col_metadata_array_in_file:** `true`

Now run the command below to generate the model classes for your db tables:

```
php ./vendor/bin/generate-leanorm-classes.php ./leanorm-cli-config.php
```

Next, add the database connection info to **./config/app-settings.php** like so:

```php
    'db_dsn'        => 'dsnval goes here',
    'db_user_name'  => 'username goes here',
    'db_password'   => 'password goes here',
```

We then go on to register Model objects (for the `movie_listings` and 
`user_authentication_accounts` database tables) in the container in our 
dependencies file by adding the code below to **`./config/dependencies.php`**:

```php
$container[\MovieCatalog\Models\MoviesListings\MoviesListingsModel::class] = 
function ($c) {

    return new \MovieCatalog\Models\MoviesListings\MoviesListingsModel(
        $c[ContainerKeys::APP_SETTINGS]['db_dsn'],
        $c[ContainerKeys::APP_SETTINGS]['db_user_name'],
        $c[ContainerKeys::APP_SETTINGS]['db_password'],
        [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
    );
};

$container[\MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class] = 
function ($c) {

    return new \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel(
        $c[ContainerKeys::APP_SETTINGS]['db_dsn'],
        $c[ContainerKeys::APP_SETTINGS]['db_user_name'],
        $c[ContainerKeys::APP_SETTINGS]['db_password'],
        [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
    );
};
```

Now, we run the command below to allow composer's autoloader to be able to find all
the Model classes we just added to our app:

```
composer dumpautoload -o
```


Next, we configure the `Vespula.Auth PDO Authentication setup` section of our
dependencies file (**`./config/dependencies.php`**) to authenticate against the 
`user_authentication_accounts` table in our MySQL `movie_catalog` database by updating 
`$container['vespula_auth']` in the `Vespula.Auth PDO Authentication setup` section 
with the code below:

```php
$container[ContainerKeys::VESPULA_AUTH] = function () {

    $pdo = new \PDO(
        $c[ContainerKeys::APP_SETTINGS]['db_dsn'],
        $c[ContainerKeys::APP_SETTINGS]['db_user_name'],
        $c[ContainerKeys::APP_SETTINGS]['db_password'], 
        [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
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
```

Next we create an action method named `actionInitUsers` in the 
`\MovieCatalog\Controllers\Users` class to create a user with the username 
`admin` and a password supplied by the user (if and only if the `user_authentication_accounts` 
table contains no data). It will be reachable at `http://localhost:8888/users/init-users/<entered_password>`.
Where `<entered_password>` is whatever value the user wants to be the password.
This method can be improved upon, by receiving the desired password via HTTP POST, 
we are receiving it via the url in this tutorial for convenience purposes (not 
secure if your app or web-server logs the url of each request).
Below is the method:

```php
    public function actionInitUsers($password) {
        
        $view_str = ''; // will hold output to be injected into the site layout 
                        // file (i.e. `./src/layout-templates/main-template.php`)
                        // when $this->renderLayout(...) is called
        
        $model_obj = $this->getContainerItem(
            \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class
        );
        $num_existing_users = 
            $model_obj->fetchValue(
                $model_obj->getSelect()
                          ->cols(['count(*)'])
            );

        if( $num_existing_users === null) {
            
            // no need to add entries for the `record_creation_date`
            // and `record_last_modification_date` fields in the 
            // $user_data array below since leanorm will 
            // automatically populate those fields when
            // the new record is saved.
            $user_data = [
                'username' => 'admin', 
                'password' => password_hash($password , PASSWORD_DEFAULT)
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
        // Note: $this->layout_template_file_name has a value of 'main-template.php'
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

**actionInitUsers($password)** above checks if there is no data in the `user_authentication_accounts` 
table and if there is none, it then proceeds to insert a row of data into the table with a 
`username` value of **`admin`** and `password` with value supplied in **$password** 
(note that it's the hashed form of the password that is stored in the 
`user_authentication_accounts` table). If there is data in the 
`user_authentication_accounts` table, the method just sets a
message to be displayed.

All we now need to do to ensure we have a user with the username **`admin`** and
password `admin` in our app is to browse to http://localhost:8888/users/init-users/admin. 
After this, we can login to our app with a `username` of **`admin`** and a `password` of
**`admin`**. We can login via any controller with the path **`<controller_name>/login`** 
in our url, where **`<controller_name>`** can be substituted with the controller 
names of any of the controllers we have created in our app. 

We can even create a manual route **/init-users/{password}[/]** in 
**`./config/routes-and-middlewares.php`** that redirects to 
`http://localhost:8888/users/init-users/<entered_password>`. 
So we can use a shorter url `http://localhost:8888/init-users/<entered_password>` 
to accomplish the same goal of creating the **`admin`** user. For example,
http://localhost:8888/init-users/admin will also create a user with the 
username **`admin`** and password `admin` (if there are no users yet in the app).

Just add the code below to **`./config/routes-and-middlewares.php`**
and `http://localhost:8888/init-users/<entered_password>` will become active:

```php
$app->get(
        
    '/init-users/{password}[/]',
        
    function(
        \Psr\Http\Message\ServerRequestInterface $request, 
        \Psr\Http\Message\ResponseInterface $response, 
        $args
    ) {
        return $response->withStatus(301)
                        ->withHeader('Location', '/users/init-users/'.$args['password']);
    }
);
```

Below are the login urls currently available in our app (they are all calling 
**`\SlimMvcTools\Controllers\BaseController::actionLogin()`**):

- **http://localhost:8888/base-controller/login :** logging in via `\SlimMvcTools\Controllers\BaseController` 
- **http://localhost:8888/hello/login :** logging in via `\SlimSkeletonMvcApp\Controllers\Hello` (a sample controller that ships with each new [SlimPHP 4 Skeleton MVC App](https://github.com/rotexsoft/slim-skeleton-mvc-app/) app) 
- **http://localhost:8888/movie-catalog-base/login :** logging in via `\MovieCatalog\Controllers\MovieCatalogBase` 
- **http://localhost:8888/movie-listings/login :** logging in via `\MovieCatalog\Controllers\MovieListings` 
- **http://localhost:8888/users/login :** logging in via `\MovieCatalog\Controllers\Users` 

Below is a list of all the features we will be implementing

* **\MovieCatalog\Controllers\Users** controller

    - List all users via **`actionIndex()`** (will be located at http://localhost:8888/users/ or http://localhost:8888/users/index)
    - View a single user via **`actionView($id)`** (will be located at http://localhost:8888/users/view/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)
    - Add the first user (i.e. the **`admin`** user) via **`actionInitUsers($password)`** **[Already Implemented]** (located at `http://localhost:8888/users/init-users/<entered_password>` [a SlimPHP 4 Skeleton mvc route] or `http://localhost:8888/init-users/<entered_password>` [a manually defined route])
    - Add a new user via **`actionAdd()`** (will be located at http://localhost:8888/users/add)
    - Edit an existing user via **`actionEdit($id)`** (will be located at http://localhost:8888/users/edit/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)
    - Delete a specific user via **`actionDelete($id)`** (will be located at http://localhost:8888/users/delete/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)

* **\MovieCatalog\Controllers\MovieListings** controller

    - List all movies via **`actionIndex()`**  (will be located at http://localhost:8888/movie-listings/ or http://localhost:8888/movie-listings/index)
    - View a single movie via **`actionView($id)`** (will be located at http://localhost:8888/movie-listings/view/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)
    - Add a new movie via **`actionAdd()`** (will be located at http://localhost:8888/movie-listings/add)
    - Edit an existing movie via **`actionEdit($id)`** (will be located at http://localhost:8888/movie-listings/edit/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)
    - Delete a specific movie via **`actionDelete($id)`** (will be located at http://localhost:8888/movie-listings/delete/21 , where `21` could be any number and will be populated into the variable **$id** by the SlimPHP 4 Skeleton mvc routing mechanism)

Next we edit our site's layout template (**`./src/layout-templates/main-template.php`**) 
to contain navigation links to some of the features we will be implementing.

**`./src/layout-templates/main-template.php`** after edit:
```php
<!doctype html>
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
    
    function makeMenuItemActive($links_controller_name, $controller_name_from_uri): string {

        return ( trim($controller_name_from_uri) === trim($links_controller_name) ) ? 'active' : '';
    }
?>

<html class="no-js" lang="<?= $__localeObj->getLanguageCode(); ?>" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        
        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!--Import materialize.css-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

        <title>Da Numba 1 Movie Catalog App</title>
        
        <style>
            .container {
                width: 98%;
            }
            
            @media only screen and (min-width : 601px) and (max-width : 1260px) {
                .toast {
                    width: 100%;
                    border-radius: 0;
                }
            }

            @media only screen and (min-width : 1261px) {
                .toast {
                    width: 100%;
                    border-radius: 0; 
                }
            }

            @media only screen and (min-width : 601px) and (max-width : 1260px) {
                #toast-container {
                    min-width: 100%;
                    bottom: 0%;
                    top: 90%;
                    right: 0%;
                    left: 0%;
                } 
            }

            @media only screen and (min-width : 1261px) {
                #toast-container {
                    min-width: 100%;
                    bottom: 0%;
                    top: 90%;
                    right: 0%;
                    left: 0%; 
                } 
            }
        </style>
    </head>
    
    <body>
        
        <div class="navbar-fixed">
            
            <!-- Dropdown Structure -->
            <ul id="dropdown1" class="dropdown-content">
                            
                <li><a href="<?= $controller_object->makeLink("/users"); ?>">Manage Users</a></li>
                            
                <li class="divider"></li>
                
                <?php if( $controller_object->isLoggedIn() ): ?>
                        
                    <li><a href="<?= $controller_object->makeLink("/users/add"); ?>">Add New User</a></li>
                    
                <?php endif; // if( $controller_object->isLoggedIn() ) ?>
            </ul>
            
            <nav>
                <div class="nav-wrapper">
                    
                    <a href="<?= $controller_object->makeLink('/movie-listings'); ?>"
                       class="brand-logo">
                        Da Numba 1 Movie Catalog App
                    </a>
                    
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger">
                        <i class="material-icons">menu</i>
                    </a>
                    
                    <ul id="nav-mobile" class="right hide-on-med-and-down">
                        
                        <li class="<?= makeMenuItemActive('movie-listings', $controller_object->getControllerNameFromUri()); ?>">
                            <a href="<?= $controller_object->makeLink('/movie-listings'); ?>">
                                <?= $__localeObj->gettext('main_template_text_home'); ?>
                            </a>
                        </li>
                        
                        <!-- Dropdown Trigger -->
                        <li class="<?= makeMenuItemActive('users', $controller_object->getControllerNameFromUri()); ?>">
                            <a class="dropdown-trigger" 
                               href="#!" data-target="dropdown1">
                                Users<i class="material-icons right">arrow_drop_down</i>
                            </a>
                        </li>
                        
                        <?php if($controller_object->isLoggedIn()): ?>
                            <li>
                                <a href="<?= $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/logout"); ?>">
                                    <?= $__localeObj->gettext('base_controller_text_logout'); ?>
                                </a>&nbsp;
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/login"); ?>">
                                    <?= $__localeObj->gettext('base_controller_text_login'); ?>
                                </a>&nbsp;
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            
        </div> <!-- <div class="navbar-fixed"> -->
        
        <ul class="sidenav" id="mobile-demo">
            <li>
                <a href="<?= $controller_object->makeLink('/movie-listings'); ?>">
                    <?= $__localeObj->gettext('main_template_text_home'); ?>
                </a>
            </li>

            <li><a href="<?= $controller_object->makeLink("/users"); ?>">Manage Users</a></li>

            <?php if( $controller_object->isLoggedIn() ): ?>

                <li><a href="<?= $controller_object->makeLink("/users/add"); ?>">Add New User</a></li>

            <?php endif; // if( $controller_object->isLoggedIn() ) ?>

            <?php if($controller_object->isLoggedIn()): ?>
                <li>
                    <a href="<?= $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/logout"); ?>">
                        <?= $__localeObj->gettext('base_controller_text_logout'); ?>
                    </a>&nbsp;
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/login"); ?>">
                        <?= $__localeObj->gettext('base_controller_text_login'); ?>
                    </a>&nbsp;
                </li>
            <?php endif; ?>
                
        </ul> <!-- <ul class="sidenav" id="mobile-demo"> -->
        
        <div class="container">

            <div class="row" style="margin-top: 1em;">
                <div class="s12">
                    <?php if( $controller_object->isLoggedIn() ): ?>

                        <strong style="color: #7f8fa4;">
                            Logged in as <?= $controller_object->getVespulaAuthObject()->getUsername(); ?>
                        </strong>

                    <?php endif; ?>
                </div>
            </div>

            <div class="row" style="margin-top: 1em;">
                <div class="s12">
                    <?php echo $content; ?>
                </div>
            </div>

            <div class="s12">
                <footer>
                    <hr/>
                    <p>Copyright &copy; <?php echo date('Y'); ?>. Da Numba 1 Movie Catalog App.</p>
                </footer>
            </div>
            
        </div>

        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                
                var elems = document.querySelectorAll('.dropdown-trigger');
                var instances = M.Dropdown.init(elems, { hover: false });

                var sideNavElems = document.querySelectorAll('.sidenav');
                var sideNavInstances = M.Sidenav.init(sideNavElems, {});
                
                // Flash Message display logic
                <?php if( isset($last_flash_message) && $last_flash_message !== null  ): ?>

                    var flash_toast_css = '<?= $this->escapeJs($last_flash_message_css_class ?? ''); ?>';
                    var flash_toast_messages = '';

                    <?php if( is_array($last_flash_message) ): ?>

                        <?php foreach($last_flash_message as $curr_flash_msg): ?>

                            flash_toast_messages += '<?= $this->escapeJs($curr_flash_msg); ?><br>';

                        <?php endforeach; // foreach($last_flash_message as $curr_flash_msg): ?>

                    <?php else: ?>

                        flash_toast_messages += '<?= $this->escapeJs($last_flash_message); ?><br>';

                    <?php endif; // if( is_array($last_flash_message) ): ?>
                        
                    M.toast({html: flash_toast_messages, displayLength: 15000, classes: flash_toast_css });

                <?php endif; //if( $last_flash_message !== null )?>
            });
        </script>
        
    </body>
</html>
```

Our layout template (**`./src/layout-templates/main-template.php`**) is now fully
configured. If you look closely at the edited template file, you will notice the
following **php** variables:

- **$__localeObj**
    - It is injected in the dependencies files in the **\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER** & **\SlimMvcTools\ContainerKeys::VIEW_RENDERER** which are responsible for rendering view and layout files
- **$controller_object**
    - this variable is the instance of the controller class that was used to render the layout file and gets automatically injected into layout files in **\SlimMvcTools\Controllers\BaseController->renderLayout(...)**
- **$last_flash_message**
    - will be injected in the renderLayout & renderView methods of **`\MovieCatalog\Controllers\MovieCatalogBase`**
- **$last_flash_message_css_class**
    - will be injected in the renderLayout & renderView methods of **`\MovieCatalog\Controllers\MovieCatalogBase`**
- **$content**
    - this variable gets automatically injected into layout files in **\SlimMvcTools\Controllers\BaseController->renderLayout(...)**, it is the output from the view for the current controller action method being executed

We need to inject some variables into our layout template via the **renderLayout**
method in our controller. We will override the **renderLayout** method in 
**`\MovieCatalog\Controllers\MovieCatalogBase`** (since it's the base controller
for our app) like so:

```php
    public function renderLayout(string $file_name, array $data=[]): string {
        
        // define common layout variables
        $common_layout_data = [];
        $common_layout_data['last_flash_message'] = $this->getLastFlashMessage();
        $common_layout_data['last_flash_message_css_class'] = $this->getLastFlashMessageCssClass();
        
        return parent::renderLayout(
            $file_name, array_merge( $common_layout_data, $data ) 
        );
    }
```

We should also make these variables available to all our views via the 
**renderView** method in our controller. We will override the **renderView** method in 
**`\MovieCatalog\Controllers\MovieCatalogBase`** (since it's the base controller
for our app) like so:

```php
    public function renderView(string $file_name, array $data=[]): string {
        
        // define common variables
        $common_layout_data = [];
        $common_layout_data['last_flash_message'] = $this->getLastFlashMessage();
        $common_layout_data['last_flash_message_css_class'] = $this->getLastFlashMessageCssClass();
        
        return parent::renderView(
            $file_name, array_merge( $common_layout_data, $data ) 
        );
    }
```

Next we edit our site's error layout template (**`./src/layout-templates/error-template.php`**) which will be used by the framework to display errors like HTTP 404 Not Found, 500 Internal Server Error, etc. We want this template to have the same look and feel as **./src/layout-templates/main-template.php**

```php
<!doctype html>

<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        
        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!--Import materialize.css-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

        <!-- title injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
        <title>Da Numba 1 Movie Catalog App &ndash; {{{TITLE}}}</title>
        
        <style>
            .container {
                width: 98%;
            }
        </style>
    </head>
    
    <body>
        
        <div class="navbar-fixed">
                        
            <nav>
                <div class="nav-wrapper">
                    
                    <a href="#" class="brand-logo">
                        Da Numba 1 Movie Catalog App
                    </a>
                    
                </div>
            </na<!doctype html>

<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        
        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!--Import materialize.css-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

        <!-- title injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
        <title>Da Numba 1 Movie Catalog App &ndash; {{{TITLE}}}</title>
        
        <style>
            .container {
                width: 98%;
            }
        </style>
    </head>
    
    <body>
        
        <div class="navbar-fixed">
                        
            <nav>
                <div class="nav-wrapper">
                    
                    <a href="#" class="brand-logo">
                        Da Numba 1 Movie Catalog App
                    </a>
                    
                </div>
            </nav>
            
        </div> <!-- <div class="navbar-fixed"> -->
        
        <div class="container">

            <div class="row" style="margin-top: 1em;">
                <div class="s12">
                    <!-- title injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
                    <h1>{{{ERROR_HEADING}}}</h1>
                </div>
            </div>

            <div class="row" style="margin-top: 1em;">
                <div class="s12">
                    <a href="#" onclick="window.history.go(-1)">Go Back</a>
                    <br>
                    <!-- error message body injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
                    <div>{{{ERROR_DETAILS}}}</div>
                </div>
            </div>

            <div class="s12">
                <footer>
                    <hr/>
                    <p>Copyright &copy; <span id="year"></span>. Da Numba 1 Movie Catalog App.</p>
                </footer>
            </div>
            
        </div>

        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        
        <script>
            document.getElementById("year").innerHTML = new Date().getFullYear();
        </script>
        
    </body>
</html>
```


We are going to add the following methods to **`\MovieCatalog\Controllers\MovieCatalogBase`** 
for managing flash messages: 
```php
    protected function setErrorFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('red darken-4');
    }

    protected function setSuccessFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('teal lighten-2');
    }

    protected function setWarningFlashMessage($msg) {
        
        $this->setFlashMessage($msg);
        $this->setFlashMessageCssClass('deep-orange darken-1');
    }
    
    protected function setFlashMessage($msg) {

        $msg_key = 'curr_msg';
        $this->getContainerItem(\Slim\Flash\Messages::class)->addMessage($msg_key, $msg);
    }

    protected function getLastFlashMessage() {

        $msg_key = 'curr_msg';
        $messages = $this->getContainerItem(\Slim\Flash\Messages::class)->getMessage($msg_key);

        if( is_array($messages) && count($messages) === 1 ) {
            
            $messages = array_pop($messages);
        }
        return $messages;
    }

    protected function setFlashMessageCssClass($css_class) {

        $msg_key = 'curr_msg_css_class';
        $this->getContainerItem(\Slim\Flash\Messages::class)->addMessage($msg_key, 'card-panel pulse ' . $css_class);
    }

    protected function getLastFlashMessageCssClass() {

        $msg_key = 'curr_msg_css_class';
        $messages = $this->getContainerItem(\Slim\Flash\Messages::class)->getMessage($msg_key);
        
        if( is_array($messages) && count($messages) > 0 ) {
            
            $messages = array_pop($messages);
        }
        return $messages;
    }
```

Our layout template file is good to go (navigation links have been set-up, a 
flash messaging mechanism is in place and required php variables are automatically 
injected whenever **\MovieCatalog\Controllers\MovieCatalogBase::renderLayout($file_name, array $data=[])** 
is called). 

We are now ready to start implementing features in our **\MovieCatalog\Controllers\Users** 
and **\MovieCatalog\Controllers\MovieListings** controllers.

Let's start with the **\MovieCatalog\Controllers\Users** controller. Let's implement
the action method to list all users; i.e. **actionIndex()**. To do this, update
**actionIndex()** in **\MovieCatalog\Controllers\Users** with the code below:

```php
    public function actionIndex() {
        
        $view_data = [];
        $model_class = \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class;
        $model_obj = $this->getContainerItem($model_class);
        
        // Grab all existing user records.
        // Note that the variable $collection_of_user_records will be available
        // in your index.php view (in this case ./src/views/users/index.php)
        // when $this->renderView('index.php', $view_data) is called.
        $view_data['collection_of_user_records'] = $model_obj->fetchRecordsIntoCollection();
        
        //render the view first and capture the output
        $view_str = $this->renderView('index.php', $view_data);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

We've implemented the controller portion of the feature to list all users. 
Now let's implement the view portion of the feature by adding the code below
to **./src/views/users/index.php**:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4>All Users</h4>

<?php if( $collection_of_user_records instanceof \MovieCatalog\Models\Collections\BaseCollection && count($collection_of_user_records) > 0 ): ?>

    <ul>
        <?php foreach ($collection_of_user_records as $user_record): ?>

            <li>
                <?php echo $user_record->username; ?> | 
                <a href="<?= $controller_object->makeLink( "users/view/" . $user_record->id ); ?>">View</a> 

                <?php if( $controller_object->isLoggedIn() ): ?>

                    | <a href="<?= $controller_object->makeLink( "users/edit/" . $user_record->id ); ?>">Edit</a> |
                    <a href="<?= $controller_object->makeLink( "users/delete/" . $user_record->id ); ?>"
                       onclick="return confirm('Are you sure?');"
                    >
                        Delete
                    </a>

                <?php endif; //if( $controller_object->isLoggedIn() )  ?>
            </li>

        <?php endforeach; ?>
    </ul>

<?php else: ?>

    <p>
        No user(s) exist. <br>
        Initialize the system with an <strong>admin</strong> user with a password of: 
        <input id="initialize-password" type="password" style="width: 25%; display:inline;">.
        <input id="initialize-button" type="submit" value="Initialize">
    </p>

    <script>
        // When the Initialize button is clicked, redirect to 
        // /users/init-users/<entered_password>, where <entered_password> 
        // is the value entered into the password text-box
        document.getElementById('initialize-button').addEventListener('click', function(event) {
            
            var entered_password = document.getElementById('initialize-password').value;

            if( entered_password === '' ) {
                alert('Password cannot be empty!');
                return false;
            }

            window.location.href = 
                '<?= $controller_object->makeLink("/users/init-users"); ?>' + '/' + entered_password;
        });
    </script>

<?php endif; ?>
```

Now our feature to list all users is completed and can be accessed at http://localhost:8888/users 
or http://localhost:8888/users/index .

Let's now implement the action method to view a single user; i.e. **actionView($id)**. 
To do this, we add **actionView($id)** to **\MovieCatalog\Controllers\Users** with the 
code below:

```php
    public function actionView($id) {
        
        $view_data = [];
        $model_class = 
            \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class;
        $model_obj = $this->getContainerItem($model_class);
        
        // Grab record for the user whose id was specified.
        // Note that the variable $user_record will be available
        // in your view.php view (in this case ./src/views/users/view.php)
        // when $this->renderView('view.php', $view_data) is called.
        $view_data['user_record'] = $model_obj->fetchOneByPkey($id);
        
        if( !($view_data['user_record'] instanceof \MovieCatalog\Models\Records\BaseRecord) ) {
            
            // We could not find any user with the specified id in the database.
            // Generate and return an http 404 resposne.
            $this->forceHttp404('Requested user does not exist.');
        }
        
        //get the contents of the view first
        $view_str = $this->renderView('view.php', $view_data);

        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

We've implemented the controller portion of the feature to view a single user. 
Now let's implement the view portion of the feature by creating a **view.php** 
file in **./src/views/users/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4>View User</h4>
<ul style="list-style: none;">
    <li>
        <strong>Username:</strong> 
        <?= $user_record->username; ?>
    </li>
    <li>
        <strong>Date Created:</strong> 
        <?= $user_record->getDateCreated(); ?>
    </li>
    <li>
        <strong>Date Last Modified:</strong> 
        <?= $user_record->getLastModfiedDate(); ?>
    </li>
</dl>
<p>
    <a href="<?= $controller_object->makeLink( "users/index" ); ?>">View all Users</a>
    <?php if( $controller_object->isLoggedIn() ): ?>

        | <a href="<?= $controller_object->makeLink( "users/edit/" . $user_record->id ); ?>">Edit</a> |
        <a href="<?= $controller_object->makeLink( "users/delete/" . $user_record->id ); ?>">Delete</a>

    <?php endif; //if( isset($is_logged_in) && $is_logged_in )  ?>
</p>
```

Now our feature to view a single user is completed and can be accessed at 
`http://localhost:8888/users/view/<some_num>` (**`<some_num>`** should be replaced
with a numeric id of a user; e.g. http://localhost:8888/users/view/2 to view details
of a single user with an id of 2.

Next, we implement the action method to add a new user; i.e. **actionAdd()**. 
To do this, we add **actionAdd()** to **\MovieCatalog\Controllers\Users** with 
the code below:

```php
    public function actionAdd() {
        
        // The call below is to get a response object for
        // redirecting the user to the login page if the
        // user is not currently logged in. You must be 
        // logged in in order to be able to add a new user.
        // If the user is logged in, $login_response will
        // receive a value of false from
        // $this->getResponseObjForLoginRedirectionIfNotLoggedIn().
        $login_response = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if( $login_response instanceof \Psr\Http\Message\ResponseInterface ) {
            
            // redirect to login page
            return $login_response;
        }
        
        $model_class = 
            \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class;
        $model_obj = $this->getContainerItem($model_class);
        $error_msgs = [];
        $error_msgs['form-errors'] = [];
        $error_msgs['username'] = [];
        $error_msgs['password'] = [];
        
        // An associative array with keys being the names of the columns in the db table 
        // associated with the model and a default value of '' for each item in the array
        $default_data = $model_obj->getDefaultColVals();
        
        // remove item whose key is primary key column name
        // since primary key values are auto-generated
        unset($default_data[$model_obj->getPrimaryColName()]); 

        // create a new record with the default data generated above
        $record = $model_obj->createNewRecord($default_data);
        
        if( $this->request->getMethod() === 'POST' ) {
            
            // POST Request
            $has_field_errors = false;
            
            // Read the post data
            $posted_data = sMVC_GetSuperGlobal('post');
            
            if( mb_strlen( ''.$posted_data['username'], 'UTF-8') <= 0 ) {
                
                $error_msgs['username'][] = 'Username Cannot be blank!';
                $has_field_errors = true;
                
            } else {
                
                // check that posted username is not already assigned to an
                // existing user
                $existing_user_with_same_username = 
                    $model_obj->fetchOneRecord(
                        $model_obj->getSelect()
                                  ->where(
                                    ' username = ? ', [$posted_data['username']]
                                  )
                    );
                
                if( $existing_user_with_same_username instanceof \MovieCatalog\Models\Records\BaseRecord ) {
                    
                    // username is already assigned to an existing user
                    $error_msgs['username'][] = 'Username already taken!';
                    $has_field_errors = true;
                }
            }
            
            if( mb_strlen( ''.$posted_data['password'], 'UTF-8') <= 0 ) {
                
                $error_msgs['password'][] = 'Password Cannot be blank!';
                $has_field_errors = true;
            }
     
            //load posted data into new record object
            $record->loadData($posted_data);

            if ( !$has_field_errors ) {
                
                // hash the password
                $record->password = password_hash($record->password, PASSWORD_DEFAULT);
                
                // try to save
                if ( $record->save() !== false ) {

                    //successfully saved;
                    $rdr_path = $this->makeLink("users/index");
                    $this->setSuccessFlashMessage('Successfully Saved!');

                    // re-direct to the list all users page
                    return $this->response->withStatus(302)->withHeader('Location', $rdr_path);
                    
                } else {

                    //Record could not be saved.
                    $error_msgs['form-errors'][] = 'Save Failed!';
                } // if ( $record->save() !== false ) 
                
            } else {
                
                $error_msgs['form-errors'][] = 'Form contains error(s)!';
            } // if ( !$has_field_errors )
                
        } //if( $this->request->getMethod() === 'POST' )

        $view_data = [];
        $view_data['error_msgs'] = $error_msgs;
        $view_data['user_record'] = $record;
        
        $view_str = $this->renderView('add.php', $view_data);
        
        return $this->renderLayout($this->layout_template_file_name, ['content'=>$view_str]);
    }
```

We've implemented the controller portion of the feature to add a new user. 
Now let's implement the view portion of the feature by creating an **add.php** 
file in **./src/views/users/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4 style="margin-bottom: 20px;">Add New User</h4>

<form method="POST" 
      action="<?= $controller_object->makeLink("users/add"); ?>" 
      enctype="multipart/form-data"
>

<?php printErrorMsg('form-errors', $error_msgs); //print form level error message(s) if any ?>

    <div class="row" id="row-username">
        
        <div class="col s3 right-align">
            <label for="username" class="">
                Username<span style="color: red;"> *</span>
            </label>                
        </div>
         
        <?php $input_elems_error_css_class = (count($error_msgs['username']) > 0)? ' class="is-invalid-input" ' : ''; ?>
        
        <div class="col s7">
            <input type="text" 
                   name="username" 
                   id="username" 
                   maxlength="255" 
                   required="required"
                   <?= $input_elems_error_css_class; ?>
                   value="<?= $user_record->username; ?>"
            >
            <?php printErrorMsg('username', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-password">
        
        <div class="col s3 right-align">
            <label for="password" class="">
                Password<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="password" 
                   name="password" 
                   id="password" 
                   maxlength="255" 
                   required="required"
                   value="<?= $user_record->password; ?>"
            >
            <?php printErrorMsg('password', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>

    <div class="row">
        <div class="col s3">
            <input type="submit" 
                   name="save-button" 
                   id="save-button" 
                   class="button" 
                   value="Save"
            >
            <input type="submit" 
                   name="cancel-button" 
                   id="cancel-button" 
                   class="button" 
                   value="Cancel"
            >
        </div>
    </div>
</form>

<script>
    // When Cancel button is clicked, redirect to list all users page
    document.getElementById('cancel-button').addEventListener('click', function(event) {

        // Do this so that when the Cancel button is clicked 
        // the browser does not try to submit the form
        event.preventDefault(); 
        window.location.href = '<?= $controller_object->makeLink("/users/index"); ?>';
    });
</script>

<?php
function printErrorMsg($element_name, array $error_msgs) {

    if( isset($error_msgs[$element_name]) ) {

        foreach($error_msgs[$element_name] as $err_msg) {

            //spit out error message for $element_name
            echo "<div style=\"padding: 0.5em;\" class=\"card red\">{$err_msg}</div>";

        } //foreach($error_msgs[$element_name] as $err_msg)
    } //if( array_key_exists($element_name, $error_msgs) )
}
```

Now our feature to add a new user is completed and can be accessed at 
http://localhost:8888/users/add.

Let's now implement the action method to edit an existing user; i.e. **actionEdit($id)**. 
To do this, we add **actionEdit($id)** to **\MovieCatalog\Controllers\Users** with the 
code below:

```php
    public function actionEdit($id) {
        
        // The call below is to get a response object for
        // redirecting the user to the login page if the
        // user is not currently logged in. You must be 
        // logged in in order to be able to edit an 
        // existing user. If the user is logged in, 
        // $login_response will receive a value of 
        // false from $this->getResponseObjForLoginRedirectionIfNotLoggedIn().
        $login_response = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if( $login_response instanceof \Psr\Http\Message\ResponseInterface ) {
            
            // redirect to login page
            return $login_response;
        }
        
        $model_class = 
            \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class;
        /** @var \MovieCatalog\Models\BaseModel $model_obj */
        $model_obj = $this->getContainerItem($model_class);
        $error_msgs = [];
        $error_msgs['form-errors'] = [];
        $error_msgs['username'] = [];
        $error_msgs['password'] = [];
        
        // fetch the record for the user with the specified $id
        $record = $model_obj->fetchOneByPkey($id);
        
        if( !($record instanceof \MovieCatalog\Models\Records\BaseRecord) ) {
            
            // Could not find record for the user with the specified $id
            $this->forceHttp404('Requested user does not exist.');
        }
        
        if( $this->request->getMethod() === 'POST' ) {
            
            // POST Request
            $has_field_errors = false;
            
            // Read the post data
            $posted_data = sMVC_GetSuperGlobal('post');
            
            if( mb_strlen( ''.$posted_data['username'], 'UTF-8') <= 0 ) {
                
                $error_msgs['username'][] = 'Username Cannot be blank!';
                $has_field_errors = true;
                
            } else {
                // check that posted username is not already assigned to an
                // existing user (except the user with the value of $id)
                $existing_user_with_same_username = 
                    $model_obj->fetchOneRecord(
                        $model_obj->getSelect()
                                  ->where(' username = :username ', ['username' => $posted_data['username']])
                                  ->where(' id != :id ', ['id' => $id])
                    );
                
                if( $existing_user_with_same_username instanceof \MovieCatalog\Models\Records\BaseRecord ) {

                    // username is already assigned to an existing user
                    $error_msgs['username'][] = 'Username already taken!';
                    $has_field_errors = true;
                }
            }

            //load posted data into record object
            $record->username = $posted_data['username'];

            if ( !$has_field_errors ) {
                
                if( 
                    $posted_data['password'] !== '' 
                    && !password_verify(''.$posted_data['password'], $record->password) 
                ) {
                    // only hash the password if it's different from the exisitng 
                    // hashed password
                    $record->password = password_hash(''.$posted_data['password'], PASSWORD_DEFAULT);
                }
                
                // try to save
                if ( $record->save() !== false ) {

                    //successfully saved;
                    $rdr_path = $this->makeLink("users/index");
                    $this->setSuccessFlashMessage('Successfully Saved!');

                    // re-direct to the list all users page
                    return $this->response->withStatus(302)->withHeader('Location', $rdr_path);
                    
                } else {

                    //Record could not be saved.
                    $error_msgs['form-errors'][] = 'Save Failed!';
                } // if ( $record->save() !== false ) 
                
            } else {
                
                $error_msgs['form-errors'][] = 'Form contains error(s)!';
            } // if ( !$has_field_errors )
                
        } //if( $this->request->getMethod() === 'POST' )

        $view_data = [];
        $view_data['user_record'] = $record;
        $view_data['error_msgs'] = $error_msgs;
        
        $view_str = $this->renderView('edit.php', $view_data);
        
        return $this->renderLayout('main-template.php', ['content'=>$view_str]);
    }
```

We've implemented the controller portion of the feature to edit an existing user. 
Now let's implement the view portion of the feature by creating an **edit.php** 
file in **./src/views/users/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4 style="margin-bottom: 20px;">Edit User</h4>

<form method="POST" 
      action="<?= $controller_object->makeLink("users/edit/{$user_record->id}"); ?>" 
      enctype="multipart/form-data"
>

<?php printErrorMsg('form-errors', $error_msgs); //print form level error message(s) if any ?>

    <div class="row" id="row-username">
        
        <div class="col s3 right-align">
            <label for="username" class="">
                Username<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="text" 
                   name="username" 
                   id="username" 
                   maxlength="255" 
                   required="required"
                   value="<?= $user_record->username; ?>"
            >
            <?php printErrorMsg('username', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-password">
        
        <div class="col s3 right-align">
            <label for="password" class="">
                Password
            </label>                
        </div>
        
        <div class="col s7">
            <input type="password" 
                   name="password" 
                   id="password" 
                   maxlength="255"
                   value=""
            >
            <?php printErrorMsg('password', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>

    <div class="row">
        <div class="col s3">
            <input type="submit" 
                   name="save-button" 
                   id="save-button" 
                   class="button" 
                   value="Save"
            >
            <input type="submit" 
                   name="cancel-button" 
                   id="cancel-button" 
                   class="button" 
                   value="Cancel"
            >
        </div>
    </div>
</form>

<script>
    // When Cancel button is clicked, redirect to list all users page
    document.getElementById('cancel-button').addEventListener('click', function(event) {

        // Do this so that when the Cancel button is clicked 
        // the browser does not try to submit the form
        event.preventDefault(); 
        window.location.href = '<?= $controller_object->makeLink("/users/index"); ?>';
    });
</script>

<?php
function printErrorMsg($element_name, array $error_msgs) {

    if( isset($error_msgs[$element_name]) ) {

        foreach($error_msgs[$element_name] as $err_msg) {

            //spit out error message for $element_name
            echo "<div style=\"padding: 0.5em;\" class=\"card red\">{$err_msg}</div>";

        } //foreach($error_msgs[$element_name] as $err_msg)
    } //if( array_key_exists($element_name, $error_msgs) )
}
```

Now our feature to edit an existing user is completed and can be accessed at 
`http://localhost:8888/users/edit/<some_num>` (**`<some_num>`** should be 
replaced with a numeric id of a user; e.g. http://localhost:8888/users/edit/2 
to edit an existing user with an id of 2.

We are now going to implement the last feature for managing users (i.e. the 
ability to delete a specific user). We would be implementing this feature 
differently because it does not require a view. We will add an **actionDelete($id)** 
method to **\MovieCatalog\Controllers\Users** and **doDelete($id, $model_key_name_in_container)** 
to **\MovieCatalog\Controllers\MovieCatalogBase**. **doDelete($id, $model_key_name_in_container)** 
will also be used by the **actionDelete($id)** we will be adding later to 
**\MovieCatalog\Controllers\MovieListings** to delete movie listings. 
Add the code below to **\MovieCatalog\Controllers\Users**:

```php
    public function actionDelete($id) {
        
        return $this->doDelete(
            $id, 
            \MovieCatalog\Models\UsersAuthenticationsAccounts\UsersAuthenticationsAccountsModel::class
        );
    }
```

Then add the code below to **\MovieCatalog\Controllers\MovieCatalogBase**:

```php
    protected function doDelete($id, $model_key_name_in_container) {

        // The call below is to get a response object for
        // redirecting the user to the login page if the
        // user is not currently logged in. You must be 
        // logged in in order to be able to delete an 
        // existing record. If the user is logged in, 
        // $login_response will receive a value of 
        // false from $this->getResponseObjForLoginRedirectionIfNotLoggedIn().
        $login_response = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if( $login_response instanceof \Psr\Http\Message\ResponseInterface ) {
            
            // redirect to login page
            return $login_response;
        }

        // get model object
        $model_obj = $this->getContainerItem($model_key_name_in_container);
        
        // fetch the record
        $record = $model_obj->fetchOneByPkey($id);
        
        if( !($record instanceof \MovieCatalog\Models\Records\BaseRecord) ) {
            
            // Could not find record with the specified $id
            $this->forceHttp404(
                'Requested item could not be deleted. It does not exist.'
            );
        }
        
        // We will be redirecting to the default action of the current 
        // controller
        $rdr_path = $this->makeLink("{$this->controller_name_from_uri}");
        
        if ( $record->delete() === false ) {
            
            // Delete operation was not successful. Set error message.
            $this->setErrorFlashMessage('Could not Delete Record!');
            
        } else {
            
            // Delete operation was successful. Set success message.
            $this->setSuccessFlashMessage('Successfully Deleted!');
        }
        
        // Redirect to the default action of the current controller
        return $this->response->withStatus(302)->withHeader('Location', $rdr_path);
    }
```

At this point, our feature to delete a specific user is completed and can be 
accessed at `http://localhost:8888/users/delete/<some_num>` (**`<some_num>`** 
should be replaced with a numeric id of a user; e.g. 
http://localhost:8888/users/delete/2 to delete a specific user with an id of 2.

Yipee! We have implemented all the earlier specified features for managing users
in the **\MovieCatalog\Controllers\Users** controller; i.e.:

- Initializing the app with an **admin** user via **actionInitUsers($password)** (if no user exists)
- Listing all users via **actionIndex()**
- Viewing a single user via **actionView($id)**
- Adding a new user via **actionAdd()**
- Editing an existing user via **actionEdit($id)**
- Deleting a specific user via **actionDelete($id)**

Now we move on to implementing features necessary for managing movie listing in 
the **\MovieCatalog\Controllers\MovieListings** controller.  

Let's implement the action method to list all movies; i.e. **actionIndex()**. 
To do this, update **actionIndex()** in **\MovieCatalog\Controllers\MovieListings** 
with the code below:

```php    
    public function actionIndex() {
        
        $view_data = [];
        $model_obj = $this->getContainerItem(
            \MovieCatalog\Models\MoviesListings\MoviesListingsModel::class
        );
        
        // Grab all existing movie records.
        // Note that the variable $collection_of_movie_records will be available
        // in your index.php view (in this case ./src/views/movie-listings/index.php)
        // when $this->renderView('index.php', $view_data) is called.
        $view_data['collection_of_movie_records'] = 
                                    $model_obj->fetchRecordsIntoCollection();
        
        //render the view first and capture the output
        $view_str = $this->renderView('index.php', $view_data);
        
        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

We've implemented the controller portion of the feature to list all movies. 
Now let's implement the view portion of the feature by adding the code below
to **./src/views/movie-listings/index.php**:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
    use \MovieCatalog\Models\Collections\BaseCollection;
?>
<?php if( $controller_object->isLoggedIn() ): ?>

    <div class="row" style="margin-top: 1em;">
        <div class="col s6">
            <h4>All Movies</h4>
        </div>
        <div class="col s6  right-align">
            <a class="btn" href="<?php echo $controller_object->makeLink( "movie-listings/add" ); ?>">
                <strong>+ Add new Movie Listing</strong>
            </a>
        </div>
    </div>

<?php endif; ?>

<?php if( $collection_of_movie_records instanceof BaseCollection && count($collection_of_movie_records) > 0 ): ?>

    <ul>
    <?php foreach ($collection_of_movie_records as $movie_record): ?>

        <li>
            <?php echo $movie_record->title; ?> | 
            <a href="<?php echo $controller_object->makeLink( "movie-listings/view/" . $movie_record->id ); ?>">View</a> 

            <?php if( $controller_object->isLoggedIn() ): ?>

                | <a href="<?php echo $controller_object->makeLink( "movie-listings/edit/" . $movie_record->id ); ?>">Edit</a> |
                <a href="<?php echo $controller_object->makeLink( "movie-listings/delete/" . $movie_record->id ); ?>"
                   onclick="return confirm('Are you sure?');"
                >
                    Delete
                </a>

            <?php endif; //if( isset($is_logged_in) && $is_logged_in )  ?>
        </li>

    <?php endforeach; ?>
    </ul>

<?php else: ?>

<p>
    No Movies yet. Please <a href="<?php echo $controller_object->makeLink( "movie-listings/add" ); ?>">Add</a> 
    one or more movie listing(s).
</p>

<?php endif; ?>
```

Now our feature to list all movies is completed and can be accessed at 
http://localhost:8888/movie-listings or http://localhost:8888/movie-listings/index .

Let's now implement the action method to view a single movie; i.e. **actionView($id)**. 
To do this, we add **actionView($id)** to **\MovieCatalog\Controllers\MovieListings** 
with the code below:

```php
    public function actionView($id) {
        
        $model_obj = $this->container->get('movie_listings_model');
        $view_data = [];
        
        // Grab record for the movie whose id was specified.
        // Note that the variable $movie_record will be available
        // in your view.php view (in this case ./src/views/movie-listings/view.php)
        // when $this->renderView('view.php', $view_data) is called.
        $view_data['movie_record'] = $model_obj->fetch($id);
        
        if( !($view_data['movie_record'] instanceof \BaseRecord) ) {
            
            // We could not find any movie with the specified id in the database.
            // Generate and return an http 404 resposne.
            return $this->generateNotFoundResponse(
                            $this->request, 
                            $this->response,
                            'Requested movie does not exist.',
                            'Requested movie does not exist.'
                        );
        }
        
        //get the contents of the view first
        $view_str = $this->renderView('view.php', $view_data);

        return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
    }
```

We've implemented the controller portion of the feature to view a single movie. 
Now let's implement the view portion of the feature by creating a **view.php** 
file in **./src/views/movie-listings/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4>View Movie</h4>
<ul style="list-style: none;">
    <li>
        <strong>Title:</strong> 
        <?= $movie_record->title; ?>
    </li>
    <li>
        <strong>Year of Release:</strong> 
        <?= $movie_record->release_year; ?>
    </li>
    <li>
        <strong>Genre:</strong> 
        <?= $movie_record->genre; ?>
    </li>
    <li>
        <strong>Duration in Minutes:</strong> 
        <?= $movie_record->duration_in_minutes; ?>
    </li>
    <li>
        <strong>MPAA Rating:</strong> 
        <?= $movie_record->mpaa_rating; ?>
    </li>
    <li>
        <strong>Date Created:</strong> 
        <?= $movie_record->getDateCreated(); ?>
    </li>
    <li>
        <strong>Date Last Modified:</strong> 
        <?= $movie_record->getLastModfiedDate(); ?>
    </li>
</dl>
<p>
    <a href="<?= $controller_object->makeLink( "movie-listings/index" ); ?>">View all Movies</a>
    <?php if( $controller_object->isLoggedIn() ): ?>

        | <a href="<?= $controller_object->makeLink( "movie-listings/edit/" . $movie_record->id ); ?>">Edit</a> |
        <a href="<?= $controller_object->makeLink( "movie-listings/delete/" . $movie_record->id ); ?>">Delete</a>

    <?php endif;  ?>
</p>
```

Now our feature to view a movie is completed and can be accessed at 
`http://localhost:8888/movie-listings/view/<some_num>` (**`<some_num>`** should 
be replaced with a numeric id of a movie; e.g. http://localhost:8888/movie-listings/view/2 
to view details of a single movie with an id of 2. At this point there is no movie in the database 
so there's nothing to view yet. 

Next, we implement the action method to add a new movie; i.e. **actionAdd()**. 
To do this, we add **actionAdd()** to **\MovieCatalog\Controllers\MovieListings** 
with the code below:

```php
    public function actionAdd() {
        
        // The call below is to get a response object for
        // redirecting the user to the login page if the
        // user is not currently logged in. You must be 
        // logged in in order to be able to add a new movie.
        // If the user is logged in, $login_response will
        // receive a value of false from
        // $this->getResponseObjForLoginRedirectionIfNotLoggedIn().
        $login_response = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if( $login_response instanceof \Psr\Http\Message\ResponseInterface ) {
            
            // redirect to login page
            return $login_response;
        }
        
        /** @var \MovieCatalog\Models\MoviesListings\MoviesListingsModel $model_obj */
        $model_obj = $this->getContainerItem(
            \MovieCatalog\Models\MoviesListings\MoviesListingsModel::class
        );
        $error_msgs = [];
        $error_msgs['form-errors'] = [];
        $error_msgs['title'] = []; // cannot be blank
        $error_msgs['release_year'] = []; // cannot be blank
        
        // create an associative array with keys being the names of the columns in the 
        // db table associated with the model and a default value of '' for each item 
        // in the array
        $default_data = $model_obj->getDefaultColVals();
        
        // remove item whose key is primary key column name
        // since primary key values are auto-generated
        unset($default_data[$model_obj->getPrimaryColName()]);
        
        // this is an integer field in the db
        $default_data['duration_in_minutes'] = '0'; 

        // create a new record with the default data generated above
        $record = $model_obj->createNewRecord($default_data);
        
        if( $this->request->getMethod() === 'POST' ) {
            
            // POST Request
            $has_field_errors = false;
            
            // Read the post data
            $posted_data = sMVC_GetSuperGlobal('post');
            
            if( mb_strlen( ''.$posted_data['title'], 'UTF-8') <= 0 ) {
                
                $error_msgs['title'][] = 'Title cannot be blank!';
                $has_field_errors = true;
            }
            
            if( mb_strlen( ''.$posted_data['release_year'], 'UTF-8') <= 0 ) {
                
                $error_msgs['release_year'][] = 'Year of Release cannot be blank!';
                $has_field_errors = true;
            }
     
            //load posted data into new record object
            $record->loadData($posted_data);

            if ( !$has_field_errors ) {
                
                // try to save
                if ( $record->save() !== false ) {

                    //successfully saved;
                    $rdr_path = $this->makeLink("movie-listings/index");
                    $this->setSuccessFlashMessage('Successfully Saved!');

                    // re-direct to the list all movies page
                    return $this->response
                                ->withStatus(302)
                                ->withHeader('Location', $rdr_path);
                    
                } else {

                    //Record could not be saved.
                    $error_msgs['form-errors'][] = 'Save Failed!';
                } // if ( $record->save() !== false ) 
                
            } else {
                
                $error_msgs['form-errors'][] = 'Form contains error(s)!';
            } // if ( !$has_field_errors )
                
        } //if( $this->request->getMethod() === 'POST' )

        $view_data = [];
        $view_data['error_msgs'] = $error_msgs;
        $view_data['movie_record'] = $record;
        
        $view_str = $this->renderView('add.php', $view_data);
        
        return $this->renderLayout('main-template.php', ['content'=>$view_str]);
    }
```

We've implemented the controller portion of the feature to add a new movie. 
Now let's implement the view portion of the feature by creating an **add.php** 
file in **./src/views/movie-listings/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4 style="margin-bottom: 20px;">Add New Movie</h4>

<form method="POST" 
      action="<?= $controller_object->makeLink("movie-listings/add"); ?>" 
      enctype="multipart/form-data"
>

<?php printErrorMsg('form-errors', $error_msgs); //print form level error message(s) if any ?>

    <div class="row" id="row-title">
        <div class="col s3 right-align">
            <label for="title" class="">
                Title<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="text" 
                   name="title" 
                   id="title" 
                   maxlength="1500" 
                   required="required"
                   value="<?= $movie_record->title; ?>"
            >
            <?php printErrorMsg('title', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-release_year">
        <div class="col s3 right-align">
            <label for="release_year" class="">
                Year of Release<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="number"
                   name="release_year" 
                   id="release_year"
                   maxlength="4"
                   min="1"
                   required="required"
                   value="<?= $movie_record->release_year; ?>"
            >
            <?php printErrorMsg('release_year', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-genre">
        <div class="col s3 right-align">
            <label for="genre" class="">
                Genre
            </label>                
        </div>
        
        <div class="col s7">
            <input type="text"
                   name="genre" 
                   id="genre" 
                   maxlength="255"
                   value="<?= $movie_record->genre; ?>"
            >
        </div>
    </div>
    
    <div class="row" id="row-duration_in_minutes">
        <div class="col s3 right-align">
            <label for="duration_in_minutes" class="">
                Duration in Minutes
            </label>                
        </div>
        
        <div class="col s7">
            <input type="number" 
                   name="duration_in_minutes" 
                   id="duration_in_minutes"
                   min="0"
                   value="<?= $movie_record->duration_in_minutes; ?>"
            >
        </div>
    </div>
    
    <div class="row" id="row-mpaa_rating">
        <div class="col s3 right-align">
            <label for="mpaa_rating" class="">
                MPAA Rating
            </label>                
        </div>
        
        <div class="col s7">
            
            <?php $selected = 'selected="selected"'; ?>
            
            <select name="mpaa_rating" id="mpaa_rating">
                
                <option value="">----None----</option>
                
                <option value="G" title="General Audiences" 
                        <?= ($movie_record->mpaa_rating === "G") ? $selected : '' ; ?> 
                >
                    G
                </option>
                
                <option value="NR" title="Not Rated"
                        <?= ($movie_record->mpaa_rating === "NR") ? $selected : '' ; ?> 
                >
                    NR
                </option>
                
                <option value="PG" title="Parental Guidance"
                        <?= ($movie_record->mpaa_rating === "PG") ? $selected : '' ; ?> 
                >
                    PG
                </option>
                
                <option value="PG-13" title="Parents Strongly Cautioned"
                        <?= ($movie_record->mpaa_rating === "PG-13") ? $selected : '' ; ?> 
                >
                    PG-13
                </option>
                
                <option value="R" title="Restricted"
                        <?= ($movie_record->mpaa_rating === "R") ? $selected : '' ; ?> 
                >
                    R
                </option>
                
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col s3">
            <input type="submit" 
                   name="save-button" 
                   id="save-button" 
                   class="button" 
                   value="Save"
            >
            <input type="submit" 
                   name="cancel-button" 
                   id="cancel-button" 
                   class="button" 
                   value="Cancel"
            >
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems, {});
    });
    
    // When Cancel button is clicked, redirect to list all users page
    document.getElementById('cancel-button').addEventListener('click', function(event) {

        // Do this so that when the Cancel button is clicked 
        // the browser does not try to submit the form
        event.preventDefault(); 
        window.location.href = '<?= $controller_object->makeLink("/movie-listings/index"); ?>';
    });
</script>

<?php
function printErrorMsg($element_name, array $error_msgs) {

    if( isset($error_msgs[$element_name]) ) {

        foreach($error_msgs[$element_name] as $err_msg) {

            //spit out error message for $element_name
            echo "<div style=\"padding: 0.5em;\" class=\"card red\">{$err_msg}</div>";

        } //foreach($error_msgs[$element_name] as $err_msg)
    } //if( array_key_exists($element_name, $error_msgs) )
}
```

Now our feature to add a new movie is completed and can be accessed at 
http://localhost:8888/movie-listings/add.

Let's now implement the action method to edit an existing movie; i.e. **actionEdit($id)**. 
To do this, we add **actionEdit($id)** to **\MovieCatalog\Controllers\MovieListings** with 
the code below:

```php
    public function actionEdit($id) {
        
        // The call below is to get a response object for
        // redirecting the user to the login page if the
        // user is not currently logged in. You must be 
        // logged in in order to be able to edit an 
        // existing movie. If the user is logged in, 
        // $login_response will receive a value of 
        // false from $this->getResponseObjForLoginRedirectionIfNotLoggedIn().
        $login_response = $this->getResponseObjForLoginRedirectionIfNotLoggedIn();
        
        if( $login_response instanceof \Psr\Http\Message\ResponseInterface ) {
            
            // redirect to login page
            return $login_response;
        }
        
        /** @var \MovieCatalog\Models\MoviesListings\MoviesListingsModel $model_obj */
        $model_obj = $this->getContainerItem(
            \MovieCatalog\Models\MoviesListings\MoviesListingsModel::class
        );
        $error_msgs = [];
        $error_msgs['form-errors'] = [];
        $error_msgs['title'] = [];
        $error_msgs['release_year'] = [];
        
        // fetch the record for the movie with the specified $id
        $record = $model_obj->fetchOneByPkey($id);
        
        if( !($record instanceof \MovieCatalog\Models\Records\BaseRecord) ) {
            
            // Could not find record for the movie with the specified $id
            $this->forceHttp404('Requested movie does not exist.');
        }
        
        if( $this->request->getMethod() === 'POST' ) {
            
            // POST Request
            $has_field_errors = false;
            
            // Read the post data
            $posted_data = sMVC_GetSuperGlobal('post');
            
            if( mb_strlen( ''.$posted_data['title'], 'UTF-8') <= 0 ) {
                
                $error_msgs['title'][] = 'Title Cannot be blank!';
                $has_field_errors = true;
            }
            
            if( mb_strlen( ''.$posted_data['release_year'], 'UTF-8') <= 0 ) {
                
                $error_msgs['release_year'][] = 'Year of Release Cannot be blank!';
                $has_field_errors = true;
            }
     
            //load posted data into new record object
            $record->loadData($posted_data);

            if ( !$has_field_errors ) {
                
                // try to save
                if ( $record->save() !== false ) {

                    //successfully saved;
                    $rdr_path = $this->makeLink("movie-listings/index");
                    $this->setSuccessFlashMessage('Successfully Saved!');

                    // re-direct to the list all movies page
                    return $this->response->withStatus(302)->withHeader('Location', $rdr_path);
                    
                } else {

                    //Record could not be saved.
                    $error_msgs['form-errors'][] = 'Save Failed!';
                } // if ( $record->save() !== false ) 
                
            } else {
                
                $error_msgs['form-errors'][] = 'Form contains error(s)!';
            } // if ( !$has_field_errors )
                
        } //if( $this->request->getMethod() === 'POST' )

        $view_data = [];
        $view_data['movie_record'] = $record;
        $view_data['error_msgs'] = $error_msgs;
        
        $view_str = $this->renderView('edit.php', $view_data);
        
        return $this->renderLayout('main-template.php', ['content'=>$view_str]);
    }
```

We've implemented the controller portion of the feature to edit an existing movie. 
Now let's implement the view portion of the feature by creating an **edit.php** 
file in **./src/views/movie-listings/** and adding the code below to it:

```php
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
<h4 style="margin-bottom: 20px;">Edit Movie</h4>

<form method="POST" 
      action="<?= $controller_object->makeLink("movie-listings/edit/{$movie_record->id}"); ?>" 
      enctype="multipart/form-data"
>

<?php printErrorMsg('form-errors', $error_msgs); //print form level error message(s) if any ?>

    <div class="row" id="row-title">
        <div class="col s3 right-align">
            <label for="title" class="">
                Title<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="text" 
                   name="title" 
                   id="title" 
                   maxlength="1500" 
                   required="required"
                   value="<?= $movie_record->title; ?>"
            >
            <?php printErrorMsg('title', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-release_year">
        <div class="col s3 right-align">
            <label for="release_year" class="">
                Year of Release<span style="color: red;"> *</span>
            </label>                
        </div>
        
        <div class="col s7">
            <input type="number"
                   name="release_year" 
                   id="release_year"
                   maxlength="4"
                   min="1"
                   required="required"
                   value="<?= $movie_record->release_year; ?>"
            >
            <?php printErrorMsg('release_year', $error_msgs); //print error message(s) if any ?>
        </div>
    </div>
    
    <div class="row" id="row-genre">
        <div class="col s3 right-align">
            <label for="genre" class="">
                Genre
            </label>                
        </div>
        
        <div class="col s7">
            <input type="text"
                   name="genre" 
                   id="genre" 
                   maxlength="255"
                   value="<?= $movie_record->genre; ?>"
            >
        </div>
    </div>
    
    <div class="row" id="row-duration_in_minutes">
        <div class="col s3 right-align">
            <label for="duration_in_minutes" class="">
                Duration in Minutes
            </label>                
        </div>
        
        <div class="col s7">
            <input type="number" 
                   name="duration_in_minutes" 
                   id="duration_in_minutes"
                   min="0"
                   value="<?= $movie_record->duration_in_minutes; ?>"
            >
        </div>
    </div>
    
    <div class="row" id="row-mpaa_rating">
        <div class="col s3 right-align">
            <label for="mpaa_rating" class="">
                MPAA Rating
            </label>                
        </div>
        
        <div class="col s7">
            
            <?php $selected = 'selected="selected"'; ?>
            
            <select name="mpaa_rating" id="mpaa_rating">
                
                <option value="">----None----</option>
                
                <option value="G" title="General Audiences" 
                        <?= ($movie_record->mpaa_rating === "G") ? $selected : '' ; ?> 
                >
                    G
                </option>
                
                <option value="NR" title="Not Rated"
                        <?= ($movie_record->mpaa_rating === "NR") ? $selected : '' ; ?> 
                >
                    NR
                </option>
                
                <option value="PG" title="Parental Guidance"
                        <?= ($movie_record->mpaa_rating === "PG") ? $selected : '' ; ?> 
                >
                    PG
                </option>
                
                <option value="PG-13" title="Parents Strongly Cautioned"
                        <?= ($movie_record->mpaa_rating === "PG-13") ? $selected : '' ; ?> 
                >
                    PG-13
                </option>
                
                <option value="R" title="Restricted"
                        <?= ($movie_record->mpaa_rating === "R") ? $selected : '' ; ?> 
                >
                    R
                </option>
                
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col s3">
            <input type="submit" 
                   name="save-button" 
                   id="save-button" 
                   class="button" 
                   value="Save"
            >
            <input type="submit" 
                   name="cancel-button" 
                   id="cancel-button" 
                   class="button" 
                   value="Cancel"
            >
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems, {});
    });
    
    // When Cancel button is clicked, redirect to list all users page
    document.getElementById('cancel-button').addEventListener('click', function(event) {

        // Do this so that when the Cancel button is clicked 
        // the browser does not try to submit the form
        event.preventDefault(); 
        window.location.href = '<?= $controller_object->makeLink("/movie-listings/index"); ?>';
    });
</script>

<?php
function printErrorMsg($element_name, array $error_msgs) {

    if( isset($error_msgs[$element_name]) ) {

        foreach($error_msgs[$element_name] as $err_msg) {

            //spit out error message for $element_name
            echo "<div style=\"padding: 0.5em;\" class=\"card red\">{$err_msg}</div>";

        } //foreach($error_msgs[$element_name] as $err_msg)
    } //if( array_key_exists($element_name, $error_msgs) )
}
```

Now our feature to edit an existing movie is completed and can be accessed at 
`http://localhost:8888/movie-listings/edit/<some_num>` (**`<some_num>`** should be 
replaced with a numeric id of a movie; e.g. http://localhost:8888/movie-listings/edit/2 
to edit an existing movie with an id of 2).

We are now going to implement the last feature for managing movies (i.e. the 
ability to delete a specific movie) by adding the code below to 
**\MovieCatalog\Controllers\MovieListings**:

```php
    public function actionDelete($id) {
        
        return $this->doDelete(
            $id,
            \MovieCatalog\Models\MoviesListings\MoviesListingsModel::class
        );
    }
```

We are done, we have successfully implemented all the features required to manage 
movie listings and users in our app. Obviously, other features like searching, e.t.c. 
can be implemented to further enhance the app.

To return a list of all movies in **json** format, you can update **actionIndex()** 
in **\MovieCatalog\Controllers\MovieListings** with the code below and add a **format** 
parameter with the value of **json** to the url like so 
(http://localhost:8888/movie-listings?format=json)

```php
    public function actionIndex() {
        
        $view_data = [];
        /** @var \MovieCatalog\Models\MoviesListings\MoviesListingsModel $model_obj */
        $model_obj = $this->getContainerItem(
            \MovieCatalog\Models\MoviesListings\MoviesListingsModel::class
        );
        
        // Grab all existing movie records.
        // Note that the variable $collection_of_movie_records will be available
        // in your index.php view (in this case ./src/views/movie-listings/index.php)
        // when $this->renderView('index.php', $view_data) is called.
        $view_data['collection_of_movie_records'] = 
                                    $model_obj->fetchRecordsIntoCollection();
        
        $response_format = sMVC_GetSuperGlobal('get', 'format', null);
        
        if( 
            $response_format !== null
            && !in_array( trim(mb_strtolower( ''.$response_format, 'UTF-8')), ['html', 'xhtml'] )
        ) {
            //handle other specified formats (non-html)
            if ( trim(mb_strtolower(''.$response_format, 'UTF-8')) === 'json' ) {
                
                // return response in json format
                $movie_listings_array = [];
                
                if( 
                    $view_data['collection_of_movie_records'] instanceof \MovieCatalog\Models\Collections\BaseCollection 
                    && count($view_data['collection_of_movie_records']) > 0 
                ) {
                    //convert collection of movie_listings records to an array of arrays
                    foreach ($view_data['collection_of_movie_records'] as $record) {

                        // $record->getData() gets the underlying associative array 
                        // containing a record's data
                        $movie_listings_array[] = $record->getData();
                    }
                }
                
                $this->response
                     ->getBody()
                     ->write($json = json_encode($movie_listings_array));

                // Ensure that the json encoding passed successfully
                if ($json === false) {
                    
                    throw new \RuntimeException(json_last_error_msg(), json_last_error());
                }
                
                return $this->response
                            ->withStatus(302)
                            ->withHeader('Content-Type', 'application/json;charset=utf-8');
                
            } else {
                
                // Unknown format specified, generate an error page
                $msg = "Unknown format `$response_format` specified";
                $this->forceHttp404($msg);
            }
            
        } else {
        
            //render the view first and capture the output
            $view_str = $this->renderView('index.php', $view_data);

            return $this->renderLayout( $this->layout_template_file_name, ['content'=>$view_str] );
        }
    }
```


The repository containing all the code we wrote above for this application can be found [here](https://github.com/rotexsoft/movie-catalog-web-app). Cheers!
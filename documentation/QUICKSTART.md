# Quick Start Guide

## Installation (Creating a Project)

* Get the most recent development version (creates the project from the master branch in the repo)

  **`$ composer create-project -n -s dev rotexsoft/slim3-skeleton-mvc-app my-app`**

* Get the most stable version (creates the project from the most recent tagged release in the repo)

  **`$ composer create-project -n rotexsoft/slim3-skeleton-mvc-app my-app`**

### Testing the Installation

1. Change directory to your new application's folder / directory by executing the command below:

    > **`cd my-app`**

2. Specify where the web-server will save your application's php session files by adding the line below to **`./config/ini-settings.php`** file (NOTE that **'/path/to/a/writable/folder'** should be replaced with a path to a folder that is writable by your web-server):

    > ` ini_set('session.save_path', '/path/to/a/writable/folder'); `

3. Now run this command to run the built-in php development server:

    > **`php -S 0.0.0.0:8888 -t public`**

4. You can then go on to browse to [http://localhost:8888](http://localhost:8888) (your new application's default home-page)

    * **Automatic routing scheme for mapping request urls to methods in Controller classes that are sub-classes of Slim3MvcTools\Controllers\BaseController:** urls in the form of
        > `http(s)://server[:port][/][<base-path>/][<controller-name>][/<method-name>][/param1]..[/paramN]`
		
        can be automatically mapped to be responded to by a specific method in a Controller class, if **`S3MVC_APP_USE_MVC_ROUTES`** is set to **`true`** in **`./public/index.php`**. Note that items enclosed in `[]` in the url scheme above are optional.

        * **`<base-path>`:** this is usually the alias setup in your webserver's configuration file that points to your site's document root folder  (in this case **`./public`**). For example in an apache web-server's configuration file you could have an alias definition like so:
            > Alias /my-app /path/to/my-app/public
        
            making your application accessible via `http://server/my-app/` or `https://server/my-app/` (if using SSL), where **my-app** is the value of **`<base-path>`** in this example. The **`<base-path>`** section of the url is optional since your web-server's main document root could be directly set to your site's document root folder (i.e. **`./public`**), as seen in step **2** above where the **php** development server is used.

    * Below are the default links that are available upon installation:

        * [http://localhost:8888/base-controller/action-index/](http://localhost:8888/base-controller/action-index/) same as [http://localhost:8888/base-controller/](http://localhost:8888/base-controller/)
            * This link is mapped to **`\Slim3MvcTools\Controllers\BaseController::actionIndex()`** under the hood

        * [http://localhost:8888/base-controller/action-login/](http://localhost:8888/base-controller/action-login/) comes with 2 default accounts **admin:admin** and **root:root**
            * This link is mapped to **`\Slim3MvcTools\Controllers\BaseController::actionLogin()`** under the hood

        * [http://localhost:8888/base-controller/action-logout/0](http://localhost:8888/base-controller/action-logout/0)
            * This link is mapped to **`\Slim3MvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/base-controller/action-logout/1](http://localhost:8888/base-controller/action-logout/1)
            * This link is mapped to **`\Slim3MvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/base-controller/action-login-status/](http://localhost:8888/base-controller/action-login-status/)
            * This link is mapped to **`\Slim3MvcTools\Controllers\BaseController::actionLoginStatus()`** under the hood

        * [http://localhost:8888/hello/action-index/](http://localhost:8888/hello/action-index/) same as [http://localhost:8888/hello/](http://localhost:8888/hello/)
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionIndex()`** under the hood

        * [http://localhost:8888/hello/action-login/](http://localhost:8888/hello/action-login/) comes with 2 default accounts **admin:admin** and **root:root**
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionLogin()`** under the hood

        * [http://localhost:8888/hello/action-logout/0](http://localhost:8888/hello/action-logout/0)
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/hello/action-logout/1](http://localhost:8888/hello/action-logout/1)
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/hello/action-login-status/](http://localhost:8888/hello/action-login-status/)
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionLoginStatus()`** under the hood

        * `http://localhost:8888/hello/action-there/{first_name}/{last_name}`
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionThere($first_name, $last_name)`** under the hood
            * you can do stuff like [http://localhost:8888/hello/action-there/john/doe](http://localhost:8888/hello/action-there/john/doe)

        * `http://localhost:8888/hello/action-world/{name}/{another_parameter}`
            * This link is mapped to **`\Slim3SkeletonMvcApp\Controllers\Hello::actionWorld($name, $another_param)`** under the hood
            * you can do stuff like [http://localhost:8888/hello/action-world/john/doe](http://localhost:8888/hello/action-world/john/doe)

    * The **`action-`** prefix can be omitted from the links above if **`S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES`** is set to **`true`**
        * For example [http://localhost:8888/hello/action-login/](http://localhost:8888/hello/action-login/) will become [http://localhost:8888/hello/login/](http://localhost:8888/hello/login/) and [http://localhost:8888/hello/action-there/john/doe](http://localhost:8888/hello/action-there/john/doe) will become [http://localhost:8888/hello/there/john/doe](http://localhost:8888/hello/there/john/doe)

5. You may need to modify the **`RewriteBase`** directive in the **`public/.htaccess`** file, if you are using aliases in your apache web server and are getting 404 errors

## Key Directories and Configuration
* **`config`:** Contains files for configuring the application
 
* **`logs`:** Log files

* **`public`:** Webserver root

* **`public/css`:** Your application's css files should be placed here 

* **`public/image`:** Your application's image files should be placed here

* **`public/js`:** Your application's javascript files should be placed here

* **`src/controllers`:** Your application's controller classes should be placed here

* **`src/layout-templates`:** The layout template(s) for your application should be placed here

* **`src/models`:** Your application's model classes should be placed here

* **`src/views`:** Should contain view files (associated with each of your controller classes' action) that should be rendered into your application's layout template(s)

* **`tests`:** Place files for testing your application (eg. PHPUnit test cases) here

* **`tmp`:** Temporary files generated by your application (like session files) should be placed here

* **`vendor`:** Composer dependencies

## Key Files and Configuration
* **`composer.json`:** contains your application's composer dependencies

* **`README.md`:** Add documentation for your application here.

* **`config/app-settings.php`:** Add settings that should be passed to the constructor of the `Slim\App` object instantiated in `public/index.php` and also other environment specific settings needed by your application (like database credentials, etc.), here. These settings will be accessible via the **$container->get('settings')** entry in the container object for your application. See https://www.slimframework.com/docs/objects/application.html#application-configuration for more information on Slim related settings. 

    * This file should not be committed to version control (it has already been added (by default) to the `.gitignore` file for your application if you are using **git** for version control). Instead, it should be created by making a copy of **`config/app-settings-dist.php`** and then configured uniquely for each environment your application is to be deployed to.


* **`config/app-settings-dist.php`:** A template file for creating **`config/app-settings.php`** in new environments your application will be deployed to. It should be version controlled. Store dummy values for sensitive settings, like database credentials for your application, in this file.

* **`config/dependencies.php`:** Add dependencies to SlimPHP3's dependency injection container (i.e. Pimple) here.

    * Below are the objects that are registered in the container:

        * **`errorHandler:`** An anonymous function that handles all uncaught PHP exceptions in your application. See http://www.slimframework.com/docs/handlers/error.html for more details.

        * **`errorHandlerClass:`** Name of controller class (must be a sub-class of **\\Slim3MvcTools\\Controllers\\BaseController**) that will be used by the **errorHandler** anonymous function to handle http 500 errors. Has a default value of **'\\Slim3MvcTools\\Controllers\\HttpServerErrorController'**. MUST be set if you have a base controller for your application that implements **preAction()** and / or **postAction(...)**

        * **`notFoundHandler:`** An anonymous function that handles all request urls that do not match any of the routes defined in your application (i.e. in **`public/index.php`** or **`config/routes-and-middlewares.php`**). See http://www.slimframework.com/docs/handlers/not-found.html for more details. 

            * The handler for this framework is slightly different from the pure Slim 3 one in that it adds two additional optional parameters in addition to the request and response parameters specified in the Slim 3 framework's default handler:
            ```php
            <?php
                function (
                    \Psr\Http\Message\ServerRequestInterface $request, 
                    \Psr\Http\Message\ResponseInterface $response,
                    $_404_page_contents_str = null,
                    $_404_page_additional_log_msg = null
                )
            ?>
            ```
        * **`notFoundHandlerClass:`** Name of controller class (must be a sub-class of **\\Slim3MvcTools\\Controllers\\BaseController**) that will be used by the **notFoundHandler** anonymous function to handle http 404 errors. Has a default value of **'\\Slim3MvcTools\\Controllers\\HttpNotFoundController'**. MUST be set if you have a base controller for your application that implements **preAction()** and / or **postAction(...)**

        * **`notAllowedHandler:`** An anonymous function that handles all requests whose **HTTP Request Method** does not match any of the **HTTP Request Methods** associated with the routes defined in your application (i.e. in **`public/index.php`** or **`config/routes-and-middlewares.php`**). See http://www.slimframework.com/docs/handlers/not-allowed.html for more details.

        * **`notAllowedHandlerClass:`** Name of controller class (must be a sub-class of **\\Slim3MvcTools\\Controllers\\BaseController**) that will be used by the **notAllowedHandler** anonymous function to handle http 405 errors. Has a default value of **'\\Slim3MvcTools\\Controllers\\HttpMethodNotAllowedController'**. MUST be set if you have a base controller for your application that implements **preAction()** and / or **postAction(...)**

        * **`logger:`** Any PSR-3 compliant logger (PSR-3 strongly recommended: HTTP 404, 405 & 500 error handlers in **\\Slim3MvcTools\\Controllers\\BaseController** rely on this), that can be used for logging in your application. See https://bitbucket.org/jelofson/vespula.log for more details on how to configure this logger to suit your application's needs.

            ```php
            <?php
                //You can access the logger from within your controller like so:
                $this->container->get('logger');
            ?>
            ```

        * **`namespaces_for_controllers:`** An array containing a list of the namespaces that your application's controller classes belong to. If all your controllers are in the global namespace, then you don't need to update **`namespaces_for_controllers`**. The default namespaces that ship with this package are **`'\\Slim3MvcTools\\Controllers\\'`** (the namespace where **`BaseController`** belongs) and **`'\\Slim3SkeletonMvcApp\\Controllers\\'`** (the namespace where **`Hello`** belongs).  
            
            * You still need to make sure that autoloading is properly configured in **./composer.json**. The **./composer.json** that ships with this framework uses the **classmap** method in the **autoload** section of **./composer.json** (meaning that you have to run the **`composer dumpautoload`** command each time you add a new class file to your **./src** folder). You can decide to use the **PSR-4** directive in the **autoload** section of your application's **./composer.json**.

        * **`new_layout_renderer:`** An object used for rendering layout-template(s) for your application (see the **`renderLayout`** method in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**). See https://github.com/rotexsoft/file-renderer for more details on how to configure this object.

            ```php
            <?php
                // You can access this renderer from within your controller methods like so:
                $this->layout_renderer; // it is automatically set as a property of the controller 
                                        // object, as long as your controller object extends
                                        // \Slim3MvcTools\Controllers\BaseController.

                // You can also access this renderer from within your controller methods like so:
                $this->container->get('new_layout_renderer'); // keep in mind that accessing it like 
                                                              // this returns a new instance with 
                                                              // each call.

                // There is also a helper method available in all your controllers that
                // extend \Slim3MvcTools\Controllers\BaseController called renderLayout 
                // via which you can interact with $this->layout_renderer
            ?>
            ```

        * **`new_view_renderer:`** An object used for rendering view file(s) associated with each action method in the controller(s) for your application (see the **`renderView`** method in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**). See https://github.com/rotexsoft/file-renderer for more details on how to configure this object.

            ```php
            <?php
                // You can access this renderer from within your controller methods like so:
                $this->view_renderer; // it is automatically set as a property of the controller 
                                      // object, as long as your controller object extends
                                      // \Slim3MvcTools\Controllers\BaseController.

                // You can also access this renderer from within your controller methods like so:
                $this->container->get('new_view_renderer'); // keep in mind that accessing it like
                                                            // this returns a new instance with 
                                                            // each call.

                // There is also a helper method available in all your controllers that
                // extend \Slim3MvcTools\Controllers\BaseController called renderView 
                // via which you can interact with $this->view_renderer
            ?>
            ```

        * **`vespula_auth:`** An object used by the **`BaseController`** to implement authentication functionality (see the **`isLoggedIn`**, **`actionLogin`**, **`actionLogout`** and **`actionLoginStatus`** methods in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**). See https://bitbucket.org/jelofson/vespula.auth for more details on how to configure this object.

            ```php
            <?php
                //You can access the auth object from within your controller like so:
                $this->container->get('vespula_auth');
            ?>
            ```

* **`config/env.php`:** Edit it to define your application's environment. It should return one of **S3MVC_APP_ENV_DEV**, **S3MVC_APP_ENV_PRODUCTION**, **S3MVC_APP_ENV_STAGING** or **S3MVC_APP_ENV_TESTING** relevant to the environment you are installing your web-application.

* **`config/ini-settings.php`:** Modify ini settings via **`ini_set(..)`** here. Remember to update **`date.timezone`** in this file to match your timezone (see http://php.net/manual/en/timezones.php).

* **`config/routes-and-middlewares.php`:** Add additional routes and middlewares (see https://www.slimframework.com/docs/concepts/middleware.html for more information on middlewares) for your application here (if needed). You can decide to define all the routes for your application here (in this case set the **S3MVC_APP_USE_MVC_ROUTES** constant in **`public/index.php`** to false). A default **`/`** route is defined in this file and will be active if **S3MVC_APP_USE_MVC_ROUTES** has a value of **`false`**.

* **`public/.htaccess`:** Apache web-server settings.

* **`public/index.php`:** Entry point to application.

	* **Figure 1: Overview of the index.php file** ![Overview of the index.php file](../index.php-overview.png)

    * Below are some constants (some of which you may edit to suit your needs) and functions defined in this file (i.e. **`public/index.php`**):

        * **`S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES:`** A boolean value. If true, the string **`'action'`** will be prepended to action method names (if the method name does not already start with the string **`'action'`**). The resulting method name will be converted to camel case before being executed. If false, then action method names will only be converted to camel case before being executed. This setting does not apply to **`S3MVC_APP_DEFAULT_ACTION_NAME`**. It only applies to the following routes **`'/{controller}/{action}[/{parameters:.+}]'`** and **`'/{controller}/{action}/'`**.

        * **`S3MVC_APP_DEFAULT_ACTION_NAME:`** A string value. This is the name of the action or method to be called on the default controller to handle the default **`/`** route. This method should return a response string (i.e. valid html) or a PSR 7 response object containing valid html in its body. This default action or method should accept no arguments or parameters.

        * **`S3MVC_APP_DEFAULT_CONTROLLER_CLASS_NAME:`** A string value. This is used to create a controller object to handle the default **`/`** route. Must be prefixed with the namespace if the controller class is in a namespace.

        * **`s3MVC_GetCurrentAppEnvironment():`** This function detects which environment your web-application is running in (i.e. one of Production, Development, Staging or Testing). Below are its possible return values. You define your application's environment inside **`config/env.php`**.

            * **`S3MVC_APP_ENV_DEV:`** A string value representing that your application is running in development mode.

            * **`S3MVC_APP_ENV_PRODUCTION:`** A string value representing that your application is running in production / live mode.

            * **`S3MVC_APP_ENV_STAGING:`** A string value representing that your application is running in staging mode.

            * **`S3MVC_APP_ENV_TESTING:`** A string value representing that your application is running in testing mode.

        * **`S3MVC_APP_PUBLIC_PATH:`** A string value. The absolute path to the **`public`** folder in your application.

        * **`S3MVC_APP_ROOT_PATH:`** A string value. The absolute path the topmost level folder in your application (i.e. the folder containing all your application's folders like **`src`**, **`config`**, etc).

        * **`S3MVC_APP_USE_MVC_ROUTES:`** A boolean value. If true, the mvc routes will be enabled. If false, then you must explicitly define all the routes for your application inside **`config/routes-and-middlewares.php`** (like working with pure Slim 3).

* **`src/controllers/Hello.php`:** Example Controller class.

* **`src/layout-templates/main-template.php`:** Default site template you can use as a starting point for your application's layout.

* **`src/views/base/index.php`:** View file associated with the **`actionIndex`** method in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/base/login.php`:** View file associated with the **`actionLogin`** method in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/base/login-status.php`:** View file associated with the **`actionLoginStatus`** method in **`vendor/rotexsoft/slim3-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/hello/world.php`:** View file associated with the **`actionWorld`** method in **`src/controllers/Hello.php`**.

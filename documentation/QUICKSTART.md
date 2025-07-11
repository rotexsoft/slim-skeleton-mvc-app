# Quick Start Guide

## Installation (Creating a Project)

* Get the most recent development version (creates the project from the master branch in the repo)

  **`$ composer create-project -n -s dev rotexsoft/slim-skeleton-mvc-app my-app`**

* Get the most stable version (creates the project from the most recent tagged release in the repo)

  **`$ composer create-project -n rotexsoft/slim-skeleton-mvc-app my-app`**

### Testing the Installation

1. Change directory to your new application's folder / directory by executing the command below:

    > **`cd my-app`**

2. Now run this command to run the built-in php development server:

    > **`php -S 0.0.0.0:8888 -t public`**

3. You can then go on to browse to [http://localhost:8888](http://localhost:8888) (your new application's default home-page)

    * **Automatic routing scheme for mapping request urls to methods in Controller classes that are sub-classes of SlimMvcTools\Controllers\BaseController:** urls in the form of
        > `http(s)://server[:port][/][<base-path>/][<controller-name>][/<method-name>][/param1]..[/paramN]`

        can be automatically mapped to be responded to by a specific method in a Controller class, if **AppSettingsKeys::USE_MVC_ROUTES** is set to **`true`** in **`./config/app-settings.php`**. Note that items enclosed in `[]` in the url scheme above are optional.

        * **`<base-path>`:** this is usually the alias setup in your webserver's configuration file that points to your site's document root folder  (in this case **`./public`**). For example in an apache web-server's configuration file you could have an alias definition like so:
            > Alias /my-app /path/to/my-app/public

            making your application accessible via `http://server/my-app/` or `https://server/my-app/` (if using SSL), where **my-app** is the value of **`<base-path>`** in this example. The **`<base-path>`** section of the url is optional since your web-server's main document root could be directly set to your site's document root folder (i.e. **`./public`**), as seen in step **2** above where the **php** development server is used.

            See also https://www.slimframework.com/docs/v4/start/web-servers.html#run-from-a-sub-directory

    * Below are the default links that are available upon installation:

        * [http://localhost:8888/base-controller/action-index/](http://localhost:8888/base-controller/action-index/) same as [http://localhost:8888/base-controller/](http://localhost:8888/base-controller/)
            * This link is mapped to **`\SlimMvcTools\Controllers\BaseController::actionIndex()`** under the hood

        * [http://localhost:8888/base-controller/action-login/](http://localhost:8888/base-controller/action-login/) comes with 2 default accounts **admin:admin** and **root:root**
            * This link is mapped to **`\SlimMvcTools\Controllers\BaseController::actionLogin()`** under the hood
        
        * [http://localhost:8888/base-controller/action-routes/1](http://localhost:8888/base-controller/action-routes/1)  displays all the potential routes in your application in a simple HTML table.
            * This link is mapped to **\SlimMvcTools\Controllers\BaseController::actionRoutes($onlyPublicMethodsPrefixedWithAction=true)** under the hood
            * You can append **/0** instead of the **/1** at the end of the link above to display all public methods in each of the controllers in your application instead of just only public methods prefixed with **action**

        * [http://localhost:8888/base-controller/action-logout/0](http://localhost:8888/base-controller/action-logout/0)
            * This link is mapped to **`\SlimMvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/base-controller/action-logout/1](http://localhost:8888/base-controller/action-logout/1)
            * This link is mapped to **`\SlimMvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/base-controller/action-login-status/](http://localhost:8888/base-controller/action-login-status/)
            * This link is mapped to **`\SlimMvcTools\Controllers\BaseController::actionLoginStatus()`** under the hood

        * [http://localhost:8888/hello/action-index/](http://localhost:8888/hello/action-index/) same as [http://localhost:8888/hello/](http://localhost:8888/hello/)
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionIndex()`** under the hood

        * [http://localhost:8888/hello/action-login/](http://localhost:8888/hello/action-login/) comes with 2 default accounts **admin:admin** and **root:root**
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionLogin()`** under the hood

        * [http://localhost:8888/hello/action-logout/0](http://localhost:8888/hello/action-logout/0)
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/hello/action-logout/1](http://localhost:8888/hello/action-logout/1)
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)`** under the hood

        * [http://localhost:8888/hello/action-login-status/](http://localhost:8888/hello/action-login-status/)
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionLoginStatus()`** under the hood

        * `http://localhost:8888/hello/action-there/{first_name}/{last_name}`
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionThere($first_name, $last_name)`** under the hood
            * you can do stuff like [http://localhost:8888/hello/action-there/john/doe](http://localhost:8888/hello/action-there/john/doe)

        * `http://localhost:8888/hello/action-world/{name}/{another_parameter}`
            * This link is mapped to **`\SlimSkeletonMvcApp\Controllers\Hello::actionWorld($name, $another_param)`** under the hood
            * you can do stuff like [http://localhost:8888/hello/action-world/john/doe](http://localhost:8888/hello/action-world/john/doe)

    * The **`action-`** prefix can be omitted from the links above if **AppSettingsKeys::AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES** is set to **`true`** in **`./config/app-settings.php`**
        * For example [http://localhost:8888/hello/action-login/](http://localhost:8888/hello/action-login/) will become [http://localhost:8888/hello/login/](http://localhost:8888/hello/login/) and [http://localhost:8888/hello/action-there/john/doe](http://localhost:8888/hello/action-there/john/doe) will become [http://localhost:8888/hello/there/john/doe](http://localhost:8888/hello/there/john/doe)

4. If you are getting 404 errors, make sure that url-rewriting is enabled on your web-server.

5. Next steps:

    - Customize **./config/app-settings.php**, **./config/dependencies.php**, **./config/env.php**, **./config/ini-settings.php** and optionally **./config/routes-and-middlewares.php** to suit your needs.

    - Start creating controllers for your application using **./vendor/bin/smvc-create-controller-wizard**
            
        > It is recommended that you first create a base controller for your application, which will contain all the logic that will be common to all your application's other controllers. The other controllers should extend your application's base controller.

    > Make sure you add the namespace for your apps controller classes to the array referenced by **$container[\SlimSkeletonMvcApp\ContainerKeys::NAMESPACES_4_CONTROLLERS]** in **./config/dependencies.php**


## Key Directories and Configuration
* **`config`:** Contains files for configuring the application

* **`config/languages`:** Contains localization files for language specific pieces of text used by **$container[\SlimSkeletonMvcApp\ContainerKeys::LOCALE_OBJ]** in **./config/dependencies.php** 

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

* **`config/app-settings.php`:** Application settings and other environment specific settings needed by your application (like database credentials, etc.) should be stored here. These settings will be stored in **$container[\SlimSkeletonMvcApp\ContainerKeys::APP_SETTINGS]** in **./config/dependencies.php**.

    * This file should not be committed to version control (it has already been added (by default) to the `.gitignore` file for your application if you are using **git** for version control). Instead, it should be created by making a copy of **`config/app-settings-dist.php`** and then configured uniquely for each environment your application is to be deployed to.


* **`config/app-settings-dist.php`:** A template file for creating **`config/app-settings.php`** in new environments your application will be deployed to. It should be version controlled. Store dummy values for sensitive settings, like database credentials for your application, in this file.

* **`config/dependencies.php`:** This is where you register dependencies needed by your application in a **psr/container** compliant container object. **\SlimMvcTools\Container** is the **psr/container** compliant container object that ships with this framework.

    * Below are the items that are registered in the container:

        * **`\SlimSkeletonMvcApp\ContainerKeys::APP_SETTINGS:`** The array returned by **`config/app-settings.php`**

        * **`\SlimSkeletonMvcApp\ContainerKeys::DEFAULT_LOCALE:`** the default locale language code for localized strings of text in your application

        * **`\SlimSkeletonMvcApp\ContainerKeys::VALID_LOCALES:`** an array of allowable locale language codes for localized strings of text in your application

        * **`\SlimSkeletonMvcApp\ContainerKeys::LOCALE_OBJ:`** an object that is used to retrieve the appropriate localized strings of text in your application

            ```php
            <?php
                //You can access the locale object from within your controllers like so:
                $this->vespula_locale;

                //or
                $this->getContainerItem(\SlimSkeletonMvcApp\ContainerKeys::LOCALE_OBJ);
            ?>
            ```

        * **`\SlimSkeletonMvcApp\ContainerKeys::LOGGER:`** Any PSR-3 compliant logger that can be used for logging in your application.

            ```php
            <?php
                //You can access the logger from within your controllers like so:
                $this->logger;

                //or
                $this->getContainerItem(\SlimSkeletonMvcApp\ContainerKeys::LOGGER);
            ?>
            ```

        * **`\SlimSkeletonMvcApp\ContainerKeys::NAMESPACES_4_CONTROLLERS:`** An array containing a list of the namespaces that your application's controller classes belong to. If all your controllers are in the global namespace, then you don't need to update this array. The default namespaces that ship with this package are **`'\\SlimMvcTools\\Controllers\\'`** (the namespace where **`BaseController`** belongs) and **`'\\SlimSkeletonMvcApp\\Controllers\\'`** (the namespace where **`Hello`** belongs).  

            * You still need to make sure that autoloading is properly configured in **./composer.json**. The **./composer.json** that ships with this framework uses the **classmap** method in the **autoload** section of **./composer.json** (meaning that you have to run the **`composer dumpautoload`** command each time you add a new class file to your **./src** folder). You can decide to use the **PSR-4** directive in the **autoload** section of your application's **./composer.json**.

        * **`\SlimSkeletonMvcApp\ContainerKeys::LAYOUT_RENDERER:`** An object used for rendering layout-template(s) for your application (see the **`renderLayout`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**). See https://github.com/rotexsoft/file-renderer for more details on how to configure this object.

            ```php
            <?php
                // You can access this renderer from within your controller methods like so:
                $this->layout_renderer; // it is automatically set as a property of the controller
                                        // object, as long as your controller object extends
                                        // \SlimMvcTools\Controllers\BaseController.

                // You can also access this renderer from within your controller methods like so:
                $this->getContainerItem(\SlimSkeletonMvcApp\ContainerKeys::LAYOUT_RENDERER); // keep in mind that accessing it like
                                                              // this returns a new instance with
                                                              // each call.

                // There is also a helper method available in all your controllers that
                // extend \SlimMvcTools\Controllers\BaseController called renderLayout
                // via which you can interact with $this->layout_renderer
            ?>
            ```

        * **`\SlimSkeletonMvcApp\ContainerKeys::VIEW_RENDERER:`** An object used for rendering view file(s) associated with each action method in the controller(s) for your application (see the **`renderView`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**). See https://github.com/rotexsoft/file-renderer for more details on how to configure this object.

            ```php
            <?php
                // You can access this renderer from within your controller methods like so:
                $this->view_renderer; // it is automatically set as a property of the controller
                                      // object, as long as your controller object extends
                                      // \SlimMvcTools\Controllers\BaseController.

                // You can also access this renderer from within your controller methods like so:
                $this->getContainerItem(\SlimSkeletonMvcApp\ContainerKeys::VIEW_RENDERER); // keep in mind that accessing it like
                                                            // this returns a new instance with
                                                            // each call.

                // There is also a helper method available in all your controllers that
                // extend \SlimMvcTools\Controllers\BaseController called renderView
                // via which you can interact with $this->view_renderer
            ?>
            ```

        * **`\SlimSkeletonMvcApp\ContainerKeys::VESPULA_AUTH:`** An object used by the **`BaseController`** to implement authentication functionality (see the **`isLoggedIn`**, **`actionLogin`**, **`actionLogout`** and **`actionLoginStatus`** methods in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**). See https://bitbucket.org/jelofson/vespula.auth for more details on how to configure this object.

            ```php
            <?php
                //You can access the auth object from within your controller like so:
                $this->vespula_auth;

                // or
                $this->getContainerItem(\SlimSkeletonMvcApp\ContainerKeys::VESPULA_AUTH);
            ?>
            ```

        * **`\SlimSkeletonMvcApp\ContainerKeys::NEW_REQUEST_OBJECT:`** Returns a new PSR 7 Request object on each access 

        * **`\SlimSkeletonMvcApp\ContainerKeys::NEW_RESPONSE_OBJECT:`** Returns a new PSR 7 Response object on each access 

* **`config/env.php`:** Edit it to define your application's environment. It should return one of **\SlimSkeletonMvcApp\AppEnvironments::DEV**, **\SlimSkeletonMvcApp\AppEnvironments::PRODUCTION**, **\SlimSkeletonMvcApp\AppEnvironments::STAGING** or **\SlimSkeletonMvcApp\AppEnvironments::TESTING** relevant to the environment you are installing your web-application.

    * This file should not be committed to version control (it has already been added (by default) to the `.gitignore` file for your application if you are using **git** for version control). Instead, it should be created by making a copy of **`config/env-dist.php`** and then configured uniquely for each environment your application is to be deployed to.

* **`config/env-dist.php`:** A template file for creating **`config/env.php`** in new environments your application will be deployed to. It should be version controlled.

* **`config/ini-settings.php`:** Modify ini settings via **`ini_set(..)`** here. Remember to update **`date.timezone`** in this file to match your timezone (see http://php.net/manual/en/timezones.php).

* **`config/routes-and-middlewares.php`:** Add additional routes and middlewares (see https://www.slimframework.com/docs/v4/concepts/middleware.html for more information on middlewares) for your application here (if needed). You can decide to define all the routes for your application here (in this case set the **AppSettingsKeys::USE_MVC_ROUTES** entry in **`config/app-settings.php`** to false). A default **`/`** route is defined in this file and will be active if  **AppSettingsKeys::USE_MVC_ROUTES** has a value of **`false`**.

* **`public/.htaccess`:** Apache web-server settings.

* **`public/index.php`:** Entry point to application.

    * Below are some constants and functions defined in this file (i.e. **`public/index.php`**) that will be accessible in your application classes like the controllers and files like the layout and view files:

        * **`SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES:`** A boolean value. If true, the string **`'action'`** will be prepended to action method names (if the method name does not already start with the string **`'action'`**). The resulting method name will be converted to camel case before being executed. If false, then action method names will only be converted to camel case before being executed. This setting does not apply to **`SMVC_APP_DEFAULT_ACTION_NAME`**. It only applies to the following routes **`'/{controller}/{action}[/{parameters:.+}]'`** and **`'/{controller}/{action}/'`**.

        * **`SMVC_APP_DEFAULT_ACTION_NAME:`** A string value. This is the name of the action or method to be called on the default controller to handle the default **`/`** route. This method should return a response string (i.e. valid html) or a PSR 7 response object containing valid html in its body. This default action or method should accept no arguments or parameters.

        * **`SMVC_APP_DEFAULT_CONTROLLER_CLASS_NAME:`** A string value. This is used to create a controller object to handle the default **`/`** route. Must be prefixed with the namespace if the controller class is in a namespace.

        * **`sMVC_GetCurrentAppEnvironment(): string:`** This function detects which environment your web-application is running in (i.e. one of Production, Development, Staging or Testing). Below are its possible return values. You define your application's environment inside **`config/env.php`**.

            * **`\SlimSkeletonMvcApp\AppEnvironments::DEV:`** A string value representing that your application is running in development mode.

            * **`\SlimSkeletonMvcApp\AppEnvironments::PRODUCTION:`** A string value representing that your application is running in production / live mode.

            * **`\SlimSkeletonMvcApp\AppEnvironments::STAGING:`** A string value representing that your application is running in staging mode.

            * **`\SlimSkeletonMvcApp\AppEnvironments::TESTING:`** A string value representing that your application is running in testing mode.

        * **`SMVC_APP_PUBLIC_PATH:`** A string value. The absolute path to the **`public`** folder in your application.

        * **`SMVC_APP_ROOT_PATH:`** A string value. The absolute path the topmost level folder in your application (i.e. the folder containing all your application's folders like **`src`**, **`config`**, etc).

        * **`SMVC_APP_USE_MVC_ROUTES:`** A boolean value. If true, the mvc routes will be enabled. If false, then you must explicitly define all the routes for your application inside **`config/routes-and-middlewares.php`**.

* **`src/controllers/Hello.php`:** Example Controller class.

* **`src/layout-templates/error-template.html`:** Default template used by this framework to display all your application's error pages like **404 - Not Found**, etc. Tweak it to match your site's look and feel while leaving the three **%s** tokens inside it (they are substituted with error title & description info when this template is rendered at run-time).

* **`src/layout-templates/main-template.php`:** Default site template you can use as a starting point for your application's layout.

* **`src/views/base/controller-classes-by-action-methods-report.php`:** View file associated with the **`actionRoutes`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/base/index.php`:** View file associated with the **`actionIndex`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/base/login.php`:** View file associated with the **`actionLogin`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/base/login-status.php`:** View file associated with the **`actionLoginStatus`** method in **`vendor/rotexsoft/slim-skeleton-mvc-tools/src/BaseController.php`**.

* **`src/views/hello/world.php`:** View file associated with the **`actionWorld`** method in **`src/controllers/Hello.php`**.

* **`src/AppEnvironments.php`:** A class containing names for possible environments an application is running as defined as constants, one of which is returned in **config/env.php**. You can add more constants to this class to define custom environment names for your application.

* **`src/AppErrorHandler.php`:** The ErrorHandler class registered with SlimPHP's ErrorMiddleware for handling all your application's errors. You can add extra logic like sending notification emails when errors occur and etc. in your application. See the **AppSettingsKeys::ERROR_HANDLER_CLASS** entry in **./config/app-settings.php** and references to **AppSettingsKeys::ERROR_HANDLER_CLASS** in **./config/routes-and-middlewares.php** for how this Handler is setup in this framework.

* **`src/AppSettingsKeys.php`:** A class containing constants to be used as keys for items in **config/app-settings.php**. You can add more constants to represent keys for custom settings for your application.

* **`src/ContainerKeys.php`:** A class containing constants to be used as keys for container items in **config/dependencies.php**. You can add more constants to represent keys for extra container items specific to your application.

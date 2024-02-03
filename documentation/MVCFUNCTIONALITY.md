# MVC Functionality

```php
<?php
namespace SlimSkeletonMvcApp\Controllers;

class Hello extends \SlimMvcTools\Controllers\BaseController
{
    public function __construct(
        \Psr\Container\ContainerInterface $container, 
        string $controller_name_from_uri, 
        string $action_name_from_uri, 
        \Psr\Http\Message\ServerRequestInterface $req, 
        \Psr\Http\Message\ResponseInterface $res     
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
    }
    
    public function actionIndex() {

        return 'in Hello::actionIndex()<br>';
    }
    
    public function actionThere($first_name, $last_name): string {

        return "Hello There $first_name, $last_name";
    }
}
?>
```
**Figure 1: an Hello Controller class**

* There are four routes that are defined in the **`./config/routes-and-middlewares.php`** file to handle MVC requests
* These four routes will be enabled, if and only if **use_mvc_routes** is set to **`true`** in **./config/app-settings.php**
* Below are the four routes:
    * **`/`**: the **default route**. It creates an instance of the default controller (configurable via **default_controller_class_name** in **./config/app-settings.php**) and executes the default action method (configurable via **default_action_name** in **./config/app-settings.php**) on the default controller. This route should be used for the home-page of your app.
        * For example, given **default_controller_class_name** with a value of **`\SlimSkeletonMvcApp\Controllers\Hello::class`** and **default_action_name** with a value of **`'actionIndex()'`**, the **default route** will lead to the execution of **`'\SlimSkeletonMvcApp\Controllers\Hello->actionIndex()'`**
    * **`/{controller}[/]`**: the **controller only route**. It creates an instance of the controller specified in the url and executes the default action method (configurable via **default_action_name**) on the specified controller.
        * For example, given a route with a value of **`/hello`** and **default_action_name** with a value of **`'actionIndex()'`**, the **controller only route** will lead to the execution of **`'\SlimSkeletonMvcApp\Controllers\Hello->actionIndex()'`**
    * **`/{controller}/{action}/`**: the **controller and action only route**. It creates an instance of the controller specified in the url and executes the action method specified in the url on the specified controller. The specified method in the specified controller should not accept any parameters / arguments.
        * For example, given a route with a value of **`/hello/index/`**, the **controller and action only route** will lead to the execution of **`'\SlimSkeletonMvcApp\Controllers\Hello->actionIndex()'`**
    * **`/{controller}/{action}[/{parameters:.+}]`**: the **controller, action and optional parameters route**. It creates an instance of the controller specified in the url and executes the action method specified in the url (with the parameters specified in the url, if any) on the specified controller.
        * For example, given a route with a value of **`/hello/there/john/Doe`**, the **controller, action and optional parameters route** will lead to the execution of **`'\SlimSkeletonMvcApp\Controllers\Hello->actionThere('john', 'Doe')'`**
        * This route also responds to **`/{controller}/{action}`** (without a trailing slash). In this case the controller method specified via **`{action}`** part of the url must not accept any arguments or parameters.

### **More on Controllers and MVC Routes** 
* Controller classes must extend `\SlimMvcTools\Controllers\BaseController`. These classes must be named using studly case / caps e.g. **StaticPages**, **MobileDataProviders** and must be referenced in the controller segment of the url in all lowercases with dashes preceding capital case characters (except for the first capital case character). For example, `http://localhost:8888/mobile-data-providers/` will be responded to by the default action (defined via **`default_action_name`**; default value is **`actionIndex`** ) method in the controller named **MobileDataProviders**, `http://localhost:8888/mobile-data-providers/list-providers` or `http://localhost:8888/mobile-data-providers/action-list-providers` (if **`auto_prepend_action_to_action_method_names`** is set to **`false`** in **./config/app-settings.php**) will be responded to by the **`actionListProviders()`** method in the controller named **`MobileDataProviders`**, etc.
    * NOTE: there is a helper script available for creating Controller Classes and some default view files (see **`./vendor/bin/smvc-create-controller`** or **`./vendor/bin/smvc-create-controller-wizard`**)
    * Controller action methods should be named using camel case (e.g. **`listProviders()`** ). In addition, they must be prefixed with the word **`action`** if **`auto_prepend_action_to_action_method_names`** is set to `true`; in this case **`actionListProviders()`**

* Action methods in Controller classes MUST either return a string (i.e. containing the output to display to the client) or an instance of **Psr\Http\Message\ResponseInterface** (e.g. $response, that has the output to be displayed to the client, injected into it via `$response->getBody()->write($data)` );

* The **$smvc_route_handler** defined in **./config/routes-and-middlewares.php**, handles the MVC route scenarios below:

    * **`The default route with default controller and default action route:`** responds to the **`/`** route by creating an instance of the default controller (defined via **`default_controller_class_name`**) and calling the default action method (defined via **`default_action_name`**) on the controller object and returning the result as a response object (if the method returns a string the default route handler will write the string into a response object and return that object). 

    * **`The controller with action and optional params route:`** The mvc route handler responds to the **`/{controller}/{action}[/{parameters:.+}]`** and **`/{controller}/{action}/`** routes by going through the steps below:
        * extracting the **`{controller}`**, **`{action}`** and **`{parameters}`** segments of a request uri. Eg. http://localhost:8888/hello/action-world/john/doe will lead to `hello` being extracted as the value of the **`{controller}`** segment, `action-world` being extracted as the value of the **`{action}`** segment and `['john', 'doe']` as the value of the **`{parameters}`** segment. It then converts the value of the **`{action}`** segment to camel case; in this case from `action-world` to `actionWorld`. If **`auto_prepend_action_to_action_method_names`** is set to `true` then the handler will try to prepend the string `'action'` to the camel-cased value of the **`{action}`** segment; however in this case it will not prepend the string `'action'` to `actionWorld` since it already starts with the string `action`. It then goes on to validate that `actionWorld` is a valid name for a php class' method name, if it's an invalid name it will throw a **\Slim\Exception\HttpBadRequestException**. If it's a valid method name it tries to create an instance of a controller class by first converting the value of the **`{controller}`** segment, in this case `hello`, to studly case which will lead to `hello` being converted to `Hello` and it then goes on to validate that `Hello` is a valid name for a php class, if it's an invalid name it will throw a **\Slim\Exception\HttpBadRequestException**. If it's a valid class name, it then goes on to check if the class exists in the global namespace first, and if not, then it continues checking in the namespaces registered in the container (**\SlimMvcTools\ContainerKeys::NAMESPACES_4_CONTROLLERS**). If the class does not exist, it will throw a **\Slim\Exception\HttpNotFoundException**. If the class exists and is not an instance / sub-class of **\SlimMvcTools\Controllers\BaseController**, a **\Slim\Exception\HttpBadRequestException** will be thrown. Else an instance will be created. The handler then goes on to check if the method named `actionWorld` exists in the instance of the controller class just created. If the method doesn't exist, the handler will throw a **\Slim\Exception\HttpNotFoundException**. Else if the method exists it will be called on the created controller object with the values of the **`{parameters}`** segment (in this case `['john', 'doe']`) as arguments (i.e. **$instance_of_hello_controller->actionWorld('john', 'doe')** ) and the result will be returned as a response object (if the method returns a string the handler will write the string into a response object and return that object). Note that if there are no values supplied for the **`{parameters}`** segment, the action method will be called on the controller with no parameter (i.e. **$instance_of_hello_controller->actionWorld()** ) this happens when the **`/{controller}/{action}/`** route is matched. 
        
    * **`The controller with no action and no params route:`** `/{controller}[/]`: works in a similar manner to how the **`/{controller}/{action}[/{parameters:.+}]`** and **`/{controller}/{action}/`** routes are handled. Except that the value of **`default_action_name`** is used for the method name and the method will always be invoked with no parameters.

### **Controller Execution Flow** 
Middlewares added to your app, like the one in **Figure 5** below, will be executed for all routes (MVC ones above included) in your app.
Such middlewares should be added to **`config/routes-and-middlewares.php`**.
You can also use the **`preAction()`** and  **`postAction(\Psr\Http\Message\ResponseInterface $response)`** methods 
in any of your controllers to inject code you want to be executed before and after each action method is run during 
a request to an action in a specific controller. You can create a BaseController 
(which extends `\SlimMvcTools\Controllers\BaseController`) in your app which all 
your app's controllers will extend. This BaseController's 
**`preAction()`** and  **`postAction(\Psr\Http\Message\ResponseInterface $response)`** methods
can then be used to implement common logic which you want to be executed before and after any 
action method is run in any of your controllers.

In a nutshell; for each request, middleware code is first executed 
(in Slim PHP the handler associated with the route matched for the current request
is also executed as a middleware). When the route handler for any of the MVC routes mentioned above is executed,
the **`preAction()`** method for the current controller is executed first, followed by the current action method
and then followed by the **`postAction(\Psr\Http\Message\ResponseInterface $response)`** method for the current 
controller. Finally, other middleware code (if any) is executed after the route handler for the current request 
has been executed. 

Given the code in **figures 4** and **5** below, executing **`http://localhost:8888/hello/`** will generate the output in **Figure 2** below and executing **`http://localhost:8888/hello/action-there/john/Doe`** (or **`http://localhost:8888/hello/there/john/Doe`** if **auto_prepend_action_to_action_method_names** is set to **`true`**) will generate the output in **Figure 3** below:

```
in Hello::__construct(...)
in Hello::preAction()
in Hello::actionIndex()
in Hello::postAction(\Psr\Http\Message\ResponseInterface $response)
in Middleware after current route handler
```
**Figure 2: Output of executing** `http://localhost:8888/hello/`

```
in Hello::__construct(...)
in Hello::preAction()
Hello There john, doe
in Hello::postAction(\Psr\Http\Message\ResponseInterface $response)
in Middleware after current route handler
```
**Figure 3: Output of executing** `http://localhost:8888/hello/action-there/john/Doe`

```php
<?php
namespace SlimSkeletonMvcApp\Controllers;

use \Psr\Container\ContainerInterface,
    \Psr\Http\Message\ServerRequestInterface,
    \Psr\Http\Message\ResponseInterface;

class Hello extends \SlimMvcTools\Controllers\BaseController
{
    public function __construct(
        ContainerInterface $container, 
        string $controller_name_from_uri, 
        string $action_name_from_uri, 
        ServerRequestInterface $req, 
        ResponseInterface $res     
    ) {
        parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
        $this->response->getBody()->write('in Hello::__construct(...)<br>'); 
    }
    
    public function actionIndex() {

        return 'in Hello::actionIndex()<br>';
    }
    
    public function actionThere($first_name, $last_name) {

        return "Hello There $first_name, $last_name<br>";
    }
    
    public function preAction(): ResponseInterface {
        
        // add code that you need to be executed before each controller action method is executed
        $response = parent::preAction();
        $response->getBody()->write('in Hello::preAction()<br>'); 
        
        return $response;
    }
    
    public function postAction(\Psr\Http\Message\ResponseInterface $response): ResponseInterface {
        
        // add code that you need to be executed after each controller action method is executed
        $response = parent::postAction($response);
        $response->getBody()->write('in Hello::postAction(\Psr\Http\Message\ResponseInterface $response)<br>');
        
        return $response;
    }
}

```
**Figure 4: Example Hello Controller Class**

```php
<?php
$app->add(function (\Psr\Http\Message\ServerRequestInterface $req, \Psr\Http\Server\RequestHandlerInterface $next) {
    
    $new_response = $next->handle($req); // this will eventually execute the route handler
                                       // for the matched route for the current request.
                                       // This is where the controller is instantiated 
                                       // and the appropriate controller method is 
                                       // invoked with / without parameters.
                                                
    $new_response->getBody()->write('in Middleware after current route handler<br>');
    
    return $new_response;
});
?>
```
**Figure 5: Sample middleware that should be placed in `./config/routes-and-middlewares.php`**

### **Using the File Renderer for Rendering Views and Layouts inside Controller Action Methods** 

There is an optional built-in template file rendering system available to all controllers that extend 
**`\SlimMvcTools\Controllers\BaseController`** via two methods in **`\SlimMvcTools\Controllers\BaseController`**:
1. **renderLayout( string $file_name, array $data = ['content'=>'Content should be placed here!'] ): string** 
2. and **renderView( string $file_name, array $data = [] ): string** 

The layout and view files for your application only need be written in php (i.e. only html, css, js 
and php code expected), no need to learn any new templating language or syntax. 
 
* **`renderLayout( string $file_name, array $data = ['content'=>'Content should be placed here!'] ): string:`** for rendering 
the layout file for your application. This file should contain the overall html structure for all your website's 
pages. Layout files should be located in **`./src/layout-templates`**, you can change this location or add extra 
location(s) by updating the **`\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER`** section in **`./config/dependencies.php`**. Just call 
**renderLayout** with the name of the php layout file you want to render (e.g. `some-page.php` which will be 
searched for in **`./src/layout-templates`** or whatever location(s) were registered in the **`\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER`** 
section in **`./config/dependencies.php`**) and an optional associative array whose key(s) will be injected into the 
layout template file as variable(s). 

    * For example, **renderLayout( 'site-layout.php', ['description'=>'You are viewing page one'] )** will render a file
    named **site-layout.php** with a variable named **$description** with the value of `You are viewing page one` 
    available inside **site-layout.php** during rendition (see Figures 6 and 7 below for examples). 
    The default layout template file (**./src/layout-templates/main-template.php**) that ships with this 
    framework contains a **$content** php variable (you should populate this variable with page-specific content).  
    ---
    
    ```php
    <?php
    class Hello extends \SlimMvcTools\Controllers\BaseController
    {
        public function __construct(
            \Psr\Container\ContainerInterface $container, 
            string $controller_name_from_uri, string $action_name_from_uri, 
            \Psr\Http\Message\ServerRequestInterface $req, 
            \Psr\Http\Message\ResponseInterface $res     
        ) {
            parent::__construct($container, $controller_name_from_uri, $action_name_from_uri, $req, $res);
        }

        public function actionIndex() {

            return $this->renderLayout( 'site-layout.php', ['description'=>'You are viewing page one'] );
        }
    }
    ?>
    ```
    **Figure 6: a sample controller class**

    ```php
    <!doctype html>
    <html class="no-js" lang="en">
        <head>
            <meta charset="utf-8" />
            <title>Site Title Goes Here</title>
        </head>
        <body>
            <div>
                <ul style="padding-left: 0;">
                    <li style="display: inline;"><a href="#">Section 1</a></li>
                    <li style="display: inline;"><a href="#">Section 2</a></li>
                </ul>
            </div>
            <div> <h1>Welcome to Your New Site</h1> </div> <br>
            <div> <?php echo $description; ?> </div>
            <footer>
                <div>
                    <hr/>
                    <p>
                        This site is powered by the 
                        <a href="https://github.com/rotexsoft/slim-skeleton-mvc-app">
                            SlimPHP 4 Skeleton MVC App.
                        </a>
                    </p>
                </div> 
            </footer>
        </body>
    </html>
    ```
    **Figure 7: a sample layout file (site-layout.php) located in ./src/layout-templates/**

* **`renderView($file_name, array $data=[]):`** for rendering the view file(s) (usually from within an 
action method in your controller). View files should be located in **`src/views/<controller_name_from_uri>`**, 
where **`<controller_name_from_uri>`** represents the name of the controller class in all lowercase format with 
words separated with dashes. For example, view files for a controller named **PostComments** should be located in 
**src/views/post-comments**. If a view file cannot be located in  **`src/views/<controller_name_from_uri>`**, 
**renderView($file_name, array $data=[])** will search the view folder(s) of the parent classes of the specified 
controller (NOTE: the view folder associated with **\SlimMvcTools\Controllers\BaseController** is **src/views/base**, 
you can change this location by updating the **`\SlimMvcTools\ContainerKeys::VIEW_RENDERER`** section in **`./config/dependencies.php`**). 

    * For example, if the **PostComments** controller extends a controller named **MyAppBase** which in turn extends 
    **\SlimMvcTools\Controllers\BaseController**, then view files for the **PostComments** controller which cannot 
    be found in **src/views/post-comments** will be searched for first in **src/views/my-app-base** and if not 
    found in **src/views/my-app-base** will be finally searched for in **src/views/base** (or whatever location
    you set for the view folder of **\SlimMvcTools\Controllers\BaseController** in the **`\SlimMvcTools\ContainerKeys::VIEW_RENDERER`** 
    section in **`./config/dependencies.php`**). If the specified view file cannot be found in any of the paths 
    then an exception would be thrown letting you know that the file could not be found in the expected paths.
    
        * Given the folder structure below in your application: 
        ```
        ./path/to/your/app
        |-- config/
        |-- logs/
        |-- public/
        |-- src/
        |    |-- controllers
        |    |   |-- MyAppBase.php
        |    |   |-- PostComments.php
        |    |-- layout-templates
        |    |   `-- main-template.php
        |    |-- models
        |    `-- views
        |        |-- base
        |        |   |-- index.php
        |        |   `-- default.php
        |        |-- my-app-base
        |        |   |-- index.php
        |        |   `-- list.php
        |        `-- post-comments
        |            |-- index.php
        |            `-- new-comments.php
        |-- tests/
        |-- tmp/
        |-- vendor/
        |
        |-- .gitignore
        |-- composer.json
        |-- composer.lock
        `-- README.md
        ```
        
        From within any action method in your **PostComments** controller, calling:
        
        * **$this->renderView('new-comments.php')** will render **src/views/post-comments/new-comments.php**

        * **$this->renderView('index.php', ['var1'=>"var 1's value"])** will render **src/views/post-comments/index.php** with a variable **$var1** (with a value of `var 1's value`) available in **src/views/post-comments/index.php**. Note that the **index.php** files in **src/views/my-app-base/** and **src/views/base/** are ignored, since **src/views/post-comments/index.php** is most specific to the **PostComments** controller (i.e. it exists in the view folder (**src/views/post-comments/**) for the **PostComments** controller) 

        * **$this->renderView('list.php',  ['var2'=>"var 2's value"])** will render **src/views/my-app-base/list.php** with a variable **$var2** (with a value of `var 2's value`) available in **src/views/my-app-base/list.php**

        * **$this->renderView('default.php')** will render **src/views/base/default.php**

        * **$this->renderView('non-existent.php')** will throw an Exception stating that **non-existent.php** could not be found
    
### Escaping View Data
If you use `\SlimMvcTools\Controllers\BaseController::renderLayout( $file_name, array $data = ['content'=>'Content should be placed here!'] )`
and `\SlimMvcTools\Controllers\BaseController::renderView( $file_name, array $data = [] )` for rendering your layout and view files in your 
application, you can easily escape data in your views.

The variable **$this** inside any view or layout file(s) rendered via the earlier mentioned
render methods references an instance of **Rotexsoft\FileRenderer\Renderer** and as a result,
the methods below (which all return an escaped string) will be available in such view or layout file(s):

- **$this->escapeCss($string):** for escaping data which is meant to be rendered within `<style></style>` tags or inside the style attribute of any html element.
- **$this->escapeHtml($string):** for escaping data that is meant to be within html elements e.g divs(`<div></div>`), paragraphs(`<p></p>`), etc.
- **$this->escapeHtmlAttr($string):** for escaping data which is meant to be rendered as an attribute value within an html element in a view.
- **$this->escapeJs($string):** for escaping data which is meant to be rendered as string literals or digits within Javascript code in a view.
- **$this->escapeUrl($string):** for escaping data being inserted into a URL and not to the whole URL itself.

The sample layout file below demonstrates how to use the above methods:

```php
<?php

$bad_css_with_xss = <<<INPUT
body { background-image: url('http://example.com/foo.jpg?'); }</style>
<script>alert('You\\'ve been XSSed!')</script><style>
INPUT;

$paragraph_data_from_file_renderer = 'This is a Paragraph!!';
$var_that_should_be_html_escaped = '<script>alert("zf2");</script>';
$var_that_should_be_html_attr_escaped = 'faketitle" onmouseover="alert(/ZF2!/);';
$var_that_should_be_css_escaped = $bad_css_with_xss;
$another_var_that_should_be_css_escaped = ' display: block; " onclick="alert(\'You\\\'ve been XSSed!\'); ';
$var_that_can_be_safely_js_escaped = "javascript's cool";
$a_var_that_can_be_safely_js_escaped = '563';
$a_var_that_cant_be_guaranteed_to_be_safely_js_escaped = ' var x = \'Yo!\'; alert(x); ';
$var_that_should_be_url_escaped = ' " onmouseover="alert(\'zf2\')';

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Escaped Entities</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <style>
            <?php // CSS escaping is being applied to the variable below ?>
            <?php echo $this->escapeCss($var_that_should_be_css_escaped); ?>
        </style>

        <script type="text/javascript">
            // Javascript escaping is being applied to the variable below
            var some_string = '<?php echo $this->escapeJs($var_that_can_be_safely_js_escaped); ?>';
            alert(some_string);
        </script>
    </head>
    <body>
        <!-- An unescaped variable -->
        <p><?php echo $paragraph_data_from_file_renderer; ?></p>

        <div>
            <!-- Html Attribute escaping is being applied to the variable below -->
            <span title="<?php echo $this->escapeHtmlAttr($var_that_should_be_html_attr_escaped); ?>" >
                What framework are you using?
                <!-- Html escaping is being applied to the variable below -->
                <?php echo $this->escapeHtml($var_that_should_be_html_escaped); ?>
            </span>
        </div>

        <!-- CSS escaping is being applied to the variable below -->
        <p style="<?php echo $this->escapeCss($another_var_that_should_be_css_escaped); ?>">
            User controlled CSS needs to be properly escaped!

            <!-- Url escaping is being applied to the variable below -->
            <a href="http://example.com/?name=<?php echo $this->escapeUrl($var_that_should_be_url_escaped); ?>">Click here!</a>
        </p>

        <!-- Javascript escaping is being applied to the variable below -->
        <p onclick="var a_number = <?php echo $this->escapeJs($a_var_that_can_be_safely_js_escaped); ?>; alert(a_number);">
            Javascript escaping the variable in this paragraph's onclick attribute should
            be safe if the variable contains basic alphanumeric characters. It will definitely
            prevent XSS attacks.
        </p>

        <!-- Javascript escaping is being applied to the variable below -->
        <p onclick="<?php echo $this->escapeJs($a_var_that_cant_be_guaranteed_to_be_safely_js_escaped); ?>">
            Javascript escaping the variable in this paragraph's onclick attribute may lead
            to Javascript syntax error(s) but will prevent XSS attacks.
        </p>
    </body>
</html>
```

### Implementing View Helpers
If you use `\SlimMvcTools\Controllers\BaseController::renderLayout( $file_name, array $data = ['content'=>'Content should be placed here!'] )`
and `\SlimMvcTools\Controllers\BaseController::renderView( $file_name, array $data = [] )` for rendering your layout and view files in your 
application, you can easily create helper functions that will be accessible to all layout and view file(s) rendered via the earlier mentioned
methods.

To accomplish this, you need to first create a sub-class of **\Rotexsoft\FileRenderer\Renderer** and swap out 
instances of **Rotexsoft\FileRenderer\Renderer** with instances of this sub-class in the **\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER** 
and **\SlimMvcTools\ContainerKeys::VIEW_RENDERER** entries in the container in **`./config/dependencies.php`**. Assuming your sub-class is
**\MyApp\Utils\MyCustomFileRenderer**, your updated dependencies entries would look like below:

```php
<?php
//Object for rendering layout files
$container[\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER] = $container->factory(function () {
    
    //return a new instance on each access to $container[\SlimMvcTools\ContainerKeys::LAYOUT_RENDERER]
    $ds = DIRECTORY_SEPARATOR;
    $path_2_layout_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'layout-templates';
    $layout_renderer = new \MyApp\Utils\MyCustomFileRenderer('', [], [$path_2_layout_files]);
    
    return $layout_renderer;
});

//Object for rendering view files
$container[\SlimMvcTools\ContainerKeys::VIEW_RENDERER] = $container->factory(function () {
    
    //return a new instance on each access to $container[\SlimMvcTools\ContainerKeys::VIEW_RENDERER]
    $ds = DIRECTORY_SEPARATOR;
    $path_2_view_files = SMVC_APP_ROOT_PATH.$ds.'src'.$ds.'views'."{$ds}base";
    $view_renderer = new \MyApp\Utils\MyCustomFileRenderer('', [], [$path_2_view_files]);

    return $view_renderer;
});
?>
```

Once you have registered your custom FileRenderer class like as illustrated above, the variable **$this** 
inside any view or layout file(s) rendered via the earlier mentioned render methods will now reference an 
instance of your custom FileRenderer class (in this case **\MyApp\Utils\MyCustomFileRenderer**). 
All you need to do at this point to create helper methods that will be available in your layout and
view file(s) is to add such methods as public methods to your custom FileRenderer class and they will 
then be accessible inside your layout and view file(s) via **$this**->`whateverTheMethodName($params..)`.

For example, if I add a public method `formatPhoneNum($phone_num)` to **\MyApp\Utils\MyCustomFileRenderer**
then I can simply call `$this->formatPhoneNum($some_string)` from within any of my layout or view files 
provided that the files are rendered via a call to `\SlimMvcTools\Controllers\BaseController::renderLayout( $file_name, array $data = ['content'=>'Content should be placed here!'] )`
or `\SlimMvcTools\Controllers\BaseController::renderView( $file_name, array $data = [] )`.

### **Creating Controller Classes via the Commnadline** 

* Helper script for creating controller classes and a default index view:

    * On *nix-like Oses
        * `./vendor/bin/smvc-create-controller`

    * On Windows
        * `.\vendor\bin\smvc-create-controller.bat`
        
    * NOTE: **`smvc-create-controller-wizard`** is the interactive version of the script above


### SMVC Helper Functions
* **`sMVC_AddLangSelectionParamToUri(\Psr\Http\Message\UriInterface $uri, string $lang='en_US') : string`:** a helper function for adding lanuage toggle query string parameters to PSR-7 uri objects. See **./src/layout-templates/main-template.php** for examples of how this function is used to generate language toggle links in this default layout template that ships with this framework. **\SlimMvcTools\Controllers\BaseController** has an **updateSelectedLanguage()** method that checks for this query string parameter and updates the currently selected language used by the Locale object in the container whose key is **\SlimMvcTools\ContainerKeys::LOCALE_OBJ** each time a controller instance is created.

* **`sMVC_addQueryStrParamToUri(\Psr\Http\Message\UriInterface $uri,  string $param_name, string $param_value): \Psr\Http\Message\UriInterface`:** a helper function for adding query string parameters to PSR-7 uri objects.

* **`sMVC_CreateController(\Psr\Container\ContainerInterface $container, $controller_name_from_url, $action_name_from_url, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)`:** used by the route handler to create controllers to handle mvc routes. You should not really need to call this function.

* **`sMVC_DumpVar(...$vals): void`:** for dumping variables during development for debugging purposes.

* **`sMVC_GetSuperGlobal(string $global_name='', string $key='', $default_val='')`:** a helper function for accessing super globals.

* **`sMVC_UriToString(\Psr\Http\Message\UriInterface $uri)`:** a helper function for converting PSR-7 uri objects to a string.

## Security Considerations
* Make sure to validate / sanitize the password value posted to `\SlimMvcTools\Controllers\BaseController::actionLogin()` in your Controller(s). It is deliberately left un-sanitized and un-validated because each application should define which characters are allowed in passwords and validation / sanitization should be based on the allowed characters.
* When configuring your webserver, make sure you set the document root to your site to the **`./public`** folder so that attackers won't be able to browse directly to sensitive folders like the **`config`** or **`src`** folders of your application. For apache HTTP webservers, **`.htaccess`** files (each with a directive to prevent browsing directly to folders) are provided in the **`config`**, **`logs`**, **`src`**, **`tests`** and **`tmp`** folders out of the box. For NGINX users, you should look into configuring something similar to prevent direct browsing to sensitive folders of your application.

## Documentation for Components Used
* SlimPHP 4 https://www.slimframework.com/docs/v4/
* Slim Skeleton MVC Tools https://github.com/rotexsoft/slim-skeleton-mvc-tools contains [\SlimMvcTools\Controllers\BaseController](https://github.com/rotexsoft/slim-skeleton-mvc-tools/blob/master/src/controllers/BaseController.php) and other Slim Skeleton MVC specific classes and helper functions that will be useful in applications built with this framework
* Vespula.Log https://bitbucket.org/jelofson/vespula.log (a PSR-3 compliant logger)
* Vespula.Auth for Authentication https://bitbucket.org/jelofson/vespula.auth
* File-Renderer https://github.com/rotexsoft/file-renderer for rendering the template and view files
* [Vespula Locale](https://packagist.org/packages/vespula/locale) used for managing localized strings in this framework
* See http://pimple.sensiolabs.org/ for more information on how the dependency injection container used to power [\SlimMvcTools\Container](https://github.com/rotexsoft/slim-skeleton-mvc-tools/blob/master/src/Container.php) (the PSR-11 compliant container that ships with this framework) works


## References
* https://getcomposer.org/doc/articles/scripts.md
* https://devedge.wordpress.com/2014/11/05/building-better-project-skeletons-with-composer-2/
* http://www.binpress.com/tutorial/better-project-skeletons-with-composer/157


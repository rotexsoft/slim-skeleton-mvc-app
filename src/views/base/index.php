<hr>

<?php $uri_obj = $controller_object->getContainer()->get('request')->getUri(); ?>

<h4><strong>Below are the default links that are available in your application:</strong></h4>
<ul>
    <li>
        <?php  ?>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-index'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-index'))->__toString(); ?>
            </a> same as 
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3MvcTools\Controllers\BaseController::actionIndex()</code></strong> under the hood</li>
        </ul>
         <br>
    </li>
    <li>
        <p><a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-login'))->__toString(); ?>">
            <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-login'))->__toString(); ?>
            </a> comes with 2 default accounts <strong>admin:admin</strong> and <strong>root:root</strong></p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3MvcTools\Controllers\BaseController::actionLogin()</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-logout/0'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-logout/0'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3MvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-routes/1'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-routes/1'))->__toString(); ?>
            </a> displays all the potential routes in your application in a simple HTML table.
        </p>
        <ul>
            <li>
                This link is mapped to 
                <strong>
                    <code>
                        \Slim3MvcTools\Controllers\BaseController::actionRoutes($onlyPublicMethodsPrefixedWithAction=true)
                    </code>
                </strong> 
                under the hood
            </li>
            <li>
                <ul>
                    <li>
                        You can append <strong>/0</strong> instead of the <strong>/1</strong> 
                        at the end of the link above to display all public methods in each of 
                        the controllers in your application instead of just only public methods 
                        prefixed with <strong>action</strong>
                    </li>
                </ul>
            </li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-logout/1'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-logout/1'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3MvcTools\Controllers\BaseController::actionLogout($show_status_on_completion = false)</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-login-status'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/base-controller/action-login-status'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3MvcTools\Controllers\BaseController::actionLoginStatus()</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-index/'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-index/'))->__toString(); ?>
            </a> same as 
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionIndex()</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login/'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login/'))->__toString(); ?>
            </a> comes with 2 default accounts <strong>admin:admin</strong> and <strong>root:root</strong></p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionLogin()</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-logout/0'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-logout/0'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-logout/1'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-logout/1'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionLogout($show_status_on_completion = false)</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p>
            <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login-status/'))->__toString(); ?>">
                <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login-status/'))->__toString(); ?>
            </a>
        </p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionLoginStatus()</code></strong> under the hood</li>
        </ul>
        <br>
    </li>
    <li>
        <p><code><?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-there/'))->__toString(); ?>{first_name}/{last_name}</code></p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionThere($first_name, $last_name)</code></strong> under the hood</li>
            <li>you can do stuff like 
                <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-there/'))->__toString(); ?>john/doe">
                    <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-there/'))->__toString(); ?>john/doe
                </a>
            </li>
        </ul>
        <br>
    </li>
    <li>
        <p><code><?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-world/'))->__toString(); ?>{name}/{another_parameter}</code></p>
        <ul>
            <li>This link is mapped to <strong><code>\Slim3SkeletonMvcApp\Controllers\Hello::actionWorld($name, $another_param)</code></strong> under the hood</li>
            <li>you can do stuff like 
                <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-world/'))->__toString(); ?>john/doe">
                    <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-world/'))->__toString(); ?>john/doe
                </a>
            </li>
        </ul>
        <br>
    </li>
</ul>

<p>The <strong><code>action-</code></strong> prefix can be omitted from the links above if <strong><code>S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES</code></strong> is set to <strong><code>true</code></strong></p>
<ul>
    <li>
        For example 
        <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login/'))->__toString(); ?>">
            <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-login/'))->__toString(); ?>
        </a> will become 
        
        <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/login/'))->__toString(); ?>">
            <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/login/'))->__toString(); ?>
        </a> and 
        
        <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-there/john/doe'))->__toString(); ?>">
            <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/action-there/john/doe'))->__toString(); ?>
        </a> will become 
        
        <a href="<?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/there/john/doe'))->__toString(); ?>">
            <?php echo $uri_obj->withPath(s3MVC_MakeLink('/hello/there/john/doe'))->__toString(); ?>
        </a>
    </li>
</ul>

<hr>

<h4><strong>A little bit about Controllers and MVC:</strong></h4>
<ul>
    <li>
        <p>
            Controller classes must extend <code>\Slim3MvcTools\Controllers\BaseController</code>. 
            These classes must be named using studly case / caps e.g. <strong>StaticPages</strong>, 
            <strong>MobileDataProviders</strong> and must be referenced in the controller segment of 
            the url in all lowercases with dashes preceding capital case characters (except for the 
            first capital case character). <br><br>
            For example,
             
            <ul style="margin-left: 4em;">
                <li>
                    <code><?php echo $uri_obj->withPath(s3MVC_MakeLink('/mobile-data-providers/'))->__toString(); ?></code> 
                    will be responded to by the default action (defined via 
                    <strong>S3MVC_APP_DEFAULT_ACTION_NAME</strong>; default value is 
                    <strong>actionIndex</strong> ) method in the controller named 
                    <strong>MobileDataProviders</strong>,<br><br>
                </li>
                <li>
                    <code><?php echo $uri_obj->withPath(s3MVC_MakeLink('/mobile-data-providers/list-providers'))->__toString(); ?></code> 
                    or <code><?php echo $uri_obj->withPath(s3MVC_MakeLink('/mobile-data-providers/action-list-providers'))->__toString(); ?></code> 
                    (if <strong>S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES</strong> 
                    is set to <strong>false</strong>) will be responded to by the 
                    <strong>actionListProviders()</strong> method in the controller named 
                    <strong>MobileDataProviders</strong>, etc.
                </li>
            </ul>
        </p>
        <ul>
            <li>
                <strong>NOTE:</strong> there is a helper script available for creating Controller Classes and some default view files.
                <br>See <strong><code>./vendor/bin/smvc-create-controller</code></strong> or <strong><code>./vendor/bin/smvc-create-controller-wizard</code></strong><br><br>
            </li>
            
        </ul>
    </li>
    
    <li>
        <p>
            Controller action methods should be named using camel case; e.g. <strong>listProviders()</strong>. 
            In addition, they must be prefixed with the word <strong>action</strong> (e.g. <strong>actionListProviders()</strong> ) if 
            <strong>S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES</strong> is set to <code>true</code>.
        </p>
    </li>
    
    <li>
        <p>
            Action methods in Controller classes MUST either return a string (i.e. containing the output 
            to display to the client) or an instance of <strong>Psr\Http\Message\ResponseInterface</strong> 
            (e.g. <strong>$response</strong>, that has the output to be displayed to the client, injected into 
            it via <code>$response-&gt;getBody()-&gt;write($data)</code>, with <strong>$data</strong> containing
            a string value in this example).
        </p>
    </li>
    
    <li>
        <p>
            For more information on Controllers and MVC visit
            <a href="https://github.com/rotexsoft/slim3-skeleton-mvc-app/blob/master/documentation/MVCFUNCTIONALITY.md">
                here
            </a>.
        </p>
    </li>
</ul>
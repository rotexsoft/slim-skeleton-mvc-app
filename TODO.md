Add them to https://github.com/rotexsoft/slim-skeleton-mvc-app/issues moving forward. 

* Look into adding documentation about using $request->getQueryParams() to get at GET parameters as opposed to $_GET

* Look into adding documentation about using $request->getParsedBody() to get at POST parameters as opposed to $_POST
    
* in index.php look into adding a fallback exception handler via set_exception_handler to catch Exceptions that the errorHandler 
in the dependencies file can't catch or just wrap index.php with a try / catch.

* Look into writing automated tests for testing vanilla installs of apps created from this framework. 
I.E. test that index.php works as expected. Might not be as straightforward as using PHPunit. 
May have to use travis or something different. Look at https://github.com/slimphp/Slim-Skeleton/tree/master/tests/Functional for inspiration.

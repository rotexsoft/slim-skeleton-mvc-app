<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Site Title Goes Here</title>
        <link rel="stylesheet" href="<?php echo s3MVC_GetBaseUrlPath(); ?>/css/foundation552/foundation.css" />
        <script src="<?php echo s3MVC_GetBaseUrlPath(); ?>/js/foundation552/vendor/modernizr.js"></script>
    </head>
    <body>
        <div class="row">
            <div class="large-12 columns">
                <h1>Welcome to Your New Site</h1>
                <p>This site is powered by the <a href="https://bitbucket.org/cfsweb/slim3-skeleton-mvc-app">SlimPHP 3 Skeleton MVC App.</a></p>
            </div>
        </div>
        <div class="row">
            <div class="large-3 columns">
                <h1><img src="http://placehold.it/400x100&text=Logo"/></h1>
            </div>
            <div class="large-9 columns">
                <ul class="inline-list right">
                    <li><a href="#">Section 1</a></li>
                    <li><a href="#">Section 2</a></li>
                    <li><a href="#">Section 3</a></li>
                    <li><a href="#">Section 4</a></li>
                </ul>
            </div>
        </div>

        <div class="row">    
            <div class="large-9 push-3 columns">
                <?php echo $content; ?>                
            </div>

            <div class="large-3 pull-9 columns">
                <ul class="side-nav">
                    <li><a href="#">Section 1</a></li>
                    <li><a href="#">Section 2</a></li>
                    <li><a href="#">Section 3</a></li>
                    <li><a href="#">Section 4</a></li>
                    <li><a href="#">Section 5</a></li>
                    <li><a href="#">Section 6</a></li>
                </ul>

                <p><img src="http://placehold.it/320x240&text=Ad"/></p>
            </div>
        </div>

        <footer class="row">
            <div class="large-12 columns">
                <hr/>
                <div class="row">
                    <div class="large-6 columns">
                        <p>© Copyright no one at all. Go to town.</p>
                    </div>
                    <div class="large-6 columns">
                        <ul class="inline-list right">
                            <li><a href="#">Section 1</a></li>
                            <li><a href="#">Section 2</a></li>
                            <li><a href="#">Section 3</a></li>
                            <li><a href="#">Section 4</a></li>
                        </ul>
                    </div>
                </div>
            </div> 
        </footer>

        <script src="<?php echo s3MVC_GetBaseUrlPath(); ?>/js/foundation552/vendor/jquery.js"></script>
        <script src="<?php echo s3MVC_GetBaseUrlPath(); ?>/js/foundation552/foundation.min.js"></script>
        <script> $(document).foundation(); </script>
    </body>
</html>

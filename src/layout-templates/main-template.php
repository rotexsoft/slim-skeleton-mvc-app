<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Site Title Goes Here</title>
        <link rel="stylesheet" href="<?php echo s3MVC_MakeLink('/css/foundation/foundation.css'); ?>" />
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/jquery.js'); ?>"></script>
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

        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/what-input.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/foundation.min.js'); ?>"></script>
        <script> $(document).foundation(); </script>
    </body>
</html>

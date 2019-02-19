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
        <div class="grid-container">     
            <div class="grid-x  grid-padding-x">
                <div class="small-12 cell">
                    <ul class="menu" style="padding-left: 0;">
                        <li><a href="#">Section 1</a></li>
                        <li><a href="#">Section 2</a></li>
                        <li><a href="#">Section 3</a></li>
                    </ul>
                </div>
            </div>
            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <h1>Welcome to Your New Site</h1>
                    <p>This site is powered by the <a href="https://github.com/rotexsoft/slim3-skeleton-mvc-app">SlimPHP 3 Skeleton MVC App Micro-Framework</a> based on SlimPHP 3. It also ships with the <a href="http://foundation.zurb.com/">Foundation</a> UI framework. Everything you need to know about using the Foundation UI framework can be found <a href="http://foundation.zurb.com/docs">here</a>.</p>
                </div>
            </div>
            <div class="grid-x grid-padding-x">    
                <div class="small-12 cell">
                    <?php echo $content; ?>                
                </div>
            </div>
            <footer class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <hr/>
                    <div class="grid-x">
                        <div class="small-6 cell">
                            <p>© Copyright no one at all. Go to town.</p>
                        </div>
                    </div>
                </div> 
            </footer>
        </div><!-- <div class="grid-container"> -->

        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/what-input.js'); ?>"></script>
        <script src="<?php echo s3MVC_MakeLink('/js/foundation/vendor/foundation.min.js'); ?>"></script>
        <script> $(document).foundation(); </script>
    </body>
</html>

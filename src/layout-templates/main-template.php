<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Site Title Goes Here</title>
        <link rel="stylesheet" href="<?php echo $sMVC_MakeLink('/css/app.css'); ?>" />
    </head>
    <body>
        <div>
            <ul style="padding-left: 0;">
                <li style="display: inline;"><a href="#">Section 1</a></li>
                <li style="display: inline;"><a href="#">Section 2</a></li>
                <li style="display: inline;"><a href="#">Section 3</a></li>
            </ul>
        </div>

        <div>
            <h1>Welcome to Your New Site</h1>
            <p>This site is powered by the <a href="https://github.com/rotexsoft/slim-skeleton-mvc-app">SlimPHP 4 Skeleton MVC App.</a></p>
        </div>

        <br>

        <div>
            <div>
                <?php echo $content; ?>
            </div>
        </div>

        <footer>
            <div>
                <hr/>
                <p>© Copyright no one at all. Go to town.</p>
            </div>
        </footer>

        <script src="<?php echo $sMVC_MakeLink('/js/app.js'); ?>"></script>
    </body>
</html>

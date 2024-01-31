<!doctype html>
<!-- 
Please DO NOT add php code to this file, just pure html, css & js 
since it will be used to display error messages and should always
display correctly.
-->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>%s</title> <!-- title injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
        <style>
            body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif}
            h1{margin:0;font-size:48px;font-weight:normal;line-height:48px}
            strong{display:inline-block;width:65px}
        </style>
    </head>
    <body>
        <h1>%s</h1>     <!-- title injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
        <br>
        <div>%s</div>   <!-- error message body injected by \SlimMvcTools\HtmlErrorRenderer->renderHtmlBody(string $title = '', string $html = '') -->
        <br>
        <a href="#" onclick="window.history.go(-1)">Go Back</a>
    </body>
</html>

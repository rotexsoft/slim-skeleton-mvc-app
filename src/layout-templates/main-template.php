<!doctype html>
<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
?>
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
                <li style="display: inline;">
                    <a href="<?= sMVC_UriToString( 
                                    sMVC_AddQueryStrParamToUri(
                                        $controller_object->getRequest()->getUri(), 
                                        SlimMvcTools\Controllers\BaseController::GET_QUERY_PARAM_SELECTED_LANG, 
                                        'en_US'
                                    ) 
                                ); 
                            ?>">
                        <?= $__localeObj->gettext('base_controller_text_english'); ?>
                    </a>&nbsp;
                </li>
                
                <li style="display: inline;">
                    <a href="<?= sMVC_UriToString( 
                                    sMVC_AddQueryStrParamToUri(
                                        $controller_object->getRequest()->getUri(), 
                                        SlimMvcTools\Controllers\BaseController::GET_QUERY_PARAM_SELECTED_LANG, 
                                        'fr_CA'
                                    )
                                ); 
                            ?>">
                        <?= $__localeObj->gettext('base_controller_text_french'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h1><?= $__localeObj->gettext('main_template_text_header_1'); ?></h1>
            <p><?= $__localeObj->gettext('main_template_text_tagline_p_start'); ?> 
                <a href="https://github.com/rotexsoft/slim-skeleton-mvc-app">
                    <?= $__localeObj->gettext('main_template_text_tagline_p_end'); ?>.
                </a>
            </p>
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
                <p>&copy; <?= $__localeObj->gettext('main_template_text_copyright_footer'); ?> </p>
            </div>
        </footer>

        <script src="<?php echo $sMVC_MakeLink('/js/app.js'); ?>"></script>
    </body>
</html>

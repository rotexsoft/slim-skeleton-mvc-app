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
        <link rel="stylesheet" href="<?php echo $controller_object->makeLink('/css/app.css'); ?>" />
    </head>
    <body>
        <div>
            <ul style="padding-left: 0;">
                <li style="display: inline;">
                    <a href="<?= $controller_object->makeLink('/');?>">
                        <?= $__localeObj->gettext('main_template_text_home'); ?>
                    </a>&nbsp;
                </li>
                <li style="display: inline;">
                    <a href="<?= $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/action-routes");?>">
                        <?= $__localeObj->gettext('main_template_text_all_mvc_routes'); ?>
                    </a>&nbsp;
                </li>
                
                <li style="display: inline;">
                    <a href="<?= sMVC_AddLangSelectionParamToUri($controller_object->getRequest()->getUri(), 'en_US') ;?>">
                        <?= $__localeObj->gettext('base_controller_text_english'); ?>
                    </a>&nbsp;
                </li>
                
                <li style="display: inline;">
                    <a href="<?= sMVC_AddLangSelectionParamToUri($controller_object->getRequest()->getUri(), 'fr_CA') ;?>">
                        <?= $__localeObj->gettext('base_controller_text_french'); ?>
                    </a>&nbsp;
                </li>
                
                <?php if($controller_object->isLoggedIn()): ?>
                    <li style="display: inline;">
                        <a href="<?= $controller_object->getRequest()->getUri()->withPath($controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/action-logout"))->__toString(); ?>">
                            <?= $__localeObj->gettext('base_controller_text_logout'); ?>
                        </a>&nbsp;
                    </li>
                <?php else: ?>
                    <li style="display: inline;">
                        <a href="<?= $controller_object->getRequest()->getUri()->withPath($controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/action-login"))->__toString(); ?>">
                            <?= $__localeObj->gettext('base_controller_text_login'); ?>
                        </a>&nbsp;
                    </li>
                <?php endif; ?>
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

        <script src="<?php echo $controller_object->makeLink('/js/app.js'); ?>"></script>
    </body>
</html>

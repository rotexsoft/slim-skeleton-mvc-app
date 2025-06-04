<p><?php echo $message; ?></p>

<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
    $prepend_action = !SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login' ;
    $login_action_path = $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/$action");
                        
    $action1 = ($prepend_action) ? 'action-logout' : 'logout' ;
    $logout_action_path = $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/$action1/1");

    $action2 = ($prepend_action) ? 'action-login-status' : 'login-status' ;
    $login_status_action_path = $controller_object->makeLink("/{$controller_object->getControllerNameFromUri()}/$action2");
?>

<?php if( $is_logged_in ): ?>

    <p>
        <a href="<?php echo $login_status_action_path; ?>"><?= $__localeObj->gettext('base_controller_text_check_login_status'); ?></a>
        <form action="<?php echo $logout_action_path; ?>" method="post">
          <input type="submit" value="<?= $this->escapeHtmlAttr( $__localeObj->gettext('base_controller_text_logout') ); ?>">
        </form>
    </p>
    
<?php else: ?>
    
    <p> <a href="<?php echo $login_action_path; ?>"><?= $__localeObj->gettext('base_controller_text_login'); ?></a> </p>
    
<?php endif; ?>

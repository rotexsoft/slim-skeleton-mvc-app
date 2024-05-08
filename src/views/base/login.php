<?php
    /** @var \Vespula\Locale\Locale $__localeObj */
    /** @var \Rotexsoft\FileRenderer\Renderer $this */
    /** @var \SlimMvcTools\Controllers\BaseController $controller_object */
    $prepend_action = !SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login';
    $login_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action");
    
    $action1 = ($prepend_action) ? 'action-logout' : 'logout';
    $logout_action_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action1/0");
?>

<?php if( !empty($error_message) ): ?>

    <p style="background-color: orange;" id="login-form-errors"><?php echo $error_message;  ?></p>
    
<?php endif; ?>

<?php if( !$controller_object->isLoggedIn() ): ?>
    
    <form action="<?php echo $login_path; ?>" method="post">
        
        <div>
            <span><?= $__localeObj->gettext('base_controller_text_user_name'); ?>: </span>
            <input type="text" name="username" placeholder="<?= $this->escapeHtmlAttr( $__localeObj->gettext('base_controller_text_user_name') ); ?>" value="<?php echo $username; ?>">
        </div>
        <br>
        <div>
            <span><?= $__localeObj->gettext('base_controller_text_password'); ?>: </span>
            <input type="password" name="password" autocomplete="off" placeholder="<?= $this->escapeHtmlAttr( $__localeObj->gettext('base_controller_text_password') ); ?>" value="<?php echo $password; ?>">
        </div>
        <br>
        <div>
            <input type="submit" value="<?= $this->escapeHtmlAttr( $__localeObj->gettext('base_controller_text_login') ); ?>">
        </div>

    </form>
    
<?php else: ?>
    
    <form action="<?php echo $logout_action_path; ?>" method="post">
        
      <input type="submit" value="<?= $this->escapeHtmlAttr( $__localeObj->gettext('base_controller_text_logout') ); ?>">
      
    </form>
    
<?php endif; //if( !$controller_object->isLoggedIn() ): ?>

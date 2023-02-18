<p><?php echo $message; ?></p>

<?php
    $prepend_action = !SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login' ;
    $login_action_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action");
                        
    $action1 = ($prepend_action) ? 'action-logout' : 'logout' ;
    $logout_action_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action1/1");

    $action2 = ($prepend_action) ? 'action-login-status' : 'login-status' ;
    $login_status_action_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action2");
?>

<?php if( $is_logged_in ): ?>

    <p>
        <a href="<?php echo $login_status_action_path; ?>">Check Login Status</a>
        <form action="<?php echo $logout_action_path; ?>" method="post">
          <input type="submit" value="Logout">
        </form>
    </p>
    
<?php else: ?>
    
    <p> <a href="<?php echo $login_action_path; ?>">Log in</a> </p>
    
<?php endif; ?>

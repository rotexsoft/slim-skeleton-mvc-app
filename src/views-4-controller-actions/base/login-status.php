<p>
    <?php echo $message; ?>
</p>

<?php
    $login_action_path = $controller_object->getBasePath()
                        . "/{$controller_object->controller_name_from_uri}/action-login";
                        
    $logout_action_path = $controller_object->getBasePath()
                        . "/{$controller_object->controller_name_from_uri}/action-logout";
                        
    $check_login_status_action_path = 
            $controller_object->getBasePath()
            . "/{$controller_object->controller_name_from_uri}/action-check-login-status";
?>

<?php if( $is_logged_in ): ?>
    <p>
        <a href="<?php echo $check_login_status_action_path; ?>">Check Login Status</a>
        <form action="<?php echo $logout_action_path; ?>" method="post">
          <input type="submit" value="Logout">
        </form>
    </p>
<?php else: ?>
    <p>
        <a href="<?php echo $login_action_path; ?>">Log in</a>
    </p>
<?php endif; ?>

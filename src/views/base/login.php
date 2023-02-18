<?php
    $prepend_action = !SMVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login';
    $login_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action");
    
    $action1 = ($prepend_action) ? 'action-logout' : 'logout';
    $logout_action_path = $sMVC_MakeLink("/{$controller_object->getControllerNameFromUri()}/$action1/0");
?>

<?php if( !empty($error_message) ): ?>

    <p style="background-color: orange;"><?php echo $error_message;  ?></p>
    
<?php endif; ?>

<?php if( !$controller_object->isLoggedIn() ): ?>
    
    <form action="<?php echo $login_path; ?>" method="post">
        
        <div>
            <span>User Name: </span>
            <input type="text" name="username" placeholder="User Name" value="<?php echo $username; ?>">
        </div>
        <br>
        <div>
            <span>Password: </span>
            <input type="password" name="password" autocomplete="off" placeholder="Password" value="<?php echo $password; ?>">
        </div>
        <br>
        <div>
            <input type="submit" value="Login">
        </div>

    </form>
    
<?php else: ?>
    
    <form action="<?php echo $logout_action_path; ?>" method="post">
        
      <input type="submit" value="Logout">
      
    </form>
    
<?php endif; //if( !$controller_object->isLoggedIn() ): ?>

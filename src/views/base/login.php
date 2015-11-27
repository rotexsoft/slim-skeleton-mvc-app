<?php
    $prepend_action = !S3MVC_APP_AUTO_PREPEND_ACTION_TO_ACTION_METHOD_NAMES;

    $action = ($prepend_action) ? 'action-login' : 'login';
    $login_path = s3MVC_GetBaseUrlPath() 
                . "/{$controller_object->controller_name_from_uri}/$action";
    
    $action = ($prepend_action) ? 'action-logout' : 'logout';
    $logout_action_path = s3MVC_GetBaseUrlPath()
                        . "/{$controller_object->controller_name_from_uri}/$action/0";
?>

<?php if( !empty($error_message) ): ?>

    <p style="background-color: orange;"><?php echo $error_message;  ?></p>
    
<?php endif; ?>

<?php if( !$controller_object->isLoggedIn() ): ?>
    
    <form action="<?php echo $login_path; ?>" method="post">
        
        <div class="row">
            <div class="large-6 columns">
                <div class="row collapse prefix-radius">
                    <div class="small-3 columns">
                        <span class="prefix">User Name: </span>
                    </div>
                    <div class="small-9 columns">
                        <input type="text" name="username" placeholder="User Name" value="<?php echo $username; ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="large-6 columns">
                <div class="row collapse prefix-radius">
                    <div class="small-3 columns">
                        <span class="prefix">Password: </span>
                    </div>
                    <div class="small-9 columns">
                        <input type="password" name="password" autocomplete="off" placeholder="Password" value="<?php echo $password; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="large-6 columns push-4">
                <input type="submit" value="Login" class="button radius">
            </div>
        </div>

    </form>
    
<?php else: ?>
    
    <form action="<?php echo $logout_action_path; ?>" method="post">
        
      <input type="submit" value="Logout">
      
    </form>
    
<?php endif; //if( !$controller_object->isLoggedIn() ): ?>

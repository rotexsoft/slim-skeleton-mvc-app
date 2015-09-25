<?php
    $login_action_path = $controller_object->getBasePath()
                        . "/{$controller_object->controller_name_from_uri}/action-login";
    
    if(!empty($redirect_path)) {
        
        $login_action_path = "$login_action_path/$redirect_path";
    }
?>

<?php if( !empty($error_message) ): ?>
    <p style="background-color: orange;"><?php echo $error_message;  ?></p>
<?php endif; ?>

<form action="<?php echo $login_action_path; ?>" method="post">
  User Name: <input type="text" name="username"><br><br>
  Last name: <input type="password" name="password"><br><br>
  <input type="submit" value="Login">
</form>

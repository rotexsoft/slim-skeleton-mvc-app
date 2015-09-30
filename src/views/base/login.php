<?php
    $login_path = 
        s3MVC_GetBaseUrlPath() . "/{$controller_object->controller_name_from_uri}/action-login";
?>

<?php if( !empty($error_message) ): ?>
    <p style="background-color: orange;"><?php echo $error_message;  ?></p>
<?php endif; ?>

<form action="<?php echo $login_path; ?>" method="post">
  User Name: <input type="text" name="username"><br><br>
  Last name: <input type="password" name="password" autocomplete="off"><br><br>
  <input type="submit" value="Login">
</form>

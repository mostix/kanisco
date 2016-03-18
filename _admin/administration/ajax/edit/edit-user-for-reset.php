<?php

include_once("../../../config.php");
  
check_for_csrf();

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
}
if (isset($_POST['user_password'])) {
    $user_password = $_POST['user_password'];
}

if (!empty($user_id) && !empty($user_password)) {

  login();
  
  //update user password
  $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
  $bcrypt_password = crypt($user_password , $bcrypt_salt);
  $theQueryUpdateUser = "UPDATE `users` SET `user_salt` = '2', `user_salted_password` = '$bcrypt_password' WHERE `user_id` = '$user_id'";
  //echo $theQueryUpdateUser;exit;

  $resultUpdateUser = mysql_query($theQueryUpdateUser);
  if(!$resultUpdateUser) {
    echo mysql_error();
  }
  
  DB_Close();
}
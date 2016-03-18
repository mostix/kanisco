<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();

  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  if(isset($_POST['user_username'])) {
    $user_username = mysqli_real_escape_string($db_link,$_POST['user_username']);
  }
  if(isset($_POST['user_password'])) {
    $user_password = $_POST['user_password'];
  }

  if (!empty($user_id) && !empty($user_username)) {

    $query = "SELECT `user_id` FROM `users` WHERE `user_username` = '$user_username' AND `user_id` <> '$user_id'";
    //echo $query;
    $result = mysqli_query($db_link,$query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo $languages[$current_lang]['error_username_taken'];
      exit;

    }

    //update user name, password or  status
    $query_update_user = "UPDATE `users` SET `user_username` = '$user_username'";
    //if password is filled
    if(!empty($user_password)) {
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
      $query_update_user .= ", `user_salted_password` = '$bcrypt_password'";
    }
    $query_update_user .= " WHERE `user_id` = '$user_id'";
    //echo $query_update_user;exit;

    $result_update_user = mysqli_query($db_link,$query_update_user);
    if(!$result_update_user) {
      echo mysqli_error();
    }
  }
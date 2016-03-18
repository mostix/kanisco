<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  
  if(!empty($user_id)) {
    
    $query = "UPDATE `users` SET `user_ip` = NULL WHERE `user_id` '$user_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
  DB_CloseI($db_link);
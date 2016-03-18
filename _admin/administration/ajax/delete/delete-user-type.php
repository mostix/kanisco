<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['user_type_id'])) {
    $user_type_id = $_POST['user_type_id'];
  }
  
  if(!empty($user_type_id) && ($user_type_id != 1 || $user_type_id != 2)) {
    $query = "DELETE FROM `user_types` WHERE `user_type_id` = '$user_type_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else {
      $query = "DELETE FROM `users_types` WHERE `user_type_id` = '$user_type_id'";
      echo $query;exit;
      $result = mysqli_query($db_link, $query);
      if(!$result) {
        echo mysqli_error($db_link);
      }
      else {
        
      }
    }
  }
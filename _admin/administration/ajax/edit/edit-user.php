<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();

  if (isset($_POST['user_id'])) {
      $user_id = $_POST['user_id'];
  }
  if (isset($_POST['user_name'])) {
      $user_username = $_POST['user_name'];
  }
  if (isset($_POST['user_password'])) {
      $user_password = $_POST['user_password'];
  }
  if (isset($_POST['user_is_ip_in_use'])) {
      $user_is_ip_in_use = $_POST['user_is_ip_in_use'];
  }
  if (isset($_POST['warehouse_id'])) {
      $warehouse_id = $_POST['warehouse_id'];
  }
  if (isset($_POST['user_is_active'])) {
      $user_is_active = $_POST['user_is_active'];
  }
  if (isset($_POST['user_rights'])) {
      $user_rights = $_POST['user_rights'];
  }
  //print_r($_POST);exit;

  if(!empty($user_id) && !empty($user_username)) {

    $query = "SELECT `user_id` FROM `users` WHERE `user_username` = '$user_username' AND `user_id` <> '$user_id'";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo "This username is already taken. Please choose another one";
      exit;
    }

    mysqli_query($db_link, "START TRANSACTION");

    $user_username = mysqli_real_escape_string($db_link,$user_username);

    //update user name, password or  status
    $query_update_user = "UPDATE `users` SET `user_username` = '$user_username', ";
    //if password is filled
    if(!empty($user_password)) {
        $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
        $bcrypt_password = crypt($user_password , $bcrypt_salt);
        $query_update_user .= "`user_salted_password` = '" . $bcrypt_password . "', ";
    }
    $query_update_user .= "`user_is_active` = '$user_is_active', `user_is_ip_in_use` = '$user_is_ip_in_use' WHERE `user_id` = '$user_id'";
    $all_queries = $query_update_user;
    //echo $query_update_user;exit;
    $result_update_user = mysqli_query($db_link, $query_update_user);
    if(!$result_update_user) echo "User data not changed!".  mysqli_error($db_link);

    //delete rights for pages
    $query_delete_rights = "DELETE FROM `users_rights` WHERE `user_id` = '$user_id'";
    $all_queries .= "\n".$query_delete_rights;
    $result_delete_rights = mysqli_query($db_link, $query_delete_rights);
    if(!$result_delete_rights) echo "User data not changed! 2. ".mysqli_error($db_link);

    //insert new rights
    if(!empty($user_rights)) {
        foreach($user_rights as $array_rights) {
          $menu_id = $array_rights[0];
          $users_rights_edit = $array_rights[1];
          $users_rights_delete = $array_rights[2];
          $query_insert_rights = "INSERT INTO `users_rights` SET `user_id` = '$user_id', 
                                                                  `menu_id` = '$menu_id', 
                                                                  `users_rights_edit` = '$users_rights_edit', 
                                                                  `users_rights_delete` = '$users_rights_delete'";
          $result_insert_rights = mysqli_query($db_link, $query_insert_rights);
          $all_queries .= "\n".$query_insert_rights;
          if($result_insert_rights) {
              if( mysqli_affected_rows($db_link) <= 0) {
                  mysqli_query($db_link, "ROLLBACK");
                  echo "User data not changed! 3. ".$query_insert_rights.mysqli_error($db_link);
                  exit();
              }
          } else {
              mysqli_query($db_link, "ROLLBACK");
              echo "User data not changed! 4. ".$query_insert_rights.mysqli_error($db_link);
              exit();
          }
        }
    }

//    echo $all_queries;
//    mysqli_query($db_link, "ROLLBACK");exit;

    mysqli_query($db_link, "COMMIT");
  }// if( !empty($user_id) && !empty($user_username) )
?>

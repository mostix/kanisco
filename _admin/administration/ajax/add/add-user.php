<?php

include_once("../../../config.php");
  
$db_link = DB_OpenI();
  
check_for_csrf();

if (isset($_POST['user_type_id'])) {
    $user_type_id = $_POST['user_type_id'];
}
if (isset($_POST['user_username'])) {
    $user_username = $_POST['user_username'];
}
if (isset($_POST['user_password'])) {
    $user_password = $_POST['user_password'];
}
if (isset($_POST['user_firstname'])) {
    $user_firstname = mysqli_real_escape_string($db_link,$_POST['user_firstname']);
}
if (isset($_POST['user_lastname'])) {
    $user_lastname = mysqli_real_escape_string($db_link,$_POST['user_lastname']);
}
if (isset($_POST['user_address'])) {
    $user_address = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['user_address']));
}
if (isset($_POST['user_phone'])) {
    $user_phone = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['user_phone']));
}
if (isset($_POST['user_email'])) {
    $user_email = $_POST['user_email'];
}
if (isset($_POST['user_info'])) {
    $user_info = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['user_info']));
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
if (isset($_POST['create_user_account'])) {
    $create_user_account = $_POST['create_user_account'];
}
//print_r($_POST);exit;

if(!empty($user_type_id) && !empty($user_firstname)) {
  
  if($create_user_account == 1 && empty($user_email)) {
    echo $laguages[$default_lang]['error_create_user_no_email'];
    exit;
  }
  if($create_user_account == 1 && empty($user_username)) {
    echo $laguages[$default_lang]['error_create_user_no_username'];
    exit;
  }

  $all_queries = "";
  
  if($create_user_account == 1 && !empty($user_username)) {
    $query = "SELECT `user_id` FROM `users` WHERE `user_username` = '$user_username'";
    $all_queries .= $query."\n";
    //echo $query;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo $laguages[$default_lang]['error_create_user_username_taken'];
      exit;
    }
  }
     
  mysqli_query($db_link, "START TRANSACTION");

  $user_username = mysqli_real_escape_string($db_link,$user_username);
  $user_email = prepare_for_null_row(mysqli_real_escape_string($db_link,$user_email));
  $bcrypt_password = "";
  if ($create_user_account == 1 && empty($user_username)) {
    $user_username = str_replace(" ", "_", strtolower(str_replace(".", "", $user_firstname)));
    $user_username .= str_replace(" ", "_", strtolower(str_replace(".", "", $user_lastname)));
  }
  if (!empty($user_password)) {
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
  }
  else {
    if ($create_user_account == 1) {
      $user_password = generateRandomString(8);
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
    }
  }
  
  $user_username = prepare_for_null_row($user_username);
  
  //update user
  $query_insert_user = "INSERT INTO `users`(`user_id`, 
                                            `user_type_id`, 
                                            `warehouse_id`, 
                                            `user_username`, 
                                            `user_salted_password`, 
                                            `user_firstname`, 
                                            `user_lastname`, 
                                            `user_address`, 
                                            `user_phone`, 
                                            `user_email`, 
                                            `user_info`, 
                                            `user_ip`, 
                                            `user_is_ip_in_use`, 
                                            `user_is_active`, 
                                            `user_has_account`) 
                                      VALUES('',
                                             '$user_type_id',
                                             '$warehouse_id',
                                             $user_username,
                                             '$bcrypt_password',
                                             '$user_firstname',
                                             '$user_lastname',
                                             $user_address,
                                             $user_phone,
                                             $user_email,
                                             $user_info,
                                             NULL,
                                             '$user_is_ip_in_use',
                                             '$user_is_active',
                                             '$create_user_account')";
  $all_queries .= $query_insert_user."\n";
  //echo $query_update_user;exit;
  $result_insert_user = mysqli_query($db_link, $query_insert_user);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $laguages[$default_lang]['sql_error_insert'];
    mysqli_query($db_link, "ROLLBACK");
    exit;
  }
  
  $user_id = mysqli_insert_id($db_link);


//  echo $all_queries;
//  mysqli_query($db_link, "ROLLBACK");exit;
  if ($create_user_account == 1) {
    // create account for the user

//    $to      = "idimitrov@eterrasystems.com";
    $to      = $_POST['user_email'];
    $subject = $laguages[$default_lang]['email_subject_text'];
    $message = "<table>";
    $message .= "<tr><td>".$laguages[$default_lang]['email_message_text_1']."</td></tr>";
    $message .= "<tr><td>".$laguages[$default_lang]['email_message_text_2'].": $user_username</td></tr>";
    $message .= "<tr><td>".$laguages[$default_lang]['email_message_text_3'].": $user_password</td></tr>";
    $message .= "<tr><td>".$laguages[$default_lang]['email_message_text_4'].": http://tyres-log.eterrasystems.eu/</td></tr>";
    $message .= "<tr><td>&nbsp;</td></tr>";
    $message .= "<tr><td>&nbsp;</td></tr>";
    $message .= "</table>";
    $headers = $laguages[$default_lang]['email_headers_text'];
    $headers .= 'Cc: idimitrov@eterrasystems.com' . "\r\n";
          
    // give default rights to new user depending on the user type
    if($user_type_id == 1) {
      // administrators
      $query = "SELECT DISTINCT `menu_id` FROM `users_rights`";
      $all_queries .= $query."\n";
      //echo $query;
      $result = mysqli_query($db_link, $query);
      if(!$result) echo mysqli_error($db_link);
      if(mysqli_num_rows($result) > 0) {
        while($user_rights = mysqli_fetch_assoc($result)) {
          $menu_id = $user_rights['menu_id'];
          $query_user_rights = "INSERT INTO `users_rights`(
                                            `users_rights_id`, 
                                            `user_id`, 
                                            `menu_id`,  
                                            `users_rights_edit`, 
                                            `users_rights_delete`)
                                    VALUES ('',
                                            '$user_id',
                                            '$menu_id',
                                            '1',
                                            '1')";
          $all_queries .= $query_user_rights."\n";
          $result_user_rights = mysqli_query($db_link, $query_user_rights);
          if(!$result_user_rights) echo mysqli_error($db_link);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $laguages[$default_lang]['sql_error_insert'];
            mysqli_query($db_link, "ROLLBACK");
            exit;
          }
        }
      }
    }
    elseif($user_type_id == 2) {
      // regular user, leave it empty for now
      $query_user_rights = "INSERT INTO `users_rights`(
                                        `users_rights_id`, 
                                        `user_id`, 
                                        `menu_id`,  
                                        `users_rights_edit`, 
                                        `users_rights_delete`)
                                VALUES ('',
                                        '$user_id',
                                        '2',
                                        '0',
                                        '0')";
      $all_queries .= $query_user_rights."\n";
      $result_user_rights = mysqli_query($db_link, $query_user_rights);
      if(!$result_user_rights) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $laguages[$default_lang]['sql_error_insert'];
        mysqli_query($db_link, "ROLLBACK");
        exit;
      }
      $query_user_rights = "INSERT INTO `users_rights`(
                                        `users_rights_id`, 
                                        `user_id`, 
                                        `menu_id`,  
                                        `users_rights_edit`, 
                                        `users_rights_delete`)
                                VALUES ('',
                                        '$user_id',
                                        '36',
                                        '1',
                                        '1')";
      $all_queries .= $query_user_rights."\n";
      $result_user_rights = mysqli_query($db_link, $query_user_rights);
      if(!$result_user_rights) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $laguages[$default_lang]['sql_error_insert'];
        mysqli_query($db_link, "ROLLBACK");
        exit;
      }
    }
    else {
      // user_type_id == 3, clients
      $query_user_rights = "INSERT INTO `users_rights`(
                                        `users_rights_id`, 
                                        `user_id`, 
                                        `menu_id`,  
                                        `users_rights_edit`, 
                                        `users_rights_delete`)
                                VALUES ('',
                                        '$user_id',
                                        '2',
                                        '0',
                                        '0')";
      $all_queries .= $query_user_rights."\n";
      $result_user_rights = mysqli_query($db_link, $query_user_rights);
      if(!$result_user_rights) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $laguages[$default_lang]['sql_error_insert'];
        mysqli_query($db_link, "ROLLBACK");
        exit;
      }
      $query_user_rights = "INSERT INTO `users_rights`(
                                        `users_rights_id`, 
                                        `user_id`, 
                                        `menu_id`,  
                                        `users_rights_edit`, 
                                        `users_rights_delete`)
                                VALUES ('',
                                        '$user_id',
                                        '36',
                                        '1',
                                        '1')";
      $all_queries .= $query_user_rights."\n";
      $result_user_rights = mysqli_query($db_link, $query_user_rights);
      if(!$result_user_rights) echo mysqli_error($db_link);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $laguages[$default_lang]['sql_error_insert'];
        mysqli_query($db_link, "ROLLBACK");
        exit;
      }
    }
    
    if(filter_var($to, FILTER_VALIDATE_EMAIL)) {
      //echo "$to, $subject, $message, $headers";
      if(mail($to, $subject, $message, $headers)) {
        echo $laguages[$default_lang]['create_user_success_text_1']." $user_firstname $user_lastname ".$laguages[$default_lang]['create_user_success_text_2']." $to";
      }
      else {
        //print_r(error_get_last());
        echo $laguages[$default_lang]['error_create_user_send_fail'];
        mysqli_query($db_link, "ROLLBACK");
        exit;
      }
    }
    else {
      echo $laguages[$default_lang]['error_create_user_valid_email'];
      mysqli_query($db_link, "ROLLBACK");
      exit;
    }
  }
  
  //echo $all_queries;mysqli_query($db_link, "ROLLBACK");exit;
  
  mysqli_query($db_link, "COMMIT");
  
  DB_CloseI($db_link);
}// if( !empty($user_id) && !empty($user_username) )
else {
  echo $laguages[$default_lang]['error_create_user_minimum_data'];
}
?>
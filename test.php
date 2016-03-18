<?php

  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
//  include_once 'frontstore/functions/include-functions.php';
  include_once '_admin/functions/include-functions.php';
  
  start_page_build_time_measure();
  
//  $db_name = "civil3db_kanisco";
//  $table_name = "products";
//  
//  $column_names_array = get_column_names($db_name,$table_name);
//  echo "<pre>";print_r($column_names_array);

//  mysqli_query($db_link,"BEGIN");
//  
//  $all_queries = "";
  
  $user_type_id = 2;
  $user_username = "kpavlov";
  $user_password = "OTW-BG1!";
  $bcrypt_password = "";
  $user_firstname = "K";
  $user_lastname = "Pavlov";
  $user_address = "NULL";
  $user_phone = "NULL";
  $user_email = "NULL";
  $user_info = "NULL";
  $user_address = "NULL";
  $user_ip = "NULL";
  $user_is_ip_in_use = 1;
  $user_is_active = 1;
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
  
//  $query_update_user = "UPDATE `users` SET `user_salted_password` = '$bcrypt_password' WHERE `user_id` = '1'";
//  $query_insert_user = "INSERT INTO `users`(`user_id`, 
//                                          `user_type_id`, 
//                                          `user_username`, 
//                                          `user_salted_password`, 
//                                          `user_firstname`, 
//                                          `user_lastname`, 
//                                          `user_address`, 
//                                          `user_phone`, 
//                                          `user_email`, 
//                                          `user_info`, 
//                                          `user_ip`, 
//                                          `user_is_ip_in_use`, 
//                                          `user_is_active`) 
//                                  VALUES ('',
//                                          '$user_type_id',
//                                          '$user_username',
//                                          '$bcrypt_password',
//                                          '$user_firstname',
//                                          '$user_lastname',
//                                          '$user_address',
//                                          '$user_phone',
//                                          '$user_email',
//                                          '$user_info',
//                                          '$user_ip',
//                                          '$user_is_ip_in_use',
//                                          '$user_is_active')";
//  //echo $query_update_user;exit;
//  $result_insert_user = mysqli_query($db_link, $query_insert_user);
//  if(!$result_insert_user) {
//    mysqli_query($db_link, "ROLLBACK");
//    exit;
//  }
  
//  $query_users_rights = "SELECT `menu_id`, `users_rights_edit`, `users_rights_delete` FROM `users_rights` WHERE `user_id` = '2'";
//  $result_users_rights = mysqli_query($db_link, $query_users_rights);
//  if(!$result_users_rights) echo mysqli_error($db_link);
//  if(mysqli_num_rows($result_users_rights) > 0) {
//    while($users_rights_row = mysqli_fetch_assoc($result_users_rights)) {
//      
//      $menu_id = $users_rights_row['menu_id'];
//      $users_rights_edit = $users_rights_row['users_rights_edit'];
//      $users_rights_delete = $users_rights_row['users_rights_delete'];
//
//      $query_insert_users_rights = "INSERT INTO `users_rights`(`users_rights_id`, `user_id`, `menu_id`, `users_rights_edit`, `users_rights_delete`) 
//                                                        VALUES ('','7','$menu_id','$users_rights_edit','$users_rights_delete')";
//      //echo $query_insert_users_rights;
//      //$all_queries .= "<br>".$query_insert_users_rights;
//      $result_insert_users_rights = mysqli_query($db_link, $query_insert_users_rights);
//      if(mysqli_affected_rows($db_link) <= 0) {
//        echo $languages[$current_lang]['sql_error_insert']." - `users_rights` ".mysqli_error($db_link);
//        mysqli_query($db_link,"ROLLBACK");
//        exit;
//      }
//    }
//  }
//  //echo $all_queries;mysqli_rollback($db_link);exit;
//     
//  echo "done - 7";
//  mysqli_commit($db_link);
  
  /*
  mysqli_select_db($db_link, 'civil3db_kanisco');
  mysqli_set_charset($db_link, 'utf8');

  $sql = 'SHOW TABLE STATUS FROM `civil3db_kanisco`;';
  $result = mysqli_query($db_link, $sql);

  $rows = array();
  while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
  }

  foreach ($rows as $row) {
    $table_name = mysqli_real_escape_string($db_link, $row['Name']);
    if($table_name != "captchas" && $table_name != "countries" && $table_name != "currency" && $table_name != "length_class" && $table_name != "length_class_description" 
      && $table_name != "menus" && $table_name != "menus_translations" && $table_name != "order_statuses" && $table_name != "sites" && $table_name != "stock_status" 
      && $table_name != "users" && $table_name != "users_rights" && $table_name != "users_types" && $table_name != "weight_class" && $table_name != "weight_class_description") {
//      $sql = 'DROP TABLE IF EXISTS `' . $table_name . '`;';
//      $sql = 'TRUNCATE TABLE `' . $table_name . '`;';
//      mysqli_query($db_link, $sql);
      echo "$table_name<br>";
    }
  }
   */
  
  
  close_page_build_time_measure($print_time = true);
?>
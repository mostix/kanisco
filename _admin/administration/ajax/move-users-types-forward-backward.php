<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['user_type_id'])) {
    $user_type_id =  $_POST['user_type_id'];
  }
  if(isset($_POST['user_type_sort_order'])) {
    $user_type_sort_order =  $_POST['user_type_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($user_type_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_user_type_sort_order = $user_type_sort_order-1;
      $query_update_user_type_1 = "UPDATE `users_types` SET `user_type_sort_order`='$user_type_sort_order' WHERE `user_type_sort_order` = '$previous_user_type_sort_order'";
      $all_queries .= "\n".$query_update_user_type_1;
        //echo $query_update_user_type_1;
      $result_update_user_type_1 = mysqli_query($db_link, $query_update_user_type_1);
      if(!$result_update_user_type_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_user_type_2 = "UPDATE `users_types` SET `user_type_sort_order`='$previous_user_type_sort_order' WHERE `user_type_id` = '$user_type_id'";
      $all_queries .= "\n".$query_update_user_type_2;
        //echo $query_update_user_type_2;
      $result_update_user_type_2 = mysqli_query($db_link, $query_update_user_type_2);
      if(!$result_update_user_type_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_user_type_sort_order = $user_type_sort_order+1;
      $query_update_user_type_1 = "UPDATE `users_types` SET `user_type_sort_order`='$user_type_sort_order' WHERE `user_type_sort_order` = '$next_user_type_sort_order'";
      $all_queries .= "\n".$query_update_user_type_1;
        //echo $query_update_user_type_1;
      $result_update_user_type_1 = mysqli_query($db_link, $query_update_user_type_1);
      if(!$result_update_user_type_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_user_type_2 = "UPDATE `users_types` SET `user_type_sort_order`='$next_user_type_sort_order' WHERE `user_type_id` = '$user_type_id'";
      $all_queries .= "\n".$query_update_user_type_2;
        //echo $query_update_user_type_2;
      $result_update_user_type_2 = mysqli_query($db_link, $query_update_user_type_2);
      if(!$result_update_user_type_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_users_types($current_language_id);

  }
?>

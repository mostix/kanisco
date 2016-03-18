<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['option_id'])) {
    $option_id =  $_POST['option_id'];
  }
  if(isset($_POST['option_sort_order'])) {
    $option_sort_order =  $_POST['option_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($option_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_option_sort_order = $option_sort_order-1;
      $query_update_option_1 = "UPDATE `options` SET `option_sort_order`='$option_sort_order' WHERE `option_sort_order` = '$previous_option_sort_order' ";
      $all_queries .= "\n".$query_update_option_1;
        //echo $query_update_option_1;
      $result_update_option_1 = mysqli_query($db_link, $query_update_option_1);
      if(!$result_update_option_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_option_2 = "UPDATE `options` SET `option_sort_order`='$previous_option_sort_order' WHERE `option_id` = '$option_id'";
      $all_queries .= "\n".$query_update_option_2;
        //echo $query_update_option_2;
      $result_update_option_2 = mysqli_query($db_link, $query_update_option_2);
      if(!$result_update_option_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_option_sort_order = $option_sort_order+1;
      $query_update_option_1 = "UPDATE `options` SET `option_sort_order`='$option_sort_order' WHERE `option_sort_order` = '$next_option_sort_order' ";
      $all_queries .= "\n".$query_update_option_1;
        //echo $query_update_option_1;
      $result_update_option_1 = mysqli_query($db_link, $query_update_option_1);
      if(!$result_update_option_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_option_2 = "UPDATE `options` SET `option_sort_order`='$next_option_sort_order' WHERE `option_id` = '$option_id'";
      $all_queries .= "\n".$query_update_option_2;
        //echo $query_update_option_2;
      $result_update_option_2 = mysqli_query($db_link, $query_update_option_2);
      if(!$result_update_option_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_products_options();

  }
?>
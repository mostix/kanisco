<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['instructor_id'])) {
    $instructor_id =  $_POST['instructor_id'];
  }
  if(isset($_POST['instructor_sort_order'])) {
    $instructor_sort_order =  $_POST['instructor_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($instructor_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_instructor_sort_order = $instructor_sort_order-1;
      $query_update_instructor_1 = "UPDATE `instructors` SET `instructor_sort_order`='$instructor_sort_order' WHERE `instructor_sort_order` = '$previous_instructor_sort_order'";
      $all_queries .= "\n".$query_update_instructor_1;
        //echo $query_update_instructor_1;
      $result_update_instructor_1 = mysqli_query($db_link, $query_update_instructor_1);
      if(!$result_update_instructor_1) {
        echo $instructors[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_instructor_2 = "UPDATE `instructors` SET `instructor_sort_order`='$previous_instructor_sort_order' WHERE `instructor_id` = '$instructor_id'";
      $all_queries .= "\n".$query_update_instructor_2;
        //echo $query_update_instructor_2;
      $result_update_instructor_2 = mysqli_query($db_link, $query_update_instructor_2);
      if(!$result_update_instructor_2) {
        echo $instructors[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_instructor_sort_order = $instructor_sort_order+1;
      $query_update_instructor_1 = "UPDATE `instructors` SET `instructor_sort_order`='$instructor_sort_order' WHERE `instructor_sort_order` = '$next_instructor_sort_order'";
      $all_queries .= "\n".$query_update_instructor_1;
        //echo $query_update_instructor_1;
      $result_update_instructor_1 = mysqli_query($db_link, $query_update_instructor_1);
      if(!$result_update_instructor_1) {
        echo $instructors[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_instructor_2 = "UPDATE `instructors` SET `instructor_sort_order`='$next_instructor_sort_order' WHERE `instructor_id` = '$instructor_id'";
      $all_queries .= "\n".$query_update_instructor_2;
        //echo $query_update_instructor_2;
      $result_update_instructor_2 = mysqli_query($db_link, $query_update_instructor_2);
      if(!$result_update_instructor_2) {
        echo $instructors[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_instructors();

  }
?>

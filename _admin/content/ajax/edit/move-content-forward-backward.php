<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_id'])) {
    $content_id =  $_POST['content_id'];
  }
  if(isset($_POST['content_parent_id'])) {
    $content_parent_id =  $_POST['content_parent_id'];
  }
  if(isset($_POST['content_menu_order'])) {
    $content_menu_order =  $_POST['content_menu_order'];
  }
  if(isset($_POST['content_hierarchy_level'])) {
    $content_hierarchy_level =  $_POST['content_hierarchy_level'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($content_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_content_menu_order = $content_menu_order-1;
      $query_update_content_1 = "UPDATE `contents` SET `content_menu_order`='$content_menu_order' 
                                WHERE `content_parent_id` = '$content_parent_id' AND `content_menu_order` = '$previous_content_menu_order' 
                                  AND `content_hierarchy_level` = '$content_hierarchy_level'";
      $all_queries .= "\n".$query_update_content_1;
        //echo $query_update_content_1;
      $result_update_content_1 = mysqli_query($db_link, $query_update_content_1);
      if(!$result_update_content_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_content_2 = "UPDATE `contents` SET `content_menu_order`='$previous_content_menu_order' WHERE `content_id` = '$content_id'";
      $all_queries .= "\n".$query_update_content_2;
        //echo $query_update_content_2;
      $result_update_content_2 = mysqli_query($db_link, $query_update_content_2);
      if(!$result_update_content_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_content_menu_order = $content_menu_order+1;
      $query_update_content_1 = "UPDATE `contents` SET `content_menu_order`='$content_menu_order' 
                              WHERE `content_parent_id` = '$content_parent_id' AND `content_menu_order` = '$next_content_menu_order' 
                                AND `content_hierarchy_level` = '$content_hierarchy_level'";
      $all_queries .= "\n".$query_update_content_1;
        //echo $query_update_content_1;
      $result_update_content_1 = mysqli_query($db_link, $query_update_content_1);
      if(!$result_update_content_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_content_2 = "UPDATE `contents` SET `content_menu_order`='$next_content_menu_order' WHERE `content_id` = '$content_id'";
      $all_queries .= "\n".$query_update_content_2;
        //echo $query_update_content_2;
      $result_update_content_2 = mysqli_query($db_link, $query_update_content_2);
      if(!$result_update_content_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_contents($parent_id = 0, $path_number = 0);

  }
?>

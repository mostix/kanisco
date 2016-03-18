<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['language_id'])) {
    $language_id =  $_POST['language_id'];
  }
  if(isset($_POST['language_menu_order'])) {
    $language_menu_order =  $_POST['language_menu_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($language_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_language_menu_order = $language_menu_order-1;
      $query_update_language_1 = "UPDATE `languages` SET `language_menu_order`='$language_menu_order' WHERE `language_menu_order` = '$previous_language_menu_order'";
      $all_queries .= "\n".$query_update_language_1;
        //echo $query_update_language_1;
      $result_update_language_1 = mysqli_query($db_link, $query_update_language_1);
      if(!$result_update_language_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_language_2 = "UPDATE `languages` SET `language_menu_order`='$previous_language_menu_order' WHERE `language_id` = '$language_id'";
      $all_queries .= "\n".$query_update_language_2;
        //echo $query_update_language_2;
      $result_update_language_2 = mysqli_query($db_link, $query_update_language_2);
      if(!$result_update_language_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_language_menu_order = $language_menu_order+1;
      $query_update_language_1 = "UPDATE `languages` SET `language_menu_order`='$language_menu_order' WHERE `language_menu_order` = '$next_language_menu_order'";
      $all_queries .= "\n".$query_update_language_1;
        //echo $query_update_language_1;
      $result_update_language_1 = mysqli_query($db_link, $query_update_language_1);
      if(!$result_update_language_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_language_2 = "UPDATE `languages` SET `language_menu_order`='$next_language_menu_order' WHERE `language_id` = '$language_id'";
      $all_queries .= "\n".$query_update_language_2;
        //echo $query_update_language_2;
      $result_update_language_2 = mysqli_query($db_link, $query_update_language_2);
      if(!$result_update_language_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_languages();

  }
?>

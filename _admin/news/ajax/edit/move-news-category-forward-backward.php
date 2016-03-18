<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['news_category_id'])) {
    $news_category_id =  $_POST['news_category_id'];
  }
  if(isset($_POST['news_cat_parent_id'])) {
    $news_cat_parent_id =  $_POST['news_cat_parent_id'];
  }
  if(isset($_POST['news_cat_sort_order'])) {
    $news_cat_sort_order =  $_POST['news_cat_sort_order'];
  }
  if(isset($_POST['news_cat_hierarchy_level'])) {
    $news_cat_hierarchy_level =  $_POST['news_cat_hierarchy_level'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($news_category_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_news_cat_sort_order = $news_cat_sort_order-1;
      $query_update_news_categories_1 = "UPDATE `news_categories` SET `news_cat_sort_order`='$news_cat_sort_order' 
                                        WHERE `news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_sort_order` = '$previous_news_cat_sort_order' 
                                          AND `news_cat_hierarchy_level` = '$news_cat_hierarchy_level'";
      $all_queries .= "<br>\n".$query_update_news_categories_1;
        //echo $query_update_news_categories_1;
      $result_update_news_categories_1 = mysqli_query($db_link, $query_update_news_categories_1);
      if(!$result_update_news_categories_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_news_categories_2 = "UPDATE `news_categories` SET `news_cat_sort_order`='$previous_news_cat_sort_order' WHERE `news_category_id` = '$news_category_id'";
      $all_queries .= "<br>\n".$query_update_news_categories_2;
        //echo $query_update_news_categories_2;
      $result_update_news_categories_2 = mysqli_query($db_link, $query_update_news_categories_2);
      if(!$result_update_news_categories_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_news_cat_sort_order = $news_cat_sort_order+1;
      $query_update_news_categories_1 = "UPDATE `news_categories` SET `news_cat_sort_order`='$news_cat_sort_order' 
                                        WHERE `news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_sort_order` = '$next_news_cat_sort_order' 
                                          AND `news_cat_hierarchy_level` = '$news_cat_hierarchy_level'";
      $all_queries .= "<br>\n".$query_update_news_categories_1;
        //echo $query_update_news_categories_1;
      $result_update_news_categories_1 = mysqli_query($db_link, $query_update_news_categories_1);
      if(!$result_update_news_categories_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_news_categories_2 = "UPDATE `news_categories` SET `news_cat_sort_order`='$next_news_cat_sort_order' WHERE `news_category_id` = '$news_category_id'";
      $all_queries .= "<br>\n".$query_update_news_categories_2;
        //echo $query_update_news_categories_2;
      $result_update_news_categories_2 = mysqli_query($db_link, $query_update_news_categories_2);
      if(!$result_update_news_categories_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_news_categories($news_cat_parent_id = 0, $path_number = 0);

  }
?>
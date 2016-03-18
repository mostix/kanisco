<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['news_category_id'])) {
    $news_category_id =  $_POST['news_category_id'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($news_category_id) && !empty($action)) {
    
    $news_cat_is_collapsed = ($action == "expand") ? 0 : 1; // else $action == collapse
    
    if($news_category_id == "all") {
      $query_update_news_categories = "UPDATE `news_categories` SET  `news_cat_is_collapsed`='$news_cat_is_collapsed' WHERE `news_cat_has_children` = '1'"; 
    }
    else {
      $query_update_news_categories = "UPDATE `news_categories` SET  `news_cat_is_collapsed`='$news_cat_is_collapsed' WHERE `news_category_id` = '$news_category_id'";
    }
    //echo $query_update_news_categories;exit;
    $result_update_news_categories = mysqli_query($db_link, $query_update_news_categories);
    if(!$result_update_news_categories) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }
    
    list_news_categories($news_cat_parent_id = 0, $path_number = 0);

  }
?>
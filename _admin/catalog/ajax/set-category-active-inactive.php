<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_id'])) {
    $category_id =  $_POST['category_id'];
  }
  if(isset($_POST['set_category'])) {
    $set_category =  $_POST['set_category'];
  }
  
  if(!empty($category_id)) {
 
    $query_update_category = "UPDATE `categories` SET  `category_is_active`='$set_category' WHERE `category_id` = '$category_id'";
 
    //echo $query_update_category;
    $result_update_category = mysqli_query($db_link, $query_update_category);
    if(!$result_update_category) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
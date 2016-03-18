<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_id'])) {
    $category_id =  $_POST['category_id'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  //echo "<pre>";print_r($_SERVER);EXIT;
  if(!empty($category_id) && !empty($action)) {
    
    $category_is_collapsed = ($action == "expand") ? 0 : 1; // else $action == collapse
    
    if($category_id == "all") {
      $query_update_category = "UPDATE `categories` SET  `category_is_collapsed`='$category_is_collapsed' WHERE `category_has_children` = '1'"; 
    }
    else {
      $query_update_category = "UPDATE `categories` SET  `category_is_collapsed`='$category_is_collapsed' WHERE `category_id` = '$category_id'";
    }
    //echo $query_update_category;
    $result_update_category = mysqli_query($db_link, $query_update_category);
    if(!$result_update_category) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

    if(strpos($_SERVER['HTTP_REFERER'], "products-categories")) {
      // products-categories.php
?>
  <table>
    <tbody>
<?php
      list_categories_for_products($parent_id = 0, $path_number = 0);
?>
    </tbody>
  </table>
<?php
    }
    else {
      // categories.php
      list_categories($parent_id = 0, $path_number = 0);
    }

  }
?>
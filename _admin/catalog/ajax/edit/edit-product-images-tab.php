<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['product_image_ids'])) {
    $product_image_ids =  $_POST['product_image_ids'];
  }
  
  if(!empty($_POST) && is_array($product_image_ids)) {
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
      
    foreach($product_image_ids as $key => $product_image_id) {
      
      $pi_is_default = ($key == 0) ? 1 : 0; // first image is default
      $pi_sort_order = $key+1;
      
      $query_update_product_image = "UPDATE `product_image` SET `pi_is_default`='$pi_is_default',`pi_sort_order`='$pi_sort_order' WHERE `product_image_id` = '$product_image_id'";
      $all_queries .= "<br>\n".$query_update_product_image;
      $result_update_product_image = mysqli_query($db_link, $query_update_product_image);
      if(!$result_update_product_image) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
    }

    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  }
  
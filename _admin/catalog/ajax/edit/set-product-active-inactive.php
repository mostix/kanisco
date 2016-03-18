<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['set_product'])) {
    $set_product =  $_POST['set_product'];
  }
  
  if(!empty($product_id)) {
 
    $query_update_product = "UPDATE `products` SET  `product_is_active`='$set_product' WHERE `product_id` = '$product_id'";
 
    //echo $query_update_product;
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
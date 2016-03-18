<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  
  if(isset($_POST['product_option_value_id'])) {
    $product_option_value_id = $_POST['product_option_value_id'];
  }
  if(isset($_POST['product_option_id'])) {
    $product_option_id = $_POST['product_option_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `product_option_value` WHERE `product_option_value_id` = '$product_option_value_id'";
  $all_queries = $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //check if this is the last product_option_value
  //if true delete the option from this product in `product_option` table
  
//  $query_more_values = "SELECT `product_option_value_id` FROM `product_option_value` WHERE `product_option_id` = '$product_option_id'";
//  $all_queries .= $query_more_values."\n<br>";
//  $result_more_values = mysqli_query($db_link, $query_more_values);
//  if(!$result_more_values) echo mysqli_error($db_link);
//  if(mysqli_num_rows($result_more_values) <= 0) {
//
//    $query = "DELETE FROM `product_option` WHERE `product_option_id` = '$product_option_id'";
//    $all_queries .= $query."\n<br>";
//    //echo $query;exit;
//    $result = mysqli_query($db_link, $query);
//    if(mysqli_affected_rows($db_link) <= 0) {
//      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
//      mysqli_query($db_link,"ROLLBACK");
//      exit;
//    }
//  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
?>
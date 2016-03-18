<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);exit;
  if(isset($_POST['products_ids_list'])) {
    $products_ids_list = $_POST['products_ids_list'];
  }
  
  $products_ids_array = explode(",", $products_ids_list);
  
  mysqli_query($db_link,"BEGIN");
  
  $all_queries = "";
  
  foreach($products_ids_array as $product_id) {
    $query = "DELETE FROM `products` WHERE `product_id` = '$product_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    $query = "DELETE FROM `product_description` WHERE `product_id` = '$product_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    $query = "DELETE FROM `product_to_category` WHERE `product_id` = '$product_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    $query_select_product_images = "SELECT `pi_name` FROM `product_image` WHERE `product_id` = '$product_id'";
    $all_queries .= $query_select_product_images."\n<br>";
    //echo $query;exit;
    $result_select_product_images = mysqli_query($db_link, $query_select_product_images);
    if(mysqli_num_rows($result_select_product_images) > 0) {

      $query_delete_product_images = "DELETE FROM `product_image` WHERE `product_id` = '$product_id'";
      $all_queries .= $query_delete_product_images."\n<br>";
      //echo $query;exit;
      $result_delete_product_images = mysqli_query($db_link, $query_delete_product_images);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

    $query_select_product_options = "SELECT `product_option_id` FROM `product_option` WHERE `product_id` = '$product_id'";
    $all_queries .= $query_select_product_options."\n<br>";
    //echo $query;exit;
    $result_select_product_options = mysqli_query($db_link, $query_select_product_options);
    if(mysqli_num_rows($result_select_product_options) > 0) {

      $query_delete_product_options = "DELETE FROM `product_option` WHERE `product_id` = '$product_id'";
      $all_queries .= $query_delete_product_options."\n<br>";
      //echo $query;exit;
      $result_delete_product_options = mysqli_query($db_link, $query_delete_product_options);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

    $query_select_product_discounts = "SELECT `product_discount_id` FROM `product_discount` WHERE `product_id` = '$product_id'";
    $all_queries .= $query_select_product_discounts."\n<br>";
    //echo $query;exit;
    $result_select_product_discounts = mysqli_query($db_link, $query_select_product_discounts);
    if(mysqli_num_rows($result_select_product_discounts) > 0) {

      $query_delete_product_discounts = "DELETE FROM `product_discount` WHERE `product_id` = '$product_id'";
      $all_queries .= $query_delete_product_discounts."\n<br>";
      //echo $query;exit;
      $result_delete_product_discounts = mysqli_query($db_link, $query_delete_product_discounts);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }

    $query_select_product_option_values = "SELECT `product_option_value_id` FROM `product_option_value` WHERE `product_id` = '$product_id'";
    $all_queries .= $query_select_product_option_values."\n<br>";
    //echo $query;exit;
    $result_select_product_option_values = mysqli_query($db_link, $query_select_product_option_values);
    if(mysqli_num_rows($result_select_product_option_values) > 0) {

      $query_delete_product_option_values = "DELETE FROM `product_option_value` WHERE `product_id` = '$product_id'";
      $all_queries .= $query_delete_product_option_values."\n<br>";
      //echo $query;exit;
      $result_delete_product_option_values = mysqli_query($db_link, $query_delete_product_option_values);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
?>
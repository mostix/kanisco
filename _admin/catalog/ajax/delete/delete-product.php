<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `products` WHERE `product_id` = '$product_id'";
  $all_queries = $query."\n<br>";
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
      
    while($row_product_images = mysqli_fetch_assoc($result_select_product_images)) {
      
      $product_image = $row_product_images['pi_name'];
      
      $product_image_name_exploded = explode(".", $product_image);
      $image_name = $product_image_name_exploded[0];
      $image_exstension = $product_image_name_exploded[1];
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/products/";
      
      $file = $upload_path.$product_image;

      if(file_exists($file)) {
        unlink($file);
      }

      $image_home_default_name = $image_name."_home_default.".$image_exstension;
      $image_home_default = $upload_path.$image_home_default_name;

      if(file_exists($image_home_default)) {
        unlink($image_home_default);
      }

    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
?>
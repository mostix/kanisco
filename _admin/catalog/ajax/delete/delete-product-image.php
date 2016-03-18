<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  //print_r($_POST);EXIT;
  if(isset($_POST['product_image_id'])) {
    $product_image_id = $_POST['product_image_id'];
  }
  if(isset($_POST['product_image'])) {
    $product_image = $_POST['product_image'];
  }
  if(isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
  }
  if(isset($_POST['type'])) {
    $type = $_POST['type'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `product_image` WHERE `product_image_id` = '$product_image_id'";
  $all_queries = $query."\n";;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  if($type == 1) {
    // default picture
//    $query = "UPDATE `products` SET `product_image` = NULL WHERE `product_id` = '$product_id'";
//    $all_queries .= $query;
//    $result = mysqli_query($db_link, $query);
//    if(mysqli_affected_rows($db_link) <= 0) {
//      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
//      mysqli_query($db_link,"ROLLBACK");
//      exit;
//    }
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  $product_image_name_exploded = explode(".", $product_image);
  $image_name = $product_image_name_exploded[0];
  $image_exstension = $product_image_name_exploded[1];
  
  $file = $_SERVER['DOCUMENT_ROOT']."/site/images/products/$product_image";
  
  if(file_exists($file)) {
    unlink($file);
  }
  
  $image_home_default_name = $image_name."_home_default.".$image_exstension;
  $image_home_default = $_SERVER['DOCUMENT_ROOT']."/site/images/products/$image_home_default_name";

  if(file_exists($image_home_default)) {
    unlink($image_home_default);
  }
?>
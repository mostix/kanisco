<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  //print_r($_POST);EXIT;
  if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
  }
  if(isset($_POST['category_image_path'])) {
    $category_image_path = $_POST['category_image_path'];
  }
  
  if(file_exists(DIRNAME.$category_image_path)) {
    unlink(DIRNAME.$category_image_path);
  }
  
  $query = "UPDATE `categories` SET `category_image_path`=NULL WHERE `category_id` = '$category_id'";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
?>
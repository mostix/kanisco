<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  $product_id = $_POST['product_id'];
  
  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      $product_errors['file'] = "Не е позлволено качването на снимка с разширение $extension<br>";
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $product_image_tmp_name  = $_FILES['file']['tmp_name'];
      $product_image_name = $_FILES['file']['name'];
      $product_image_name_exploded = explode(".", $product_image_name);
      $image_name = $product_image_name_exploded[0];
      $image_exstension = mb_convert_case($product_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $product_image_name = "$image_name.$image_exstension";
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/products/";
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      $product_errors['file'] .= "You have exceeded the size limit! Please choose a default image smaller then 4MB<br>";
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        $product_errors['file'] .= "An error occured while uploading the file<br>";
      }
    }
    
    $query_select_product_images = "SELECT `product_image_id` FROM `product_image` WHERE `pi_name` = '$product_image_name'";
    //echo $query;exit;
    $result_select_product_images = mysqli_query($db_link, $query_select_product_images);
    if(mysqli_num_rows($result_select_product_images) > 0) {
      echo $languages[$current_lang]['warning_image_is_already_in_database'];
      exit;
    }
    
    $pi_sort_order = get_product_last_image_order_value($product_id);
    if($pi_sort_order == 0) {
      $pi_is_default = 1;
      $pi_sort_order = 1;
    }
    else {
      $pi_is_default = 0;
      $pi_sort_order+1;
    }
    
    $query_insert_img = "INSERT INTO `product_image`(`product_image_id`, 
                                                      `product_id`, 
                                                      `pi_name`, 
                                                      `pi_is_default`, 
                                                      `pi_sort_order`) 
                                              VALUES ('',
                                                      '$product_id',
                                                      '$product_image_name',
                                                      '$pi_is_default',
                                                      '$pi_sort_order')";
    //echo $query_insert_img;
    $result_insert_img = mysqli_query($db_link, $query_insert_img);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($product_image_tmp_name)) {
      move_uploaded_file($product_image_tmp_name, $upload_path.$product_image_name);
    }
    else {
      echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $file = $upload_path.$product_image_name;
    
    list($width,$height) = getimagesize($file);
    
    $image = new SimpleImage(); 
    $image->load($file);

    $image_home_default_name = $image_name."_home_default.".$image_exstension;
    $image_home_default = $upload_path.$image_home_default_name;

    switch($image_exstension) {
      case "gif" : $image_type = 1;
        break;
      case "jpg" : $image_type = 2;
        break;
      case "jpeg" : $image_type = 2;
        break;
      case "png" : $image_type = 3;
        break;
    }

    if($width > $height) {
      if($width > 1280) {
        $image->resizeToWidth(1280);

        $image->save($file,$image_type);
      }

      $image->resizeToWidth(250);

      $image->save($image_home_default,$image_type);

    }
    else {
      if($height > 1280) {
        $image->resizeToHeight(1280);

        $image->save($file,$image_type);
      }

      $image->resizeToHeight(250);

      $image->save($image_home_default,$image_type);
    }
    
  }
<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  $client_id = $_POST['client_id'];
  
  $query_client_details = "SELECT `client_image`
                            FROM `clients`
                            WHERE `client_id` = '$client_id'";
  //echo $query_client_details;exit;
  $result_client_details = mysqli_query($db_link, $query_client_details);
  if(!$result_client_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_client_details) > 0) {
    $client_details = mysqli_fetch_assoc($result_client_details);

    $client_image = $client_details['client_image'];
  }
  $client_image_exploded = explode(".", $client_image);
  $current_client_image_name = $client_image_exploded[0];
  $current_client_image_exstension = $client_image_exploded[1];
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/clients/";
  
  $file = $upload_path."$current_client_image_name.$current_client_image_exstension";
  
  unlink($file);

  $image_admin_thumb_name = $current_client_image_name."_admin_thumb.".$current_client_image_exstension;
  $image_admin_thumb = "$upload_path$image_admin_thumb_name";

  unlink($image_admin_thumb);
  
  $image_site_name = $current_client_image_name."_site.".$current_client_image_exstension;
  $image_site = "$upload_path$image_site_name";
  
  unlink($image_site);

  //print_r($_FILES);exit;
  
  if(isset($_FILES['file']) && $_FILES['file']['error'] != 4) {
    $extension_array = explode("/", $_FILES['file']['type']);
    $extension = mb_convert_case($extension_array[1], MB_CASE_LOWER, "UTF-8");
    if(!in_array($extension, $valid_formats)) {
      echo "Не е позлволено качването на снимка с разширение $extension<br>";
      exit;
    }

    if((isset($_FILES['file'])) && ($_FILES['file']['size'] < MAX_FILE_SIZE) && ($_FILES['file']['error'] == 0)) {
      // no error
      $client_image_tmp_name  = $_FILES['file']['tmp_name'];
      $client_image_name = $_FILES['file']['name'];
      $client_image_name_exploded = explode(".", $client_image_name);
      $image_name = $client_image_name_exploded[0];
      $image_exstension = mb_convert_case($client_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      //echo $upload_path;
    }
    elseif((isset($_FILES['file'])) && ($_FILES['file']['size'] > MAX_FILE_SIZE) || ($_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2)) {
      echo "You have exceeded the size limit! Please choose a default image smaller then 4MB<br>";
        exit;
    }
    else {
      if($_FILES['file']['error'] != 4) { // error 4 means no file was uploaded
        echo "An error occured while uploading the file<br>";
        exit;
      }
    }

    $query_update_product = "UPDATE `clients` SET `client_image` = '$client_image_name' WHERE `client_id` = '$client_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages[$current_lang]['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($client_image_tmp_name)) {
      move_uploaded_file($client_image_tmp_name, $upload_path.$client_image_name);
    }
    else {
      echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $file = $upload_path.$client_image_name;
    
    list($width,$height) = getimagesize($file);
    
    $image = new SimpleImage();
    $image->load($file);
    
    $image_site_name = $image_name."_site.".$image_exstension;
    $image_site = $upload_path.$image_site_name;
    
    $image_admin_thumb_name = $image_name."_admin_thumb.".$image_exstension;
    $image_admin_thumb = $upload_path.$image_admin_thumb_name;
      
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
      $image->resizeToWidth(170);

      $image->save($image_site,$image_type);

      $image->resizeToWidth(170);

      $image->save($image_admin_thumb,$image_type);

    }
    else {
      $image->resizeToHeight(90);

      $image->save($image_site,$image_type);

      $image->resizeToHeight(90);

      $image->save($image_admin_thumb,$image_type);
    }
    
  }
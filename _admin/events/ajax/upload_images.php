<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  if(isset($_POST['event_id']) && !empty($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $event_image = $_POST['event_image'];

    $event_image_exploded = explode(".", $event_image);
    $current_event_image_name = $event_image_exploded[0];
    $current_event_image_exstension = $event_image_exploded[1];
    $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/events/";

    $file = $upload_path."$current_event_image_name.$current_event_image_exstension";

    if(file_exists($file)) {
      unlink($file);
    }

    $image_admin_thumb_name = $current_event_image_name."_thumb.".$current_event_image_exstension;
    $image_admin_thumb = "$upload_path$image_admin_thumb_name";

    if(file_exists($image_admin_thumb)) {
      unlink($image_admin_thumb);
    }
  }

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
      $event_image_tmp_name  = $_FILES['file']['tmp_name'];
      $event_image_name = $_FILES['file']['name'];
      $event_image_name_exploded = explode(".", $event_image_name);
      $image_name = $event_image_name_exploded[0];
      $image_exstension = mb_convert_case($event_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
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

    $query_update_product = "UPDATE `events` SET `event_image` = '$event_image_name' WHERE `event_id` = '$event_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages[$current_lang]['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($event_image_tmp_name)) {
      move_uploaded_file($event_image_tmp_name, $upload_path.$event_image_name);
    }
    else {
      echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $file = $upload_path.$event_image_name;
    
    list($width,$height) = getimagesize($file);
    
    $image = new SimpleImage();
    $image->load($file);
    
    $image_thumb_name = $image_name."_thumb.".$image_exstension;
    $image_thumb = $upload_path.$image_thumb_name;
      
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
    
    $image->resizeToWidth(351);

    $image->save($image_thumb,$image_type);

  }
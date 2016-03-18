<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  //echo $_SERVER['DOCUMENT_ROOT'];exit;
  
  define ("MAX_FILE_SIZE","2048000");
  $valid_formats = array("jpg", "jpeg", "png", "gif");
  $upload_path = "";

  $team_member_id = $_POST['team_member_id'];
  
  $query_slider_details = "SELECT `team_member_image`
                            FROM `team_members`
                            WHERE `team_member_id` = '$team_member_id'";
  //echo $query_slider_details;exit;
  $result_slider_details = mysqli_query($db_link, $query_slider_details);
  if(!$result_slider_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_details) > 0) {
    $slider_details = mysqli_fetch_assoc($result_slider_details);

    $team_member_image = $slider_details['team_member_image'];
  }
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/team_members/";
  
  $file = $upload_path.$team_member_image;
  
  unlink($file);

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
      $team_member_image_tmp_name  = $_FILES['file']['tmp_name'];
      $team_member_image_name = $_FILES['file']['name'];
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

    $query_update_product = "UPDATE `team_members` SET `team_member_image` = '$team_member_image_name' WHERE `team_member_id` = '$team_member_id'";
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages[$current_lang]['sql_error_update']." - 1 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
      
    if(is_uploaded_file($team_member_image_tmp_name)) {
      move_uploaded_file($team_member_image_tmp_name, $upload_path.$team_member_image_name);
    }
    else {
      echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
  }
<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  //print_r($_POST);EXIT;
  if(isset($_POST['option_value_id'])) {
    $option_value_id = $_POST['option_value_id'];
  }
  if(isset($_POST['ov_image_path'])) {
    $ov_image_path = $_POST['ov_image_path'];
  }
  
  unlink(DIRNAME.$ov_image_path);
  
  $query = "UPDATE `option_value` SET `ov_image_path`=NULL WHERE `option_value_id` = '$option_value_id'";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
?>
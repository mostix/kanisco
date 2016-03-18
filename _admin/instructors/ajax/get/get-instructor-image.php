<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['instructor_id'])) {
    $current_instructor_id =  $_POST['instructor_id'];
  }
  
  $query_instructor_details = "SELECT `instructor_image`
                              FROM `instructors`
                              WHERE `instructor_id` = '$current_instructor_id'";
  //echo $query_instructor_details;exit;
  $result_instructor_details = mysqli_query($db_link, $query_instructor_details);
  if(!$result_instructor_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_instructor_details) > 0) {
    $instructor_details = mysqli_fetch_assoc($result_instructor_details);

    $instructor_image = $instructor_details['instructor_image'];
    $instructor_image_exploded = explode(".", $instructor_image);
    $instructor_image_name = $instructor_image_exploded[0];
    $instructor_image_exstension = $instructor_image_exploded[1];
    $instructor_image_thumb = "/frontstore/images/instructors/".$instructor_image_name.".".$instructor_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$instructor_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$instructor_image_thumb;?>" <?=$thumb_image_dimensions;?>>
<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['course_id'])) {
    $current_course_id =  $_POST['course_id'];
  }
  
  $query_course_details = "SELECT `course_image`
                          FROM `courses`
                          WHERE `course_id` = '$current_course_id'";
  //echo $query_course_details;exit;
  $result_course_details = mysqli_query($db_link, $query_course_details);
  if(!$result_course_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_course_details) > 0) {
    $course_details = mysqli_fetch_assoc($result_course_details);

    $course_image = $course_details['course_image'];
    $course_image_exploded = explode(".", $course_image);
    $course_image_name = $course_image_exploded[0];
    $course_image_exstension = $course_image_exploded[1];
    $course_image_thumb = "/site/images/courses/".$course_image_name.".".$course_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$course_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$course_image_thumb;?>" width="700" height="auto">
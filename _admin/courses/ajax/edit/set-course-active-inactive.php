<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['course_id'])) {
    $course_id =  $_POST['course_id'];
  }
  if(isset($_POST['set_course'])) {
    $set_course =  $_POST['set_course'];
  }
  
  if(!empty($course_id)) {
 
    $query_update_course = "UPDATE `courses` SET `course_is_active`='$set_course' WHERE `course_id` = '$course_id'";
 
    //echo $query_update_course;
    $result_update_course = mysqli_query($db_link, $query_update_course);
    if(!$result_update_course) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['event_id'])) {
    $event_id =  $_POST['event_id'];
  }
  if(isset($_POST['set_event'])) {
    $set_event =  $_POST['set_event'];
  }
  
  if(!empty($event_id)) {
 
    $query_update_event = "UPDATE `events` SET  `event_is_active`='$set_event' WHERE `event_id` = '$event_id'";
 
    //echo $query_update_event;
    $result_update_event = mysqli_query($db_link, $query_update_event);
    if(!$result_update_event) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
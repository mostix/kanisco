<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['event_id'])) {
    $current_event_id =  $_POST['event_id'];
  }
  
  $query_event_details = "SELECT `event_image`
                          FROM `events`
                          WHERE `event_id` = '$current_event_id'";
  //echo $query_event_details;exit;
  $result_event_details = mysqli_query($db_link, $query_event_details);
  if(!$result_event_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_event_details) > 0) {
    $event_details = mysqli_fetch_assoc($result_event_details);

    $event_image = $event_details['event_image'];
    $event_image_exploded = explode(".", $event_image);
    $event_image_name = $event_image_exploded[0];
    $event_image_exstension = $event_image_exploded[1];
    $event_image_thumb = "/site/images/events/".$event_image_name.".".$event_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$event_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$event_image_thumb;?>" <?=$thumb_image_dimensions;?>>
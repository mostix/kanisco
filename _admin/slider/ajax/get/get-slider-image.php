<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['slider_id'])) {
    $current_slider_id =  $_POST['slider_id'];
  }
  
  $query_slider_details = "SELECT `slider_image`
                            FROM `sliders`
                            WHERE `slider_id` = '$current_slider_id'";
  //echo $query_slider_details;exit;
  $result_slider_details = mysqli_query($db_link, $query_slider_details);
  if(!$result_slider_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_details) > 0) {
    $slider_details = mysqli_fetch_assoc($result_slider_details);

    $slider_image = $slider_details['slider_image'];
    $slider_image_exploded = explode(".", $slider_image);
    $slider_image_name = $slider_image_exploded[0];
    $slider_image_exstension = $slider_image_exploded[1];
    $slider_image_thumb = "/site/images/slider/".$slider_image_name.".".$slider_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$slider_image_thumb;?>" <?=$thumb_image_dimensions;?>>
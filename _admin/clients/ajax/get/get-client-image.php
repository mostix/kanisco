<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['client_id'])) {
    $current_client_id =  $_POST['client_id'];
  }
  
  $query_client_details = "SELECT `client_image`
                            FROM `clients`
                            WHERE `client_id` = '$current_client_id'";
  //echo $query_client_details;exit;
  $result_client_details = mysqli_query($db_link, $query_client_details);
  if(!$result_client_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_client_details) > 0) {
    $client_details = mysqli_fetch_assoc($result_client_details);

    $client_image = $client_details['client_image'];
    $client_image_exploded = explode(".", $client_image);
    $client_image_name = $client_image_exploded[0];
    $client_image_exstension = $client_image_exploded[1];
    $client_image_thumb = "/site/images/clients/".$client_image_name.".".$client_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$client_image_thumb;?>" <?=$thumb_image_dimensions;?>>
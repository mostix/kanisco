<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['news_id'])) {
    $current_news_id =  $_POST['news_id'];
  }
  
  $query_news_details = "SELECT `news_image`
                          FROM `news`
                          WHERE `news_id` = '$current_news_id'";
  //echo $query_news_details;exit;
  $result_news_details = mysqli_query($db_link, $query_news_details);
  if(!$result_news_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_details) > 0) {
    $news_details = mysqli_fetch_assoc($result_news_details);

    $news_image = $news_details['news_image'];
    $news_image_exploded = explode(".", $news_image);
    $news_image_name = $news_image_exploded[0];
    $news_image_exstension = $news_image_exploded[1];
    $news_image_thumb = "/site/images/news/".$news_image_name.".".$news_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$news_image_thumb;?>" <?=$thumb_image_dimensions;?>>
<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['team_member_id'])) {
    $current_team_member_id =  $_POST['team_member_id'];
  }
  
  $query_team_member_details = "SELECT `team_member_image`
                              FROM `team_members`
                              WHERE `team_member_id` = '$current_team_member_id'";
  //echo $query_team_member_details;exit;
  $result_team_member_details = mysqli_query($db_link, $query_team_member_details);
  if(!$result_team_member_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_team_member_details) > 0) {
    $team_member_details = mysqli_fetch_assoc($result_team_member_details);

    $team_member_image = $team_member_details['team_member_image'];
    $team_member_image_exploded = explode(".", $team_member_image);
    $team_member_image_name = $team_member_image_exploded[0];
    $team_member_image_exstension = $team_member_image_exploded[1];
    $team_member_image_thumb = "/frontstore/images/team_members/".$team_member_image_name.".".$team_member_image_exstension;
    @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$team_member_image_thumb);
    $thumb_image_dimensions = $thumb_image_params[3];
    
  }
?>
  <img src="<?=$team_member_image_thumb;?>" <?=$thumb_image_dimensions;?>>
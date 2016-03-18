<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_id'])) {
    $content_id =  $_POST['content_id'];
  }
  if(isset($_POST['set_content'])) {
    $set_content =  $_POST['set_content'];
  }
  
  if(!empty($content_id)) {
 
    $query_update_content = "UPDATE `contents` SET  `content_is_active`='$set_content' WHERE `content_id` = '$content_id'";
 
    //echo $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
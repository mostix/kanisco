<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_id'])) {
    $content_id =  $_POST['content_id'];
  }
  
  if(!empty($content_id)) {
 
    mysqli_query($db_link,"BEGIN");
    
    $query_update_content = "UPDATE `contents` SET `content_is_default`='0' WHERE `content_is_default`='1'";
    //echo $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_update_content = "UPDATE `contents` SET `content_is_default`='1' WHERE `content_id` = '$content_id'";
    //echo $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    mysqli_query($db_link,"COMMIT");
    
    list_contents($parent_id = 0, $path_number = 0);

  }
?>
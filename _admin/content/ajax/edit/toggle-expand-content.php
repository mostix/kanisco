<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['content_id'])) {
    $content_id =  $_POST['content_id'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($content_id) && !empty($action)) {
    
    $content_collapsed = ($action == "expand") ? 0 : 1; // else $action == collapse
    
    if($content_id == "all") {
      $query_update_content = "UPDATE `contents` SET  `content_collapsed`='$content_collapsed' WHERE `content_has_children` = '1'"; 
    }
    else {
      $query_update_content = "UPDATE `contents` SET  `content_collapsed`='$content_collapsed' WHERE `content_id` = '$content_id'";
    }
    //echo $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

    list_contents($parent_id = 0, $path_number = 0);

  }
?>
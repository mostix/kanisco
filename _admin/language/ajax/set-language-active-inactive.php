<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['language_id'])) {
    $language_id =  $_POST['language_id'];
  }
  if(isset($_POST['set_language'])) {
    $set_language =  $_POST['set_language'];
  }
  
  if(!empty($language_id)) {
    
    $query_update_content = "UPDATE `languages` SET  `language_is_active`='$set_language' WHERE `language_id` = '$language_id'";
 
    //echo $query_update_content;
    $result_update_content = mysqli_query($db_link, $query_update_content);
    if(!$result_update_content) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
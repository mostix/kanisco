<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['language_id'])) {
    $language_id =  $_POST['language_id'];
  }
  if(isset($_POST['default_for'])) {
    $default_for =  $_POST['default_for'];
  }
  
  if(!empty($language_id)) {
 
    mysqli_query($db_link,"BEGIN");
    
    $query_update_language = "UPDATE `languages` SET `language_is_default$default_for`='0' WHERE `language_is_default$default_for`='1'";
    //echo $query_update_language;
    $result_update_language = mysqli_query($db_link, $query_update_language);
    if(!$result_update_language) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $query_update_language = "UPDATE `languages` SET `language_is_default$default_for`='1' WHERE `language_id` = '$language_id'";
    //echo $query_update_language;
    $result_update_language = mysqli_query($db_link, $query_update_language);
    if(!$result_update_language) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }

    mysqli_query($db_link,"COMMIT");
    
    list_languages();

  }
?>
<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
  }
  if(isset($_POST['option_id'])) {
    $option_id = $_POST['option_id'];
  }
  
  $query = "DELETE FROM `option_to_category` WHERE `option_id` = '$option_id' AND `category_id` = '$category_id'";
  $all_queries = $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
?>
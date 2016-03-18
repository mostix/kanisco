<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `events` WHERE `event_id` = '$event_id'";
  $all_queries = $query;
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `events_descriptions` WHERE `event_id` = '$event_id'";
  $all_queries .= $query;
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");

  list_events();
?>

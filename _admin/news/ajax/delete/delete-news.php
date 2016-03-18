<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);exit;
  
  if(isset($_POST['news_id'])) {
    $news_id = $_POST['news_id'];
  }
  
  //mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `news` WHERE `news_id` = '$news_id'";
  $all_queries = $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `news_descriptions` WHERE `news_id` = '$news_id'";
  $all_queries = $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
    
  list_news();
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  //mysqli_query($db_link,"COMMIT");
?>
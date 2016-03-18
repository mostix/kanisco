<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['news_id'])) {
    $news_id =  $_POST['news_id'];
  }
  if(isset($_POST['set_news'])) {
    $set_news =  $_POST['set_news'];
  }
  
  if(!empty($news_id)) {
 
    $query_update_news = "UPDATE `news` SET  `news_is_active`='$set_news' WHERE `news_id` = '$news_id'";
 
    //echo $query_update_news;
    $result_update_news = mysqli_query($db_link, $query_update_news);
    if(!$result_update_news) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    }

  }
?>
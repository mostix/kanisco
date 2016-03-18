<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';
  
//  if(isset($_POST['campaigns'])) {
//    $campaigns =  $_POST['campaigns'];
//  }
//  if(isset($_POST['subscribe_name'])) {
//    $subscribe_name =  $_POST['subscribe_name'];
//  }
  if(isset($_POST['email'])) {
    $newsletter_email =  $_POST['email'];
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  //echo "<pre>";print_r($_POST);echo "</pre>";exit;
  
  mysqli_query($db_link,"BEGIN");

  $campaign_id = 1; //this is hardcoded for now
  
  $query_check_email = "SELECT `campaign_list_id` FROM `email_campaign_list` WHERE `email` = '$newsletter_email'";
  $result_check_email = mysqli_query($db_link, $query_check_email);
  if(mysqli_affected_rows($db_link) <= 0) {
    
    $query_subscribe = "INSERT INTO `email_campaign_list`(`campaign_list_id`, `campaign_id`, `email`) 
                                                  VALUES ('','$campaign_id','$newsletter_email')";
    //echo "$query_subscribe<br>";
    $result_subscribe = mysqli_query($db_link, $query_subscribe);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
?>
      <div class="alert alert-success"><?=$languages[$current_lang]['text_newsletter_subscription_success'];?></div>
<?php
  }
  else {
?>
      <div class="alert alert-danger"><?=$languages[$current_lang]['error_email_already_in_newsletter_list'];?></div>
<?php
  }
  
  mysqli_query($db_link,"COMMIT");
?>

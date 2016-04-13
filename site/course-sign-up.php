<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';
  
  //echo "<pre>";print_r($_POST);echo "</pre>";exit;
  
  if(isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
  }
  if(isset($_POST['cd_name'])) {
    $cd_name = mysqli_real_escape_string($db_link, $_POST['cd_name']);
  }
  if(isset($_POST['firstname'])) {
    $firstname = mysqli_real_escape_string($db_link, $_POST['firstname']);
  }
  if(isset($_POST['lastname'])) {
    $lastname =  mysqli_real_escape_string($db_link, $_POST['lastname']);
  }
  $customer_name =  "$firstname $lastname";
  
  if(isset($_POST['company'])) {
    $company =  mysqli_real_escape_string($db_link, $_POST['company']);
  }
  if(isset($_POST['phone'])) {
    $customer_phone =  mysqli_real_escape_string($db_link, $_POST['phone']);
  }
  if(isset($_POST['email'])) {
    $customer_email =  mysqli_real_escape_string($db_link, $_POST['email']);
  }
  if(isset($_POST['participants_count'])) {
    $participants_count = intval($_POST['participants_count']);
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  
//  $query_subscribe = "INSERT INTO `email_campaign_list`(`campaign_list_id`, `campaign_id`, `email`) 
//                                                VALUES ('','$campaign_id','$newsletter_email')";
//  //echo "$query_subscribe<br>";
//  $result_subscribe = mysqli_query($db_link, $query_subscribe);
//  if(mysqli_affected_rows($db_link) <= 0) {
//    echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
//    mysqli_query($db_link,"ROLLBACK");
//    exit;
//  }
    
//  $to_kanisco = "kanisco@kanisco.com";
//  $to_kanisco = "idimitrov@eterrasystems.com";
  $to_kanisco = "monywhy@gmail.com";
//  $to_customer = $customer_email;

  $subject_kanisco = "Записване за $cd_name";
  
  $logo_image = "http://".$_SERVER['HTTP_HOST']."/site/images/logo.jpg";

  $message_kanisco = "<table cellpadding='10' border='0' style='width:100%;'>
                        <tr>
                          <td colspan='5'>
                            <a href='http://".$_SERVER['HTTP_HOST']."' target='_blank'><img src='$logo_image'></a>
                          </td>
                        </tr>
                        <tr><td colspan='5'>&nbsp;</td></tr>
                        <tr>
                          <td style='background: #efefef;' colspan='5'>
                            <font style='color:#000;font-size: 14pt;font-weight: bold;'>$subject_kanisco</font>
                          </td>
                        </tr>
                        <tr><td colspan='5'>&nbsp;</td></tr>
                        <tr>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Име и Фамилия</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Фирма</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Телефон</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Email</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Брой участници</font>
                          </td>
                        </tr>
                        <tr>
                          <td>$customer_name</td>
                          <td>$company</td>
                          <td>$customer_phone</td>
                          <td>$customer_email</td>
                          <td>$participants_count</td>
                        </tr>
                        <tr><td colspan='5'>&nbsp;</td></tr>
                      </table>";
  //$message_customer = "";
  
  $headers_kanisco = $languages[$current_lang]['email_headers_text'];

  if(mail($to_kanisco, "Записване за курс", $message_kanisco, $headers_kanisco)) {
    echo $message_kanisco;
  }
  else {
    print_r(error_get_last());
    echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
    $email_was_sended_successfull = false;
  }
?>

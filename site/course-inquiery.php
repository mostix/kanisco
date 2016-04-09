<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  include_once 'config.php';
  include_once 'languages/languages.php';
  include_once 'functions/include-functions.php';

  //echo "<pre>";print_r($_POST);echo "</pre>";exit;
  
  if(isset($_POST['cd_name'])) {
    $cd_name =  $_POST['cd_name'];
  }
  if(isset($_POST['name'])) {
    $customer_name =  $_POST['name'];
  }
  if(isset($_POST['phone'])) {
    $customer_phone =  $_POST['phone'];
  }
  if(isset($_POST['email'])) {
    $customer_email =  $_POST['email'];
  }
  if(isset($_POST['message'])) {
    $inquiery_message = stripslashes($_POST['message']);
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $to_kanisco = "kanisco@kanisco.com";
//  $to_kanisco = "idimitrov@eterrasystems.com";
//  $to_kanisco = "monywhy@gmail.com";
//  $to_customer = $customer_email;

  $subject_kanisco = $languages[$current_lang]['email_inquiry_subject_text']." за $cd_name";
  
  $logo_image = "http://".$_SERVER['HTTP_HOST']."/site/images/logo.jpg";
  $logo_image_params = getimagesize($logo_image);
  $logo_image_dimensions = $logo_image_params[3];

  $message_kanisco = "<table cellpadding='10' border='0' style='width:100%;'>
                        <tr>
                          <td colspan='3'>
                            <a href='http://".$_SERVER['HTTP_HOST']."' target='_blank'><img src='$logo_image' $logo_image_dimensions></a>
                          </td>
                        </tr>
                        <tr><td colspan='3'>&nbsp;</td></tr>
                        <tr>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Потребител</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Телефон</font>
                          </td>
                          <td style='background: #4986b3;'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>Email</font>
                          </td>
                        </tr>
                        <tr>
                          <td>$customer_name</td>
                          <td>$customer_phone</td>
                          <td>$customer_email</td>
                        </tr>
                        <tr><td colspan='3'>&nbsp;</td></tr>
                        <tr>
                          <td style='background: #4986b3;' colspan='3'>
                            <font style='color:#FFF;font-size: 12pt;font-weight: bold;'>$subject_kanisco</font>
                          </td>
                        </tr>
                        <tr>
                          <td colspan='3'>$inquiery_message</td>
                        </tr>
                        <tr><td colspan='3'>&nbsp;</td></tr>
                      </table>";
  //$message_customer = "";
  
  $headers_kanisco = $languages[$current_lang]['email_headers_text'];

  if(mail($to_kanisco, $subject_kanisco, $message_kanisco, $headers_kanisco)) {
    echo $message_kanisco;
    //echo $all_queries;mysqli_query($db_link, "ROLLBACK");
  }
  else {
    print_r(error_get_last());
    echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
    $email_was_sended_successfull = false;
  }
  
?>
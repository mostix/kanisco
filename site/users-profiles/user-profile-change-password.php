<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  $success = false;
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    
    $customer_email =   $_POST['customer_email'];
    $query_customer_email = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
    //echo $query_customer_email;
    $result_customer_email = mysqli_query($db_link, $query_customer_email);
    if(!$result_customer_email) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_customer_email) > 0) {
      
      $row = mysqli_fetch_assoc($result_customer_email);
      $customer_id = $row['customer_id'];
      
      $customer_password = generate_strong_password();
      //echo $customer_password;
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($customer_password , $bcrypt_salt);

      mysqli_query($db_link,"BEGIN");
      
      $query_update_user = "UPDATE `customers` SET `customer_salted_password`='$bcrypt_password' WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
      }
      else {
        
        $to      = $customer_email;
        $subject = $languages[$current_lang]['forgotten_pass_subject_text'];
        $logo_image = "http://www.art93.com/site/images/img/art93-logo-text.png";
        $logo_image_params = getimagesize($logo_image);
        $logo_image_dimensions = $logo_image_params[3];
        $message = "<table>";
        $message .= "<tr>
                      <td>
                        <a href='".BASEPATH."' target='_blank'><img src='$logo_image' $logo_image_dimensions></a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                      </td>
                    </tr>";
        $message .= "<tr><td>&nbsp;</td></tr>";
        $message .= "<tr><td>".$languages[$current_lang]['forgotten_pass_message_text_1']." $customer_password</td></tr>";
        $message .= "<tr><td>&nbsp;</td></tr>";
        $message .= "<tr><td>".$languages[$current_lang]['forgotten_pass_message_text_2']."</td></tr>";
        $message .= "<tr><td>&nbsp;</td></tr>";
        $message .= "<tr><td>&nbsp;</td></tr>";
        $message .= "</table>";
        $headers = $languages[$current_lang]['email_headers_text'];
        //$headers .= 'Cc: idimitrov@eterrasystems.com' . "\r\n";

        if(mail($to, $subject, $message, $headers)) {
          mysqli_commit($db_link);
          $success = true;
        }
        else {
          print_r(error_get_last());
          echo $languages[$current_lang]['error_registration_customer_send_email_fail'];
          mysqli_query($db_link, "ROLLBACK");
        }
      }
    }
    else {
      $success = false;
      $errors['customer_email'] = $languages[$current_lang]['error_user_profile_forgotten_password'];
    }
  }
?>
  <div class="row ">
<?php
    if(!$success) echo "<p class=\"red\">".$languages[$current_lang]['text_user_profile_forgotten_password']."</p>";
?>
    <form name="user_profile_settings" id="user_profile_settings" class="form" method="post" action="/<?=$_GET['page'];?>">
      <fieldset>
        <legend>Смяна на паролата</legend>
<?php
    if($success) echo "<div class='success_field'>".$languages[$current_lang]['text_user_profile_forgotten_password_success']."</div>";
    else {
?>
        <div<?php if(!empty($errors['customer_email'])) echo ' class="error_field"';?>>
          <label for="customer_email"><?=$languages[$current_lang]['header_customer_email'];?></label>
          <input type="text" name="customer_email" id="customer_email" value="<?php if(isset($customer_email)) echo $customer_email;?>" />
          <?php if(!empty($errors['customer_email'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_email'];?></span><?php } ?>
        </div>

        <div class="clearfix">&nbsp;</div>

        <div>
          <button type="submit" name="update_profile" class="btn btn-primary button outline-outward"><?=$languages[$current_lang]['btn_generate_password'];?></button>
        </div>   
<?php
    }
?>
        <div class="clearfix"></div>
      </fieldset>
    </form> 
  </div>
  <!--<div class="row"> -->
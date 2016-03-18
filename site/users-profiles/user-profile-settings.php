<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  $customer_id = $_SESSION['customer']['customer_id'];
  $customer_fullname = $_SESSION['customer']['customer_firstname']." ".$_SESSION['customer']['customer_lastname'];
  
  $query_customer = "SELECT `customer_firstname`, `customer_lastname`, `customer_email`, `customer_phone`, `customer_is_in_mailist` 
                    FROM `customers` WHERE `customer_id` = '$customer_id'";
  //echo $query_customer;
  $result_customer = mysqli_query($db_link, $query_customer);
  if(!$result_customer) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_customer) > 0) {
    $customer = mysqli_fetch_assoc($result_customer);
    $customer_firstname = $customer['customer_firstname'];
    $customer_lastname = $customer['customer_lastname'];
    $customer_email = $customer['customer_email'];
    $customer_phone = $customer['customer_phone'];
    $customer_is_in_mailist = $customer['customer_is_in_mailist'];
  }
  //echo "<pre>";print_r($_SERVER);
  
  if(isset($_POST['update_profile'])) {
    //echo "<pre>";print_r($_POST);
    
    $customer_firstname = $_POST['customer_firstname'];
    $customer_lastname = $_POST['customer_lastname'];
    $customer_email = $_POST['customer_email'];
    $customer_phone = $_POST['customer_phone'];
    $customer_is_in_mailist = 0;
      if(isset($_POST['customer_is_in_mailist'])) $customer_is_in_mailist = 1;
    if(!empty($_POST['customer_password'])) {
      $customer_password = $_POST['customer_password'];
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($customer_password , $bcrypt_salt);
      $customer_password_retype = $_POST['customer_password_retype'];
      
      $uppercase = preg_match('@[A-Z]@', $customer_password);
      $lowercase = preg_match('@[a-z]@', $customer_password);
      $number    = preg_match('@[0-9]@', $customer_password);

      if(!$uppercase || !$lowercase || !$number || strlen($customer_password) < 8) {
        // tell the user something went wrong
        $errors['customer_password'] = $languages[$current_lang]['error_registration_password_is_not_valid'];
      }
    
      $customer_passwords_mismatch = check_if_users_passwords_match($customer_password,$customer_password_retype);
      if(!empty($customer_passwords_mismatch)) {
        $errors['customer_passwords_mismatch'] = $customer_passwords_mismatch;
      }
    } 
      
    if(empty($errors)) {
      
      $query_update_user = "UPDATE `customers` SET ";
      
      if(!empty($_POST['customer_password'])) {
        $query_update_user .= "`customer_salted_password`='$bcrypt_password',";
      }
      $query_update_user .= " `customer_firstname`='$customer_firstname',
                              `customer_lastname`='$customer_lastname',
                              `customer_email`='$customer_email',
                              `customer_phone`='$customer_phone',
                              `customer_is_in_mailist`='$customer_is_in_mailist' 
                            WHERE `customer_id` = '$customer_id'";
      //echo $query_update_user."<br>";
      $result_update_user = mysqli_query($db_link, $query_update_user);
      if(!$result_update_user) {
        echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
      }
      else $success = true;
    }
  }
?>
  <div class="dt-sc-hr-invisible-small"></div>
      
  <form name="user_profile_settings" id="user_profile_settings" class="form" method="post" action="/<?=$_GET['page'];?>">
<?php
  if(isset($success)) echo "<div class='success_field'>Промерните бяха запазени успешно</div>";
  if(!empty($errors)) {

    //foreach($errors as $error) echo "<div class='warning_field'>$error</div>";
  }
?>
<!--        <h2>Общи настройки</h2>-->
    <input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id;?>">
    <div<?php if(!empty($errors['customer_email_status']) || !empty($errors['customer_email'])) echo ' class="error_field"';?>>
      <label for="customer_email"><?=$languages[$current_lang]['header_customer_email'];?></label>
      <input type="text" name="customer_email" id="customer_email" value="<?php if(isset($customer_email)) echo $customer_email;?>" onBlur="CheckIfUserEmailIsValidForUpdate(this.value,'<?=$current_lang;?>')" />
      <input type="hidden" name="customer_email_status" id="customer_email_status" value="<?php if(!empty($errors['customer_email_status'])) echo "error"; else echo "ok"?>" />
      <span id="customer_email_is_valid"></span>
      <?php if(!empty($errors['customer_email'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_email'];?></span><?php } ?>
      <?php if(!empty($errors['customer_email_status'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_email_status'];?></span><?php } ?>
    </div>
    <div class="clear"></div>

    <div<?php if(!empty($errors['customer_firstname'])) echo ' class="error_field"';?>>
      <label for="customer_firstname"><?=$languages[$current_lang]['header_customer_firstname'];?></label>
      <input type="text" name="customer_firstname" id="customer_firstname" value="<?php if(isset($customer_firstname)) echo $customer_firstname;?>" />
      <?php if(!empty($errors['customer_firstname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_firstname'];?></span><?php } ?>
    </div>
    <div class="clear"></div>

    <div<?php if(!empty($errors['customer_lastname'])) echo ' class="error_field"';?>>
      <label for="customer_lastname"><?=$languages[$current_lang]['header_customer_lastname'];?></label>
      <input type="text" name="customer_lastname" id="customer_lastname" value="<?php if(isset($customer_lastname)) echo $customer_lastname;?>" />
      <?php if(!empty($errors['customer_lastname'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_lastname'];?></span><?php } ?>
    </div>
    <div class="clear"></div>

    <div<?php if(!empty($errors['customer_phone'])) echo ' class="error_field"';?>>
      <label for="customer_phone"><?=$languages[$current_lang]['header_customer_phone'];?></label>
      <input type="text" name="customer_phone" id="customer_phone" value="<?php if(isset($customer_phone)) echo $customer_phone;?>" />
      <?php if(!empty($errors['customer_phone'])) { ?>&nbsp;&nbsp;<span class="error"><?=$errors['customer_phone'];?></span><?php } ?>
    </div>
    <div class="clear"></div>

    <div>
      <label for="customer_is_in_mailist" class="title"><?=$languages[$current_lang]['header_customer_is_in_mailist'];?></label>
      <?php
        if($customer_is_in_mailist == 0) echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" />';
        else echo '<input type="checkbox" name="customer_is_in_mailist" id="customer_is_in_mailist" checked="checked" />';
      ?>
    </div>

    <div class="clear">&nbsp;</div>

    <small><i>Ако искате да промените паролата си напишете новата парола и я повторете в полето отдолу</i></small>
    <div<?php if(!empty($errors['customer_password'])) echo ' class="error_field"';?>>
      <label for="customer_password"><?=$languages[$current_lang]['header_customer_new_password'];?></label>
      <input type="password" name="customer_password" id="customer_password" onBlur="ValidateUserPassword(this.value,'<?=$current_lang;?>')"  />
      <span id="customer_password_is_valid"></span>
      <?php if(!empty($errors['customer_password'])) { ?><br><span class="error"><?=$errors['customer_password'];?></span><?php } ?>
    </div>
    <div class="clear"></div>

    <div<?php if(!empty($errors['customer_password_retype'])) echo ' class="error_field"';?>>
      <label for="customer_password_retype"><?=$languages[$current_lang]['header_customer_password_retype'];?></label>
      <input type="password" name="customer_password_retype" id="customer_password_retype" />
      <?php if(!empty($errors['customer_passwords_mismatch'])) { ?><br><span class="error"><?=$errors['customer_passwords_mismatch'];?></span><?php } ?>
    </div>

    <div class="clear">&nbsp;</div>

    <div>
      <button type="submit" name="update_profile" class="btn btn-primary button outline-outward"><?=$languages[$current_lang]['btn_save'];?></button>
    </div>
    <div class="clear"></div>
  </form>
  <div class="dt-sc-hr-invisible-normal"></div>
    <!--col-lg-9 --> 
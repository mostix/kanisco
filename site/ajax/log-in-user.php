<?php

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once '../languages/languages.php';
 
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }
  //echo"<pre>";print_r($_SERVER);exit;
  
  if($_SERVER['HTTP_REFERER'] == "http://www.art93.com/bg/shopping-cart/shopping-cart-addresses" || $_SERVER['HTTP_REFERER'] == "http://art93.com/bg/shopping-cart/shopping-cart-addresses") {
    $_SESSION['cart']['order_login'] = true;
  }
//  unset($_SESSION['captcha123']);
//  unset($_SESSION['captcha_error']);
//  unset($_SESSION['login_error']);
//  unset($_SESSION['bfa']);
//  session_destroy();
//  echo"<pre>Session<br>";print_r($_SESSION);
//  echo"<pre>Post<br>";print_r($_POST);
//  if($_POST['customer_captcha'] == $_SESSION['captcha123']['code']) {
//    echo "cool";exit;
//  }

  if(isset($_SESSION['bfa'])) {
    if( $_SESSION['bfa']['last_activity'] < time()-$_SESSION['bfa']['expire_time'] ) {
      unset($_SESSION['captcha123']);
      unset($_SESSION['captcha_error']);
      unset($_SESSION['login_error']);
      unset($_SESSION['bfa']);
    }
    else {
      $_SESSION['bfa']['last_activity'] = time(); //your last activity was now, having logged in.
      echo "<h1 style=\"text-align:center;color:red;\">When there are more than two wrong login attempts, we count this for non-human.
        <br><br> Wait for 1 minute and try again!</h1>";
      exit;
    }
  }
  if(isset($_SESSION['captcha_error']['count']) && $_SESSION['captcha_error']['count'] > 4 || isset($_SESSION['login_error']['count']) && $_SESSION['login_error']['count'] > 4) {
    $_SESSION['bfa']['last_activity'] = time(); //your last activity was now, having logged in.
    $_SESSION['bfa']['expire_time'] = 1*1*15; //expire time in seconds: one minute
    unset($_SESSION['captcha123']);
    unset($_SESSION['captcha_error']);
    unset($_SESSION['login_error']);
    echo "<h1 style=\"text-align:center;color:red;\">When there are more than two wrong login attempts, we count this for non-human.
      <br><br> Wait for 15 seconds and try again!</h1>";
    exit;
  }

  if(!isset($_SESSION['login_error']['count'])) {
    $_SESSION['login_error'] = array();
    $_SESSION['login_error']['count'] = 0;
  }

  if($_POST['customer_captcha'] == $_SESSION['captcha123']['code']) {
  //if(true){
    //unset($_SESSION['captcha_error']);
    $customer_password = $_POST['customer_password'];
    $customer_email = $_POST['customer_email'];
    $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
    $bcrypt_password = crypt($customer_password , $bcrypt_salt);

    $query_user = "SELECT `customer_id`,`customer_group_id`,`customer_salted_password`,`customer_firstname`,`customer_lastname`,`customer_phone` 
                  FROM `customers` 
                  WHERE `customer_email` = '$customer_email' AND `customers`.`customer_is_active` = '1' AND `customers`.`customer_is_blocked` = '0'";
    //$_SESSION['query'] = $query_user."<br>";
    $result_user = mysqli_query($db_link,$query_user);
    if (!$result_user) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_user) > 0) {
      $customer = mysqli_fetch_assoc($result_user);
      
      $db_customer_id = $customer['customer_id'];
      $customer_group_id = $customer['customer_group_id'];
      $password_hash = $customer['customer_salted_password'];
      $customer_firstname = $customer['customer_firstname'];
      $customer_lastname = $customer['customer_lastname'];
      $customer_phone = $customer['customer_phone'];
//      $customer_ip = $_SERVER['REMOTE_ADDR'] ? : ($_SERVER['HTTP_X_FORWARDED_FOR'] ? : $_SERVER['HTTP_CLIENT_IP']);
      $customer_ip = $_SERVER['REMOTE_ADDR'];

      if(crypt($customer_password, $password_hash) == $password_hash) {
        // password is correct

        //make record for table users_log
        $query = "INSERT INTO `customers_logs`(`customer_log_id`, 
                                                `customer_id`,
                                                `customer_ip`, 
                                                `customer_log_date`)
                                        VALUES ('',
                                                '$db_customer_id',
                                                '$customer_ip',
                                                NOW())";
        $result = mysqli_query($db_link, $query);
        if (!$result) echo mysqli_error($db_link);

        $_SESSION['customer']['customer_id'] = $db_customer_id;
        $_SESSION['customer']['customer_group_id'] = $customer_group_id;
        $_SESSION['customer']['customer_firstname'] = $customer_firstname;
        $_SESSION['customer']['customer_lastname'] = $customer_lastname;
        $_SESSION['customer']['customer_email'] = $customer_email;
        $_SESSION['customer']['customer_phone'] = $customer_phone;
        unset($_SESSION['captcha123']);
        unset($_SESSION['captcha_error']);
        unset($_SESSION['login_error']);
        unset($_SESSION['bfa']);
        exit;
//        header('Location: '.$_POST['current_page'].'');
        //echo "<script type='text/jscript'>\n window.location='".$_SERVER['PHP_SELF']."'\n</script>\n";
      }
      else {
        $_SESSION['login_error']['count']++;
        $_SESSION['login_error']['text'] = $languages[$current_lang]['customer_login_error'];
        generate_captcha();
      }

    } // if(mysqli_num_rows($result_user) > 0)
    else {
      $_SESSION['login_error']['count'] ++;
      $_SESSION['login_error']['text'] = $languages[$current_lang]['customer_login_error'];
      generate_captcha();
    }
  }
  else {
    $_SESSION['captcha_error']['count']++;
    $_SESSION['captcha_error']['text'] = $languages[$current_lang]['customer_captcha_error'];
    unset($_SESSION['login_error']);
    generate_captcha();
  }
  echo '<div class="warning_field">';
  if(isset($_SESSION['captcha_error']['text'])) echo $_SESSION['captcha_error']['text'];
  if(isset($_SESSION['login_error']['text'])) echo $_SESSION['login_error']['text'];
  //echo $_SESSION['query']."<br>";
  echo "</div>";
?>
  <table>
    <tr>
      <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
        <label><?=$languages[$current_lang]['login_customer_email'];?>:</label>
        <input type="text" autofocus name="login_customer_email" id="login_customer_email" class="input_text" value="<?php if(isset($_POST['customer_email'])) echo $_POST['customer_email'];?>"  required="required" />
      </td>
    </tr>
    <tr>
      <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
        <label><?=$languages[$current_lang]['login_customer_password'];?>:</label>
        <input type="password" name="login_customer_password" id="login_customer_password" class="input_text" value="<?php if(isset($_POST['customer_password'])) echo $_POST['customer_password'];?>" required="required" />
      </td>
    </tr>
    <tr>
      <td <?php if(isset($_SESSION['captcha_error']) && $_SESSION['captcha_error']['count'] > 0) echo 'class="error"';?>>
        <label class="captcha"><?=$languages[$current_lang]['login_customer_captcha'];?></label>
        <img src="/captchas/<?=$_SESSION['captcha123']['img'];?>" class="float_left" alt="<?=$languages[$current_lang]['alt_login_captcha'];?>" style="margin-right:10px;" />
        <input type="text" name="login_customer_captcha" id="login_customer_captcha" class="input_text" maxlength="6" required="required" />
      </td>
    </tr>
    <tr>
      <td>
        <button type="submit" name="login" class="btn btn-primary button outline-outward promotion-button" onClick="LogInUser('<?=$current_lang;?>');">
          <?=$languages[$current_lang]['btn_login'];?>
        </button>
      </td>
    </tr>
  </table>
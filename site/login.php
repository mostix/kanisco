<?php
  //echo"<pre>";print_r($_SERVER);exit;

//  session_destroy();
//  echo"<pre>Session<br>";print_r($_SESSION);
//  echo"<pre>Post<br>";print_r($_POST);

  if(isset($_POST['login'])) {
  
    $recaptcha_response = false;
    if(isset($_POST['g-recaptcha-response'])) {
      $g_recaptcha_response = $_POST['g-recaptcha-response'];
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $data = array('secret' => '6LcwcRQTAAAAAPWfdj14MLQoc3V9oYbAJuaPGhNl', 'response' => $g_recaptcha_response);

      // use key 'http' even if you send the request to https://...
      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data),
          ),
      );
      $context  = stream_context_create($options);
      $result = json_decode(file_get_contents($url, false, $context));
      $recaptcha_response = $result->success;
    }
    
    if($recaptcha_response) {
      
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
          //header('Location: '.$_SERVER['PHP_SELF'].'');
          echo "<script type='text/jscript'>\n window.location='".$_SERVER['PHP_SELF']."'\n</script>\n";
          exit;
        }
        else {
          $_SESSION['login_error']['text'] = $languages[$current_lang]['customer_login_error'];
        }

      } // if(mysqli_num_rows($result_user) > 0)
      else {
        $_SESSION['login_error']['text'] = $languages[$current_lang]['customer_login_error'];
      }
    } //if($recaptcha_response) 
    else {
      $errors['recaptcha_response_field'] = $languages[$current_lang]['error_create_customer_recaptcha']; 
    }
  }
?>
  <div class="container m-bot-35">                           
    <div class="form-wrapper login">
      <form method="post" id="loginform" name="frmLogin">
<?php
        if(isset($_SESSION['login_error']['text'])) {
          echo '<div class="styled-box error">';
          echo $_SESSION['login_error']['text'];
          echo "</div>";
        }
        //echo $_SESSION['query']."<br>";
?>
        <p>
          <input value="<?php if(isset($_POST['customer_email'])) echo $_POST['customer_email'];?>" autofocus name="customer_email" autocomplete="off" type="text" required="required" placeholder="<?=$languages[$current_lang]['header_customer_username'];?>">
        </p>
        <p>
          <input name="customer_password" type="password" placeholder="<?=$languages[$current_lang]['header_customer_password'];?>" required="required">
        </p>
          
        <?php if(!empty($errors['recaptcha_response_field'])) { ?>
          <div class="styled-box error"><?=$errors['recaptcha_response_field'];?></div>
        <?php } ?>
        <div class="g-recaptcha" data-sitekey="6LcwcRQTAAAAAHXbU1mwgTdCFM1UHPtjLGvCTusn"></div>
        <p>&nbsp;</p>
        <!--<label><input value="forever" id="rememberme" name="rememberme" type="checkbox"> Remember Me</label>-->
        <input class="button gray medium" name="login" value="<?=$languages[$current_lang]['btn_login'];?>" type="submit">     
      </form>
    </div>
  </div>
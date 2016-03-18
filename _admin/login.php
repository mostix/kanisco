<?php
//  unset($_SESSION['captcha_error']);
//  unset($_SESSION['login_error']);
//  unset($_SESSION['bfa']);
//  unset($_SESSION);
//  session_destroy();
//  echo"<pre>";print_r($_SESSION);
  
  if(isset($_SESSION['bfa'])) {
    if( $_SESSION['bfa']['last_activity'] < time()-$_SESSION['bfa']['expire_time'] ) {
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
  
  $form_is_submitted = false;
  
  $db_link = DB_OpenI();

  if(!isset($_SESSION['login_error']['count'])){
    $_SESSION['login_error'] = array();
    $_SESSION['login_error']['count'] = 0;
  }
  
  if(isset($_POST['login'])) {
    
    $form_is_submitted = true;
    $recaptcha_response = true;
  
    //your site secret key
    $secret = '6LcwcRQTAAAAAPWfdj14MLQoc3V9oYbAJuaPGhNl';
    //get verify response data
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    $responseData = json_decode($verifyResponse);
    if($responseData->success) {
//    if(true){
      //unset($_SESSION['captcha_error']);
      $password = $_POST['password'];
      $user_username = $_POST['user_username'];
      $post_user_ip = $_SERVER['REMOTE_ADDR'];
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($password , $bcrypt_salt);

      $query_user = "SELECT `user_id`,`user_type_id`,`user_is_ip_in_use`,`user_ip` FROM `users` WHERE `user_username` = '$user_username'";
      $_SESSION['query'] = $query_user."<br>";
      $result_user = mysqli_query($db_link,$query_user);
      if (!$result_user) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_user) > 0) {
        $user = mysqli_fetch_assoc($result_user);
      
        $db_user_id = $user['user_id'];
        $user_is_ip_in_use = $user['user_is_ip_in_use'];
        $user_ip_in_database = $user['user_ip'];
//        $user_remote_ip = ($user_is_ip_in_use == 1) ? (!empty($user_ip_in_database)) ? $post_user_ip : "" : "";
        $user_remote_ip = "";

        $query_user = "SELECT `users`.*
                        FROM `users`
                        WHERE `users`.`user_id` = '$db_user_id' AND `users`.`user_is_active` = '1'". (!empty($user_remote_ip) ? " 
                          AND `users`.`user_ip` = '$user_remote_ip'" : NULL);
        $_SESSION['query'] .= $query_user."<br>";
        //echo $query_user;EXIT;
        $result_user = mysqli_query($db_link, $query_user);
        if(!$result_user) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_user) > 0) {
          $user_details = mysqli_fetch_assoc($result_user);

          $password_hash = $user_details['user_salted_password'];

          if(crypt($password, $password_hash) == $password_hash) {
            // password is correct

            //update ip if empty START
            if (empty($user_ip_in_database)) {
                $query_update_ip = "UPDATE `users` SET `user_ip`='$post_user_ip' WHERE `user_id` = '$db_user_id'";
                //$_SESSION['$query'] = $query_update_ip;
                mysqli_query($db_link,$query_update_ip);
            }

            //make record for table users_log
            $query = "INSERT INTO `users_logs`(
                                          `user_log_id`, 
                                          `user_id`, 
                                          `user_ip`, 
                                          `user_location_city`, 
                                          `user_location_latitude`, 
                                          `user_location_longitude`, 
                                          `user_log_date`)
                                  VALUES ('',
                                          '$db_user_id',
                                          '$post_user_ip',
                                          NULL,
                                          NULL,
                                          NULL,
                                          NOW())";
            $result = mysqli_query($db_link, $query);
            if (!$result) echo mysqli_error($db_link);
            
            $user_username = $user_details['user_username'];
            $contact_first_name = $user_details['user_firstname'];
            $contact_last_name = $user_details['user_lastname'];
            $user_type_id = $user_details['user_type_id'];

            $_SESSION['admin']['user_id'] = $db_user_id;
            $_SESSION['admin']['user_type_id'] = $user_type_id;
            $_SESSION['admin']['user_username'] = $user_username;
            $_SESSION['admin']['user_fullname'] = (empty($contact_last_name) ? "$contact_first_name" : "$contact_first_name $contact_last_name");
            unset($_SESSION['login_error']);
            unset($_SESSION['bfa']);
            ?>
              <script>window.location.href="<?=$_SERVER['PHP_SELF'];?>"</script>
            <?php
          }
          else {
            $_SESSION['login_error']['count']++;
            $_SESSION['login_error']['text'] = "<h2 class='red'>Username and password mismatch</h2>";
          }

        } // if(mysqli_num_rows($result_user)
      } // if(mysqli_num_rows($result_user) > 0)
      else {
        $_SESSION['login_error']['count'] ++;
        $_SESSION['login_error']['text'] = "<h2 class='red'>Username and password mismatch</h2>";
      }
    }
    else {
      $recaptcha_response = false;
      $errors['recaptcha_response_field'] = "<h2 class='red'>".$languages[$current_lang]['error_create_customer_recaptcha']."</h2>";  
    }
  }// if(isset($_POST['login']))
  //echo $_SESSION['query'];
  DB_CloseI($db_link);
?>
<main id="login">
  <h1><?=$languages[$current_lang]['header_login_page'];?></h1>
  <section>
<?php if(isset($errors['recaptcha_response_field'])) echo $errors['recaptcha_response_field'];?>
<?php if(isset($_SESSION['login_error']['text'])) echo $_SESSION['login_error']['text'];?>
    <form name="loginform" method="post" action="<?=$_SERVER['PHP_SELF'];?>" id="loginform">
      <table>
        <tr>
          <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
            <label for="user_username"><?=$languages[$current_lang]['login_username'];?>:</label>
            <input name="user_username" autofocus type="text" id="user_username" class="input_text">
          </td>
        </tr>
        <tr>
          <td <?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']['count'] > 0) echo 'class="error"';?>>
            <label for="password"><?=$languages[$current_lang]['login_password'];?>:</label>
            <input name="password" type="password" id="password" class="input_text">
          </td>
        </tr>
        <tr>
          <td <?php if($form_is_submitted && !$recaptcha_response) echo 'class="error"';?>>
            <div id="recaptcha_dark" class="g-recaptcha" data-sitekey="6LcwcRQTAAAAAHXbU1mwgTdCFM1UHPtjLGvCTusn"></div>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" name="login" class="button blue"><?=$languages[$current_lang]['btn_login'];?></button>
          </td>
        </tr>
      </table>
    </form>
  </section>
  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
  <script type="text/javascript">
    var onloadCallback = function() {
      grecaptcha.render('recaptcha_dark', {
        'sitekey' : '6LcwcRQTAAAAAHXbU1mwgTdCFM1UHPtjLGvCTusn',
        'theme' : 'dark'
      });
    };
  </script>
</main>
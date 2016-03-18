<?php
define("BASEPATH", "http://www.procad-bg.com/");
define("BASEPATHNOSLASH", "http://www.procad-bg.com");
//define("DIRNAME", dirname(__FILE__)); // no trailing slash
define("DIRNAME", "/home/art93/public_html"); // no trailing slash

//setlocale(LC_ALL, 'bg_BG.UTF-8');
date_default_timezone_set('Europe/Sofia');

//start session
if(!strpos($_SERVER['PHP_SELF'], "ajax") || strlen(session_id()) < 1) {
    session_start();
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // last request was more than 1 hour ago
    unset($_SESSION['customer']);
    unset($_SESSION['cart']);
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

function DB_OpenI() {

    $db_name = "civil3db_kanisco";
    $db_user = "civil3db_kanisco";
    $db_password = "{_%lM;w4M}7m";

    $mysqli = new mysqli("localhost", $db_user, $db_password, $db_name);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
    } else {
        //printf("Current character set: %s\n", $mysqli->character_set_name());
    }

    return $mysqli;
}

function DB_CloseI($db_link) {
  mysqli_close($db_link);
}

function secured() {
  
  if(!defined('BASEPATH')) exit('<h1>No sufficient rights!</h1>');
  
  if(isset($_SESSION['customer']['customer_id']) && !empty($_SESSION['customer']['customer_id'])) {
    // it's ok
    return true;
  }
  else {
    // this seems to be an outside atack
    return false;
  }
  
}

function check_ajax_request() {
  
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // this is an ajax request
  }
  else {
    exit ("<h1>No sufficient rights!</h1>");
  }
}

function check_for_csrf() {
  
  global $db_link;
  
  secured();
  check_ajax_request();
  
  if(isset($_POST['user_access'])) {
    $user_access = $_POST['user_access'];
  }
    
  if(strpos($_SERVER['PHP_SELF'], "ajax/edit") || strpos($_SERVER['PHP_SELF'], "ajax/add")) {
    
    $query = "SELECT `users_rights_edit` FROM `users_rights` WHERE `user_id` = '".$_SESSION['customer']['customer_id']."' AND SHA1( menu_id ) = '$user_access'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      $users_rights = mysqli_fetch_assoc($result);
      $users_rights_edit = $users_rights['users_rights_edit'];
    }

    if($users_rights_edit == 0) {
      exit('<h1>No sufficient rights!</h1>');
    }
  }
  
  if(strpos($_SERVER['PHP_SELF'], "ajax/delete")) {
    
    $query = "SELECT `users_rights_delete` FROM `users_rights` WHERE `user_id` = '".$_SESSION['customer']['customer_id']."' AND SHA1( menu_id ) = '$user_access'";
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else {
      $users_rights = mysqli_fetch_assoc($result);
      $users_rights_delete = $users_rights['users_rights_delete'];
    }

    if($users_rights_delete == 0) {
      exit('<h1>No sufficient rights!</h1>');
    }
  }
  
}

function check_for_csrf_in_reports() {
  
  secured();
  
}

$db_link = DB_OpenI();

?>
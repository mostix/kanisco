<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

define("BASEPATH", "http://www.procad-bg.com/");
define("DIRNAME", dirname(__FILE__)); // no trailing slash
define("URLWEBSITE", "");
//setlocale(LC_ALL, 'bg_BG.UTF-8');
date_default_timezone_set('Europe/Sofia');

$current_lang = "bg";
if(isset($_COOKIE['admin_lang'])) {
  $current_lang = $_COOKIE['admin_lang'];
}
  
require_once("languages/languages.php");

//start session
if(!strpos($_SERVER['PHP_SELF'], "ajax") || strlen(session_id()) < 1) {
    session_start();
}

if(!isset($_SESSION['admin']['user_id']) || (isset($_SESSION['admin']['user_id']) && empty($_SESSION['admin']['user_id']))) {
  //header("Location:/_admin");
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    // last request was more than 1 hour ago
    unset($_SESSION['admin']);
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
  
  if(isset($_SESSION['admin']['user_id']) && !empty($_SESSION['admin']['user_id'])) {
    // it's ok
  }
  else {
    // this seems to be an outside atack
    exit('<h1>No sufficient rights!</h1>');
  }
  
}

function check_ajax_request() {
  
  secured();
  
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])) {
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
    
    $query = "SELECT `users_rights_edit` FROM `users_rights` WHERE `user_id` = '".$_SESSION['admin']['user_id']."' AND SHA1( menu_id ) = '$user_access'";
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
    
    $query = "SELECT `users_rights_delete` FROM `users_rights` WHERE `user_id` = '".$_SESSION['admin']['user_id']."' AND SHA1( menu_id ) = '$user_access'";
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
  
if(isset($_GET['logout']) && $_GET['logout'] == "yes") {
  unset($_SESSION['admin']['user_id']);
  unset($_SESSION['admin']['user_type_id']);
  unset($_SESSION['admin']['user_username']);
  unset($_SESSION['admin']['user_fullname']);
  //session_destroy();
  header("Location:/_admin");
}
else {
  $db_link = DB_OpenI();

  $query_content_hierarchy_ids = "SELECT `language_root_content_id`,`language_id` FROM `languages` WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_content_hierarchy_ids = mysqli_query($db_link, $query_content_hierarchy_ids);
  if(!$result_content_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_hierarchy_ids) > 0) {
    $row_content_hierarchy_ids = mysqli_fetch_assoc($result_content_hierarchy_ids);
    $current_language_id = $row_content_hierarchy_ids['language_id'];
    $content_hierarchy_ids = $row_content_hierarchy_ids['language_root_content_id'];
  }
}
?>
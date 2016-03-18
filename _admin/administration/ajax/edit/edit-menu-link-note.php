<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);exit;
  if(isset($_POST['menu_id'])) {
    $menu_id = $_POST['menu_id'];
  }
  if(isset($_POST['language_id'])) {
    $language_id = $_POST['language_id'];
  }
  if(isset($_POST['menu_link_note'])) {
    $menu_link_note = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_link_note']));
  }
  
  if(!empty($menu_id) && !empty($language_id)) {
    
    $query = "UPDATE `menus_notes` SET `menu_note` = $menu_link_note WHERE `menu_id` = '$menu_id' AND `language_id` = '$language_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
  DB_CloseI($db_link);
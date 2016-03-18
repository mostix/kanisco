<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['menu_id'])) {
    $menu_id = $_POST['menu_id'];
  }
  if(isset($_POST['language_id'])) {
    $language_id = $_POST['language_id'];
  }
  
  if(!empty($menu_id) && !empty($language_id)) {
    
    $query = "DELETE FROM `menus_notes` WHERE `menu_id` = '$menu_id' AND `language_id` = '$language_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
?>
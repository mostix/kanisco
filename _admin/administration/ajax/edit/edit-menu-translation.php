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
  if(isset($_POST['menu_translation_text'])) {
    $menu_translation_text = $_POST['menu_translation_text'];
  }
  //print_r($_POST);
  if(!empty($menu_id) && !empty($language_id) && !empty($menu_translation_text)) {
    $menu_translation_text = mysqli_real_escape_string($db_link,$menu_translation_text);
    $query = "UPDATE `menus_translations` SET `menu_translation_text`='$menu_translation_text' WHERE `menu_id` = '$menu_id' AND `language_id` = '$language_id'";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) echo mysqli_error($db_link);
    if(mysqli_num_rows($result) > 0) {
      echo mysqli_error($db_link);
      exit;
    }
  }
  
?>

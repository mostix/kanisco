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
  
  if(!empty($menu_id) && !empty($language_id) && !empty($menu_translation_text)) {
    $menu_translation_text = mysqli_real_escape_string($db_link,$menu_translation_text);
    $query = "INSERT INTO `menus_translations`(`menu_id`, `language_id`, `menu_translation_text`) VALUES ('$menu_id','$language_id','$menu_translation_text')";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
?>

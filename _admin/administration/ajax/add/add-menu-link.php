<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);exit;
  if(isset($_POST['menu_parent_id'])) {
    $menu_parent_id = $_POST['menu_parent_id'];
  }
  if(isset($_POST['menu_parent_level'])) {
    $menu_parent_level = $_POST['menu_parent_level']+1;
  }
  if(isset($_POST['menu_name'])) {
    $menu_name = mysqli_real_escape_string($db_link,$_POST['menu_name']);
  }
  if(isset($_POST['menu_has_children'])) {
    $menu_has_children = $_POST['menu_has_children'];
  }
  if(isset($_POST['menu_url'])) {
    $menu_url = mysqli_real_escape_string($db_link,$_POST['menu_url']);
  }
  if(isset($_POST['menu_friendly_url'])) {
    $menu_friendly_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_friendly_url']));
  }
  if(isset($_POST['menu_image_url'])) {
    $menu_image_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['menu_image_url']));
  }
  if(isset($_POST['menu_path_name'])) {
    $menu_path_name = $_POST['menu_path_name'];
  }
  if(isset($_POST['menu_sort_order'])) {
    $menu_sort_order = $_POST['menu_sort_order'];
  }
  if(isset($_POST['menu_show_in_menu'])) {
    $menu_show_in_menu = $_POST['menu_show_in_menu'];
  }
  if(isset($_POST['menu_is_active'])) {
    $menu_is_active = $_POST['menu_is_active'];
  }
  
  if(!empty($menu_name)) {
    
    $query = "INSERT INTO `menus`(`menu_id`, 
                                  `menu_parent_id`, 
                                  `menu_hierarchy_level`, 
                                  `menu_has_children`, 
                                  `menu_path_name`, 
                                  `menu_name`, 
                                  `menu_url`, 
                                  `menu_friendly_url`, 
                                  `menu_image_url`, 
                                  `menu_sort_order`, 
                                  `menu_show_in_menu`, 
                                  `menu_is_active`) 
                          VALUES ('',
                                  '$menu_parent_id',
                                  '$menu_parent_level',
                                  '$menu_has_children',
                                  '$menu_path_name',
                                  '$menu_name',
                                  '$menu_url',
                                  $menu_friendly_url,
                                  $menu_image_url,
                                  '$menu_sort_order',
                                  '$menu_show_in_menu',
                                  '$menu_is_active')";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo mysqli_error($db_link);
    }
    else { 
      
    }
  }
  
  DB_CloseI($db_link);
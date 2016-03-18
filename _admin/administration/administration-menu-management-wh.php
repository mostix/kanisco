<?php 
  // we use $first_iteration in list_menu function as a global variable
  $first_iteration = true;
  $menu_id = 1; 
  $main_path_number = 0;
  do_menu_management_page($first_iteration,$menu_id,$main_path_number);
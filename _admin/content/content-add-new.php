<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: contents.php');
  }
  
  $content_parent_id = 0;
  $change_content_type = false;
  
  if(isset($_POST['content_type_id'])) {
    $change_content_type_id = $_POST['change_content_type_id'];
    $current_content_type_id = $_POST['content_type_id'];
    $change_content_type = ($change_content_type_id == $current_content_type_id) ? false : true;
  }
  
  if(isset($_POST['submit_content'])) {
   
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $content_errors = array();
    $all_queries = "";
    
    if($current_content_type_id == 1 || $current_content_type_id == 2 || $current_content_type_id == 6 || $current_content_type_id == 7 || $current_content_type_id == 8 || $current_content_type_id == 9 ) { // content or categories or language
      
      $content_name = $_POST['content_name'];
        if(empty($content_name)) $content_errors['content_name'] = $languages[$current_lang]['required_field_error'];
      $content_menu_text = $_POST['content_menu_text'];
        if(empty($content_menu_text)) $content_errors['content_menu_text'] = $languages[$current_lang]['required_field_error'];
      // $_POST['content_parent_id_level'] has two parameters - id and level
      // first one is the id, second is the level
      $content_parent_id_level = explode(".", $_POST['content_parent_id_level']);
      $content_parent_id = $content_parent_id_level[0];
      $content_hierarchy_level = $content_parent_id_level[1]+1;
      $content_text = $_POST['content_text'];
        if(empty($content_text)) $content_errors['content_text'] = $languages[$current_lang]['required_field_error'];

      if(empty($_POST['content_pretty_url'])) {
        $content_pretty_url = str_replace(" ", "-", mb_convert_case($_POST['content_name'], MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
            $content_pretty_url = $content_pretty_url."-1";
          }
        }
      }
      else {
        $content_pretty_url = $_POST['content_pretty_url'];
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
            $content_pretty_url = $content_pretty_url."-1";
          }
        }
      }

      $content_show_in_menu = 0;
      $content_show_in_footer = 0;
      $content_is_active = 0;
      $content_show_newsletter = 0;
        if(isset($_POST['content_show_in_menu'])) $content_show_in_menu = 1;
        if(isset($_POST['content_show_in_footer'])) $content_show_in_footer = 1;
        if(isset($_POST['content_is_active'])) $content_is_active = 1;
        if(isset($_POST['content_show_newsletter'])) $content_show_newsletter = 1;
      if(isset($_POST['content_show_clients'])) $content_show_clients = "'".$_POST['content_show_clients']."'";
      else $content_show_clients = "NULL";
      $content_target = $_POST['content_target'];
      $content_attribute_1 = $_POST['content_attribute_1'];
      $content_attribute_2 = $_POST['content_attribute_2'];
      $content_meta_title = $_POST['content_meta_title'];
      $content_meta_description = $_POST['content_meta_description'];
      $content_meta_keywords = $_POST['content_meta_keywords'];

      if($content_parent_id != 0) {

        //update the parent column `content_has_children` to 1, wich means it has children
        //no matter if it was set to 1 or 0
        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '1' WHERE `content_id` = '$content_parent_id'";
        $all_queries .= $query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }
      else {
        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }

      $user_id = $_SESSION['admin']['user_id'];

      if(empty($content_errors)) {
        //if there are no form errors we can insert the information

        $content_name = mysqli_real_escape_string($db_link, $_POST['content_name']);
        $content_menu_text = mysqli_real_escape_string($db_link, $_POST['content_menu_text']);
        $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_text']));
        $content_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_title']));
        $content_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_description']));
        $content_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_keywords']));
        $content_target = prepare_for_null_row($content_target);
        $content_attribute_1 = prepare_for_null_row($content_attribute_1);
        $content_attribute_2 = prepare_for_null_row($content_attribute_2);
        $content_hierarchy_ids = 0;
        $content_has_children = 0;
        $content_is_default = 0;
        $content_is_home_page = 0;
        $content_collapsed = 1;

        $query_insert_content = "INSERT INTO `contents`(`content_id`, 
                                                        `content_type_id`, 
                                                        `content_parent_id`, 
                                                        `content_hierarchy_ids`, 
                                                        `content_hierarchy_level`, 
                                                        `content_hierarchy_path`, 
                                                        `content_has_children`, 
                                                        `content_is_default`, 
                                                        `content_is_home_page`, 
                                                        `content_name`, 
                                                        `content_menu_text`, 
                                                        `content_show_in_menu`, 
                                                        `content_show_in_footer`, 
                                                        `content_collapsed`, 
                                                        `content_meta_title`, 
                                                        `content_meta_keywords`, 
                                                        `content_meta_description`, 
                                                        `content_text`, 
                                                        `content_pretty_url`, 
                                                        `content_menu_order`, 
                                                        `content_is_active`, 
                                                        `content_show_newsletter`, 
                                                        `content_show_clients`, 
                                                        `content_target`, 
                                                        `content_attribute_1`, 
                                                        `content_attribute_2`, 
                                                        `content_last_modified_by`, 
                                                        `content_created_date`, 
                                                        `content_modified_date`) 
                                                VALUES ('',
                                                        '$current_content_type_id',
                                                        '$content_parent_id',
                                                        '$content_hierarchy_ids',
                                                        '$content_hierarchy_level',
                                                        '',
                                                        '$content_has_children',
                                                        '$content_is_default',
                                                        '$content_is_home_page',
                                                        '$content_name',
                                                        '$content_menu_text',
                                                        '$content_show_in_menu',
                                                        '$content_show_in_footer',
                                                        '$content_collapsed',
                                                        $content_meta_title,
                                                        $content_meta_keywords,
                                                        $content_meta_description,
                                                        $content_text,
                                                        '$content_pretty_url',
                                                        '$content_menu_order',
                                                        '$content_is_active',
                                                        '$content_show_newsletter',
                                                        $content_show_clients,
                                                        $content_target,
                                                        $content_attribute_1, 
                                                        $content_attribute_2, 
                                                        '$user_id',
                                                        NOW(),
                                                        NOW())";
        $all_queries .= "<br>".$query_insert_content;
        $result_insert_content = mysqli_query($db_link, $query_insert_content);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_id = mysqli_insert_id($db_link);

        //update the content's `content_hierarchy_ids` and `content_hierarchy_path` after insertion
        $content_hierarchy_ids_list = "";
        if($content_parent_id != 0) {
          $content_hierarchy_ids_and_path = get_contents_hierarchy_ids_and_path($content_parent_id);
          $content_hierarchy_ids = $content_hierarchy_ids_and_path['content_hierarchy_ids'];
          $content_hierarchy_path = $content_hierarchy_ids_and_path['content_hierarchy_path']."/$content_pretty_url";
          $content_hierarchy_ids_list .= "$content_hierarchy_ids.$content_id";
        }
        else {
          $content_hierarchy_ids_list = $content_id;
          $content_hierarchy_path = $content_pretty_url;
        }

        $query_update_parent = "UPDATE `contents` 
                                SET `content_hierarchy_ids` = '$content_hierarchy_ids_list', `content_hierarchy_path` = '$content_hierarchy_path' 
                                WHERE `content_id` = '$content_id'";
        $all_queries .= "<br>".$query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

        mysqli_query($db_link,"COMMIT");

        header('Location: contents.php');
      }//if(empty($content_errors))
    }
    elseif($current_content_type_id == 3) { // error page
      
      //echo"<pre>";print_r($_POST);
      $content_type_id = $_POST['content_type_id'];
      $content_name = $_POST['content_name'];
        if(empty($content_name)) $content_errors['content_name'] = $languages[$current_lang]['required_field_error'];
      $content_menu_text = "NULL";
      $content_text = $_POST['content_text'];
        if(empty($content_text)) $content_errors['content_text'] = $languages[$current_lang]['required_field_error'];
      // $_POST['content_parent_id_level'] has two parameters - id and level
      // first one is the id, second is the level
      $content_parent_id_level = explode(".", $_POST['content_parent_id_level']);
      $content_parent_id = $content_parent_id_level[0];
      $content_hierarchy_level = $content_parent_id_level[1]+1;

      if($content_parent_id != 0) {

        //update the parent column `content_has_children` to 1, wich means it has children
        //no matter if it was set to 1 or 0
        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '1' WHERE `content_id` = '$content_parent_id'";
        $all_queries .= $query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }
      else {
        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }

      $content_hierarchy_ids = 0;
      $content_has_children = 0;
      $content_is_default = 0;
      $content_is_home_page = 0;
      $content_show_in_menu = 0;
      $content_show_in_footer = 0;
      $content_collapsed = 1;
      $content_is_active = 1;
      $content_show_newsletter = 0;
      $content_show_clients = "NULL";
      $content_target = "NULL";
      $content_attribute_1 = "NULL";
      $content_attribute_2 = "NULL";
      $content_pretty_url = str_replace(" ", "-", mb_convert_case($content_name, MB_CASE_LOWER, "UTF-8"));
      $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
      if(!$is_pretty_url_unique) {
        $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
        $content_pretty_url = $content_pretty_url."-1";
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
          $content_pretty_url = $content_pretty_url."-1";
        }
      }
      $content_meta_title = $_POST['content_meta_title'];
      $content_meta_description = $_POST['content_meta_description'];
      $content_meta_keywords = $_POST['content_meta_keywords'];
      $user_id = $_SESSION['admin']['user_id'];
      
      if(empty($content_errors)) {
        //if there are no form errors we can insert the information
        
        $content_name = mysqli_real_escape_string($db_link, $_POST['content_name']);
        $content_text = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_text']));
        $content_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_title']));
        $content_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_description']));
        $content_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_keywords']));
        
        $query_insert_content = "INSERT INTO `contents`(`content_id`, 
                                                        `content_type_id`, 
                                                        `content_parent_id`, 
                                                        `content_hierarchy_ids`, 
                                                        `content_hierarchy_level`, 
                                                        `content_hierarchy_path`, 
                                                        `content_has_children`, 
                                                        `content_is_default`, 
                                                        `content_is_home_page`, 
                                                        `content_name`, 
                                                        `content_menu_text`, 
                                                        `content_show_in_menu`, 
                                                        `content_show_in_footer`, 
                                                        `content_collapsed`, 
                                                        `content_meta_title`, 
                                                        `content_meta_keywords`, 
                                                        `content_meta_description`, 
                                                        `content_text`, 
                                                        `content_pretty_url`,  
                                                        `content_menu_order`, 
                                                        `content_is_active`, 
                                                        `content_show_newsletter`, 
                                                        `content_show_clients`, 
                                                        `content_target`, 
                                                        `content_attribute_1`, 
                                                        `content_attribute_2`, 
                                                        `content_last_modified_by`, 
                                                        `content_created_date`, 
                                                        `content_modified_date`) 
                                                VALUES ('',
                                                        '$current_content_type_id',
                                                        '$content_parent_id',
                                                        '$content_hierarchy_ids',
                                                        '$content_hierarchy_level',
                                                        '',
                                                        '$content_has_children',
                                                        '$content_is_default',
                                                        '$content_is_home_page',
                                                        '$content_name',
                                                        $content_menu_text,
                                                        '$content_show_in_menu',
                                                        '$content_show_in_footer',
                                                        '$content_collapsed',
                                                        $content_meta_title,
                                                        $content_meta_keywords,
                                                        $content_meta_description,
                                                        $content_text,
                                                        '$content_pretty_url',
                                                        '$content_menu_order',
                                                        '$content_is_active',
                                                        '$content_show_newsletter',
                                                        $content_show_clients,
                                                        $content_target,
                                                        $content_attribute_1, 
                                                        $content_attribute_2, 
                                                        '$user_id',
                                                        NOW(),
                                                        NOW())";
        $all_queries .= $query_insert_content;
        $result_insert_content = mysqli_query($db_link, $query_insert_content);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
        $content_id = mysqli_insert_id($db_link);

        $content_hierarchy_ids_list = "";
        if($content_parent_id != 0) {
          $content_hierarchy_ids_and_path = get_contents_hierarchy_ids_and_path($content_parent_id);
          $content_hierarchy_ids = $content_hierarchy_ids_and_path['content_hierarchy_ids'];
          $content_hierarchy_path = $content_hierarchy_ids_and_path['content_hierarchy_path']."/$content_pretty_url";
          $content_hierarchy_ids_list .= "$content_hierarchy_ids.$content_id";
        }
        else {
          $content_hierarchy_ids_list = $content_id;
          $content_hierarchy_path = $content_pretty_url;
        }

        //update the content's `content_hierarchy_ids` and `content_hierarchy_path` after insertion
        $query_update_parent = "UPDATE `contents` 
                                SET `content_hierarchy_ids` = '$content_hierarchy_ids_list', `content_hierarchy_path` = '$content_hierarchy_path' 
                                WHERE `content_id` = '$content_id'";
        $all_queries .= "<br>".$query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
        
        mysqli_query($db_link,"COMMIT");

        header('Location: contents.php');
      }
    }
    elseif($current_content_type_id == 4) { // redirect url
      
      //echo"<pre>";print_r($_POST);
      $content_name = $_POST['content_name'];
        if(empty($content_name)) $content_errors['content_name'] = $languages[$current_lang]['required_field_error'];
      $content_menu_text = $_POST['content_menu_text'];
        if(empty($content_menu_text)) $content_errors['content_menu_text'] = $languages[$current_lang]['required_field_error'];
      // $_POST['content_parent_id_level'] has two parameters - id and level
      // first one is the id, second is the level
      $content_parent_id_level = explode(".", $_POST['content_parent_id_level']);
      $content_parent_id = $content_parent_id_level[0];
      $content_hierarchy_level = $content_parent_id_level[1]+1;
      $content_redirect_url = $_POST['content_redirect_url'];
        if(empty($content_redirect_url)) $content_errors['content_redirect_url'] = $languages[$current_lang]['required_field_error'];

      if(empty($_POST['content_pretty_url'])) {
        $content_pretty_url = str_replace(" ", "-", mb_convert_case($_POST['content_name'], MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
            $content_pretty_url = $content_pretty_url."-1";
          }
        }
      }
      else {
        $content_pretty_url = $_POST['content_pretty_url'];
        $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
        if(!$is_pretty_url_unique) {
          $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
          $content_pretty_url = $content_pretty_url."-1";
          $is_pretty_url_unique = check_if_content_pretty_url_is_unique($content_pretty_url);
          if(!$is_pretty_url_unique) {
            $content_errors['content_pretty_url'] = $languages[$current_lang]['content_pretty_url_error'];
            $content_pretty_url = $content_pretty_url."-1";
          }
        }
      }

      $content_show_in_menu = 0;
      $content_show_in_footer = 0;
      $content_is_active = 0;
      $content_show_newsletter = 0;
        if(isset($_POST['content_show_in_menu'])) $content_show_in_menu = 1;
        if(isset($_POST['content_show_in_footer'])) $content_show_in_footer = 1;
        if(isset($_POST['content_is_active'])) $content_is_active = 1;
        if(isset($_POST['content_show_newsletter'])) $content_show_newsletter = 1;
      if(isset($_POST['content_show_clients'])) $content_show_clients = "'".$_POST['content_show_clients']."'";
      else $content_show_clients = "NULL";
      $content_target = $_POST['content_target'];
      $content_attribute_1 = $_POST['content_attribute_1'];
      $content_attribute_2 = $_POST['content_attribute_2'];
      $content_meta_title = $_POST['content_meta_title'];
      $content_meta_description = $_POST['content_meta_description'];
      $content_meta_keywords = $_POST['content_meta_keywords'];

      if($content_parent_id != 0) {

        //update the parent column `content_has_children` to 1, wich means it has children
        //no matter if it was set to 1 or 0
        $query_update_parent = "UPDATE `contents` SET `content_has_children` = '1' WHERE `content_id` = '$content_parent_id'";
        $all_queries .= $query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }
      else {
        $content_menu_order = get_content_lаst_child_order_value($content_parent_id);
        $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
      }

      $user_id = $_SESSION['admin']['user_id'];

      if(empty($content_errors)) {
        //if there are no form errors we can insert the information

        $content_name = mysqli_real_escape_string($db_link, $_POST['content_name']);
        $content_menu_text = mysqli_real_escape_string($db_link, $_POST['content_menu_text']);
        $content_redirect_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['content_redirect_url']));
        $content_pretty_url = mysqli_real_escape_string($db_link, $content_pretty_url);
        $content_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_title']));
        $content_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_description']));
        $content_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['content_meta_keywords']));
        $content_target = prepare_for_null_row($content_target);
        $content_attribute_1 = prepare_for_null_row($content_attribute_1);
        $content_attribute_2 = prepare_for_null_row($content_attribute_2);
        $content_hierarchy_ids = 0;
        $content_has_children = 0;
        $content_is_default = 0;
        $content_is_home_page = 0;
        $content_collapsed = 1;

        $query_insert_content = "INSERT INTO `contents`(`content_id`, 
                                                        `content_type_id`, 
                                                        `content_parent_id`, 
                                                        `content_hierarchy_ids`, 
                                                        `content_hierarchy_level`, 
                                                        `content_hierarchy_path`, 
                                                        `content_has_children`, 
                                                        `content_is_default`, 
                                                        `content_is_home_page`, 
                                                        `content_name`, 
                                                        `content_menu_text`, 
                                                        `content_show_in_menu`, 
                                                        `content_show_in_footer`, 
                                                        `content_collapsed`, 
                                                        `content_meta_title`, 
                                                        `content_meta_keywords`, 
                                                        `content_meta_description`, 
                                                        `content_text`, 
                                                        `content_pretty_url`, 
                                                        `content_menu_order`, 
                                                        `content_is_active`,
                                                        `content_show_newsletter`, 
                                                        `content_show_clients`,  
                                                        `content_target`, 
                                                        `content_attribute_1`, 
                                                        `content_attribute_2`, 
                                                        `content_last_modified_by`, 
                                                        `content_created_date`, 
                                                        `content_modified_date`) 
                                                VALUES ('',
                                                        '$current_content_type_id',
                                                        '$content_parent_id',
                                                        '$content_hierarchy_ids',
                                                        '$content_hierarchy_level',
                                                        '',
                                                        '$content_has_children',
                                                        '$content_is_default',
                                                        '$content_is_home_page',
                                                        '$content_name',
                                                        '$content_menu_text',
                                                        '$content_show_in_menu',
                                                        '$content_show_in_footer',
                                                        '$content_collapsed',
                                                        $content_meta_title,
                                                        $content_meta_keywords,
                                                        $content_meta_description,
                                                        $content_redirect_url,
                                                        '$content_pretty_url',
                                                        '$content_menu_order',
                                                        '$content_is_active',
                                                        '$content_show_newsletter',
                                                        $content_show_clients,
                                                        $content_target,
                                                        $content_attribute_1, 
                                                        $content_attribute_2, 
                                                        '$user_id',
                                                        NOW(),
                                                        NOW())";
        $all_queries .= "<br>".$query_insert_content;
        $result_insert_content = mysqli_query($db_link, $query_insert_content);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $content_id = mysqli_insert_id($db_link);

        $content_hierarchy_ids_list = "";
        if($content_parent_id != 0) {
          $content_hierarchy_ids_and_path = get_contents_hierarchy_ids_and_path($content_parent_id);
          $content_hierarchy_ids = $content_hierarchy_ids_and_path['content_hierarchy_ids'];
          $content_hierarchy_path = $content_hierarchy_ids_and_path['content_hierarchy_path']."/$content_pretty_url";
          $content_hierarchy_ids_list .= "$content_hierarchy_ids.$content_id";
        }
        else {
          $content_hierarchy_ids_list = $content_id;
          $content_hierarchy_path = $content_pretty_url;
        }

        //update the content's `content_hierarchy_ids` and `content_hierarchy_path` after insertion
        $query_update_parent = "UPDATE `contents` 
                                SET `content_hierarchy_ids` = '$content_hierarchy_ids_list', `content_hierarchy_path` = '$content_hierarchy_path' 
                                WHERE `content_id` = '$content_id'";
        $all_queries .= "<br>".$query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

//        echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

        mysqli_query($db_link,"COMMIT");

        header('Location: contents.php');
      }//if(empty($content_errors))
    }
      
  }//if(isset($_POST['submit_content']))
  else {
    if(isset($_POST['content_type_id'])) $current_content_type_id = $_POST['content_type_id'];
    else $current_content_type_id = 1; //default content_type is content
  }

  $page_title = $languages[$current_lang]['content_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/content/contents.php" title="<?=$languages[$current_lang]['title_breadcrumbs_pages'];?>"><?=$languages[$current_lang]['header_pages'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_content_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_content_add_new'];?></h1>
      
      <ul class="tabs">
        <li><a href="#content_main_tab"><?=$languages[$current_lang]['header_content_main_tab'];?></a></li>
        <li><a href="#content_options_tab"><?=$languages[$current_lang]['header_content_options_tab'];?></a></li>
      </ul>
      <div class="clearfix"></div>
      
      <form method="post" name="edit_content" id="edit_content" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>">
        <div>
          <button type="submit" name="submit_content" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <p class="clearfix"></p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
<?php
      if($current_content_type_id == 1 || $current_content_type_id == 2 || $current_content_type_id == 6 || $current_content_type_id == 7 || $current_content_type_id == 8 || $current_content_type_id == 9 ) {
        // content_type is content or categories or language
        
        // if the form was not submitted but the content type was changed
        // reset the errors array
        if($change_content_type) $content_errors = array();
?>
        <input type="hidden" name="change_content_type_id" value="<?=$current_content_type_id;?>" />
        <section id="content_main_tab" class="tab">
          
          <div>
            <label for="content_type" class="title"><?=$languages[$current_lang]['header_content_type'];?></label>
            <select name="content_type_id" id="content_type_id" onchange="document.edit_content.submit()" style="width: 200px;">
              <?php
                //don't show language type beacause language contents are inserted automatically when adding new language
                $query_content_types = "SELECT `content_type_id`, `content_type` FROM `contents_types` WHERE `content_type_id` <> '6'";
                $result_content_types = mysqli_query($db_link, $query_content_types);
                if(!$result_content_types) echo mysqli_error($db_link);
                if(mysqli_num_rows($result_content_types) > 0) {
                  while($row_content_types = mysqli_fetch_assoc($result_content_types)) {

                    $content_type_id = $row_content_types['content_type_id'];
                    $content_type = $row_content_types['content_type'];
                    $content_type_lang = $languages[$current_lang][$content_type];
                    $selected = ($content_type_id == $current_content_type_id) ? ' selected="selected"' : "";

                    echo "<option value='$content_type_id'$selected>$content_type_lang</option>";
                  }
                }
              ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_name" class="title"><?=$languages[$current_lang]['header_content_name'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_name'])) {
                echo "<div class='error'>".$content_errors['content_name']."</div>";
              }
            ?>
            <input type="text" name="content_name" id="content_name" style="width: 400px;" value="<?php if(isset($content_name)) echo $content_name;?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_menu_text" class="title"><?=$languages[$current_lang]['header_content_menu_text'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_menu_text'])) {
                echo "<div class='error'>".$content_errors['content_menu_text']."</div>";
              }
            ?>
            <input type="text" name="content_menu_text" id="content_menu_text" style="width: 400px;" value="<?php if(isset($content_menu_text)) echo $content_menu_text;?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_parent" class="title"><?=$languages[$current_lang]['header_content_parent'];?></label>
            <select name="content_parent_id_level" id="content_parent_id_level" style="width: 600px;">
              <option value="0.0" level="0"><?=$languages[$current_lang]['option_no_content_parent'];?></option>
              <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = $content_parent_id, $current_content_id = 0); ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_text" class="title"><?=$languages[$current_lang]['header_content_text'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_text'])) {
                echo "<div class='error'>".$content_errors['content_text']."</div>";
              }
            ?>
            <textarea name="content_text" id="ckeditor" class="default_text"><?php if(isset($content_text)) echo $content_text;?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </section>
        
        <section id="content_options_tab" class="tab">

          <div>
            <label for="content_pretty_url" class="title"><?=$languages[$current_lang]['header_content_pretty_url'];?></label>
            <input type="text" name="content_pretty_url" id="content_pretty_url" style="width: 500px;" value="<?php if(isset($content_pretty_url)) echo $content_pretty_url;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_pretty_url'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_in_menu" class="title"><?=$languages[$current_lang]['header_content_show_in_menu'];?></label>
            <?php
              if(isset($content_show_in_menu)) {
                if($content_show_in_menu == 0) echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" />';
                else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_in_footer" class="title"><?=$languages[$current_lang]['header_content_show_in_footer'];?></label>
            <?php
              if(isset($content_show_in_footer)) {
                if($content_show_in_footer == 0) echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
                else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_is_active" class="title"><?=$languages[$current_lang]['header_content_is_active'];?></label>
            <?php
              if(isset($content_is_active)) {
                if($content_is_active == 0) echo '<input type="checkbox" name="content_is_active" id="content_is_active" />';
                else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_newsletter" class="title"><?=$languages[$current_lang]['header_content_show_newsletter'];?></label>
            <?php
              if(isset($content_show_newsletter)) {
                if($content_show_newsletter == 0) echo '<input type="checkbox" name="content_show_newsletter" id="content_show_newsletter" />';
                else echo '<input type="checkbox" name="content_show_newsletter" id="content_show_newsletter" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_newsletter" id="content_show_newsletter" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_clients" class="title"><?=$languages[$current_lang]['header_content_show_clients'];?></label>
            <?php
              if(isset($content_show_clients)) {
                if($content_show_clients == 0) echo '<input type="checkbox" name="anable_disable_show_clients" id="anable_disable_show_clients" />';
                else echo '<input type="checkbox" name="anable_disable_show_clients" id="anable_disable_show_clients" checked="checked" />';
              }
              else echo '<input type="checkbox" name="anable_disable_show_clients" id="anable_disable_show_clients" />';
            ?>
            <input type="text" name="content_show_clients" style="width: 40px;" id="content_show_clients" <?php if($content_show_clients == 0) echo "disabled='disabled'"; else echo "value='$content_show_clients'";?>>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_target" class="title"><?=$languages[$current_lang]['header_content_target'];?></label>
            <select name="content_target" id="content_target" style="width: 100px;">
              <option value=""><?=$languages[$current_lang]['option_no_content_target'];?></option>
              <option value="_blank" <?php if(isset($content_target) && $content_target == "_blank") echo "selected" ;?>><?=$languages[$current_lang]['option_content_target_blank'];?></option>
            </select>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_target_blank'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_title" class="title"><?=$languages[$current_lang]['header_content_meta_title'];?></label>
            <input type="text" name="content_meta_title" id="content_meta_title" onkeyup="CountCharacters(this,'100')" style="width: 60%;" value="<?php if(isset($content_meta_title)) echo $content_meta_title;?>" />
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_title'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_keywords" class="title"><?=$languages[$current_lang]['header_content_meta_keywords'];?></label>
            <input type="text" name="content_meta_keywords" id="content_meta_keywords" style="width: 60%;" value="<?php if(isset($content_meta_keywords)) echo $content_meta_keywords;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_keywords'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_description" class="title"><?=$languages[$current_lang]['header_content_meta_description'];?></label>
            <textarea name="content_meta_description" id="content_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"><?php if(isset($content_meta_description)) echo $content_meta_description;?></textarea>
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_description'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_1" class="title"><?=$languages[$current_lang]['header_extra_attribute_1'];?></label>
            <input type="text" name="content_attribute_1" id="content_attribute_1" style="width: 500px;" value="<?php if(isset($content_attribute_1)) echo $content_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_2" class="title"><?=$languages[$current_lang]['header_extra_attribute_2'];?></label>
            <input type="text" name="content_attribute_2" id="content_attribute_2" style="width: 500px;" value="<?php if(isset($content_attribute_2)) echo $content_attribute_2;?>" />
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </section>
<?php
    } elseif($current_content_type_id == 3) {
        // content_type is error_page

        // if the form was not submitted but the content type was changed
        // reset the errors array
        if($change_content_type) $content_errors = array();
?>
        <input type="hidden" name="change_content_type_id" value="<?=$current_content_type_id;?>" />
        <section id="content_main_tab" class="tab">
          
          <div>
            <label for="content_type" class="title"><?=$languages[$current_lang]['header_content_type'];?></label>
            <select name="content_type_id" id="content_type_id" onchange="document.edit_content.submit()" style="width: 200px;">
              <?php 
                //don't show language type beacause language contents are inserted automatically when adding new language
                $query_content_types = "SELECT `content_type_id`, `content_type` FROM `contents_types` WHERE `content_type_id` <> '6'";
                $result_content_types = mysqli_query($db_link, $query_content_types);
                if(!$result_content_types) echo mysqli_error($db_link);
                if(mysqli_num_rows($result_content_types) > 0) {
                  while($row_content_types = mysqli_fetch_assoc($result_content_types)) {

                    $content_type_id = $row_content_types['content_type_id'];
                    $content_type = $row_content_types['content_type'];
                    $content_type_lang = $languages[$current_lang][$content_type];
                    $selected = ($content_type_id == $current_content_type_id) ? ' selected="selected"' : "";

                    echo "<option value='$content_type_id'$selected>$content_type_lang</option>";
                  }
                }
              ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_name" class="title"><?=$languages[$current_lang]['header_content_name'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_name'])) {
                echo "<div class='error'>".$content_errors['content_name']."</div>";
              }
            ?>
            <input type="text" name="content_name" id="content_name" style="width: 400px;" value="<?php if(isset($content_name)) echo $content_name;?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_parent" class="title"><?=$languages[$current_lang]['header_content_parent'];?></label>
            <select name="content_parent_id_level" id="content_parent_id_level" style="width: 600px;">
              <option value="0.0" level="0"><?=$languages[$current_lang]['option_no_content_parent'];?></option>
              <?php 
                $get_only_content_type_language = true; 
                list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = $content_parent_id, $current_content_id = 0); 
              ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_text" class="title"><?=$languages[$current_lang]['header_content_text'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_text'])) {
                echo "<div class='error'>".$content_errors['content_text']."</div>";
              }
            ?>
            <textarea name="content_text" id="ckeditor" class="default_text"><?php if(isset($content_text)) echo $content_text;?></textarea>
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
          
        </section>
        
        <section id="content_options_tab" class="tab">
          
          <div>
            <label for="content_pretty_url" class="title"><?=$languages[$current_lang]['header_content_pretty_url'];?></label>
            <input type="text" name="content_pretty_url" id="content_pretty_url" style="width: 500px;" value="<?php if(isset($content_pretty_url)) echo $content_pretty_url;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_pretty_url'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_title" class="title"><?=$languages[$current_lang]['header_content_meta_title'];?></label>
            <input type="text" name="content_meta_title" id="content_meta_title" onkeyup="CountCharacters(this,'100')" style="width: 60%;" value="<?php if(isset($content_meta_title)) echo $content_meta_title;?>" />
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_title'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_keywords" class="title"><?=$languages[$current_lang]['header_content_meta_keywords'];?></label>
            <input type="text" name="content_meta_keywords" id="content_meta_keywords" style="width: 60%;" value="<?php if(isset($content_meta_keywords)) echo $content_meta_keywords;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_keywords'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_description" class="title"><?=$languages[$current_lang]['header_content_meta_description'];?></label>
            <textarea name="content_meta_description" id="content_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"><?php if(isset($content_meta_description)) echo $content_meta_description;?></textarea>
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_description'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_1" class="title"><?=$languages[$current_lang]['header_extra_attribute_1'];?></label>
            <input type="text" name="content_attribute_1" id="content_attribute_1" style="width: 500px;" value="<?php if(isset($content_attribute_1)) echo $content_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_2" class="title"><?=$languages[$current_lang]['header_extra_attribute_2'];?></label>
            <input type="text" name="content_attribute_2" id="content_attribute_2" style="width: 500px;" value="<?php if(isset($content_attribute_2)) echo $content_attribute_2;?>" />
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
          
        </section>
<?php
    } elseif($current_content_type_id == 4) {
        // content_type is redirecting_link
        
        // if the form was not submitted but the content type was changed
        // reset the errors array
        if($change_content_type) $content_errors = array();
?>
        <input type="hidden" name="change_content_type_id" value="<?=$current_content_type_id;?>" />
        <section id="content_main_tab" class="tab">
          
          <div>
            <label for="content_type" class="title"><?=$languages[$current_lang]['header_content_type'];?></label>
            <select name="content_type_id" id="content_type_id" onchange="document.edit_content.submit()" style="width: 200px;">
              <?php
                //don't show language type beacause language contents are inserted automatically when adding new language
                $query_content_types = "SELECT `content_type_id`, `content_type` FROM `contents_types` WHERE `content_type_id` <> '6'";
                $result_content_types = mysqli_query($db_link, $query_content_types);
                if(!$result_content_types) echo mysqli_error($db_link);
                if(mysqli_num_rows($result_content_types) > 0) {
                  while($row_content_types = mysqli_fetch_assoc($result_content_types)) {

                    $content_type_id = $row_content_types['content_type_id'];
                    $content_type = $row_content_types['content_type'];
                    $content_type_lang = $languages[$current_lang][$content_type];
                    $selected = ($content_type_id == $current_content_type_id) ? ' selected="selected"' : "";

                    echo "<option value='$content_type_id'$selected>$content_type_lang</option>";
                  }
                }
              ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_name" class="title"><?=$languages[$current_lang]['header_content_name'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_name'])) {
                echo "<div class='error'>".$content_errors['content_name']."</div>";
              }
            ?>
            <input type="text" name="content_name" id="content_name" style="width: 400px;" value="<?php if(isset($content_name)) echo $content_name;?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_menu_text" class="title"><?=$languages[$current_lang]['header_content_menu_text'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_menu_text'])) {
                echo "<div class='error'>".$content_errors['content_menu_text']."</div>";
              }
            ?>
            <input type="text" name="content_menu_text" id="content_menu_text" style="width: 400px;" value="<?php if(isset($content_menu_text)) echo $content_menu_text;?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_parent" class="title"><?=$languages[$current_lang]['header_content_parent'];?></label>
            <select name="content_parent_id_level" id="content_parent_id_level" style="width: 600px;">
              <option value="0.0" level="0"><?=$languages[$current_lang]['option_no_content_parent'];?></option>
              <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = $content_parent_id, $current_content_id = 0); ?> 
            </select>
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="content_redirect_url" class="title"><?=$languages[$current_lang]['header_content_redirect_url'];?><span class="red">*</span></label>
            <?php
              if(isset($content_errors['content_redirect_url'])) {
                echo "<div class='error'>".$content_errors['content_redirect_url']."</div>";
              }
            ?>
            <input type="text" name="content_redirect_url" id="content_redirect_url" style="width: 500px;" value="<?php if(isset($content_redirect_url)) echo $content_redirect_url;?>" />
            <div class="clearfix"></div>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </section>
        
        <section id="content_options_tab" class="tab">

          <div>
            <label for="content_pretty_url" class="title"><?=$languages[$current_lang]['header_content_pretty_url'];?></label>
            <input type="text" name="content_pretty_url" id="content_pretty_url" style="width: 500px;" value="<?php if(isset($content_pretty_url)) echo $content_pretty_url;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_pretty_url'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_in_menu" class="title"><?=$languages[$current_lang]['header_content_show_in_menu'];?></label>
            <?php
              if(isset($content_show_in_menu)) {
                if($content_show_in_menu == 0) echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" />';
                else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_menu" id="content_show_in_menu" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_show_in_footer" class="title"><?=$languages[$current_lang]['header_content_show_in_footer'];?></label>
            <?php
              if(isset($content_show_in_footer)) {
                if($content_show_in_footer == 0) echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
                else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_show_in_footer" id="content_show_in_footer" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_is_active" class="title"><?=$languages[$current_lang]['header_content_is_active'];?></label>
            <?php
              if(isset($content_is_active)) {
                if($content_is_active == 0) echo '<input type="checkbox" name="content_is_active" id="content_is_active" />';
                else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
              }
              else echo '<input type="checkbox" name="content_is_active" id="content_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_target" class="title"><?=$languages[$current_lang]['header_content_target'];?></label>
            <select name="content_target" id="content_target" style="width: 100px;">
              <option value=""><?=$languages[$current_lang]['option_no_content_target'];?></option>
              <option value="_blank" <?php if(isset($content_target) && $content_target == "_blank") echo "selected" ;?>><?=$languages[$current_lang]['option_content_target_blank'];?></option>
            </select>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_target_blank'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_title" class="title"><?=$languages[$current_lang]['header_content_meta_title'];?></label>
            <input type="text" name="content_meta_title" id="content_meta_title" onkeyup="CountCharacters(this,'100')" style="width: 60%;" value="<?php if(isset($content_meta_title)) echo $content_meta_title;?>" />
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_title'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_keywords" class="title"><?=$languages[$current_lang]['header_content_meta_keywords'];?></label>
            <input type="text" name="content_meta_keywords" id="content_meta_keywords" style="width: 60%;" value="<?php if(isset($content_meta_keywords)) echo $content_meta_keywords;?>" />
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_keywords'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_meta_description" class="title"><?=$languages[$current_lang]['header_content_meta_description'];?></label>
            <textarea name="content_meta_description" id="content_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"><?php if(isset($content_meta_description)) echo $content_meta_description;?></textarea>
            <span class="info"><b></b></span>
            <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['content_meta_characters_warning'];?></b></span>
            <div class="clearfix"></div>
            <i class="info"><?=$languages[$current_lang]['info_content_meta_description'];?></i>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_1" class="title"><?=$languages[$current_lang]['header_extra_attribute_1'];?></label>
            <input type="text" name="content_attribute_1" id="content_attribute_1" style="width: 500px;" value="<?php if(isset($content_attribute_1)) echo $content_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="content_attribute_2" class="title"><?=$languages[$current_lang]['header_extra_attribute_2'];?></label>
            <input type="text" name="content_attribute_2" id="content_attribute_2" style="width: 500px;" value="<?php if(isset($content_attribute_2)) echo $content_attribute_2;?>" />
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </section>
<?php
    } elseif($current_content_type_id == 5) {
        // content_type is section_header
?>
        
<?php
    } else {
        // content_type is wrong
    }
?>
        

        <div>
          <button type="submit" name="submit_content" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
  if($current_content_type_id == 1 || $current_content_type_id == 2 || $current_content_type_id == 3 || $current_content_type_id == 6 || $current_content_type_id == 7 || $current_content_type_id == 8 || $current_content_type_id == 9 ) {
?>
<!-- CK Configuration -->
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
<!-- CK Configuration -->
<?php
  }
?>
  <script type="text/javascript">
    $(document).ready(function() {
<?php if($current_content_type_id == 1 || $current_content_type_id == 2 || $current_content_type_id == 3 || $current_content_type_id == 6 || $current_content_type_id == 7 || $current_content_type_id == 8 || $current_content_type_id == 9 ) echo "CKEDITOR.replace('ckeditor');";?>

      // tab switcher
      $(".tabs li").removeClass("active");
      $(".tab").hide();
      $(".tabs li:first").addClass("active");
      $(".input_form .tab:first").show();
      $(".tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".input_form .tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end tab switcher
      
      $("#anable_disable_show_clients").click(function() {
        if($(this).is(':checked')) {
          $("#content_show_clients").attr("disabled",false);
        }
        else {
          $("#content_show_clients").attr("disabled",true);
        }
      });
    });
  </script>
</body>
</html>
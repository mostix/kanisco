<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: categories.php');
  }
  
  $languages_array = array();
  $query_languages = "SELECT `language_id`,`language_code`,`language_menu_name` 
                      FROM `languages` 
                      WHERE `language_is_active` = '1' 
                      ORDER BY `language_menu_order` ASC";
  $result_languages = mysqli_query($db_link, $query_languages);
  if (!$result_languages) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_languages) > 0) {
    while($row_languages = mysqli_fetch_assoc($result_languages)) {
      $languages_array[] = $row_languages; 
    }
  }
  
  if(isset($_GET['category_id'])) {
    $current_category_id= $_GET['category_id'];
  }
  else {
    exit("Error");
  }
  
  $category_parent_id = 0;

  if(isset($_POST['submit_category'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
//    $extension_array = explode("/", $_FILES['category_image']['type']);
//    $extension = $extension_array[1];
//    echo $extension;exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $category_errors = array();
    $all_queries = "";
      
    foreach($_POST['cd_name'] as $language_id => $cd_name) {
      if(empty($cd_name)) $category_errors['cd_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $cd_names_array[$language_id] = $_POST['cd_name'][$language_id];
      $cd_pretty_urls_array[$language_id] = $_POST['cd_pretty_url'][$language_id];
      $current_cd_pretty_urls_array[$language_id] = $_POST['current_cd_pretty_url'][$language_id];
      $cd_meta_titles_array[$language_id] = $_POST['cd_meta_title'][$language_id];
      $cd_meta_keywords_array[$language_id] = $_POST['cd_meta_keywords'][$language_id];
      $cd_meta_descriptions_array[$language_id] = $_POST['cd_meta_description'][$language_id];
      $cd_descriptions_array[$language_id] = $_POST['cd_description'][$language_id];
    }
    // $_POST['category_parent_id_level'] has two parameters - id and level
    // first one is the id, second is the level
    $category_parent_id_level = explode(".", $_POST['category_parent_id_level']);
    $category_parent_id = $category_parent_id_level[0];
    $category_hierarchy_level = $category_parent_id_level[1]+1;
    $current_category_parent_id = $_POST['current_category_parent_id'];
    $current_category_hierarchy_level = $_POST['current_category_hierarchy_level'];
    $current_category_sort_order = $_POST['current_category_sort_order'];

    foreach($cd_pretty_urls_array as $language_id => $cd_pretty_url) {
      
      if(empty($cd_pretty_url)) {
        $category_errors['cd_pretty_url'][$language_id] = $languages[$current_lang]['required_field_error'];
      }
      else {
        //if the pretty url was changed - check if the new url is unique
        //and if not print an error
        if($cd_pretty_url != $current_cd_pretty_urls_array[$language_id]) {
          $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url,$current_category_id);
          if(!$is_pretty_url_unique) {
            $category_errors['cd_pretty_url_is_not_unique'][$language_id] = $languages[$current_lang]['cd_pretty_url_is_not_unique_error'];
          }
        }
      }
    }

    $category_is_section_header = 0;
    $category_show_in_menu = 0;
    $category_is_active = 0;
      if(isset($_POST['category_is_section_header'])) $category_is_section_header = 1;
      if(isset($_POST['category_show_in_menu'])) $category_show_in_menu = 1;
      if(isset($_POST['category_is_active'])) $category_is_active = 1;
    $category_attribute_1 = $_POST['category_attribute_1'];
    $category_attribute_2 = $_POST['category_attribute_2'];
    $cd_meta_title = $_POST['cd_meta_title'];
    $cd_meta_keywords = $_POST['cd_meta_keywords'];
    $cd_meta_description = $_POST['cd_meta_description'];
    
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $upload_path = "";
    $category_image_name = "";
    
    if(isset($_FILES['category_image']) && $_FILES['category_image']['error'] != 4) {
      $extension_array = explode("/", $_FILES['category_image']['type']);
      $extension = $extension_array[1];
      if(!in_array($extension, $valid_formats)) {
        $category_errors['category_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
      }
          
      if((isset($_FILES['category_image'])) && ($_FILES['category_image']['size'] < MAX_FILE_SIZE) && ($_FILES['category_image']['error'] == 0)) {
        // no error
        $category_image_tmp_name  = $_FILES['category_image']['tmp_name'];
        $category_image_name = $_FILES['category_image']['name'];
        $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/category-thumbs/";
        
        $category_image_name_exploded = explode(".", $category_image_name);
        $image_name = $category_image_name_exploded[0];
        $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      }
      elseif((isset($_FILES['category_image'])) && ($_FILES['category_image']['size'] > MAX_FILE_SIZE) || ($_FILES['category_image']['error'] == 1 || $_FILES['category_image']['error'] == 2)) {
        $category_errors['category_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
      }
      else {
        if($_FILES['category_image']['error'] != 4) { // error 4 means no file was uploaded
          $category_errors['category_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
        }
      }
    }
    
    /*
     * we have to check if the category has new parent
     * i.e. $current_category_parent_id(from hidden input) is not equal to $category_parent_id(from select parent option)
     * if the parent is changed, not counting the case when setting the category from not having a parent to having one
     * wich means $current_category_parent_id == 0 and $category_parent_id != 0
     * in case the user has choosen new parent for the category
     * we need to update the new category's column `category_has_children` to 1, wich means it has children
     * we also need to update the category's `category_hierarchy_ids` and `category_sort_order` columns
    */
    
    $category_hierarchy_ids_list = "";
    if($current_category_parent_id != $category_parent_id) {

      if($category_parent_id == 0) {
        $category_hierarchy_ids_list = $current_category_id;
      }
      else {
        
        $query_update_parent = "UPDATE `categories` SET `category_has_children` = '1' WHERE `category_id` = '$category_parent_id'";
        $all_queries .= $query_update_parent."<br>";
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $category_hierarchy_ids = get_categories_hierarchy_ids($category_parent_id);
        $category_hierarchy_ids_list .= "$category_hierarchy_ids.$current_category_id";
      }
      
      $category_sort_order = get_category_lаst_child_order_value($category_parent_id);
      $category_sort_order = ($category_sort_order == 0) ? 1 : $category_sort_order+1;
    }

    $user_id = $_SESSION['admin']['user_id'];

    if(empty($category_errors)) {
      //if there are no form errors we can insert the information

      $category_image_name_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $category_image_name));

      //category_image_path
      $query_update_category = "UPDATE `categories` SET ";
      if($current_category_parent_id != $category_parent_id) {
              $query_update_category .= "`category_parent_id`='$category_parent_id',
                                        `category_hierarchy_ids`='$category_hierarchy_ids_list',
                                        `category_hierarchy_level`='$category_hierarchy_level',
                                        `category_sort_order`='$category_sort_order',";
      }
      if(isset($_FILES['category_image']) && $_FILES['category_image']['error'] != 4) {
              $query_update_category .= "`category_image_path`=$category_image_name_db,";
      }
              $query_update_category .= "`category_is_section_header`='$category_is_section_header',
                                        `category_show_in_menu`='$category_show_in_menu',
                                        `category_is_active`='$category_is_active',
                                        `category_attribute_1`='$category_attribute_1',
                                        `category_attribute_2`='$category_attribute_2',
                                        `category_modified_by`='$user_id',
                                        `category_date_modified`=NOW()
                                  WHERE `category_id` = '$current_category_id'";
      $all_queries .= "<br>".$query_update_category;
      $result_update_category = mysqli_query($db_link, $query_update_category);
      if(!$result_update_category) {
        echo $languages[$current_lang]['sql_error_insert']." - 3 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      /*
       * we need to check if the old parent has any children left, and if not - setting it's `category_has_children` parameter to 0
       */
      if($current_category_parent_id != 0 && $current_category_parent_id != $category_parent_id) {
        $query_categories_siblings = "SELECT `category_id` FROM `categories` WHERE `category_parent_id` = '$current_category_parent_id'";
        $all_queries .= $query_categories_siblings."<br>";
        $result_categories_siblings = mysqli_query($db_link, $query_categories_siblings);
        if(!$result_categories_siblings) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_categories_siblings) <= 0) {

          $query_update_parent = "UPDATE `categories` SET `category_has_children` = '0' WHERE `category_id` = '$current_category_parent_id'";
          $all_queries .= $query_update_parent."<br>";
          $result_update_parent = mysqli_query($db_link, $query_update_parent);
          if(!$result_update_parent) {
            echo $languages[$current_lang]['sql_error_update']." - 4 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          mysqli_free_result($result_categories_siblings);
        }
      }

      //if the category has new parent we have to update it's children's `category_hierarchy_ids` (if any)
      if($current_category_parent_id != $category_parent_id) {
        update_category_children_hierarchy_ids($current_category_id, $category_hierarchy_ids_list, $category_hierarchy_level);
      }
      
      /*
       * if the category has new parent we have to reorder the category's old siblings, if any at all,
       * that have higher `category_sort_order` value and move them with one forward
       */
      if($current_category_parent_id != $category_parent_id) {
        $query_categories_for_reorder = "SELECT `category_id` FROM `categories` 
                                        WHERE `category_parent_id` = '$current_category_parent_id' AND `category_hierarchy_level` = '$current_category_hierarchy_level' 
                                          AND `category_sort_order` > '$current_category_sort_order'";
        $all_queries .= $query_categories_for_reorder."<br>";
        $result_categories_for_reorder = mysqli_query($db_link, $query_categories_for_reorder);
        if(!$result_categories_for_reorder) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_categories_for_reorder) > 0) {
          while($row_categories_for_reorder = mysqli_fetch_assoc($result_categories_for_reorder)) {
            $row_category_id = $row_categories_for_reorder['category_id'];

            $query_update_category = "UPDATE `categories` SET  `category_sort_order`= `category_sort_order` - 1 WHERE `category_id` = '$row_category_id'";
            $all_queries .= $query_update_category."<br>";
            $result_update_category = mysqli_query($db_link, $query_update_category);
            if(!$result_update_category) {
              echo $languages[$current_lang]['sql_error_update']." - 5 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }
          mysqli_free_result($result_categories_for_reorder);
        }
      }
      
      foreach($cd_names_array as $language_id => $cd_name) {
        
        $query_check_for_record = "SELECT `category_id` FROM `category_descriptions` WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
        $all_queries .= "<br>\n".$query_check_for_record;
        $result_check_for_record = mysqli_query($db_link, $query_check_for_record);
        if(!$result_check_for_record) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_check_for_record) > 0) {
          
          $cd_name = mysqli_real_escape_string($db_link, $cd_name);
          $cd_pretty_url = $cd_pretty_urls_array[$language_id];
          $current_cd_pretty_url = $current_cd_pretty_urls_array[$language_id];

          if($current_category_parent_id != $category_parent_id) {
            if($category_parent_id == 0) {
              $cd_hierarchy_path = $cd_pretty_url;
            }
            else {
              $cd_hierarchy_path = get_categories_hierarchy_path($category_parent_id,$language_id)."/$cd_pretty_url";
            }
            update_category_children_hierarchy_paths($current_category_id, $cd_hierarchy_path, $language_id);
          }
          else {
            /*
            * if the category doesn't have new parent set, but the `cd_pretty_url` was changed
            * we need to update it's `category_hierarchy_path` and check if it has children and if so update it's children's `cd_hierarchy_path`
            */
            if($current_cd_pretty_url != $cd_pretty_url) {
              if($category_parent_id == 0) {
                $cd_hierarchy_path = $cd_pretty_url;
              }
              else {
                $cd_hierarchy_path = get_categories_hierarchy_path($category_parent_id,$language_id)."/$cd_pretty_url";
              }
              update_category_children_hierarchy_paths($current_category_id, $cd_hierarchy_path, $language_id);
            }
          }

          $cd_pretty_url = mysqli_real_escape_string($db_link, $cd_pretty_urls_array[$language_id]);
          $current_cd_pretty_url = mysqli_real_escape_string($db_link, $current_cd_pretty_urls_array[$language_id]);
          $cd_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_descriptions_array[$language_id]));
          $cd_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_titles_array[$language_id]));
          $cd_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_descriptions_array[$language_id]));
          $cd_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $cd_meta_keywords_array[$language_id]));

          $query_update_cd_description = "UPDATE `category_descriptions` SET `cd_name` = '$cd_name',";
          if($current_cd_pretty_url != $cd_pretty_url) {
                                            $query_update_cd_description .= "`cd_pretty_url` = '$cd_pretty_url',";
          }
          if(($current_category_parent_id != $category_parent_id) || ($current_cd_pretty_url != $cd_pretty_url)) {
                                            $query_update_cd_description .= "`cd_hierarchy_path`='$cd_hierarchy_path',";
          }
                                            $query_update_cd_description .= "`cd_description` = $cd_description, 
                                                                            `cd_meta_title` = $cd_meta_title,
                                                                            `cd_meta_description` = $cd_meta_description, 
                                                                            `cd_meta_keywords` = $cd_meta_keywords
                                        WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
          //echo $query_update_cd_description;
          $all_queries .= "<br>".$query_update_cd_description;
          $result_update_cd_description = mysqli_query($db_link, $query_update_cd_description);
          if(!$result_update_cd_description) {
            echo $languages[$current_lang]['sql_error_insert']." - 6 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          
        } //if(mysqli_num_rows($result_check_for_record) > 0)
        else {
          $cd_name = mysqli_real_escape_string($db_link, $cd_name);
          $cd_pretty_url = mysqli_real_escape_string($db_link, $_POST['cd_pretty_url'][$language_id]);
          
          if($category_parent_id != 0) {
            $cd_hierarchy_path = get_categories_hierarchy_path($category_parent_id,$language_id);
            $cd_hierarchy_path = mysqli_real_escape_string($db_link, "$cd_hierarchy_path/$cd_pretty_url");
          }
          else {
            $cd_hierarchy_path = mysqli_real_escape_string($db_link, $cd_pretty_url);
          }
          
          $cd_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['cd_meta_title'][$language_id]));
          $cd_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['cd_meta_keywords'][$language_id]));
          $cd_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['cd_meta_description'][$language_id]));
          $cd_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['cd_description'][$language_id]));

      
          $query_insert_cd_description = "INSERT INTO `category_descriptions`(`category_id`, 
                                                                            `language_id`, 
                                                                            `cd_name`, 
                                                                            `cd_pretty_url`, 
                                                                            `cd_hierarchy_path`, 
                                                                            `cd_description`, 
                                                                            `cd_meta_title`,  
                                                                            `cd_meta_description`,  
                                                                            `cd_meta_keywords`) 
                                                                    VALUES ('$current_category_id',
                                                                            '$language_id',
                                                                            '$cd_name',
                                                                            '$cd_pretty_url',
                                                                            '$cd_hierarchy_path',
                                                                            $cd_description,
                                                                            $cd_meta_title,
                                                                            $cd_meta_description,
                                                                            $cd_meta_keywords)";
          //echo $query_insert_cd_description;
          $all_queries .= "<br>".$query_insert_cd_description;
          $result_insert_cd_description = mysqli_query($db_link, $query_insert_cd_description);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 3 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        
      }

      //handling the category picture
      if((isset($_FILES['category_image'])) && ($_FILES['category_image']['size'] < MAX_FILE_SIZE) && ($_FILES['category_image']['error'] == 0)) {
        
        if(is_uploaded_file($category_image_tmp_name)) {
          move_uploaded_file($category_image_tmp_name, $upload_path.$category_image_name);
    
          $file = $upload_path.$category_image_name;
          
          $image = new SimpleImage(); 
          $image->load($file);
      
          $image_cat_thumb_name = $image_name."_cat_thumb.".$image_exstension;
          $image_cat_thumb = $upload_path.$image_cat_thumb_name;

          switch($image_exstension) {
            case "gif" : $image_type = 1;
              break;
            case "jpg" : $image_type = 2;
              break;
            case "jpeg" : $image_type = 2;
              break;
            case "png" : $image_type = 3;
              break;
          }
          $image->resizeToWidth(50);

          $image->save($image_cat_thumb,$image_type);
        }
        else {
          echo $languages[$current_lang]['sql_error_insert']." - 7 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      //handling the category picture
    
//      echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: categories.php');
    }//if(empty($category_errors))
    
  }//if(isset($_POST['submit_category']))
  else {
    $query_category = "SELECT `categories`.*, CONCAT(`users`.`user_firstname`, ' ', `users`.`user_lastname`) as userfullname
                      FROM `categories`
                      LEFT JOIN `users` ON `users`.`user_id` = `categories`.`category_modified_by`
                      WHERE `categories`.`category_id` = '$current_category_id'";
    //echo $query_category;
    $result_category = mysqli_query($db_link, $query_category);
    if(!$result_category) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_category) > 0) {
      $category_array = mysqli_fetch_assoc($result_category);
      //echo"<pre>";print_r($category_array);
      $category_parent_id = $category_array['category_parent_id'];
      $category_hierarchy_level = $category_array['category_hierarchy_level'];
      $category_show_in_menu = $category_array['category_show_in_menu'];
      $category_sort_order = $category_array['category_sort_order'];
      $category_image_name = $category_array['category_image_path'];
//      $category_image_name_exploded = explode(".", $category_image_name);
//      $image_name = $category_image_name_exploded[0];
//      $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      $image_path = "/site/images/category-thumbs/";
      $category_image_path = (!empty($category_image_name)) ? $image_path.$category_image_name : "";
      $category_is_section_header = $category_array['category_is_section_header'];
      $category_is_active = $category_array['category_is_active'];
      $category_attribute_1 = $category_array['category_attribute_1'];
      $category_attribute_2 = $category_array['category_attribute_2'];
      $category_last_modified_by = $category_array['userfullname']; // user_id
      $category_modified_date = $category_array['category_date_modified'];
    }
  }
  
  $page_title = $languages[$current_lang]['category_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/catalog/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/catalog/categories.php" title="<?=$languages[$current_lang]['title_breadcrumbs_categories'];?>"><?=$languages[$current_lang]['header_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_category_add_new'];?>
      </div>
      
<?php if(isset($category_errors) && !empty($category_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
<!--      <div class="warning"></div>
      <div class="success"></div>-->
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_category_add_new'];?></h1>
      
      <ul class="category_tabs tabs">
        <li><a href="#category_main_tab"><?=$languages[$current_lang]['header_category_main_tab'];?></a></li>
        <li><a href="#category_options_tab"><?=$languages[$current_lang]['header_category_options_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
      <form method="post" name="add_category" id="add_category" class="input_form" action="<?=$_SERVER['PHP_SELF']."?category_id=".$current_category_id;?>" enctype="multipart/form-data">
        <div>
          <button type="submit" name="submit_category" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <p class="clearfix"></p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div id="category_main_tab" class="category_tab tab">

          <ul id="languages" class="language_tabs tabs">
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
              <li><a href="#<?=$language_code;?>"><img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?></a></li>
<?php
    }
  }
?>
          </ul>
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
              
              $query_category_descriptions = "SELECT `category_descriptions`.*
                                                    FROM `category_descriptions`
                                                    WHERE `category_id` = '$current_category_id' AND `language_id` = '$language_id'";
              //echo $query_category_descriptions;
              $result_category_descriptions = mysqli_query($db_link, $query_category_descriptions);
              if(!$result_category_descriptions) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_category_descriptions) > 0) {
                $category_descriptions_array = mysqli_fetch_assoc($result_category_descriptions);
                //echo"<pre>";print_r($category_array);
                $cd_names_array[$language_id] = $category_descriptions_array['cd_name'];
                $cd_pretty_urls_array[$language_id] = $category_descriptions_array['cd_pretty_url'];
                $current_cd_pretty_urls[$language_id] = $category_descriptions_array['cd_pretty_url'];
                $cd_descriptions_array[$language_id] = $category_descriptions_array['cd_description'];
                $cd_meta_titles_array[$language_id] = $category_descriptions_array['cd_meta_title'];
                $cd_meta_descriptions_array[$language_id] = $category_descriptions_array['cd_meta_description'];
                $cd_meta_keywords_array[$language_id] = $category_descriptions_array['cd_meta_keywords'];
              }
              
?>
          <div id="<?=$language_code;?>" class="language_tab tab">
            <div>
              <label for="category_name" class="title"><?=$languages[$current_lang]['header_category_name'];?><span class="red">*</span></label>
              <?php
                if(isset($category_errors['cd_name'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_name[<?=$language_id;?>]" class="cd_name" style="width: 400px;" value="<?php if(isset($cd_names_array[$language_id])) echo $cd_names_array[$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="category_pretty_url" class="title"><?=$languages[$current_lang]['header_category_pretty_url'];?></label>
              <?php
                if(isset($category_errors['cd_pretty_url'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_pretty_url'][$language_id]."</div>";
                }
                if(isset($category_errors['cd_pretty_url_is_not_unique'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_pretty_url_is_not_unique'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_pretty_url[<?=$language_id;?>]" class="cd_pretty_url" style="width: 400px;" value="<?php if(isset($cd_pretty_urls_array[$language_id])) echo $cd_pretty_urls_array[$language_id];?>" />
              <input type="hidden" name="current_cd_pretty_url[<?=$language_id;?>]" value="<?php if(isset($current_cd_pretty_urls[$language_id])) echo $current_cd_pretty_urls[$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_pretty_url'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_title" class="title"><?=$languages[$current_lang]['header_category_meta_title'];?></label>
              <input type="text" name="cd_meta_title[<?=$language_id;?>]" id="cd_meta_title" onkeyup="CountCharacters(this,'55')" style="width: 60%;" value="<?php if(isset($cd_meta_titles_array[$language_id])) echo $cd_meta_titles_array[$language_id];?>" />
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_keywords" class="title"><?=$languages[$current_lang]['header_category_meta_keywords'];?></label>
              <input type="text" name="cd_meta_keywords[<?=$language_id;?>]" id="cd_meta_keywords" style="width: 60%;" value="<?php if(isset($cd_meta_keywords_array[$language_id])) echo $cd_meta_keywords_array[$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_description" class="title"><?=$languages[$current_lang]['header_category_meta_description'];?></label>
              <textarea name="cd_meta_description[<?=$language_id;?>]" id="cd_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"/><?php if(isset($cd_meta_descriptions_array[$language_id])) echo $cd_meta_descriptions_array[$language_id];?></textarea>
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="category_description" class="title"><?=$languages[$current_lang]['header_category_description'];?></label>
              <?php
                if(isset($category_errors['category_meta_keywords'])) {
                  echo "<div class='error'>".$category_errors['category_meta_keywords']."</div>";
                }
              ?>
              <textarea name="cd_description[<?=$language_id;?>]" id="ckeditor_<?=$language_code;?>" class="default_text"><?php if(isset($cd_descriptions_array[$language_id])) echo $cd_descriptions_array[$language_id];?></textarea>
            </div>
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>
          </div>
<?php
    }
  }
?>
        </div>
        
        <div id="category_options_tab" class="category_tab tab">

          <div>
            <label for="category_parent" class="title"><?=$languages[$current_lang]['header_category_parent'];?></label>
            <input type="hidden" name="current_category_parent_id" value="<?=$category_parent_id;?>" />
            <input type="hidden" name="current_category_hierarchy_level" value="<?=$category_hierarchy_level;?>" />
            <input type="hidden" name="current_category_sort_order" value="<?=$category_sort_order;?>" />
            <select name="category_parent_id_level" class="category_parent_id_level" style="width: 600px;">
              <option value="0.0" level="0"><?=$languages[$current_lang]['option_no_category_parent'];?></option>
              <?php list_categories_for_select($parent_id = 0, $path_number = 0, $category_parent_id = $category_parent_id, $current_category_id_fn = 0); ?> 
            </select>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_is_section_header" class="title"><?=$languages[$current_lang]['header_category_is_section_header'];?></label>
            <?php
              if(isset($category_is_section_header) && $category_is_section_header == 0) echo '<input type="checkbox" name="category_is_section_header" id="category_is_section_header" />';
              else echo '<input type="checkbox" name="category_is_section_header" id="category_is_section_header" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_show_in_menu" class="title"><?=$languages[$current_lang]['header_category_show_in_menu'];?></label>
            <?php
              if(isset($category_show_in_menu) && $category_show_in_menu == 0) echo '<input type="checkbox" name="category_show_in_menu" id="category_show_in_menu" />';
              else echo '<input type="checkbox" name="category_show_in_menu" id="category_show_in_menu" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_is_active" class="title"><?=$languages[$current_lang]['header_category_is_active'];?></label>
            <?php
              if(isset($category_is_active) && $category_is_active == 0) echo '<input type="checkbox" name="category_is_active" id="category_is_active" />';
              else echo '<input type="checkbox" name="category_is_active" id="category_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_attribute_1" class="title"><?=$languages[$current_lang]['header_cat_extra_attribute_1'];?></label>
            <input type="text" name="category_attribute_1" id="category_attribute_1" style="width: 500px;" value="<?php if(isset($category_attribute_1)) echo $category_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_attribute_2" class="title"><?=$languages[$current_lang]['header_cat_extra_attribute_2'];?></label>
            <input type="text" name="category_attribute_2" id="category_attribute_2" style="width: 500px;" value="<?php if(isset($category_attribute_2)) echo $category_attribute_2;?>" />
          </div>
          
          <div class="category_image">
            <label for="category_image" class="title"><?=$languages[$current_lang]['header_category_image'];?></label>
            <?php
              $class_hidden_input = "";
              if(isset($category_image_path) && !empty($category_image_path)) {
                $class_hidden_input = "hidden";
                $confirm_text = $languages[$current_lang]['delete_category_image_warning'];
            ?>
              <div class="category_image_div">
                <a class="delete_image button red" onclick="if(confirm('<?=$confirm_text;?>')) DeleteCategoryImage('<?=$current_category_id;?>')">
                  <?=$languages[$current_lang]['btn_delete'];?>
                </a>
                <img src="<?=$category_image_path?>">
              </div>
            <?php
              }
              if(isset($category_errors['category_image'])) {
                echo "<div class='error'>".$category_errors['category_image']."</div>";
              }
            ?>
            <input type="file" name="category_image" class="category_image_file <?=$class_hidden_input;?>" style="width: auto;" />
            <input type="hidden" name="category_image_path" id="category_image_path" value="<?=$category_image_path;?>" />
          </div>
          
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        
        </div>

        <div>
          <button type="submit" name="submit_category" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="http://www.procad-bg.com/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {

          $language_code = $row_languages['language_code'];
?>
          CKEDITOR.replace('ckeditor_<?=$language_code;?>');
<?php
        }
      }
?>
      // category tab switcher
      $(".category_tabs li").removeClass("active");
      $(".category_tab").hide();
      $(".category_tabs li:first").addClass("active");
      $(".category_tab:first").show();
      $(".category_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".category_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".category_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end category tab switcher
      
      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>
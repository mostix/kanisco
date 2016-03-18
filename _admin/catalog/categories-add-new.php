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
  
  $category_parent_id = 0;

  if(isset($_POST['submit_category'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);
//    $extension_array = explode("/", $_FILES['category_image']['type']);
//    $extension = $extension_array[1];
//    echo $extension;exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $category_errors = array();
    $all_queries = "";
      
    foreach($_POST['cd_name'] as $language_id => $cd_name) {
      if(empty($cd_name)) $category_errors['cd_name'][$language_id] = $languages[$current_lang]['required_field_error'];
    }
    // $_POST['category_parent_id_level'] has two parameters - id and level
    // first one is the id, second is the level
    $category_parent_id_level = explode(".", $_POST['category_parent_id_level']);
    $category_parent_id = $category_parent_id_level[0];
    $category_hierarchy_level = $category_parent_id_level[1]+1;
    $cd_hierarchy_path_array = array();

    foreach($_POST['cd_pretty_url'] as $language_id => $cd_pretty_url) {
      
      if(empty($cd_pretty_url)) {
        $cd_pretty_url = str_replace(" ", "-", mb_convert_case($_POST['cd_name'][$language_id], MB_CASE_LOWER, "UTF-8"));
        $cd_pretty_url = str_replace("'", "", mb_convert_case($cd_pretty_url, MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url,$current_category_id = 0);
        if(!$is_pretty_url_unique) {
          $cd_pretty_url = $cd_pretty_url."-1";
          $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url,$current_category_id = 0);
          if(!$is_pretty_url_unique) {
            $cd_pretty_url = $cd_pretty_url."-1";
          }
        }
      }
      else {
        $cd_pretty_url = str_replace(" ", "-", mb_convert_case($cd_pretty_url, MB_CASE_LOWER, "UTF-8"));
        $cd_pretty_url = str_replace("'", "", mb_convert_case($cd_pretty_url, MB_CASE_LOWER, "UTF-8"));
        $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url);
        if(!$is_pretty_url_unique) {
          $cd_pretty_url = $cd_pretty_url."-1";
          $is_pretty_url_unique = check_if_cd_pretty_url_is_unique($cd_pretty_url);
          if(!$is_pretty_url_unique) {
            $cd_pretty_url = $cd_pretty_url."-1";
          }
        }
      }
      
      $_POST['cd_pretty_url'][$language_id] = $cd_pretty_url;
      
      if($category_parent_id != 0) {
        $cd_hierarchy_path = get_categories_hierarchy_path($category_parent_id,$language_id);
        $cd_hierarchy_path_array[$language_id] = "$cd_hierarchy_path/$cd_pretty_url";
      }
      else {
        $cd_hierarchy_path_array[$language_id] = $cd_pretty_url;
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
        $category_errors['category_image'] = "Не е позлволено качването на снимка с разширение $extension<br>";
      }
          
      if((isset($_FILES['category_image'])) && ($_FILES['category_image']['size'] < MAX_FILE_SIZE) && ($_FILES['category_image']['error'] == 0)) {
        // no error
        $category_image_tmp_name  = $_FILES['category_image']['tmp_name'];
        $category_image_name = $_FILES['category_image']['name'];
        $upload_path = $_SERVER['DOCUMENT_ROOT']."/frontstore/images/category-thumbs/";
      
        $category_image_name_exploded = explode(".", $category_image_name);
        $image_name = $category_image_name_exploded[0];
        $image_exstension = mb_convert_case($category_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      }
      elseif((isset($_FILES['category_image'])) && ($_FILES['category_image']['size'] > MAX_FILE_SIZE) || ($_FILES['category_image']['error'] == 1 || $_FILES['category_image']['error'] == 2)) {
        $category_errors['category_image'] .= "You have exceeded the size limit! Please choose a default picture smaller then 4MB<br>";
      }
      else {
        if($_FILES['category_image']['error'] != 4) { // error 4 means no file was uploaded
          $category_errors['category_image'] .= "An error occured while uploading the file<br>";
        }
      }
    }

    if($category_parent_id != 0) {

      //update the parent column `category_has_children` to 1, wich means it has children
      //no matter if it was set to 1 or 0
      $query_update_parent = "UPDATE `categories` SET `category_has_children` = '1' WHERE `category_id` = '$category_parent_id'";
      $all_queries .= $query_update_parent;
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages[$current_lang]['sql_error_update']." - 1 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $category_sort_order = get_category_lаst_child_order_value($category_parent_id);
      $category_sort_order = ($category_sort_order == 0) ? 1 : $category_sort_order+1;
    }
    else {
      $category_sort_order = get_category_lаst_child_order_value($category_parent_id);
      $category_sort_order = ($category_sort_order == 0) ? 1 : $category_sort_order+1;
    }

    $user_id = $_SESSION['admin']['user_id'];

    if(empty($category_errors)) {
      //if there are no form errors we can insert the information

      $category_image_name_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $category_image_name));
      $category_hierarchy_ids = 0;
      $category_has_children = 0;
      $category_is_collapsed = 1;

      $query_insert_category = "INSERT INTO `categories`(`category_id`, 
                                                        `category_parent_id`, 
                                                        `category_hierarchy_ids`, 
                                                        `category_hierarchy_level`, 
                                                        `category_sort_order`, 
                                                        `category_has_children`, 
                                                        `category_image_path`, 
                                                        `category_is_section_header`, 
                                                        `category_show_in_menu`, 
                                                        `category_is_active`, 
                                                        `category_is_collapsed`, 
                                                        `category_attribute_1`, 
                                                        `category_attribute_2`, 
                                                        `category_modified_by`, 
                                                        `category_date_added`, 
                                                        `category_date_modified`) 
                                                VALUES ('',
                                                        '$category_parent_id',
                                                        '$category_hierarchy_ids',
                                                        '$category_hierarchy_level',
                                                        '$category_sort_order',
                                                        '$category_has_children',
                                                        $category_image_name_db,
                                                        '$category_is_section_header',
                                                        '$category_show_in_menu',
                                                        '$category_is_active',
                                                        '$category_is_collapsed',
                                                        '$category_attribute_1',
                                                        '$category_attribute_2',
                                                        '$user_id',
                                                        NOW(),
                                                        NOW())";
      $all_queries .= "<br>".$query_insert_category;
      $result_insert_category = mysqli_query($db_link, $query_insert_category);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - 2 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $category_id = mysqli_insert_id($db_link);
      
      foreach($_POST['cd_name'] as $language_id => $cd_name) {
        
        $cd_name = mysqli_real_escape_string($db_link, $cd_name);
        $cd_pretty_url = mysqli_real_escape_string($db_link, $_POST['cd_pretty_url'][$language_id]);
        $cd_hierarchy_path = mysqli_real_escape_string($db_link, $cd_hierarchy_path_array[$language_id]);
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
                                                                  VALUES ('$category_id',
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
          echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      //handling the category picture

      //update the category's `category_hierarchy_ids` after insertion
      $category_hierarchy_ids_list = "";
      if($category_parent_id != 0) {
        $category_hierarchy_ids = get_categories_hierarchy_ids($category_parent_id);
        $category_hierarchy_ids_list .= "$category_hierarchy_ids.$category_id";
      }
      else {
        $category_hierarchy_ids_list = $category_id;
      }

      $query_update_parent = "UPDATE `categories` 
                              SET `category_hierarchy_ids` = '$category_hierarchy_ids_list' 
                              WHERE `category_id` = '$category_id'";
      $all_queries .= "<br>".$query_update_parent;
      $result_update_parent = mysqli_query($db_link, $query_update_parent);
      if(!$result_update_parent) {
        echo $languages[$current_lang]['sql_error_update']." - 6 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      //update the category's `category_hierarchy_ids` and `cd_hierarchy_path` after insertion
    
//      echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: categories.php');
    }//if(empty($category_errors))
//    print_r($category_errors);
    
  }//if(isset($_POST['submit_category']))
  
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
      
      <form method="post" name="add_category" id="add_category" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
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
              <li>
                <a href="#<?=$language_code;?>"><img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?></a>
              </li>
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
?>
          <div id="<?=$language_code;?>" class="language_tab tab">
            <div>
              <label for="category_name" class="title"><?=$languages[$current_lang]['header_category_name'];?><span class="red">*</span></label>
              <?php
                if(isset($category_errors['cd_name'][$language_id])) {
                  echo "<div class='error'>".$category_errors['cd_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="cd_name[<?=$language_id;?>]" class="cd_name" style="width: 400px;" value="<?php if(isset($_POST['cd_name'][$language_id])) echo $_POST['cd_name'][$language_id];?>" />
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="category_pretty_url" class="title"><?=$languages[$current_lang]['header_category_pretty_url'];?></label>
              <input type="text" name="cd_pretty_url[<?=$language_id;?>]" class="cd_pretty_url" style="width: 400px;" value="<?php if(isset($_POST['cd_pretty_url'][$language_id])) echo $_POST['cd_pretty_url'][$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_pretty_url'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_title" class="title"><?=$languages[$current_lang]['header_category_meta_title'];?></label>
              <input type="text" name="cd_meta_title[<?=$language_id;?>]" id="cd_meta_title" onkeyup="CountCharacters(this,'100')" style="width: 60%;" value="<?php if(isset($_POST['cd_meta_title'][$language_id])) echo $_POST['cd_meta_title'][$language_id];?>" />
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_keywords" class="title"><?=$languages[$current_lang]['header_category_meta_keywords'];?></label>
              <input type="text" name="cd_meta_keywords[<?=$language_id;?>]" id="cd_meta_keywords" style="width: 60%;" value="<?php if(isset($_POST['cd_meta_keywords'][$language_id])) echo $_POST['cd_meta_keywords'][$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="category_meta_description" class="title"><?=$languages[$current_lang]['header_category_meta_description'];?></label>
              <textarea name="cd_meta_description[<?=$language_id;?>]" id="cd_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"/><?php if(isset($_POST['cd_meta_description'][$language_id])) echo $_POST['cd_meta_description'][$language_id];?></textarea>
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['category_meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_category_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="category_description" class="title"><?=$languages[$current_lang]['header_category_description'];?></label>
              <textarea name="cd_description[<?=$language_id;?>]" id="ckeditor_<?=$language_code;?>" class="default_text"><?php if(isset($_POST['cd_description'][$language_id])) echo $_POST['cd_description'][$language_id];?></textarea>
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
            <select name="category_parent_id_level" class="category_parent_id_level" style="width: 600px;">
              <option value="0.0" level="0"><?=$languages[$current_lang]['option_no_category_parent'];?></option>
              <?php list_categories_for_select($parent_id = 0, $path_number = 0, $category_parent_id = $category_parent_id, $current_category_id = 0); ?> 
            </select>
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_is_section_header" class="title"><?=$languages[$current_lang]['header_category_is_section_header'];?></label>
            <?php
              if(isset($category_is_section_header)) {
                if($category_is_section_header == 0) echo '<input type="checkbox" name="category_is_section_header" id="category_is_section_header" />';
                else echo '<input type="checkbox" name="category_is_section_header" id="category_is_section_header" checked="checked" />';
              }
              else echo '<input type="checkbox" name="category_is_section_header" id="category_is_section_header" />';
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
            <label for="category_attribute_1" class="title"><?=$languages[$current_lang]['header_extra_attribute_1'];?></label>
            <input type="text" name="category_attribute_1" id="category_attribute_1" style="width: 500px;" value="<?php if(isset($category_attribute_1)) echo $category_attribute_1;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="category_attribute_2" class="title"><?=$languages[$current_lang]['header_extra_attribute_2'];?></label>
            <input type="text" name="category_attribute_2" id="category_attribute_2" style="width: 500px;" value="<?php if(isset($category_attribute_2)) echo $category_attribute_2;?>" />
          </div>
          
          <div>
            <label for="category_image" class="title"><?=$languages[$current_lang]['header_category_image'];?></label>
            <?php
              if(isset($category_errors['category_image'])) {
                echo "<div class='error'>".$category_errors['category_image']."</div>";
              }
            ?>
            <input type="file" name="category_image" class="category_image" style="width: auto;" />
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
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
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
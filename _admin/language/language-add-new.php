<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: languages.php');
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $language_code = mysqli_real_escape_string($db_link, $_POST['language_code']);
    // $_POST['language_default_content_id'] has two parameters - id and level
    // but here we need only the id, wich is the first one
    $language_default_content_id_level = explode(".", $_POST['language_default_content_id']);
    $language_default_content_id = $language_default_content_id_level[0];
    $language_name = mysqli_real_escape_string($db_link, $_POST['language_name']);
    $language_menu_name = mysqli_real_escape_string($db_link, $_POST['language_menu_name']);
    
    $language_menu_order = get_lаst_language_menu_order_value();
    $language_menu_order_db = ($language_menu_order == 0) ? 1 : $language_menu_order+1;
    
    // if $language_menu_order == 0, that means this is the first entry,
    // so make this language default
    $language_is_default = ($language_menu_order == 0) ? 1 : 0;
    $language_is_active = 0;
    if(isset($_POST['language_is_active'])) $language_is_active = $_POST['language_is_active'];
    
    $query_insert_language = "INSERT INTO `languages`(`language_id`, 
                                                    `language_code`,  
                                                    `language_root_content_id`, 
                                                    `language_default_content_id`, 
                                                    `language_name`, 
                                                    `language_menu_name`, 
                                                    `language_menu_order`, 
                                                    `language_is_default_frontend`, 
                                                    `language_is_default_backend`, 
                                                    `language_is_active`) 
                                            VALUES ('',
                                                    '$language_code',
                                                    '0',
                                                    '$language_default_content_id',
                                                    '$language_name',
                                                    '$language_menu_name',
                                                    '$language_menu_order_db',
                                                    '$language_is_default',
                                                    '$language_is_default',
                                                    '$language_is_active')";
    $all_queries .= "<br>".$query_insert_language;
    $result_insert_language = mysqli_query($db_link, $query_insert_language);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $language_id = mysqli_insert_id($db_link);
    
    $content_type_id = 6;
    $content_parent_id = 0; // we make a language content only as a parent, so it has no parent
    $content_hierarchy_ids = 0; // the content_hierarchy_ids will be only the language content_id, we gonna update it after insertion
    $content_hierarchy_level = 1;
    $content_has_children = 0;
    $content_is_default = 0;
    $content_is_home_page = 0;
    $content_show_in_menu = 1;
    $content_collapsed = 1;
    $content_is_active = 1;
    $content_pretty_url = str_replace(" ", "-", mb_convert_case($language_name, MB_CASE_LOWER, "UTF-8"));
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
    $content_hierarchy_path = $content_pretty_url;
    $content_menu_order = get_content_lаst_child_order_value($content_parent_id = 0);
    $content_menu_order = ($content_menu_order == 0) ? 1 : $content_menu_order+1;
    $user_id = $_SESSION['admin']['user_id'];
    
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
                                                    `content_collapsed`, 
                                                    `content_meta_title`, 
                                                    `content_meta_keywords`, 
                                                    `content_meta_description`, 
                                                    `content_text`, 
                                                    `content_pretty_url`, 
                                                    `content_menu_order`, 
                                                    `content_is_active`, 
                                                    `content_last_modified_by`, 
                                                    `content_created_date`, 
                                                    `content_modified_date`) 
                                            VALUES ('',
                                                    '$content_type_id',
                                                    '$content_parent_id',
                                                    '$content_hierarchy_ids',
                                                    '$content_hierarchy_level',
                                                    '$content_hierarchy_path',
                                                    '$content_has_children',
                                                    '$content_is_default',
                                                    '$content_is_home_page',
                                                    '$language_name',
                                                    '$language_menu_name',
                                                    '$content_show_in_menu',
                                                    '$content_collapsed',
                                                    NULL,
                                                    NULL,
                                                    NULL,
                                                    NULL,
                                                    '$content_pretty_url',
                                                    '$content_menu_order',
                                                    '$content_is_active',
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
    
    $query_update_language = "UPDATE `languages` SET `language_root_content_id`='$content_id' WHERE `language_id` = '$language_id'";
    $all_queries .= "<br>".$query_update_language;
    $result_update_language = mysqli_query($db_link, $query_update_language);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    //update the content's `content_hierarchy_ids` after insertion
    $query_update_parent = "UPDATE `contents` SET `content_hierarchy_ids` = '$content_id' WHERE `content_id` = '$content_id'";
    $all_queries .= "<br>".$query_update_parent;
    $result_update_parent = mysqli_query($db_link, $query_update_parent);
    if(!$result_update_parent) {
      echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    header('Location: languages.php');
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages[$current_lang]['language_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/language/languages.php" title="<?=$languages[$current_lang]['title_breadcrumbs_languages'];?>"><?=$languages[$current_lang]['header_languages'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_language_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_language_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>">
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_code'];?><span class="red">*</span></p>
          <input type="text" name="language_code" id="language_code" style="width: 100px;" /> &nbsp;&nbsp;&nbsp;
          <a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" title="<?=$languages[$current_lang]['title_check_iso_language_codes'];?>" target="_blank">
            <img src="/_admin/images/info.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_language_check_iso_codes'];?>" />
          </a>
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_name'];?><span class="red">*</span></p>
          <input type="text" name="language_name" id="language_name" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_menu_name'];?><span class="red">*</span></p>
          <input type="text" name="language_menu_name" id="language_menu_name" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_default_content'];?><span class="red">*</span></p>
          <select name="language_default_content_id" id="language_default_content_id" style="width: 600px;">
            <option value="0.0"><?=$languages[$current_lang]['option_no_default_content_for_language'];?></option>
            <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = 0, $current_content_id = 0); ?> 
          </select>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_is_active'];?></p>
          <input type="checkbox" name="language_is_active" id="language_is_active" value="1" checked="checked" />
        </div>
        <div class="clearfix"></div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="submit" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
  
?>
</body>
</html>
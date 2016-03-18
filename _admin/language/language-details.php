<?php

//  include_once '../../config.php';
//  include_once '../functions/include-functions.php';
//      
//  if(isset($_GET['language_id'])) {
//    $current_language_id = $_GET['language_id'];
//  }
  
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
    
    $language_is_active = 0;
    if(isset($_POST['language_is_active'])) $language_is_active = $_POST['language_is_active'];
    
    $query_update_language = "UPDATE `languages` SET `language_code`='$language_code',
                                                     `language_default_content_id`='$language_default_content_id',
                                                     `language_name`='$language_name',
                                                     `language_menu_name`='$language_menu_name',
                                                     `language_is_active`='$language_is_active' 
                                                  WHERE `language_id` = '$current_language_id'";
    $all_queries .= "<br>".$query_update_language;
    $result_update_language = mysqli_query($db_link, $query_update_language);
    if(mysqli_affected_rows($db_link) <= 0) {
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
  
  $query_language = "SELECT `language_id`, `language_code`, `language_default_content_id`, `language_name`, `language_menu_name`, 
                            `language_menu_order`, `language_is_active` 
                    FROM `languages` 
                    WHERE `language_id` = '$current_language_id'";
  //echo $query_language;
  $result_language = mysqli_query($db_link, $query_language);
  if(!$result_language) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language) > 0) {
    $language_array = mysqli_fetch_assoc($result_language);
    $language_code = stripslashes($language_array['language_code']);
    $language_default_content_id = $language_array['language_default_content_id'];
    $language_name = stripslashes($language_array['language_name']);
    $language_menu_name = stripslashes($language_array['language_menu_name']);
    $language_is_active = $language_array['language_is_active'];
  }
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/language/languages.php" title="<?=$languages[$current_lang]['title_breadcrumbs_languages'];?>"><?=$languages[$current_lang]['header_languages'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_language_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_language_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?language_id=".$current_language_id;?>">
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_code'];?><span class="red">*</span></p>
          <input type="text" name="language_code" id="language_code" value="<?=$language_code;?>" style="width: 100px;" /> &nbsp;&nbsp;&nbsp;
          <a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" title="<?=$languages[$current_lang]['title_check_iso_language_codes'];?>" target="_blank">
            <img src="/_admin/images/info.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_language_check_iso_codes'];?>" />
          </a>
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_name'];?><span class="red">*</span></p>
          <input type="text" name="language_name" id="language_name" value="<?=$language_name;?>" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_menu_name'];?><span class="red">*</span></p>
          <input type="text" name="language_menu_name" id="language_menu_name" value="<?=$language_menu_name;?>" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_default_content'];?><span class="red">*</span></p>
          <select name="language_default_content_id" id="language_default_content_id" style="width: 600px;">
            <option value="0.0"><?=$languages[$current_lang]['option_no_default_content_for_language'];?></option>
            <?php list_contents_for_select($parent_id = 0, $path_number = 0, $content_parent_id = $language_default_content_id, $current_content_id = 0); ?> 
          </select>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_language_is_active'];?></p>
          <input type="checkbox" name="language_is_active" id="language_is_active" value="<?=$language_is_active;?>" checked="checked" />
        </div>
        <div class="clearfix"></div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
        <?php if($users_rights_edit == 1) { ?>
          <button type="submit" name="submit" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
        <?php } else { ?>
          <a href="javascript:;"  onclick="alert('<?php echo $languages[$current_lang]['no_rights_for_edit'];?>')" class="save_product_tab button red">
            <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?>
          </a>
        <?php } ?>
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
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: news-categories.php');
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
  
  $news_category_image = "";
    
  if(isset($_POST['add_news_category'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['news_cat_names'] as $language_id => $news_cat_name) {
      if(empty($news_cat_name)) $news_category_errors['news_cat_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $news_cat_names[$language_id] = $_POST['news_cat_names'][$language_id];
    }
    
    $news_category_image = $_POST['news_category_image'];
    
    if(!isset($news_category_errors)) {
      //if there are no form errors we can insert the information
      
      // $_POST['news_cat_parent_params'] has three parameters - parent_id, hierarchy_ids and hierarchy_level
      $news_cat_parent_params = explode("+", $_POST['news_cat_parent_params']);
      $news_cat_parent_id = $news_cat_parent_params[0];
      $news_cat_hierarchy_ids = $news_cat_parent_params[1];
      $news_cat_hierarchy_level = $news_cat_parent_params[2]+1;
      $news_cat_has_children = 0;
      $news_cat_sort_order = get_lаst_news_category_order_value($news_cat_parent_id);
      $news_cat_sort_order_db = ($news_cat_sort_order == 0) ? 1 : $news_cat_sort_order+1;
      $news_category_image_db = prepare_for_null_row($news_category_image);
      $news_cat_is_collapsed = 0;
      $news_cat_created_user = $_SESSION['admin']['user_id'];

      $query_insert_news_category = "INSERT INTO `news_categories`(`news_category_id`, 
                                                                    `news_cat_parent_id`, 
                                                                    `news_cat_hierarchy_ids`, 
                                                                    `news_cat_hierarchy_level`, 
                                                                    `news_cat_has_children`, 
                                                                    `news_cat_sort_order`, 
                                                                    `news_category_image`, 
                                                                    `news_cat_is_collapsed`, 
                                                                    `news_cat_created_user`, 
                                                                    `news_cat_created_date`, 
                                                                    `news_cat_modified_date`)
                                                            VALUES ('',
                                                                    '$news_cat_parent_id',
                                                                    '$news_cat_hierarchy_ids',
                                                                    '$news_cat_hierarchy_level',
                                                                    '$news_cat_has_children',
                                                                    '$news_cat_sort_order_db',
                                                                    $news_category_image_db,
                                                                    '$news_cat_is_collapsed',
                                                                    '$news_cat_created_user',
                                                                    NOW(),
                                                                    NOW())";
      $all_queries .= "<br>".$query_insert_news_category;
      $result_insert_news_category = mysqli_query($db_link, $query_insert_news_category);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $news_category_id = mysqli_insert_id($db_link);
      $news_cat_hierarchy_ids_db = ($news_cat_hierarchy_ids == 0) ? $news_category_id : "$news_cat_hierarchy_ids.$news_category_id";
      $query_update_news_category = "UPDATE `news_categories` SET `news_cat_hierarchy_ids` = '$news_cat_hierarchy_ids_db' WHERE `news_category_id` = '$news_category_id'";
      $all_queries .= "<br>".$query_update_news_category;
      $result_update_news_category = mysqli_query($db_link, $query_update_news_category);
      if(!$result_update_news_category) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      if($news_cat_parent_id != 0) {
        $query_update_parent = "UPDATE `news_categories` SET `news_cat_has_children` = '1' WHERE `news_category_id` = '$news_cat_parent_id'";
        $all_queries .= "<br>".$query_update_parent;
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      foreach($news_cat_names as $language_id => $news_cat_name) {

        $news_cat_hierarchy_path = "";
        $news_cat_long_name = "";

        if($news_cat_parent_id != 0) {
          $query_select_parent_params = "SELECT `news_cat_hierarchy_path`, `news_cat_long_name` FROM `news_cat_desc` WHERE `news_category_id` = '$news_cat_parent_id'";
          $all_queries .= "<br>".$query_select_parent_params;
          $result_select_parent_params = mysqli_query($db_link, $query_select_parent_params);
          if(mysqli_num_rows($result_select_parent_params) > 0) {

            $parent_params = mysqli_fetch_assoc($result_select_parent_params);
            $news_cat_hierarchy_path .= $parent_params['news_cat_hierarchy_path'];
            $news_cat_long_name .= $parent_params['news_cat_long_name'];
          }
        }

        $news_cat_long_name_db =  (empty($news_cat_long_name)) ? $news_cat_name : "$news_cat_long_name | $news_cat_name";
        $news_cat_hierarchy_path_db =  (empty($news_cat_hierarchy_path)) ? str_replace(" ", "-", mb_convert_case($news_cat_name, MB_CASE_LOWER, "UTF-8")) : "$news_cat_hierarchy_path/". str_replace(" ", "-", mb_convert_case($news_cat_name, MB_CASE_LOWER, "UTF-8"));

        $query_insert_news_cat_desc = "INSERT INTO `news_cat_desc`(`news_category_id`, 
                                                                  `language_id`, 
                                                                  `news_cat_name`, 
                                                                  `news_cat_hierarchy_path`, 
                                                                  `news_cat_long_name`)
                                                          VALUES ('$news_category_id',
                                                                  '$language_id',
                                                                  '$news_cat_name',
                                                                  '$news_cat_hierarchy_path_db',
                                                                  '$news_cat_long_name_db')";
        $all_queries .= "<br>".$query_insert_news_cat_desc;
        $result_insert_news_cat_desc = mysqli_query($db_link, $query_insert_news_cat_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: news-categories.php');
      
    } //if(empty($news_category_errors))
      
  } //if(isset($_POST['add_news_category'])
  
  $page_title = $languages[$current_lang]['news_categories_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/news/news-categories.php" title="<?=$languages[$current_lang]['title_breadcrumbs_news_categories'];?>"><?=$languages[$current_lang]['header_news_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_news_categories_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_news_categories_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $key => $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
            $news_cat_name = (isset($news_cat_names[$language_id])) ? $news_cat_names[$language_id] : "";
?>
          <div>
            <?php
              if($key == 0) {
            ?>
              <label for="news_cat_names" class="title"><?=$languages[$current_lang]['header_news_category_name'];?>
                <span class="red">*</span>
              </label>
            <?php
              }
              $input_class = "";
              if(isset($news_category_errors['news_cat_name'][$language_id])) {
                echo "<div class='error'>".$news_category_errors['news_cat_name'][$language_id]."</div>";
                $input_class = " error";
              }
            ?>
            <input type="text" name="news_cat_names[<?=$language_id;?>]" placeholder="<?=$language_menu_name;?>" class="news_cat_names<?=$input_class;?>" value="<?=$news_cat_name;?>" style="width: 49%;" />
            &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            <p class="clearfix"></p>
          </div>
<?php
    }
  }
?>
        <div>
          <label for="news_cat_parent_params" class="title"><?=$languages[$current_lang]['header_news_category_parent'];?></label>
          <select name="news_cat_parent_params" id="news_cat_parent_params" style="width: 50%;">
            <option value="0+0+0"><?=$languages[$current_lang]['option_no_parent'];?></option>
<?php
            list_news_categories_for_select($news_category_id = 0);
?> 
          </select>
        </div>
        <div class="clearfix"></div>
        
        <div>
          <p class="title"><?=$languages[$current_lang]['header_image'];?></p>
          <input type="text" name="news_category_image" style="width: 49%;" value="<?=$news_category_image;?>" />
        </div>
        <div class="clearfix"></div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="add_news_category" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: news-categories.php');
  }
  
  $current_news_category_id = $_GET['news_category_id'];
  
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
  
  if(isset($_POST['update_news_category'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['news_cat_names'] as $language_id => $news_cat_name) {
      if(empty($news_cat_name)) $news_category_errors['news_cat_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $news_cat_names[$language_id] = $_POST['news_cat_names'][$language_id];
      $current_news_cat_names[$language_id] = $_POST['current_news_cat_names'][$language_id];
    }
    
    // $_POST['news_cat_parent_id'] has three parameters - parent_id, hierarchy_ids and hierarchy_level
    $news_cat_parent_id_level = explode("+", $_POST['news_cat_parent_id']);
    $news_cat_parent_id = $news_cat_parent_id_level[0];
    $news_cat_hierarchy_ids = $news_cat_parent_id_level[1];
    $news_cat_hierarchy_level = $news_cat_parent_id_level[2]+1;
    $current_news_cat_parent_id = $_POST['current_news_cat_parent_id'];
    $current_news_cat_hierarchy_level = $_POST['current_news_cat_hierarchy_level'];
    $current_news_cat_sort_order = $_POST['current_news_cat_sort_order'];
    
    /*
     * we have to check if the news_category has new parent
     * i.e. $current_news_cat_parent_id(from hidden input) is not equal to $news_cat_parent_id(from select parent option)
     * if the parent is changed, not counting the case when setting the news_category from not having a parent to having one
     * wich means $current_news_cat_parent_id == 0 and $news_cat_parent_id != 0
     * in case the user has choosen new parent for the news_category
     * we need to update the new news_category's column `news_cat_has_children` to 1, wich means it has children
     * we need to check if the old parent has any children left, and if not - setting it's `news_cat_has_children` parameter to 0
     * we also need to update the news_category's `news_cat_hierarchy_ids` and `news_cat_sort_order` columns
    */
    
    $news_cat_hierarchy_ids_list = "";
    if($current_news_cat_parent_id != $news_cat_parent_id) {

      if($news_cat_parent_id == 0) {
        $news_cat_hierarchy_ids_list = $current_news_category_id;
      }
      else {
        
        $query_update_parent = "UPDATE `news_categories` SET `news_cat_has_children` = '1' WHERE `news_category_id` = '$news_cat_parent_id'";
        $all_queries .= $query_update_parent."<br>";
        $result_update_parent = mysqli_query($db_link, $query_update_parent);
        if(!$result_update_parent) {
          echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $news_cat_hierarchy_ids = get_news_category_hierarchy_ids($news_cat_parent_id);
        $news_cat_hierarchy_ids_list .= "$news_cat_hierarchy_ids.$current_news_category_id";
      }
      
      $news_cat_sort_order = get_lаst_news_category_order_value($news_cat_parent_id);
      $news_cat_sort_order_db = ($news_cat_sort_order == 0) ? 1 : $news_cat_sort_order+1;
    }
    
    if(!isset($news_category_errors)) {
      //if there are no form errors we can insert the information
      
      $news_cat_created_user = $_SESSION['admin']['user_id'];

      $query_update_news_category = "UPDATE `news_categories` SET ";
      if($current_news_cat_parent_id != $news_cat_parent_id) {
            $query_update_news_category .= "`news_cat_parent_id`='$news_cat_parent_id',
                                            `news_cat_hierarchy_ids`='$news_cat_hierarchy_ids_list',
                                            `news_cat_hierarchy_level`='$news_cat_hierarchy_level',
                                            `news_cat_sort_order`='$news_cat_sort_order_db',";
      }
            $query_update_news_category .= "`news_cat_modified_date`=NOW()
                                  WHERE `news_category_id` = '$current_news_category_id'";
      $all_queries .= "<br>".$query_update_news_category;
      $result_update_news_category = mysqli_query($db_link, $query_update_news_category);
      if(!$result_update_news_category) {
        echo $languages[$current_lang]['sql_error_update']." - 3 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      //if the parent was changed we have to check if it has any children left
      //if not - setting it's `news_cat_has_children` parameter to 0
      if($current_news_cat_parent_id != 0 && $current_news_cat_parent_id != $news_cat_parent_id) {
        $query_news_categories_siblings = "SELECT `news_category_id` FROM `news_categories` WHERE `news_cat_parent_id` = '$current_news_cat_parent_id'";
        $all_queries .= "<br>".$query_news_categories_siblings;
        $result_news_categories_siblings = mysqli_query($db_link, $query_news_categories_siblings);
        if(!$result_news_categories_siblings) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_news_categories_siblings) <= 0) {

          $query_update_parent = "UPDATE `news_categories` SET `news_cat_has_children` = '0' WHERE `news_category_id` = '$current_news_cat_parent_id'";
          $all_queries .= "<br>".$query_update_parent."<br>";
          $result_update_parent = mysqli_query($db_link, $query_update_parent);
          if(!$result_update_parent) {
            echo $languages[$current_lang]['sql_error_update']." - 4 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          mysqli_free_result($result_news_categories_siblings);
        }
      }

      //if the news_category has new parent we have to reorder the it's old siblings, if any at all,
      //that have higher `news_cat_sort_order` value and move them with one forward
      if($current_news_cat_parent_id != $news_cat_parent_id) {
        $query_news_categories_for_reorder = "SELECT `news_category_id` FROM `news_categories` 
                                              WHERE `news_cat_parent_id` = '$current_news_cat_parent_id' AND `news_cat_hierarchy_level` = '$current_news_cat_hierarchy_level' 
                                               AND `news_cat_sort_order` > '$current_news_cat_sort_order'";
        $all_queries .= "<br>".$query_news_categories_for_reorder."<br>";
        $result_news_categories_for_reorder = mysqli_query($db_link, $query_news_categories_for_reorder);
        if(!$result_news_categories_for_reorder) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_news_categories_for_reorder) > 0) {
          while($row_news_categories_for_reorder = mysqli_fetch_assoc($result_news_categories_for_reorder)) {
            $row_news_category_id = $row_news_categories_for_reorder['news_category_id'];

            $query_update_category = "UPDATE `news_categories` SET  `news_cat_sort_order`= `news_cat_sort_order` - 1 WHERE `news_category_id` = '$row_news_category_id'";
            $all_queries .= "<br>".$query_update_category."<br>";
            $result_update_category = mysqli_query($db_link, $query_update_category);
            if(!$result_update_category) {
              echo $languages[$current_lang]['sql_error_update']." - 5 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }
          mysqli_free_result($result_news_categories_for_reorder);
        }
      }

      foreach($news_cat_names as $language_id => $news_cat_name) {

        $news_cat_hierarchy_path = "";
        $news_cat_long_name = "";

        $current_news_cat_name = $current_news_cat_names[$language_id];
        
        //if the name of the category is changed we gonna update it
        //in case there was added a new language or an inactive one was activated
        //we gonna have to make a new record for this language
        
        if(empty($current_news_cat_name)) {
          //no record for this language, insert new
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
                                                            VALUES ('$current_news_category_id',
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
        else {
          if($current_news_cat_name != $news_cat_name) {
            //update the name
            
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
            
            $query_update_news_cat_desc = "UPDATE `news_cat_desc` SET `news_cat_name`='$news_cat_name',
                                                                      `news_cat_hierarchy_path`='$news_cat_hierarchy_path_db',
                                                                      `news_cat_long_name`='$news_cat_long_name_db' 
                                                                WHERE `news_category_id` = '$current_news_category_id' AND `language_id` = '$language_id'";
            $all_queries .= "<br>".$query_update_news_cat_desc."<br>";
            $result_update_news_cat_desc = mysqli_query($db_link, $query_update_news_cat_desc);
            if(!$result_update_news_cat_desc) {
              echo $languages[$current_lang]['sql_error_update']." - 5 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
            
          } //if($current_news_cat_name != $news_cat_name)
        }
          
      } //foreach($news_cat_names as $language_id => $news_cat_name)

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: news-categories.php');
      
    } //if(empty($news_category_errors))
      
  } //if(isset($_POST['submit'])
  {
    //$current_news_category_id
    $query_news_categories = "SELECT `news_category_id`,`news_cat_parent_id`,`news_cat_hierarchy_level`,`news_cat_sort_order` 
                              FROM `news_categories` 
                              WHERE `news_category_id` = '$current_news_category_id'
                              ORDER BY `news_cat_sort_order` ASC";
    //echo $query_news_categories;exit;
    $result_news_categories = mysqli_query($db_link, $query_news_categories);
    if(!$result_news_categories) echo mysqli_error($db_link);
    $news_count = mysqli_num_rows($result_news_categories);
    if($news_count > 0) {

      $news_category_row = mysqli_fetch_assoc($result_news_categories);

      $news_category_id = $news_category_row['news_category_id'];
      $news_cat_parent_id = $news_category_row['news_cat_parent_id'];
      $news_cat_hierarchy_level = $news_category_row['news_cat_hierarchy_level'];
      $news_cat_sort_order = $news_category_row['news_cat_sort_order'];
    }
  }
  
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
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?news_category_id=$current_news_category_id";?>">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $key => $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
            
            if(!isset($_POST['update_news_category'])) {
              $query_news_cat_desc = "SELECT `news_cat_name`
                                      FROM `news_cat_desc`
                                      WHERE `news_category_id` = '$current_news_category_id' AND `language_id` = '$language_id'";
              //echo $query_news_cat_desc;
              $result_news_cat_desc = mysqli_query($db_link, $query_news_cat_desc);
              if(!$result_news_cat_desc) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_news_cat_desc) > 0) {
                $news_cat_names_array = mysqli_fetch_assoc($result_news_cat_desc);
                //echo"<pre>";print_r($attribute_group_array);
                $news_cat_names[$language_id] = $news_cat_names_array['news_cat_name'];
              }
            }
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
            <input type="text" name="news_cat_names[<?=$language_id;?>]" placeholder="<?=$language_menu_name;?>" class="news_cat_names<?=$input_class;?>" style="width: 49%;" value="<?php if(isset($news_cat_names[$language_id])) echo $news_cat_names[$language_id];?>" />
            <input type="hidden" name="current_news_cat_names[<?=$language_id;?>]" value="<?php if(isset($news_cat_names[$language_id])) echo $news_cat_names[$language_id];?>" />
            &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            <p class="clearfix"></p>
          </div>
<?php
    }
  }
?>
        <div>
          <p class="title"><?=$languages[$current_lang]['header_news_category_parent'];?><span class="red">*</span></p>
          <input type="hidden" name="current_news_cat_parent_id" value="<?=$news_cat_parent_id;?>" />
          <input type="hidden" name="current_news_cat_hierarchy_level" value="<?=$news_cat_hierarchy_level;?>" />
          <input type="hidden" name="current_news_cat_sort_order" value="<?=$news_cat_sort_order;?>" />
          <select name="news_cat_parent_id" id="news_cat_parent_id" style="width: 50%;">
            <option value="0+0+0"><?=$languages[$current_lang]['option_no_parent'];?></option>
<?php
            list_news_categories_for_select($news_cat_parent_id);
?> 
          </select>
        </div>
        <div class="clearfix"></div>
        
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="update_news_category" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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

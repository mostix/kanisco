<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: administration-users-types.php');
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

  if(isset($_POST['add_user_type'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $user_type_errors = array();
    $all_queries = "";
    
    foreach($_POST['user_type_name'] as $language_id => $user_type_name) {
      if(empty($user_type_name)) $user_type_errors['user_type_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $user_type_names_array[$language_id] = $_POST['user_type_name'][$language_id];
    }

    if(empty($user_type_errors)) {
      //if there are no form errors we can insert the information
      
      $user_type_id = get_lаst_inserted_id("users_types","user_type_id")+1;
      
      foreach($user_type_names_array as $language_id => $user_type_name) {
        
        $user_type_name = mysqli_real_escape_string($db_link, $user_type_name);
        $user_type_sort_order = get_last_sort_order($table_name = 'users_types', $column_name = 'user_type_sort_order',$language_id)+1;

        $query_user_type = "INSERT INTO `users_types`(`user_type_id`,`language_id`,`user_type_name`,`user_type_sort_order`) 
                                                VALUES ('$user_type_id','$language_id','$user_type_name','$user_type_sort_order')";
        //echo $query_user_type;
        $all_queries .= "<br>".$query_user_type;
        $result_inser_user_type_name = mysqli_query($db_link, $query_user_type);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." users_types - insert ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
  
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: administration-users-types.php');
    }//if(empty($user_type_errors))
    
  }//if(isset($_POST['add_user_type']))
  
  $page_title = $languages[$current_lang]['text_user_type_add_new'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/administration/administration-users-types.php"><?=$languages[$current_lang]['text_users_types'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['text_user_type_add_new'];?>
      </div>
      
<?php if(isset($user_type_errors) && !empty($user_type_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
<!--      <div class="warning"></div>
      <div class="success"></div>-->
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <form method="post" name="add_user_type" id="add_user_type" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $key => $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          $language_menu_name = $row_languages['language_menu_name'];
?>
          <div>
            <?php
              if($key == 0) {
            ?>
              <label for="user_type_name" class="title"><?=$languages[$current_lang]['header_name'];?>
                <span class="red">*</span>
              </label>
            <?php
              }
              if(isset($user_type_errors['user_type_name'][$language_id])) {
                echo "<div class='error'>".$user_type_errors['user_type_name'][$language_id]."</div>";
              }
              if(!isset($user_type_names_array[$language_id])) {
                /*
                 * no record for this language, because the language was added after the first time the status was created
                 */
            ?>
              <input type="hidden" name="user_type_sort_order" value="<?=$user_type_sort_order?>" />
              <input type="hidden" name="new_entry[<?=$language_id;?>]" value="1" />
            <?php 
              }
            ?>
            <input type="text" name="user_type_name[<?=$language_id;?>]" class="user_type_name" style="width: 400px;" value="<?php if(isset($user_type_names_array[$language_id])) echo $user_type_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            <p class="clearfix"></p>
          </div>
<?php
        }
      }
?>
        
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        <div>
          <button type="submit" name="add_user_type" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
</body>
</html>
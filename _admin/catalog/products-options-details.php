<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: products-options.php');
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
  
  if(isset($_GET['option_id'])) {
    $current_option_id = $_GET['option_id'];
  }
  else {
    exit("Error");
  }
 
  $isset_submit_product_option = false;
  
  if(isset($_POST['submit_product_option'])) {
   
    //echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    $isset_submit_product_option = true;
    
    mysqli_query($db_link,"BEGIN");
    
    $product_option_errors = array();
    $all_queries = "";
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $ov_image_paths = array();
    $upload_paths = array();
    $ov_image_tmp_name = array();
      
    if(isset($_POST['current_product_option_type'])) {
      $current_product_option_type = $_POST['current_product_option_type'];
    }
    if(isset($_POST['product_option_type'])) {
      $product_option_type = $_POST['product_option_type'];
    }
    foreach($_POST['option_desc_name'] as $language_id => $option_desc_name) {
      if(empty($option_desc_name)) $product_option_errors['option_desc_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $option_desc_names_array[$language_id] = $_POST['option_desc_name'][$language_id];
    }
    foreach($_POST['option_value'] as $option_value_key => $option_value_row) {
      
      $option_value_id = $option_value_row['option_value_id'];
      $option_values_array[$option_value_key]['option_value_id'] = $option_value_id;
      $option_values_array[$option_value_key]['ov_image_path'] = NULL;
      $option_values_array[$option_value_key]['ov_sort_order'] = $option_value_row['ov_sort_order'];

      $ov_image_paths[$option_value_key] = "";
      
      if(isset($_FILES['ov_image_file'])) {
        if($_FILES['ov_image_file']['error'][$option_value_key] != 4) {
          $extension_array = explode("/", $_FILES['ov_image_file']['type'][$option_value_key]);
          $extension = $extension_array[1];
          if(!in_array($extension, $valid_formats)) {
            $product_option_errors[$option_value_key]['ov_image_file'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
          }

          if((isset($_FILES['ov_image_file'])) && ($_FILES['ov_image_file']['size'][$option_value_key] < MAX_FILE_SIZE) && ($_FILES['ov_image_file']['error'][$option_value_key] == 0)) {
            // no error
            $ov_image_tmp_name[$option_value_key] = $_FILES['ov_image_file']['tmp_name'][$option_value_key];
            $ov_image_name = $_FILES['ov_image_file']['name'][$option_value_key];
            $ov_image_paths[$option_value_key] = "/site/images/ov-image-thumbs/$ov_image_name";
            $upload_paths[$option_value_key] = $_SERVER['DOCUMENT_ROOT']."/site/images/ov-image-thumbs/$ov_image_name";
          }
          elseif((isset($_FILES['ov_image_file'])) && ($_FILES['ov_image_file']['size'][$option_value_key] > MAX_FILE_SIZE) || ($_FILES['ov_image_file']['error'][$option_value_key] == 1 || $_FILES['ov_image_file']['error'][$option_value_key] == 2)) {
            $product_option_errors[$option_value_key]['ov_image_file'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
          }
          else {
            if($_FILES['ov_image_file']['error'][$option_value_key] != 4) { // error 4 means no file was uploaded
              $product_option_errors[$option_value_key]['ov_image_file'] .= $languages[$current_lang]['image_uploading_error']."<br>";
            }
          }
        }
      }
        
      foreach($option_value_row['ovd_name'] as $language_id => $ovd_name) {
        if(empty($ovd_name)) $product_option_errors[$option_value_key]['ovd_name'][$language_id] = $languages[$current_lang]['required_field_error'];

        if(isset($option_value_row['new_entry'])) {
          $ovd_names_array['new_entry'][$option_value_key][$language_id] = $ovd_name;
        }
        else {
          $ovd_names_array[$option_value_id][$language_id] = $ovd_name;
        }
      }
    }
    
    $is_there_old_categories_list = $_POST['is_there_old_categories_list'];
    $old_categories_list = $_POST['old_categories_list'];
    if(!empty($_POST['new_categories_ids'])) {
      //removing the last string element, because it's a comma
      //and we need only the ids
      $categories_list = substr($_POST['new_categories_ids'], 0, -1);
      $new_categories_ids = explode(",",$categories_list);
      $new_all_categories_ids = array();
      foreach($new_categories_ids as $parent_id) {
        $new_all_categories_ids[] = $parent_id;
        //get_all_subcategories($parent_id);
      }
      //echo"<pre>";print_r($new_all_categories_ids);exit;
    }
    else $categories_list = 0;
    
    $option_is_frontend_sortable = 0;
    if(isset($_POST['option_is_frontend_sortable'])) $option_is_frontend_sortable = 1;
    $option_modifys_product_isbn = 0;
    if(isset($_POST['option_modifys_product_isbn'])) $option_modifys_product_isbn = 1;

    if(empty($product_option_errors)) {
      //if there are no form errors we can insert the information
     
      $query_update_option_type = "UPDATE `options` 
                                  SET `option_type` = '$product_option_type',`option_is_frontend_sortable` = '$option_is_frontend_sortable',`option_modifys_product_isbn` = '$option_modifys_product_isbn' 
                                  WHERE `option_id` = '$current_option_id'";
      //echo $query_update_option_type;
      $all_queries .= "<br>".$query_update_option_type;
      $result_update_option_type = mysqli_query($db_link, $query_update_option_type);
      if(!$result_update_option_type) {
        echo $languages[$current_lang]['sql_error_insert']." - 6 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      if(!empty($_POST['new_categories_ids'])) {
        foreach($new_all_categories_ids as $category_id) {
          $query_insert_opt_to_cat = "INSERT INTO `option_to_category`(`option_id`, `category_id`) 
                                                              VALUES ('$current_option_id','$category_id')";
          //echo $query_insert_opt_to_cat."<br>";
          $all_queries .= "<br>".$query_insert_opt_to_cat;
          $result_insert_opt_to_cat = mysqli_query($db_link, $query_insert_opt_to_cat);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 1 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
      else {
        // if there was categories selected and all of them remove
        // that means the option will be valid for all categories (we use category_id =  0 for all cat)
        if($is_there_old_categories_list == 1 && empty($old_categories_list)) {
          $query_insert_opt_to_cat = "INSERT INTO `option_to_category`(`option_id`, `category_id`) 
                                                              VALUES ('$current_option_id','0')";
          //echo $query_insert_opt_to_cat;
          $all_queries .= "<br>".$query_insert_opt_to_cat;
          $result_insert_opt_to_cat = mysqli_query($db_link, $query_insert_opt_to_cat);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 2 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
        
      foreach($option_desc_names_array as $language_id => $option_desc_name) {
        
        $option_desc_name = mysqli_real_escape_string($db_link, $option_desc_name);

        $query_update_option_desc_name = "UPDATE `option_description` SET `option_desc_name` = '$option_desc_name'
                                          WHERE `option_id` = '$current_option_id' AND `language_id` = '$language_id'";
        //echo $query_update_option_desc_name;
        $all_queries .= "<br>".$query_update_option_desc_name;
        $result_update_option_desc_name = mysqli_query($db_link, $query_update_option_desc_name);
        if(!$result_update_option_desc_name) {
          echo $languages[$current_lang]['sql_error_insert']." - 3 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
      }
      
      foreach($_POST['option_value'] as $option_value_key => $option_value_row) {
        
        $option_value_id = $option_value_row['option_value_id'];
        $ov_sort_order = $option_value_row['ov_sort_order'];
        $ov_image_path = prepare_for_null_row(mysqli_real_escape_string($db_link, $ov_image_paths[$option_value_key]));
        
        
        if(isset($option_value_row['new_entry']) && $option_value_row['new_entry'] == 1) {
          
          $ov_sort_order = get_ov_lаst_child_order_value($current_option_id);
          $ov_sort_order = ($ov_sort_order == 0) ? 1 : $ov_sort_order+1;
          
          $query_insert_option_value = "INSERT INTO `option_value`(
                                                        `option_value_id`, 
                                                        `option_id`, 
                                                        `ov_image_path`, 
                                                        `ov_sort_order`) 
                                                VALUES ('',
                                                        '$current_option_id',
                                                        $ov_image_path,
                                                        '$ov_sort_order')";
          //echo $query_insert_option_value;
          $all_queries .= "<br>".$query_insert_option_value;
          $result_insert_option_value = mysqli_query($db_link, $query_insert_option_value);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
          
          $option_value_id = mysqli_insert_id($db_link);
        }
        else {
          $update_image = (empty($ov_image_paths[$option_value_key])) ? "" : "`ov_image_path` = $ov_image_path,";
          $query_update_option_value = "UPDATE `option_value` SET $update_image `ov_sort_order` = '$ov_sort_order'
                                        WHERE `option_value_id` = '$option_value_id'";
          //echo $query_update_option_value;
          $all_queries .= "<br>".$query_update_option_value;
          $result_update_option_value = mysqli_query($db_link, $query_update_option_value);
          if(!$result_update_option_value) {
            echo $languages[$current_lang]['sql_error_insert']." - 5 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }

        foreach($option_value_row['ovd_name'] as $language_id => $ovd_name) {
          
          $ovd_name = mysqli_real_escape_string($db_link, $ovd_name);

          if(isset($option_value_row['new_entry']) && $option_value_row['new_entry'] == 1) {
            $query_insert_ovd_name = "INSERT INTO `option_value_description`(
                                                        `option_value_id`, 
                                                        `language_id`, 
                                                        `option_id`, 
                                                        `ovd_name`) 
                                                VALUES ('$option_value_id',
                                                        '$language_id',
                                                        '$current_option_id',
                                                        '$ovd_name')";
            //echo $query_insert_ovd_name;
            $all_queries .= "<br>".$query_insert_ovd_name;
            $result_insert_ovd_name = mysqli_query($db_link, $query_insert_ovd_name);
            if(mysqli_affected_rows($db_link) <= 0) {
              echo $languages[$current_lang]['sql_error_insert']." - 6 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }
          else {
            $query_update_ovd_name = "UPDATE `option_value_description` SET `ovd_name` = '$ovd_name'
                                      WHERE `option_value_id` = '$option_value_id' AND `language_id` = '$language_id'";
            //echo $query_update_ovd_name;
            $all_queries .= "<br>".$query_update_ovd_name;
            $result_update_ovd_name = mysqli_query($db_link, $query_update_ovd_name);
            if(!$result_update_ovd_name) {
              echo $languages[$current_lang]['sql_error_insert']." - 7 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }
            
        }
        
        //handling the category picture
        if((isset($_FILES['ov_image_file'])) && ($_FILES['ov_image_file']['size'][$option_value_key] < MAX_FILE_SIZE) && ($_FILES['ov_image_file']['error'][$option_value_key] == 0)) {

          if(is_uploaded_file($ov_image_tmp_name[$option_value_key])) {
            move_uploaded_file($ov_image_tmp_name[$option_value_key], $upload_paths[$option_value_key]);
          }
          else {
            echo $languages[$current_lang]['sql_error_insert']." - 8 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        //handling the category picture
      
      }
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: products-options.php');
    }//if(empty($product_option_errors))
    
  }//if(isset($_POST['submit_product_option']))
  
  $page_title = $languages[$current_lang]['products_option_edit_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
  if(!$isset_submit_product_option) {
    $query_option_type = "SELECT `option_type`,`option_is_frontend_sortable`,`option_modifys_product_isbn` FROM `options` WHERE `option_id` = '$current_option_id'";
    //echo $query_option_type;
    $result_option_type = mysqli_query($db_link, $query_option_type);
    if(!$result_option_type) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_option_type) > 0) {
      $product_option_row = mysqli_fetch_assoc($result_option_type);
      //echo"<pre>";print_r($product_option_array);
      $current_product_option_type = $product_option_row['option_type'];
      $option_is_frontend_sortable = $product_option_row['option_is_frontend_sortable'];
      $option_modifys_product_isbn = $product_option_row['option_modifys_product_isbn'];
    }
  }
  
  $category_li_block = "";
  $old_categories_list = "";
  $is_there_old_categories_list = false;
  $query_categories = "SELECT `option_to_category`.`category_id`,`category_descriptions`.`cd_name`
                      FROM `option_to_category` 
                      INNER JOIN `category_descriptions` USING(`category_id`)
                      INNER JOIN `languages` USING(`language_id`)
                      WHERE `option_to_category`.`option_id` = '$current_option_id' AND `languages`.`language_is_default_backend` = '1'";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $categories_count = mysqli_num_rows($result_categories);
  if($categories_count > 0) {
    $categories_key = 0;
    $is_there_old_categories_list = true;
    while($row_categories = mysqli_fetch_assoc($result_categories)) {
      //echo"<pre>";print_r($row_categories);
      $category_id = $row_categories['category_id'];
      $cd_name = $row_categories['cd_name'];
      $old_categories_list .= "$category_id,";
      $exclude_categories_array[] = $category_id;
      $delete_warning = $languages[$current_lang]['delete_category_warning']." $cd_name?";
      $category_li_block .= "<li id='$category_id'><b>-$cd_name</b> (<a onclick='if(confirm(\"$delete_warning\")) DeleteCategoryFromOption(\"$category_id\",\"$current_option_id\")' style='display:inline-block;color:red;'>x</a>)</li>";
      $categories_key++;
    }
  }
  else {
    $query_categories = "SELECT `category_id` FROM `option_to_category` WHERE `option_id` = '$current_option_id' AND `category_id` = '0'";
    //echo $query_categories;
    $result_categories = mysqli_query($db_link, $query_categories);
    if(!$result_categories) echo mysqli_error($db_link);
    $categories_count = mysqli_num_rows($result_categories);
    if($categories_count > 0) {
      $all_categories_to_attribute_text = $languages[$current_lang]['header_categories_to_attribute_text'];
      $delete_warning = $languages[$current_lang]['delete_category_warning']." $all_categories_to_attribute_text?";
      $category_li_block .= "<li id='0'><b>-$all_categories_to_attribute_text</b> (<a onclick='if(confirm(\"$delete_warning\")) DeleteCategoryFromOption(\"0\",\"$current_option_id\")' style='display:inline-block;color:red;'>x</a>)</li>";
    }
  }
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/catalog/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/catalog/products-options.php" title="<?=$languages[$current_lang]['title_breadcrumbs_option'];?>"><?=$languages[$current_lang]['header_products_options'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products_option_edit'];?>
      </div>
      
<?php if(isset($product_option_errors) && !empty($product_option_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
<!--      <div class="warning"></div>
      <div class="success"></div>-->
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_products_option_edit'];?></h1>
      
      <form method="post" name="add_product_option" enctype="multipart/form-data" id="add_product_option" class="input_form" action="<?=$_SERVER['PHP_SELF']."?option_id=".$current_option_id;?>">
        <input type="hidden" name="current_product_option_type" value="<?=$current_product_option_type;?>" />
        <div style="margin-top: -20px;">
          <label for="product_option_type" class="title"><?=$languages[$current_lang]['header_products_option_type'];?><span class="red">*</span></label>
          <select name="product_option_type" style="width: 250px;">
            <optgroup label="<?=$languages[$current_lang]['option_choose_label'];?>">
              <option value="select" <?php if($current_product_option_type == "select") echo "selected";?>><?=$languages[$current_lang]['option_select'];?></option>
              <option value="radio" <?php if($current_product_option_type == "radio") echo "selected";?>><?=$languages[$current_lang]['option_radio'];?></option>
              <option value="checkbox" <?php if($current_product_option_type == "checkbox") echo "selected";?>><?=$languages[$current_lang]['option_checkbox'];?></option>
              <option value="image" <?php if($current_product_option_type == "image") echo "selected";?>><?=$languages[$current_lang]['option_image'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_input_label'];?>">
              <option value="text" <?php if($current_product_option_type == "text") echo "selected";?>><?=$languages[$current_lang]['option_text'];?></option>
              <option value="textarea" <?php if($current_product_option_type == "textarea") echo "selected";?>><?=$languages[$current_lang]['option_textarea'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_file_label'];?>">
              <option value="file" <?php if($current_product_option_type == "file") echo "selected";?>><?=$languages[$current_lang]['option_file'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_date_label'];?>">
              <option value="date" <?php if($current_product_option_type == "date") echo "selected";?>><?=$languages[$current_lang]['option_date'];?></option>
              <option value="time" <?php if($current_product_option_type == "time") echo "selected";?>><?=$languages[$current_lang]['option_time'];?></option>
              <option value="datetime" <?php if($current_product_option_type == "datetime") echo "selected";?>><?=$languages[$current_lang]['option_datetime'];?></option>
            </optgroup>
          </select>
        </div>
        
        <div>
          <label for="option_is_frontend_sortable" class="title"><?=$languages[$current_lang]['header_option_is_frontend_sortable'];?></label>
          <?php
            if(isset($option_is_frontend_sortable) && $option_is_frontend_sortable == 0) echo '<input type="checkbox" name="option_is_frontend_sortable" id="option_is_frontend_sortable" />';
            else echo '<input type="checkbox" name="option_is_frontend_sortable" id="option_is_frontend_sortable" checked="checked" />';
          ?>
        </div>
        
        <div>
          <label for="option_modifys_product_isbn" class="title"><?=$languages[$current_lang]['header_option_modifys_product_isbn'];?></label>
          <?php
            if(isset($option_modifys_product_isbn) && $option_modifys_product_isbn == 0) echo '<input type="checkbox" name="option_modifys_product_isbn" id="option_modifys_product_isbn" />';
            else echo '<input type="checkbox" name="option_modifys_product_isbn" id="option_modifys_product_isbn" checked="checked" />';
          ?>
        </div>
        <p><i class="info"><?=$languages[$current_lang]['info_modifys_product_isbn'];?></i></p>
        
        <div>
            <label for="product_categories" class="title"><?=$languages[$current_lang]['header_product_categories'];?></label>
            <select name="select_categories" id="select_categories" style="width: 600px;" onChange="AddCategoryToProduct(this.value,'#new_categories_ids')">
              <option value="0"><?=$languages[$current_lang]['option_choose_categories_for_product'];?></option>
<?php
              list_categories_in_select_for_products($category_id = 0, $path_number = 0);
?> 
            </select>
            <p><i class="info"><?=$languages[$current_lang]['info_option_category'];?></i></p>
            <ul id="categories_list" style="margin:6px 0;">
              <?=$category_li_block;?>
            </ul>
            <input type="hidden" name="is_there_old_categories_list" id="is_there_old_categories_list" value="<?=$is_there_old_categories_list;?>" />
            <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$old_categories_list;?>" />
            <input type="hidden" name="new_categories_ids" id="new_categories_ids" value="" />
            <input type="hidden" id="choosen_category_already" value="<?=$languages[$current_lang]['choosen_category_already_warning'];?>" />
            <input type="hidden" id="category_is_not_choosable" value="<?=$languages[$current_lang]['category_is_not_choosable_warning'];?>" />
          </div>
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $key => $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];

            if(!$isset_submit_product_option) {
              $query_product_option_descriptions = "SELECT `option_desc_name`
                                                    FROM `option_description`
                                                    WHERE `option_id` = '$current_option_id' AND `language_id` = '$language_id'";
              //echo $query_product_option_descriptions;
              $result_product_option_descriptions = mysqli_query($db_link, $query_product_option_descriptions);
              if(!$result_product_option_descriptions) echo mysqli_error($db_link);
              if(mysqli_num_rows($result_product_option_descriptions) > 0) {
                $product_option_descriptions_array = mysqli_fetch_assoc($result_product_option_descriptions);
                //echo"<pre>";print_r($product_option_array);
                $option_desc_names_array[$language_id] = $product_option_descriptions_array['option_desc_name'];
              }
            }
?>
          <div>
            <?php
              if($key == 0) {
            ?>
              <label for="option_desc_name" class="title"><?=$languages[$current_lang]['header_products_option_name'];?><span class="red">*</span></label>
            <?php
              }
              if(isset($product_option_errors['option_desc_name'][$language_id])) {
                echo "<div class='error'>".$product_option_errors['option_desc_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="option_desc_name[<?=$language_id;?>]" class="option_desc_name" style="width: 400px;" value="<?php if(isset($option_desc_names_array[$language_id])) echo $option_desc_names_array[$language_id];?>" />
            &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
            <p class="clearfix"></p>
          </div>
<?php
          }
        }
?>
          <div id="option_values" style="padding-top: 20px;<?php if($current_product_option_type != "select" && $current_product_option_type != "radio" && $current_product_option_type != "checkbox" && $current_product_option_type != "image") echo "display:none;"?>">
            
            <?php list_products_options_values();?>
            
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        <div>
          <button type="submit" name="submit_product_option" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
  <script type="text/javascript">
    $(document).ready(function() {
      $('select[name=\'product_option_type\']').bind('change', function() {
          if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
            $('#option_values').show();
          } else {
            $('#option_values').hide();
          }
      });
    });
    function AddOptionValue() {
      var option_value_row = $("#option_values_count").val();

      var html  = '<tbody id="option_value_row_'+option_value_row+'">';
      html += '  <tr>';	
      html += '    <td width="55%" class="text_left">';
<?php
      foreach($languages_array as $row_languages) {

        $language_id = $row_languages['language_id'];
        $language_code = $row_languages['language_code'];
        $language_menu_name = $row_languages['language_menu_name'];
?>
      html += '<div><input type="hidden" name="option_value['+option_value_row+'][new_entry]" value="1" /><input type="hidden" name="option_value['+option_value_row+'][option_value_id]" value="" /><input type="text" name="option_value['+option_value_row+'][ovd_name][<?=$language_id;?>]" value="" style="width: 400px;" />&nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /><p class="clearfix"></p></div>';
<?php
      } // foreach($languages_array)
?>
      html += '    </td>';
      html += '    <td width="5%"></td>';
      html += '    <td width="10%"><input type="text" name="option_value['+option_value_row+'][ov_sort_order]" value="" style="width: 20px;"></td>';
      html += '    <td width="20%"><div class="ov_image"><input type="file" name="ov_image_file['+option_value_row+']" class="ov_image_file" style="width: auto;" /></div></td>';
      html += '    <td width="10%"><a onclick="RemoveOptionValue('+option_value_row+')" class="button red"><?=$languages[$current_lang]['btn_delete'];?></a></td>';
      html += '  </tr>';	
      html += '</tbody>';

      $("#option_values table tfoot").before(html);

      option_value_row++;
      $("#option_values_count").val(option_value_row);
    }
    function RemoveOptionValue(option_value_row) {
      $('#option_value_row_'+option_value_row).remove();
    }
  </script>
</body>
</html>
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

  //we gonna set default values
  $product_option_type = "select";
  $category_li_block  = "";
  $option_values_count = 1;
    
  if(isset($_POST['submit_product_option'])) {
   
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $product_option_errors = array();
    $all_queries = "";
      
    if(isset($_POST['option_values_count'])) {
      $option_values_count = $_POST['option_values_count'];
    }
    if(isset($_POST['product_option_type'])) {
      $product_option_type = $_POST['product_option_type'];
    }
    $option_sort_order = get_option_lаst_child_sort_order();
    $option_sort_order = ($option_sort_order == 0) ? 1 : $option_sort_order+1;
    $option_is_frontend_sortable = 0;
    if(isset($_POST['option_is_frontend_sortable'])) $option_is_frontend_sortable = 1;
    $option_modifys_product_isbn = 0;
    if(isset($_POST['option_modifys_product_isbn'])) $option_modifys_product_isbn = 1;
    
    foreach($_POST['option_desc_name'] as $language_id => $option_desc_name) {
      if(empty($option_desc_name)) $product_option_errors['option_desc_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $option_desc_names_array[$language_id] = $_POST['option_desc_name'][$language_id];
    }
    foreach($_POST['option_value'] as $option_value_key => $option_value_row) {
      $option_values_array[$option_value_key]['option_value_id'] = $option_value_row['option_value_id'];
      $option_values_array[$option_value_key]['ov_image_path'] = NULL;
      $option_values_array[$option_value_key]['ov_sort_order'] = $option_value_row['ov_sort_order'];
      
      foreach($option_value_row['ovd_name'] as $language_id => $ovd_name) {
        if(empty($ovd_name)) $product_option_errors[$option_value_key]['ovd_name'][$language_id] = $languages[$current_lang]['required_field_error'];

        $ovd_names_array[$option_value_key][$language_id] = $ovd_name;
      }
    }
    
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
    }
    else $categories_list = 0;

    $query_categories = "SELECT `category_descriptions`.`category_id`,`category_descriptions`.`cd_name`
                        FROM `category_descriptions` 
                        INNER JOIN `languages` USING(`language_id`)
                        WHERE `category_descriptions`.`category_id` IN($categories_list) AND `languages`.`language_is_default_backend` = '1'";
    //echo $query_categories;
    $result_categories = mysqli_query($db_link, $query_categories);
    if (!$result_categories) echo mysqli_error($db_link);
    $categories_count = mysqli_num_rows($result_categories);
    if($categories_count > 0) {
      $categories_key = 0;
      $is_there_old_categories_list = true;
      while($row_categories = mysqli_fetch_assoc($result_categories)) {
        //echo"<pre>";print_r($row_categories);
        $category_id = $row_categories['category_id'];
        $cd_name = $row_categories['cd_name'];
        $delete_warning = $languages[$current_lang]['delete_category_warning']." $cd_name?";
        $category_li_block .= "<li id='$category_id'><b>-$cd_name</b> (<a onclick='RemoveCategoryFromProduct(\"$category_id\",\"#new_categories_ids\")' style='display:inline-block;color:red;'>x</a>)</li>";
        $categories_key++;
      }
    }
    else {
      $all_categories_to_attribute_text = $languages[$current_lang]['header_categories_to_attribute_text'];
      $delete_warning = $languages[$current_lang]['delete_category_warning']." $all_categories_to_attribute_text?";
      $category_li_block .= "<li id='0'><b>-$all_categories_to_attribute_text</b> (<a onclick='RemoveCategoryFromProduct(\"0\",\"#new_categories_ids\")' style='display:inline-block;color:red;'>x</a>)</li>";
    }
    
    if(empty($product_option_errors)) {
      //if there are no form errors we can insert the information
     
      $query_insert_option = "INSERT INTO `options`(`option_id`, `option_type`, `option_sort_order`, `option_is_frontend_sortable`, `option_modifys_product_isbn`) 
                                          VALUES ('','$product_option_type','$option_sort_order','$option_is_frontend_sortable','$option_modifys_product_isbn')";
      //echo $query_insert_option;
      $all_queries .= "<br>".$query_insert_option;
      $result_insert_option = mysqli_query($db_link, $query_insert_option);
      if(!$result_insert_option) {
        echo $languages[$current_lang]['sql_error_insert']." - 1 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $option_id = mysqli_insert_id($db_link);
      
      if(!empty($_POST['new_categories_ids'])) {
        foreach($new_all_categories_ids as $category_id) {
          $query_insert_opt_to_cat = "INSERT INTO `option_to_category`(`option_id`, `category_id`) 
                                                              VALUES ('$option_id','$category_id')";
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
      else {
        $query_insert_opt_to_cat = "INSERT INTO `option_to_category`(`option_id`, `category_id`) 
                                                            VALUES ('$option_id','0')";
        //echo $query_insert_opt_to_cat;
        $all_queries .= "<br>".$query_insert_opt_to_cat;
        $result_insert_opt_to_cat = mysqli_query($db_link, $query_insert_opt_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 3 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
        
      foreach($option_desc_names_array as $language_id => $option_desc_name) {
        
        $option_desc_name = mysqli_real_escape_string($db_link, $option_desc_name);

        $query_insert_option_desc_name = "INSERT INTO `option_description`(`option_id`, `language_id`, `option_desc_name`) 
                                                                  VALUES ('$option_id','$language_id','$option_desc_name')";
        //echo $query_insert_option_desc_name;
        $all_queries .= "<br>".$query_insert_option_desc_name;
        $result_insert_option_desc_name = mysqli_query($db_link, $query_insert_option_desc_name);
        if(!$result_insert_option_desc_name) {
          echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
      }
      
      foreach($_POST['option_value'] as $option_value_row) {
          
        $ov_sort_order = $option_value_row['ov_sort_order'];

        $query_insert_option_value = "INSERT INTO `option_value`(
                                                      `option_value_id`, 
                                                      `option_id`, 
                                                      `ov_image_path`, 
                                                      `ov_sort_order`) 
                                              VALUES ('',
                                                      '$option_id',
                                                      NULL,
                                                      '$ov_sort_order')";
        //echo $query_insert_option_value."<br>";
        $all_queries .= "<br>".$query_insert_option_value;
        $result_insert_option_value = mysqli_query($db_link, $query_insert_option_value);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 5 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $option_value_id = mysqli_insert_id($db_link);
        
        foreach($option_value_row['ovd_name'] as $language_id => $ovd_name) {
          
          $ovd_name = mysqli_real_escape_string($db_link, $ovd_name);

          $query_insert_ovd_name = "INSERT INTO `option_value_description`(
                                                      `option_value_id`, 
                                                      `language_id`, 
                                                      `option_id`, 
                                                      `ovd_name`) 
                                              VALUES ('$option_value_id',
                                                      '$language_id',
                                                      '$option_id',
                                                      '$ovd_name')";
          //echo $query_insert_ovd_name."<br>";
          $all_queries .= "<br>".$query_insert_ovd_name;
          $result_insert_ovd_name = mysqli_query($db_link, $query_insert_ovd_name);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 6 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
            
        }
        
      } //foreach($_POST['option_value'] as $option_value_key => $option_value_row)
    
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: products-options.php');
    }//if(empty($product_option_errors))
    
  }//if(isset($_POST['submit_product_option']))
  
  $page_title = $languages[$current_lang]['products_option_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/catalog/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/catalog/products-options.php" title="<?=$languages[$current_lang]['title_breadcrumbs_option'];?>"><?=$languages[$current_lang]['header_products_options'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products_option_add_new'];?>
      </div>
      
<?php if(isset($product_option_errors) && !empty($product_option_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
<!--      <div class="warning"></div>
      <div class="success"></div>-->
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_products_option_add_new'];?></h1>
      
      <form method="post" name="add_product_option" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF'];?>" id="add_product_option" class="input_form">
        <div style="margin-top: -20px;">
          <label for="product_option_type" class="title"><?=$languages[$current_lang]['header_products_option_type'];?><span class="red">*</span></label>
          <select name="product_option_type" style="width: 250px;">
            <optgroup label="<?=$languages[$current_lang]['option_choose_label'];?>">
              <option value="select" <?php if($product_option_type == "select") echo "selected";?>><?=$languages[$current_lang]['option_select'];?></option>
              <option value="radio" <?php if($product_option_type == "radio") echo "selected";?>><?=$languages[$current_lang]['option_radio'];?></option>
              <option value="checkbox" <?php if($product_option_type == "checkbox") echo "selected";?>><?=$languages[$current_lang]['option_checkbox'];?></option>
              <option value="image" <?php if($product_option_type == "image") echo "selected";?>><?=$languages[$current_lang]['option_image'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_input_label'];?>">
              <option value="text" <?php if($product_option_type == "text") echo "selected";?>><?=$languages[$current_lang]['option_text'];?></option>
              <option value="textarea" <?php if($product_option_type == "textarea") echo "selected";?>><?=$languages[$current_lang]['option_textarea'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_file_label'];?>">
              <option value="file" <?php if($product_option_type == "file") echo "selected";?>><?=$languages[$current_lang]['option_file'];?></option>
            </optgroup>
            <optgroup label="<?=$languages[$current_lang]['option_date_label'];?>">
              <option value="date" <?php if($product_option_type == "date") echo "selected";?>><?=$languages[$current_lang]['option_date'];?></option>
              <option value="time" <?php if($product_option_type == "time") echo "selected";?>><?=$languages[$current_lang]['option_time'];?></option>
              <option value="datetime" <?php if($product_option_type == "datetime") echo "selected";?>><?=$languages[$current_lang]['option_datetime'];?></option>
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
            list_categories_in_select_for_products($parent_id = 0, $path_number = 0);
?> 
          </select>
          <ul id="categories_list" style="margin:6px 0;">
            <?=$category_li_block?>
          </ul>
          <input type="hidden" name="new_categories_ids" id="new_categories_ids" value="<?php if(isset($_POST['new_categories_ids'])) echo $_POST['new_categories_ids'];?>" />
          <input type="hidden" id="choosen_category_already" value="<?=$languages[$current_lang]['choosen_category_already_warning'];?>" />
          <input type="hidden" id="category_is_not_choosable" value="<?=$languages[$current_lang]['category_is_not_choosable_warning'];?>" />
        </div>
        <p><i class="info red"><?=$languages[$current_lang]['info_category_to_attribute_group'];?></i></p>
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
            <label for="option_desc_name" class="title"><?=$languages[$current_lang]['header_products_option_name'];?><span class="red">*</span></label>
          <?php
            }
          ?>
          <input type="text" name="option_desc_name[<?=$language_id;?>]" class="option_desc_name" style="width: 400px;" value="<?php if(isset($option_desc_names_array[$language_id])) echo $option_desc_names_array[$language_id];?>" />
          &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
          <?php
            if(isset($product_option_errors['option_desc_name'][$language_id])) {
              echo "<div class='error'>".$product_option_errors['option_desc_name'][$language_id]."</div>";
            }
          ?>
          <p class="clearfix"></p>
        </div>
<?php
          }
        }
?>
        <div id="option_values" style="padding-top: 20px;<?php if($product_option_type != "select" && $product_option_type != "radio" && $product_option_type != "checkbox" && $product_option_type != "image") echo "display:none;"?>">
          <table class="border">
            <thead>
              <tr>
                <th width="60%" class="text_left"><?=$languages[$current_lang]['header_option_value'];?></th>
                <th width="10%"><?=$languages[$current_lang]['header_sort_order'];?></th>
                <th width="20%"><?=$languages[$current_lang]['header_option_value_image'];?></th>
                <th width="10%"><?=$languages[$current_lang]['header_actions'];?></th>
              </tr>
            </thead>
<?php
        if(!empty($option_values_array)) {
          
          foreach($option_values_array as $option_key => $option_value) {
            
            $option_value_id = $option_value['option_value_id'];
            $ov_image_path = $option_value['ov_image_path'];
            $ov_sort_order = $option_value['ov_sort_order'];
            $ov_image_path_html = (!is_null($ov_image_path)) ? "<img src='$ov_image_path' width='150' />" : "";
?>
            <tbody id="option_value_row_<?=$option_key;?>">
              <tr>
                <td width="60%" class="text_left">         
<?php
            foreach($languages_array as $key => $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
            <div>
                <input type="hidden" name="option_value[<?=$option_key;?>][option_value_id]" value="<?=$option_value_id;?>" />
                <input type="text" name="option_value[<?=$option_key;?>][ovd_name][<?=$language_id;?>]" style="width: 400px;" value="<?php if(isset($ovd_names_array[$option_key][$language_id])) echo $ovd_names_array[$option_key][$language_id];?>" />
              &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
              <?php
                if(isset($product_option_errors[$option_key]['ovd_name'][$language_id])) {
                  echo "<div class='error'>".$product_option_errors[$option_key]['ovd_name'][$language_id]."</div>";
                }
              ?>
              <p class="clearfix"></p>
            </div>
<?php
            } // foreach($languages_array)
?>
                </td>
                <td width="10%">
                  <input type="text" name="option_value[<?=$option_key;?>][ov_sort_order]" value="<?=$ov_sort_order;?>" style="width: 20px;">
                </td>
                <td width="20%"><?=$ov_image_path_html;?></td>
                <td width="10%">
<?php if($option_key != 0) { ?><a onclick="$('#option_value_row_<?=$option_key;?>').remove();" class="button red"><?=$languages[$current_lang]['btn_delete'];?></a><?php } ?>
                </td>
              </tr>
            </tbody>       
<?php
          } // foreach($option_values_array)
        } // if(!empty($option_values_array))
        else {
?>
            <tbody id="option_value_row_0">
              <tr>
                <td width="60%" class="text_left">         
<?php
            foreach($languages_array as $key => $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
            <div>
                <input type="hidden" name="option_value[0][new_entry]" value="1" />
                <input type="hidden" name="option_value[0][option_value_id]" value="" />
                <input type="text" name="option_value[0][ovd_name][<?=$language_id;?>]" style="width: 400px;" />
              &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" />
              <p class="clearfix"></p>
            </div>
<?php
            } // foreach($languages_array)
?>
                </td>
                <td width="10%">
                  <input type="text" name="option_value[0][ov_sort_order]" value="" style="width: 20px;">
                </td>
                <td width="20%"></td>
                <td width="10%"></td>
              </tr>
            </tbody>
<?php
        }
?>
                  
              <tfoot>
                <tr>
                  <td width="60%" class="text_left">
                    <a class="button green" onClick="AddOptionValue()"><i class="icon icon_plus_sign"></i>Добави нов ред за стойност</a>
                  </td>
                  <td width="20%"></td>
                  <td width="20%"></td>
                </tr>
              </tfoot>
            </table>
            <input type="hidden" name="option_values_count" id="option_values_count" value="<?=$option_values_count;?>" />
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
      html += '    <td width="60%" class="text_left">';
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
      html += '    <td width="10%"><input type="text" name="option_value['+option_value_row+'][ov_sort_order]" value="" style="width: 20px;"></td>';
      html += '    <td width="20%"></td>';
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
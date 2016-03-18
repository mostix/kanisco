<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();

  if(isset($_POST['product_id'])) {
    $current_product_id =  $_POST['product_id'];
  }
  if(isset($_POST['category_id'])) {
    $current_category_id =  $_POST['category_id'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  
  if(!empty($current_product_id)) {
 
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

    $query_attributes = "SELECT `attributes`.`attribute_id`,`attribute_description`.`attribute_description`
                      FROM `attributes` 
                      INNER JOIN `attribute_description` ON `attribute_description`.`attribute_id` = `attributes`.`attribute_id`
                      INNER JOIN `attribute_to_category` ON `attribute_to_category`.`attribute_id` = `attributes`.`attribute_id`
                      WHERE `attribute_to_category`.`category_id` IN(0,$current_category_id)
                        AND `attribute_description`.`language_id` = '$current_language_id'
                      GROUP BY `attributes`.`attribute_id`
                      ORDER BY `attributes`.`attribute_sort_order` ASC ";
    //echo $query_attributes."<br>";
    $result_attributes = mysqli_query($db_link, $query_attributes);
    if(!$result_attributes) echo mysqli_error($db_link);
    $count_attributes = mysqli_num_rows($result_attributes);
    if($count_attributes > 0) {

      $attribute_key = 0;
      $attributes_array = array();

      while($row_attribute = mysqli_fetch_assoc($result_attributes)) {

        $attributes_array[] = $row_attribute;

        $attribute_key++;
      }

      mysqli_free_result($result_attributes);
    } // if($count_attributes > 0)
?>
      <select name="attributes_select" class="attributes_select hidden">
<?php
      /*
       * we gonna use this hidden select for adding a new attribute row
       * then the same with the languages
       */

      if(!empty($attributes_array)) {
        foreach($attributes_array as $row_attribute) {

          $attribute_id = $row_attribute['attribute_id'];
          $attribute_description = stripslashes($row_attribute['attribute_description']);

          echo "<option value='$attribute_id'>$attribute_description</option>";
        }
      }
?>
      </select>
<?php
    if(!empty($languages_array)) {
      foreach($languages_array as $key => $row_languages) {

        $language_id = $row_languages['language_id'];
        $language_code = $row_languages['language_code'];
        $language_menu_name = $row_languages['language_menu_name'];  
?>
        <div class="existing_language" data-id="<?=$language_id;?>" data-code="<?=$language_code;?>" data-name="<?=$language_menu_name;?>" style="display: none;"></div>
<?php
      }
    }
?>
      <table class="border">
        <thead>
          <tr>
            <th class="text_left" style="width: 25%"><?=$languages[$current_lang]['header_attribute'];?></th>
            <th class="text_left" style="width: 65%"><?=$languages[$current_lang]['header_product_attribute_value'];?></th>
            <th style="width: 10%"></th>
          </tr>
        </thead>
<?php
    $query_product_attribute = "SELECT `attribute_id`
                                FROM `product_attribute` 
                                WHERE `product_id` = '$current_product_id'
                                GROUP BY `attribute_id`";
    //echo $query_product_attribute."<br>";
    $result_product_attribute = mysqli_query($db_link, $query_product_attribute);
    if(!$result_product_attribute) echo mysqli_error($db_link);
    $product_attributes_count = mysqli_num_rows($result_product_attribute);
    if($product_attributes_count > 0) {

      $product_attribute_key = 0;
      while($row_product_attribute = mysqli_fetch_assoc($result_product_attribute)) {
        //echo"<pre>";print_r($row_product_attribute);

        $product_current_attribute_id = $row_product_attribute['attribute_id'];
?>
        <tbody id="product_attribute_<?=$product_current_attribute_id;?>" class="product_attribute_row_<?=$product_attribute_key;?> product_attribute_row" row-key="<?=$product_attribute_key;?>">
          <tr>
            <td class="text_left">
              <select name="product_attribute[<?=$product_attribute_key?>][attribute_id]" class="attribute_id">
<?php
              if(!empty($attributes_array)) {
                foreach($attributes_array as $row_attribute) {

                  $attribute_id = $row_attribute['attribute_id'];
                  $attribute_description = stripslashes($row_attribute['attribute_description']);


                  $selected_option = ($product_current_attribute_id == $attribute_id) ? 'selected="selected"' : "";

                  echo "<option value='$attribute_id' $selected_option>$attribute_description</option>";
                }
              }
?>
              </select>
            </td>
            <td class="text_left">
<?php
    if(!empty($languages_array)) {
      foreach($languages_array as $key => $row_languages) {

        $language_id = $row_languages['language_id'];
        $language_code = $row_languages['language_code'];
        $language_menu_name = $row_languages['language_menu_name'];

        $query_product_attribute_value = "SELECT `product_attribute_id`,`product_attribute_value`
                                          FROM `product_attribute`
                                          WHERE `product_id` = '$current_product_id' AND `attribute_id` = '$product_current_attribute_id' AND `language_id` = '$language_id'";
        //echo $query_product_attribute_value;
        $result_product_attribute_value = mysqli_query($db_link, $query_product_attribute_value);
        if(!$result_product_attribute_value) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_product_attribute_value) > 0) {
          $product_attribute_value_array = mysqli_fetch_assoc($result_product_attribute_value);
          //echo"<pre>";print_r($attribute_group_array);
          $product_attribute_id = $product_attribute_value_array['product_attribute_id'];
          $product_attribute_value = $product_attribute_value_array['product_attribute_value'];
        }
        else {
          $product_attribute_id = "new_entry";
          $product_attribute_value = "";
        }
?>
          <textarea name="product_attribute_value[<?=$product_attribute_key?>][<?=$language_id;?>]" data-id="<?=$product_attribute_id;?>" class="product_attribute_value"><?=$product_attribute_value;?></textarea>
          &nbsp;&nbsp;<img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>">
          <p class="clearfix"></p>
<?php
      }
    } //if(!empty($languages_array))
?>
            </td>
            <td>
              <a href="javascript:;" class="delete_attribute" data-pa-id="<?=$product_current_attribute_id;?>">
                <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_delete'];?>" title="<?=$languages[$current_lang]['title_delete'];?>" width="16" height="16" />
              </a>
            </td>
          </tr>
        </tbody>
<?php
        $product_attribute_key++;
      } //while($row_product_attribute)

      mysqli_free_result($result_product_attribute);
    } // if($product_attributes_count > 0)
    else {
      $product_attributes_count = 0;
    }
?>
        <tfoot>
          <tr>
            <td colspan="6" class="text_left">
              <a class="button green" onClick="AddProductAttributeRow()"><i class="icon icon_plus_sign"></i><?=$languages[$current_lang]['btn_add_new_row'];?></a>
            </td>
          </tr>
        </tfoot>
      </table>
      <input type="hidden" class="product_attributes_count" value="<?=$product_attributes_count;?>" />
      <input type="hidden" id="ajaxmessage_delete_product_attribute_success" value="<?=$languages[$current_lang]['ajaxmessage_delete_product_attribute_success'];?>" />
      <p>&nbsp;</p>

  <script type="text/javascript">
    $(document).ready(function() {
      $("#modal_confirm_delete_product_attribute").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm_delete_product_attribute",
        buttons: {
          "<?=$languages[$current_lang]['btn_delete'];?>": function() {
            var attribute_id = $(".delete_attribute.active").attr("data-pa-id");
            //alert(image_data);
            DeleteProductAttributeValue(attribute_id);
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(this).dialog("close");
          }
        }
      });
      
      $(".delete_attribute").click(function() {
        $(".delete_attribute").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_product_attribute").dialog("open");
      });
    });
  </script>
<?php
  } //if(!empty($current_product_id))
?>
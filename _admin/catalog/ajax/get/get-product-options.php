<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_id'])) {
    $current_category_id=  $_POST['category_id'];
  }
  if(isset($_POST['product_id'])) {
    $current_product_id =  $_POST['product_id'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  
  if(!empty($current_product_id)) {
 
?>
    <div id="product_options_tabs" class="left_column">
<?php

    $query_options = "SELECT `options`.`option_type`,`options`.`option_id`,`options`.`option_modifys_product_isbn`,`option_description`.`option_desc_name`
                      FROM `options` 
                      INNER JOIN `option_description` ON `option_description`.`option_id` = `options`.`option_id`
                      INNER JOIN `option_to_category` ON `option_to_category`.`option_id` = `options`.`option_id`
                      WHERE `option_to_category`.`category_id` IN(0,$current_category_id)
                        AND `option_description`.`language_id` = '$current_language_id'
                      GROUP BY `options`.`option_id`
                      ORDER BY `options`.`option_sort_order` ASC ";
    //echo $query_options."<br>";
    $result_options = mysqli_query($db_link, $query_options);
    if(!$result_options) echo mysqli_error($db_link);
    $count_options = mysqli_num_rows($result_options);
    if($count_options > 0) {

      $option_key = 0;
      $options_array = array();

      while($row_option = mysqli_fetch_assoc($result_options)) {

        $option_id = $row_option['option_id'];
        $option_type = $row_option['option_type'];
        $option_desc_name = stripslashes($row_option['option_desc_name']);
        $options_array[] = $row_option;
?>
        <a href="#option_<?=$option_key?>"><?=$option_desc_name?></a>
<?php
        $option_key++;
      }

      mysqli_free_result($result_options);
    } // if($count_options > 0)
?>
    </div>
    <div class="right_column">
<?php
    foreach($options_array as $option_key => $row_option) {

      $option_id = $row_option['option_id'];
      $option_type = $row_option['option_type'];
      $option_desc_name = stripslashes($row_option['option_desc_name']);

      $option_values_array = array();
      $query_option_values = "SELECT `option_value`.`option_value_id`,`option_value`.`ov_image_path`,`option_value`.`ov_sort_order`,
                                      `option_value_description`.`ovd_name` 
                              FROM `option_value`
                              INNER JOIN `option_value_description` ON `option_value_description`.`option_value_id` = `option_value`.`option_value_id`
                              WHERE `option_value`.`option_id` = '$option_id'
                                AND `option_value_description`.`language_id` = '$current_language_id'
                              ORDER BY `ov_sort_order` ASC";
      //echo $query_option_values;
      $result_option_values = mysqli_query($db_link, $query_option_values);
      if(!$result_option_values) echo mysqli_error($db_link);
      $option_values_count = mysqli_num_rows($result_option_values);
      if($option_values_count > 0) {
        while($row_option_values = mysqli_fetch_assoc($result_option_values)) {

          $option_values_array[] = $row_option_values;
        }
      }

      $query_product_options = "SELECT `product_option_id`,`po_is_required`, `po_value`
                                FROM `product_option` 
                                WHERE `product_option`.`product_id` = '$current_product_id' AND `product_option`.`option_id` = '$option_id'";
      $result_product_options = mysqli_query($db_link, $query_product_options);
      if(!$result_product_options) echo mysqli_error($db_link);
      $product_options_count = mysqli_num_rows($result_product_options);
      if($product_options_count > 0) {
        $row_product_options = mysqli_fetch_assoc($result_product_options);

        $product_option_id = $row_product_options['product_option_id'];
        $po_is_required = $row_product_options['po_is_required'];
        $po_value = $row_product_options['po_value'];
?>
        <div id="option_<?=$option_key?>" class="product_option_tab" style="display:none;">
          <input type="hidden" name="product_option[<?=$option_key?>][product_option_id]" value="<?=$product_option_id?>" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_name]" value="<?=$option_desc_name?>" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_id]" value="<?=$option_id?>" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_type]" value="<?=$option_type?>" />
          <select class="product_options_select" style="display:none;">
<?php
          if(!empty($option_values_array)) {

            $first_iteration = true;

            foreach($option_values_array as $row_option_values) {

              $option_value_id = $row_option_values['option_value_id'];
              $ovd_name = stripslashes($row_option_values['ovd_name']);

              $selected_option = ($first_iteration) ? 'selected="selected"' : "";

              echo "<option value='$option_value_id' $selected_option>$ovd_name</option>";

              $first_iteration = false;
            }
          }
?>
          </select>

          <label for="po_is_required" class="title"><?=$languages[$current_lang]['header_po_is_required'];?></label>
          <select name="product_option[<?=$option_key?>][po_is_required]" class="po_is_required" style="width: 92px;">
            <option value="0" <?php if(isset($po_is_required) && $po_is_required == 0) echo 'selected="selected"';?>><?=$languages[$current_lang]['no'];?></option>
            <option value="1" <?php if(isset($po_is_required) && $po_is_required == 1) echo 'selected="selected"';?>><?=$languages[$current_lang]['yes'];?></option>
          </select>
          <p>&nbsp;</p>
          <table class="border gray_thead">
<?php
//              if($option_type == 1) {
          if(true) {
?>
            <thead>
              <tr>
                <th class="text_left"><?=$languages[$current_lang]['header_option_value'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_quantity'];?></th>
                <th class="text_left"><?=$languages[$current_lang]['header_product_subtract_stock'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_price'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_points'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_weight'];?></th>
                <th></th>
              </tr>
            </thead>
<?php
            $query_po_values = "SELECT `product_option_value`.*
                                FROM `product_option_value` 
                                WHERE `product_option_value`.`product_option_id` = '$product_option_id'";
            //echo $query_po_values;
            $result_po_values = mysqli_query($db_link, $query_po_values);
            if(!$result_po_values) echo mysqli_error($db_link);
            $po_values_count = mysqli_num_rows($result_po_values);
            if($po_values_count > 0) {

              $po_values_key = 0;
              while($row_po_values = mysqli_fetch_assoc($result_po_values)) {

                $product_option_value_id = $row_po_values['product_option_value_id'];
                $product_current_option_value_id = $row_po_values['option_value_id'];
                $pov_quantity = $row_po_values['pov_quantity'];
                $pov_subtract = $row_po_values['pov_subtract'];
                $pov_price = $row_po_values['pov_price'];
                $pov_price_prefix = $row_po_values['pov_price_prefix'];
                $pov_points = $row_po_values['pov_points'];
                $pov_points_prefix = $row_po_values['pov_points_prefix'];
                $pov_weight = $row_po_values['pov_weight'];
                $pov_weight_prefix = $row_po_values['pov_weight_prefix'];
                $product_option_value_row = "#option_$option_key .product_option_value_row_$po_values_key";
?>
                <tbody class="product_option_value_row_<?=$po_values_key;?> product_option_value_row" row-key="<?=$po_values_key;?>">
                  <tr>
                    <td class="text_left">
                      <select name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][option_value_id]">
<?php
                      if(!empty($option_values_array)) {
                        foreach($option_values_array as $row_option_values) {

                          $option_value_id = $row_option_values['option_value_id'];
                          $ovd_name = stripslashes($row_option_values['ovd_name']);

                          $selected_option = ($product_current_option_value_id == $option_value_id) ? 'selected="selected"' : "";

                          echo "<option value='$option_value_id' $selected_option>$ovd_name</option>";
                        }
                      }
?>
                      </select>
                      <input type="hidden" name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][product_option_value_id]" value="<?=$product_option_value_id;?>" />
                    </td>
                    <td class="text_right"><input type="text" name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_quantity]" value="<?=$pov_quantity;?>" size="3" /></td>
                    <td class="text_left">
                      <select name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_subtract]">
                        <option value="1" <?php if($pov_subtract == 1) echo 'selected="selected"';?>><?=$languages[$current_lang]['yes'];?></option>
                        <option value="0" <?php if($pov_subtract == 0) echo 'selected="selected"';?>><?=$languages[$current_lang]['no'];?></option>
                      </select>
                    </td>
                    <td class="text_right">
                      <select name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_price_prefix]">
                        <option value="+" <?php if($pov_price_prefix == "+") echo 'selected="selected"';?>>&plus;</option>
                        <option value="-" <?php if($pov_price_prefix == "-") echo 'selected="selected"';?>>&minus;</option>
                      </select>
                      <input type="text" name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_price]" value="<?=$pov_price;?>" size="5" />
                    </td>
                    <td class="text_right">
                      <select name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_points_prefix]">
                        <option value="+" <?php if($pov_points_prefix == "+") echo 'selected="selected"';?>>&plus;</option>
                        <option value="-" <?php if($pov_points_prefix == "-") echo 'selected="selected"';?>>&minus;</option>
                      </select>
                      <input type="text" name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_points]" value="<?=$pov_points;?>" size="5" />
                    </td>
                    <td class="text_right">
                      <select name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_weight_prefix]">
                        <option value="+" <?php if($pov_weight_prefix == "+") echo 'selected="selected"';?>>&plus;</option>
                        <option value="-" <?php if($pov_weight_prefix == "-") echo 'selected="selected"';?>>&minus;</option>
                      </select>
                      <input type="text" name="product_option[<?=$option_key?>][product_option_value][<?=$po_values_key;?>][pov_weight]" value="<?=$pov_weight;?>" size="9" />
                    </td>
                    <td>
                      <a href="javascript:;" class="delete_option_value" data-pov-id="<?=$product_option_value_id;?>" data-po-id="<?=$product_option_id;?>" data-pov-row="<?=$product_option_value_row;?>">
                        <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_delete'];?>" title="<?=$languages[$current_lang]['title_delete'];?>" width="16" height="16" />
                      </a>
                    </td>
                  </tr>
                </tbody>
<?php
                $po_values_key++;
              }
              mysqli_free_result($result_po_values);
            }
          }
          else {
?>

<?php   
          }
          //echo"<pre>";print_r($row_product_options);
?>
            <tfoot>
              <tr>
                <td colspan="7" class="text_left">
                  <a class="button green" onClick="AddProductOptionValue('<?=$option_key?>')"><i class="icon icon_plus_sign"></i><?=$languages[$current_lang]['btn_add_new_row'];?></a>
                </td>
              </tr>
            </tfoot>
          </table>
          <input type="hidden" class="product_option_values_count" value="<?=$po_values_count;?>" />
        </div>
<?php
        } //if($product_options_count > 0)
        else {
?>
        <div id="option_<?=$option_key?>" class="product_option_tab">
          <input type="hidden" name="product_option[<?=$option_key?>][product_option_id]" value="new_entry" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_name]" value="<?=$option_desc_name?>" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_id]" value="<?=$option_id?>" />
          <input type="hidden" name="product_option[<?=$option_key?>][option_type]" value="<?=$option_type?>" />
          <select class="product_options_select" style="display:none;">
<?php
          if(!empty($option_values_array)) {

            $first_iteration = true;

            foreach($option_values_array as $row_option_values) {

              $option_value_id = $row_option_values['option_value_id'];
              $ovd_name = stripslashes($row_option_values['ovd_name']);

              $selected_option = ($first_iteration) ? 'selected="selected"' : "";

              echo "<option value='$option_value_id' $selected_option>$ovd_name</option>";

              $first_iteration = false;
            }
          }
?>
          </select>

          <label for="po_is_required" class="title"><?=$languages[$current_lang]['header_po_is_required'];?></label>
          <select name="product_option[<?=$option_key?>][po_is_required]" class="po_is_required" style="width: 92px;">
            <option value="0" selected="selected"><?=$languages[$current_lang]['no'];?></option>
            <option value="1"><?=$languages[$current_lang]['yes'];?></option>
          </select>
          <p>&nbsp;</p>
          <table class="border gray_thead">
<?php
//              if($option_type == 1) {
          if(true) {
?>
            <thead>
              <tr>
                <th class="text_left"><?=$languages[$current_lang]['header_option_value'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_quantity'];?></th>
                <th class="text_left"><?=$languages[$current_lang]['header_product_subtract_stock'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_price'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_points'];?></th>
                <th class="text_right"><?=$languages[$current_lang]['header_product_weight'];?></th>
                <th></th>
              </tr>
            </thead>
<?php
          }
          else {
?>

<?php
          }
          //echo"<pre>";print_r($row_product_options);
?>
            <tfoot>
              <tr>
                <td colspan="7" class="text_left">
                  <a class="button green" onClick="AddProductOptionValue('<?=$option_key?>')"><i class="icon icon_plus_sign"></i><?=$languages[$current_lang]['btn_add_new_row'];?></a>
                </td>
              </tr>
            </tfoot>
          </table>
          <input type="hidden" class="product_option_values_count" value="0" />
        </div>
<?php
        }
      } //foreach($options_array as $row_option)
?>
      </div>
      <!--<div class="right_column">-->
      <div class="clearfix"></div>
      <p>&nbsp;</p>
      <script type="text/javascript">
        $(document).ready(function() {
          // options tab switcher
          $("#product_options_tabs a").click(function() {
            var this_link = $(this);
            var clicked_tab = this_link.attr("href");
            $("#product_options_tabs a").removeClass("active");
            this_link.addClass("active");
            $(".product_option_tab").hide();
            $(clicked_tab).fadeIn();
            event.preventDefault();
          });
          // end options tab switcher

          $(".delete_option_value").click(function() {
            $(".delete_option_value").removeClass("active");
            $(this).addClass("active");
            $("#modal_confirm_delete_option_value").dialog("open");
          });

          $("#modal_confirm_delete_option_value").dialog({
            resizable: false,
            width: 400,
            height: 200,
            autoOpen: false,
            modal: true,
            draggable: false,
            closeOnEscape: true,
            dialogClass: "modal_confirm_delete_option_value",
            buttons: {
              "<?=$languages[$current_lang]['btn_delete'];?>": function() {
                var product_option_value_id = $(".delete_option_value.active").attr("data-pov-id");
                var product_option_id = $(".delete_option_value.active").attr("data-po-id");
                var product_option_value_row = $(".delete_option_value.active").attr("data-pov-row");
                //alert(image_data);
                DeleteProductOptionValue(product_option_value_id,product_option_id,product_option_value_row);
              },
              "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
                $(this).dialog("close");
              }
            }
          });
        });
      </script>
<?php
  }
?>
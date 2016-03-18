<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();

  if(isset($_POST['product_id'])) {
    $current_product_id =  $_POST['product_id'];
  }
  
  if(!empty($current_product_id)) {
 
    $customer_groups_array = array();
    $query_customer_groups = "SELECT `customer_group_id`, `customer_group_name` FROM `customers_groups`";
    $result_customer_groups = mysqli_query($db_link, $query_customer_groups);
    if(!$result_customer_groups) echo mysqli_error($db_link);
    $count_customer_groups = mysqli_num_rows($result_customer_groups);
    if($count_customer_groups > 0) {

      while($row_customer_group = mysqli_fetch_assoc($result_customer_groups)) {
        $customer_groups_array[] = $row_customer_group;

      }
    }
?>
  <select name="customer_groups_select" class="customer_groups_select hidden">
<?php
    if(!empty($customer_groups_array)) {
      foreach($customer_groups_array as $customer_group) {

        $customer_group_id = $customer_group['customer_group_id'];
        $customer_group_name = stripslashes($customer_group['customer_group_name']);

        echo "<option value='$customer_group_id'>$customer_group_name</option>";
      }
    }
?>
  </select>
  <table class="border">
    <thead>
      <tr>
        <th class="text_left"><?=$languages[$current_lang]['header_customer_group'];?></th>
        <th class="text_right"><?=$languages[$current_lang]['header_product_quantity'];?></th>
        <th class="text_right"><?=$languages[$current_lang]['header_priority'];?></th>
        <th class="text_right"><?=$languages[$current_lang]['header_product_price'];?></th>
        <th class="text_left"><?=$languages[$current_lang]['header_date_start'];?></th>
        <th class="text_left"><?=$languages[$current_lang]['header_date_end'];?></th>
        <th></th>
      </tr>
    </thead>
<?php
    $query_product_discount = "SELECT `product_discount`.*
                                FROM `product_discount` 
                                WHERE `product_id` = '$current_product_id'
                                ORDER BY `customer_group_id` ASC";
    //echo $query_product_discount."<br>";
    $result_product_discount = mysqli_query($db_link, $query_product_discount);
    if(!$result_product_discount) echo mysqli_error($db_link);
    $count_product_discount = mysqli_num_rows($result_product_discount);
    if($count_product_discount > 0) {

      $product_discount_key = 0;
      while($row_product_discount = mysqli_fetch_assoc($result_product_discount)) {
        //echo"<pre>";print_r($row_product_discount);

        $product_discount_id = $row_product_discount['product_discount_id'];
        $product_current_customer_group_id = $row_product_discount['customer_group_id'];
        $pd_quantity = $row_product_discount['pd_quantity'];
        $pd_priority = $row_product_discount['pd_priority'];
        $pd_price = $row_product_discount['pd_price'];
        $pd_date_start = $row_product_discount['pd_date_start'];
        $pd_date_end = $row_product_discount['pd_date_end'];
?>
        <tbody id="product_discount_<?=$product_discount_id;?>" class="product_discount_row_<?=$product_discount_key;?> product_discount_row" data-id="<?=$product_discount_id;?>" row-key="<?=$product_discount_key;?>">
          <tr>
            <td class="text_left">
              <select name="product_discount[<?=$product_discount_key?>][customer_group_id]" class="customer_group_id">
<?php
              if(!empty($customer_groups_array)) {
                foreach($customer_groups_array as $customer_group) {

                  $customer_group_id = $customer_group['customer_group_id'];
                  $customer_group_name = stripslashes($customer_group['customer_group_name']);

                  $selected_option = ($product_current_customer_group_id == $customer_group_id) ? 'selected="selected"' : "";

                  echo "<option value='$customer_group_id' $selected_option>$customer_group_name</option>";
                }
              }
?>
              </select>
            </td>
            <td class="text_right">
              <input type="text" name="product_discount[<?=$product_discount_key?>][pd_quantity]" class="pd_quantity" style="width: 80px;" value="<?php if(isset($pd_quantity)) echo $pd_quantity;?>" />
            </td>
            <td class="text_right">
              <input type="text" name="product_discount[<?=$product_discount_key?>][pd_priority]" class="pd_priority" style="width: 80px;" value="<?php if(isset($pd_priority)) echo $pd_priority;?>" />
            </td>
            <td class="text_right">
              <input type="text" name="product_discount[<?=$product_discount_key?>][pd_price]" class="pd_price" style="width: auto;" value="<?php if(isset($pd_price)) echo $pd_price;?>" />
            </td>
            <td class="text_left">
              <input type="text" name="product_discount[<?=$product_discount_key?>][pd_date_start]" class="pd_date_start datepicker" style="width: auto;" value="<?php if(isset($pd_date_start)) echo $pd_date_start;?>" />
            </td>
            <td class="text_left">
              <input type="text" name="product_discount[<?=$product_discount_key?>][pd_date_end]" class="pd_date_end datepicker" style="width: auto;" value="<?php if(isset($pd_date_end)) echo $pd_date_end;?>" />
            </td>
            <td>
              <a href="javascript:;" class="delete_discount" data-pd-id="<?=$product_discount_id;?>">
                <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_delete'];?>" title="<?=$languages[$current_lang]['title_delete'];?>" width="16" height="16" />
              </a>
            </td>
          </tr>
        </tbody>
<?php
        $product_discount_key++;
      }

      mysqli_free_result($result_product_discount);
    } // if($count_product_discount > 0)
    else {
      $product_discounts_count = 0;
    }
?>
      <tfoot>
        <tr>
          <td colspan="6" class="text_left">
            <a class="button green" onClick="AddProductDiscount()"><i class="icon icon_plus_sign"></i><?=$languages[$current_lang]['btn_add_new_row'];?></a>
          </td>
        </tr>
      </tfoot>
    </table>
    <input type="hidden" class="product_discounts_count" value="<?=$product_discounts_count;?>" />
  </table>
  <script type="text/javascript">
    $(document).ready(function() {
      $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
      
      $("#modal_confirm_delete_discount").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm_delete_discount",
        buttons: {
          "<?=$languages[$current_lang]['btn_delete'];?>": function() {
            var product_discount_id = $(".delete_discount.active").attr("data-pd-id");
            //alert(image_data);
            DeleteProductDiscount(product_discount_id);
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(this).dialog("close");
          }
        }
      });
      
      $(".delete_discount").click(function() {
        $(".delete_discount").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_discount").dialog("open");
      });
    });
  </script>
<?php
  }
?>
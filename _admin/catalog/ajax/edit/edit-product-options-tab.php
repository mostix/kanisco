<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['product_options'])) {
    $product_options =  $_POST['product_options'];
  }
  
  if(!empty($product_options)) {
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
    
    foreach($product_options as $key => $product_option) {
      
      $product_option_id = $product_option['product_option_id'];
      $option_id = $product_option['option_id'];
      $option_name = $product_option['option_name'];
      $option_type = $product_option['option_type'];
      $po_is_required = $product_option['po_is_required'];
      
      if($product_option_id == "new_entry") {
        
        if(isset($product_option['product_option_values'])) {
          
          // there is no record for the current option
          // so we need to insert one
          $query_insert_product_option = "INSERT INTO `product_option`(`product_option_id`, 
                                                                      `product_id`, 
                                                                      `option_id`, 
                                                                      `po_value`, 
                                                                      `po_is_required`) 
                                                              VALUES ('',
                                                                      '$product_id',
                                                                      '$option_id',
                                                                      NULL,
                                                                      '$po_is_required')";
          $all_queries .= "<br>\n".$query_insert_product_option;
          $result_insert_product_option = mysqli_query($db_link, $query_insert_product_option);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - 3 ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }

          $product_option_id = mysqli_insert_id($db_link);
          
        }
      }
      else {
        
        // there is a record already for the current option
        // so we need to update it
        $query_update_product_option = "UPDATE `product_option` SET `po_is_required`='$po_is_required'
                                                                WHERE `product_option_id` = '$product_option_id'";
        $all_queries .= "<br>\n".$query_update_product_option;
        $result_update_product_option = mysqli_query($db_link, $query_update_product_option);
        if(!$result_update_product_option) {
          echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      if(isset($product_option['product_option_values'])) {
        foreach($product_option['product_option_values'] as $key => $product_option_value) {

          $product_option_value_id = $product_option_value['product_option_value_id'];
          $option_value_id = $product_option_value['option_value_id'];
          $pov_quantity = $product_option_value['pov_quantity'];
          $pov_subtract = $product_option_value['pov_subtract'];
          $pov_price = str_replace(",", ".", $product_option_value['pov_price']);
          $pov_price_prefix = $product_option_value['pov_price_prefix'];
          $pov_points = $product_option_value['pov_points'];
          $pov_points_prefix = $product_option_value['pov_points_prefix'];
          $pov_weight = str_replace(",", ".", $product_option_value['pov_weight']);
          $pov_weight_prefix = $product_option_value['pov_weight_prefix'];

          if($product_option_value_id == "new_entry") {

            // there is no record for the current product_option
            // so we need to insert one
            $query_insert_pov = "INSERT INTO `product_option_value`(`product_option_value_id`, 
                                                                    `product_option_id`, 
                                                                    `product_id`, 
                                                                    `option_id`, 
                                                                    `option_value_id`, 
                                                                    `pov_quantity`, 
                                                                    `pov_subtract`, 
                                                                    `pov_price`, 
                                                                    `pov_price_prefix`, 
                                                                    `pov_points`, 
                                                                    `pov_points_prefix`, 
                                                                    `pov_weight`, 
                                                                    `pov_weight_prefix`) 
                                                            VALUES ('',
                                                                    '$product_option_id',
                                                                    '$product_id',
                                                                    '$option_id',
                                                                    '$option_value_id',
                                                                    '$pov_quantity',
                                                                    '$pov_subtract',
                                                                    '$pov_price',
                                                                    '$pov_price_prefix',
                                                                    '$pov_points',
                                                                    '$pov_points_prefix',
                                                                    '$pov_weight',
                                                                    '$pov_weight_prefix')";
            $all_queries .= "<br>\n".$query_insert_pov;
            $result_insert_pov = mysqli_query($db_link, $query_insert_pov);
            if(mysqli_affected_rows($db_link) <= 0) {
              echo $languages[$current_lang]['sql_error_insert']." - `product_option_value` ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }

            $product_option_value_id = mysqli_insert_id($db_link);
          }
          else {

            // there is a record already for the current product_option
            // so we need to update it
            $query_update_product_option = "UPDATE `product_option_value` SET `option_value_id`='$option_value_id',
                                                                              `pov_quantity`='$pov_quantity',
                                                                              `pov_subtract`='$pov_subtract',
                                                                              `pov_price`='$pov_price',
                                                                              `pov_price_prefix`='$pov_price_prefix',
                                                                              `pov_points`='$pov_points',
                                                                              `pov_points_prefix`='$pov_points_prefix',
                                                                              `pov_weight`='$pov_weight',
                                                                              `pov_weight_prefix`='$pov_weight_prefix'
                                                                    WHERE `product_option_value_id` = '$product_option_value_id'";
            $all_queries .= "<br>\n".$query_update_product_option;
            $result_update_product_option = mysqli_query($db_link, $query_update_product_option);
            if(!$result_update_product_option) {
              echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }
          }

        }
      }

    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  }
  
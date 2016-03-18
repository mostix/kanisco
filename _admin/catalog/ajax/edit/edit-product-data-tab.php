<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['product_isbn'])) {
    $product_isbn =  $_POST['product_isbn'];
  }
  if(isset($_POST['product_model'])) {
    $product_model =  $_POST['product_model'];
  }
  if(isset($_POST['product_price'])) {
    $product_price =  $_POST['product_price'];
  }
  if(isset($_POST['product_quantity'])) {
    $product_quantity =  $_POST['product_quantity'];
  }
  if(isset($_POST['product_minimum'])) {
    $product_minimum =  $_POST['product_minimum'];
  }
  if(isset($_POST['product_subtract'])) {
    $product_subtract =  $_POST['product_subtract'];
  }
  if(isset($_POST['product_weight'])) {
    $product_weight =  $_POST['product_weight'];
  }
  if(isset($_POST['weight_class_id'])) {
    $weight_class_id =  $_POST['weight_class_id'];
  }
  if(isset($_POST['product_width'])) {
    $product_width =  $_POST['product_width'];
  }
  if(isset($_POST['product_height'])) {
    $product_height =  $_POST['product_height'];
  }
  if(isset($_POST['product_length'])) {
    $product_length =  $_POST['product_length'];
  }
  if(isset($_POST['length_class_id'])) {
    $length_class_id =  $_POST['length_class_id'];
  }
  if(isset($_POST['product_is_active'])) {
    $product_is_active = $_POST['product_is_active'];
  }
  if(isset($_POST['product_date_available'])) {
    $product_date_available = prepare_for_null_row($_POST['product_date_available']);
  }
  if(isset($_POST['stock_status_id'])) {
    $stock_status_id =  $_POST['stock_status_id'];
  }
  if(isset($_POST['is_there_old_categories_list'])) {
    $is_there_old_categories_list =  $_POST['is_there_old_categories_list'];
  }
  if(isset($_POST['old_categories_list'])) {
    $old_categories_list =  $_POST['old_categories_list'];
  }
  if(!empty($_POST['new_categories_ids'])) {
    //removing the last string element, because it's a comma
    //and we need only the ids
    $categories_list = substr($_POST['new_categories_ids'], 0, -1);
    $new_categories_ids = explode(",",$categories_list);
    //echo"<pre>";print_r($new_all_categories_ids);exit;
  }
      
  if(!empty($_POST)) {
      
    $all_queries = "";
    mysqli_query($db_link,"BEGIN");
  
    $query_update_product = "UPDATE `products` SET `stock_status_id`='$stock_status_id',
                                                    `weight_class_id`='$weight_class_id',
                                                    `length_class_id`='$length_class_id',
                                                    `product_model`='$product_model',
                                                    `product_isbn`='$product_isbn',
                                                    `product_quantity`='$product_quantity',
                                                    `product_price`='$product_price',
                                                    `product_weight`='$product_weight',
                                                    `product_length`='$product_length',
                                                    `product_width`='$product_width',
                                                    `product_height`='$product_height',
                                                    `product_subtract`='$product_subtract',
                                                    `product_minimum`='$product_minimum',
                                                    `product_is_active`='$product_is_active',
                                                    `product_date_available`=$product_date_available,
                                                    `product_date_modified`=NOW()
                                        WHERE `product_id` = '$product_id'";
    $all_queries .= "<br>\n".$query_update_product;
    $result_update_product = mysqli_query($db_link, $query_update_product);
    if(!$result_update_product) {
      echo $languages[$current_lang]['sql_error_update']." - 2 ".mysqli_error($db_link);
    }
    
    if(!empty($_POST['new_categories_ids'])) {
      foreach($new_categories_ids as $category_id) {
        $query_insert_prod_to_cat = "INSERT INTO `product_to_category`(`product_id`, `category_id`) 
                                                            VALUES ('$product_id','$category_id')";
        //echo $query_insert_prod_to_cat."<br>";
        $all_queries .= "<br>\n".$query_insert_prod_to_cat;
        $result_insert_prod_to_cat = mysqli_query($db_link, $query_insert_prod_to_cat);
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
        $query_insert_prod_to_cat = "INSERT INTO `product_to_category`(`product_id`, `category_id`) 
                                                            VALUES ('$product_id','0')";
        //echo $query_insert_prod_to_cat;
        $all_queries .= "<br>\n".$query_insert_prod_to_cat;
        $result_insert_prod_to_cat = mysqli_query($db_link, $query_insert_prod_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 2 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    mysqli_query($db_link,"COMMIT");
  }
  
<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['category_id'])) {
    $category_id =  $_POST['category_id'];
  }
  if(isset($_POST['product_sort_order'])) {
    $product_sort_order =  $_POST['product_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($product_id) && !empty($category_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      //if($product_sort_order == 0) $product_sort_order = 1;
      $previous_product_sort_order = $product_sort_order-1;
      $query_update_category_1 = "UPDATE `products` 
                                  INNER JOIN `product_to_category` USING(`product_id`)
                                  SET `product_sort_order`='$product_sort_order' 
                                  WHERE `product_to_category`.`category_id` = '$category_id' AND `products`.`product_sort_order` = '$previous_product_sort_order'";
      $all_queries .= "\n".$query_update_category_1;
        //echo $query_update_category_1;
      $result_update_category_1 = mysqli_query($db_link, $query_update_category_1);
      if(!$result_update_category_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_category_2 = "UPDATE `products` SET `product_sort_order`='$previous_product_sort_order' WHERE `product_id` = '$product_id'";
      $all_queries .= "\n".$query_update_category_2;
        //echo $query_update_category_2;
      $result_update_category_2 = mysqli_query($db_link, $query_update_category_2);
      if(!$result_update_category_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_product_sort_order = $product_sort_order+1;
      $query_update_category_1 = "UPDATE `products` 
                                  INNER JOIN `product_to_category` USING(`product_id`)
                                  SET `product_sort_order`='$product_sort_order' 
                                  WHERE `product_to_category`.`category_id` = '$category_id' AND `products`.`product_sort_order` = '$next_product_sort_order'";
      $all_queries .= "\n".$query_update_category_1;
        //echo $query_update_category_1;
      $result_update_category_1 = mysqli_query($db_link, $query_update_category_1);
      if(!$result_update_category_1) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_category_2 = "UPDATE `products` SET `product_sort_order`='$next_product_sort_order' WHERE `product_id` = '$product_id'";
      $all_queries .= "\n".$query_update_category_2;
        //echo $query_update_category_2;
      $result_update_category_2 = mysqli_query($db_link, $query_update_category_2);
      if(!$result_update_category_2) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");

    list_products($category_id, $first_iteration = true);

  }
?>
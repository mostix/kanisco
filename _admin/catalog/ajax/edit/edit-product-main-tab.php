<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
//  print_r($_POST);EXIT;
  
  if(isset($_POST['product_id'])) {
    $product_id =  $_POST['product_id'];
  }
  if(isset($_POST['language_ids'])) {
    $language_ids =  $_POST['language_ids'];
  }
  if(isset($_POST['pd_names'])) {
    $pd_names =  $_POST['pd_names'];
  }
  if(isset($_POST['pd_meta_titles'])) {
    $pd_meta_titles =  $_POST['pd_meta_titles'];
  }
  if(isset($_POST['pd_meta_keywords'])) {
    $pd_meta_keywords_array =  $_POST['pd_meta_keywords'];
  }
  if(isset($_POST['pd_meta_descriptions'])) {
    $pd_meta_descriptions =  $_POST['pd_meta_descriptions'];
  }
  if(isset($_POST['pd_descriptions'])) {
    $pd_descriptions =  $_POST['pd_descriptions'];
  }
  if(isset($_POST['pd_overviews'])) {
    $pd_overviews =  $_POST['pd_overviews'];
  }
  if(isset($_POST['pd_novations'])) {
    $pd_novations =  $_POST['pd_novations'];
  }
  if(isset($_POST['pd_system_requirements'])) {
    $pd_system_requirements =  $_POST['pd_system_requirements'];
  }
  if(isset($_POST['product_trial_url'])) {
    $product_trial_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$_POST['product_trial_url']));
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
  if(isset($_POST['product_is_active'])) {
    $product_is_active =  $_POST['product_is_active'];
  }
  
  if(!empty($language_ids)) {
    
    mysqli_query($db_link,"BEGIN");
    $all_queries = "";
    
    $query = "UPDATE `products` SET `product_trial_url`=$product_trial_url,`product_is_active`='$product_is_active', `product_date_modified`=NOW() 
                                WHERE `product_id` = '$product_id'";
    $all_queries .= "<br>\n".$query;
    //echo $query_update_pd;
    $result = mysqli_query($db_link, $query);
    if(!$result) {
      echo $languages[$current_lang]['sql_error_update']." - 1 `product_description` ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
        
    foreach($language_ids as $key => $language_id) {
      
      $pd_name = mysqli_real_escape_string($db_link, $pd_names[$key]);
      $pd_description = mysqli_real_escape_string($db_link, $pd_descriptions[$key]);
      $pd_overview = mysqli_real_escape_string($db_link, $pd_overviews[$key]);
      $pd_novations_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $pd_novations[$key]));
      $pd_system_requirements_db = mysqli_real_escape_string($db_link, $pd_system_requirements[$key]);
      $pd_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $pd_meta_titles[$key]));
      $pd_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $pd_meta_descriptions[$key]));
      $pd_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $pd_meta_keywords_array[$key]));
      $pd_tags = "NULL";
      
      $query_check_for_record = "SELECT `product_id` FROM `product_description` WHERE `product_id` = '$product_id' AND `language_id` = '$language_id'";
      $all_queries .= "<br>\n".$query_check_for_record;
      $result_check_for_record = mysqli_query($db_link, $query_check_for_record);
      if(!$result_check_for_record) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_check_for_record) > 0) {

        // there is a record already for the current language
        // so we need to update it
        $query_update_pd = "UPDATE `product_description` SET `pd_name`='$pd_name',
                                                              `pd_description`='$pd_description',
                                                              `pd_overview`='$pd_overview',
                                                              `pd_novations`=$pd_novations_db,
                                                              `pd_system_requirements`='$pd_system_requirements_db',
                                                              `pd_meta_title`=$pd_meta_title,
                                                              `pd_meta_description`=$pd_meta_description,
                                                              `pd_meta_keywords`=$pd_meta_keywords,
                                                              `pd_tags`=$pd_tags 
                                                        WHERE `product_id` = '$product_id' AND `language_id` = '$language_id'";
        $all_queries .= "<br>\n".$query_update_pd;
        //echo $query_update_pd;
        $result_update_pd = mysqli_query($db_link, $query_update_pd);
        if(!$result_update_pd) {
          echo $languages[$current_lang]['sql_error_update']." - 1 `product_description` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      else {
        // there is no record for the current language
        // so we need to insert one
        
        $query_insert_pd = "INSERT INTO `product_description`(`product_id`, 
                                                              `language_id`, 
                                                              `pd_name`, 
                                                              `pd_description`, 
                                                              `pd_overview`, 
                                                              `pd_novations`, 
                                                              `pd_system_requirements`, 
                                                              `pd_meta_title`, 
                                                              `pd_meta_description`, 
                                                              `pd_meta_keywords`, 
                                                              `pd_tags`) 
                                                      VALUES ('$product_id',
                                                              '$language_id',
                                                              '$pd_name',
                                                              '$pd_description',
                                                              '$pd_overview',
                                                              $pd_novations_db,
                                                              '$pd_system_requirements_db',
                                                              $pd_meta_title,
                                                              $pd_meta_description,
                                                              $pd_meta_keywords,
                                                              $pd_tags)";
        $all_queries .= "<br>\n".$query_insert_pd;
        //echo $query_insert_pd;
        $result_insert_pd = mysqli_query($db_link, $query_insert_pd);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 2 `product_description`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

    }
    
    if(!empty($_POST['new_categories_ids'])) {
      foreach($new_categories_ids as $category_id) {
        $query_insert_prod_to_cat = "INSERT INTO `product_to_category`(`product_id`, `category_id`) 
                                                            VALUES ('$product_id','$category_id')";
        //echo $query_insert_prod_to_cat."<br>";
        $all_queries .= "<br>\n".$query_insert_prod_to_cat;
        $result_insert_prod_to_cat = mysqli_query($db_link, $query_insert_prod_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 3 `product_to_category`".mysqli_error($db_link);
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
          echo $languages[$current_lang]['sql_error_insert']." - 4 `product_to_category`".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

    mysqli_query($db_link,"COMMIT");
  }
  
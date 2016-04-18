<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  //echo "<pre>";print_r($_SERVER);
  //echo DIRNAME;
  
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

  if(isset($_POST['submit_product'])) {
   
//    echo"<pre>";print_r($_POST);echo"</pre>";
//    echo"<pre>";print_r($_POST);print_r($_FILES);exit;
//    $extension_array = explode("/", $_FILES['product_image']['type']);
//    $extension = $extension_array[1];
//    echo $extension;exit;
//    exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $product_errors = array();
    $all_queries = "";
      
    foreach($_POST['pd_name'] as $language_id => $pd_name) {
      if(empty($pd_name)) $product_errors['pd_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['pd_description'][$language_id])) $product_errors['pd_description'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['pd_overview'][$language_id])) $product_errors['pd_overview'][$language_id] = $languages[$current_lang]['required_field_error'];
      //if(empty($_POST['pd_novations'][$language_id])) $product_errors['pd_novations'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['pd_system_requirements'][$language_id])) $product_errors['pd_system_requirements'][$language_id] = $languages[$current_lang]['required_field_error'];
    }

    // product's data tab
    $pd_meta_title = $_POST['pd_meta_title'];
    $pd_meta_keywords = $_POST['pd_meta_keywords'];
    $pd_meta_description = $_POST['pd_meta_description'];
    // product's data tab
    
    // product's data tab
    if(isset($_POST['product_trial_url'])) {
      $product_trial_url = $_POST['product_trial_url'];
    }
    $product_is_active = 0;
      if(isset($_POST['product_is_active'])) $product_is_active = 1;
    if(!empty($_POST['categories_ids'])) {
      //removing the last string element, because it's a comma
      //and we need only the ids
      $categories_list = substr($_POST['categories_ids'], 0, -1);
      $categories_ids = explode(",",$categories_list);
    }
    // product's data tab

    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $product_image_path = "";
    $upload_path = "";
    $default_product_image_name = "";
    
    if(isset($_FILES['default_product_image'])) {
      if($_FILES['default_product_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['default_product_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $product_errors['default_product_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['default_product_image']['size'] < MAX_FILE_SIZE) && ($_FILES['default_product_image']['error'] == 0)) {
          // no error

          $default_product_image_name = $_FILES['default_product_image']['name'];
          $default_product_image_name_exploded = explode(".", $default_product_image_name);
          $image_name = str_replace(" ", "-", $default_product_image_name_exploded[0]);
          $image_exstension = mb_convert_case($default_product_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
          $default_product_image_name = "$image_name.$image_exstension";
        }
        elseif(($_FILES['default_product_image']['size'] > MAX_FILE_SIZE) || ($_FILES['default_product_image']['error'] == 1 || $_FILES['default_product_image']['error'] == 2)) {
          $product_errors['default_product_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['default_product_image']['error'] != 4) { // error 4 means no file was uploaded
            $product_errors['default_product_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      } 
    }
    
    if(isset($_FILES['product_image'])) {
      foreach($_FILES['product_image']['error'] as $key_image => $error) {
        if($error != 4) {
          $extension_array = explode("/", $_FILES['product_image']['type'][$key_image]);
          $extension = $extension_array[1];
          if(!in_array($extension, $valid_formats)) {
            $product_errors['product_image'][$key_image] = $languages[$current_lang]['image_extension_error']."$extension<br>";
          }

          if(($_FILES['product_image']['size'][$key_image] < MAX_FILE_SIZE) && ($_FILES['product_image']['error'][$key_image] == 0)) {
            // no error
          }
          elseif(($_FILES['product_image']['size'][$key_image] > MAX_FILE_SIZE) || ($_FILES['product_image']['error'][$key_image] == 1 || $_FILES['product_image']['error'][$key_image] == 2)) {
            $product_errors['product_image'][$key_image] .= $languages[$current_lang]['image_size_error']."4MB<br>";
          }
          else {
            if($_FILES['product_image']['error'][$key_image] != 4) { // error 4 means no file was uploaded
              $product_errors['product_image'][$key_image] .= $languages[$current_lang]['image_uploading_error']."<br>";
            }
          }
        }  
      } 
    }
  
    $user_id = $_SESSION['admin']['user_id'];

    if(empty($product_errors)) {
      //if there are no form errors we can insert the information
      
      $product_trial_url = prepare_for_null_row(mysqli_real_escape_string($db_link,$product_trial_url));
      $product_sort_order = get_product_highest_order_value_for_category($_GET['category_id'])+1;
      $product_viewed = 0;

      $query_insert_product = "INSERT INTO `products`(`product_id`, 
                                                      `product_trial_url`, 
                                                      `product_sort_order`, 
                                                      `product_is_active`, 
                                                      `product_viewed`, 
                                                      `product_date_added`, 
                                                      `product_date_modified`)
                                              VALUES ('',
                                                      $product_trial_url,
                                                      '$product_sort_order',
                                                      '$product_is_active',
                                                      '$product_viewed',
                                                      NOW(),
                                                      NOW())";
      $all_queries .= "<br>".$query_insert_product;
      $result_insert_product = mysqli_query($db_link, $query_insert_product);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - 1 `products` - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $product_id = mysqli_insert_id($db_link);
        
      foreach($_POST['pd_name'] as $language_id => $pd_name) {
        
        $pd_name = mysqli_real_escape_string($db_link, $pd_name);
        $pd_meta_title = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['pd_meta_title'][$language_id]));
        $pd_meta_keywords = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['pd_meta_keywords'][$language_id]));
        $pd_meta_description = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['pd_meta_description'][$language_id]));
        $pd_description = mysqli_real_escape_string($db_link, $_POST['pd_description'][$language_id]);
        $pd_overview = mysqli_real_escape_string($db_link, $_POST['pd_overview'][$language_id]);
        $pd_novations = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['pd_novations'][$language_id]));
        $pd_system_requirements = mysqli_real_escape_string($db_link, $_POST['pd_system_requirements'][$language_id]);
        $pd_downloads = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['pd_downloads'][$language_id]));
        $pd_news_url = mysqli_real_escape_string($db_link, $_POST['pd_news_url'][$language_id]);
        $pd_tags = "NULL";

        $query_insert_pd_description = "INSERT INTO `product_description`(`product_id`, 
                                                                          `language_id`, 
                                                                          `pd_name`, 
                                                                          `pd_description`, 
                                                                          `pd_overview`, 
                                                                          `pd_novations`, 
                                                                          `pd_system_requirements`, 
                                                                          `pd_downloads`, 
                                                                          `pd_news_url`, 
                                                                          `pd_meta_title`, 
                                                                          `pd_meta_description`, 
                                                                          `pd_meta_keywords`, 
                                                                          `pd_tags`) 
                                                                  VALUES ('$product_id',
                                                                          '$language_id',
                                                                          '$pd_name',
                                                                          '$pd_description',
                                                                          '$pd_overview',
                                                                          $pd_novations,
                                                                          '$pd_system_requirements',
                                                                          $pd_downloads,
                                                                          $pd_news_url,
                                                                          $pd_meta_title,
                                                                          $pd_meta_description,
                                                                          $pd_meta_keywords,
                                                                          $pd_tags)";
        //echo $query_insert_pd_description;
        $all_queries .= "<br>".$query_insert_pd_description;
        $result_insert_pd_description = mysqli_query($db_link, $query_insert_pd_description);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 3 `product_description` - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
        
      }
      
      foreach($categories_ids as $category_id) {
        $query_insert_prod_to_cat = "INSERT INTO `product_to_category`(`product_id`, `category_id`) 
                                                            VALUES ('$product_id','$category_id')";
        //echo $query_insert_opt_to_cat;
        $all_queries .= "<br>".$query_insert_prod_to_cat;
        $result_insert_prod_to_cat = mysqli_query($db_link, $query_insert_prod_to_cat);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 4 `product_to_category` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //handling the product images
      //echo"<pre>";print_r($_FILES);
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/products/";
      if($_FILES['default_product_image']['error'] != 4) {

        $default_product_image_tmp_name  = $_FILES['default_product_image']['tmp_name'];
        $default_product_image_name = $_FILES['default_product_image']['name'];
        $default_product_image_name_exploded = explode(".", $default_product_image_name);
        $image_name = str_replace(" ", "-", $default_product_image_name_exploded[0]);
        $image_exstension = mb_convert_case($default_product_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        $default_product_image_name = "$image_name.$image_exstension";
        $pi_is_default = 1;
        $pi_sort_order = 0;

        $query_insert_default_img = "INSERT INTO `product_image`(`product_image_id`, 
                                                                `product_id`, 
                                                                `pi_name`, 
                                                                `pi_is_default`, 
                                                                `pi_sort_order`) 
                                                        VALUES ('',
                                                                '$product_id',
                                                                '$default_product_image_name',
                                                                '$pi_is_default',
                                                                '$pi_sort_order')";
        //echo $query_insert_default_img;
        $result_insert_default_img = mysqli_query($db_link, $query_insert_default_img);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - 5 `product_image` ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        if(is_uploaded_file($default_product_image_tmp_name)) {
          move_uploaded_file($default_product_image_tmp_name, $upload_path.$default_product_image_name);
        }
        else {
          echo $languages[$current_lang]['sql_error_insert']." - 5 image $default_product_image_name ($default_product_image_tmp_name) - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $file = $upload_path.$default_product_image_name;

        list($width,$height) = getimagesize($file);

        $image = new SimpleImage(); 
        $image->load($file);
        
        $image_home_default_name = $image_name."_home_default.".$image_exstension;
        $image_home_default = $upload_path.$image_home_default_name;

        switch($image_exstension) {
          case "gif" : $image_type = 1;
            break;
          case "jpg" : $image_type = 2;
            break;
          case "jpeg" : $image_type = 2;
            break;
          case "png" : $image_type = 3;
            break;
        }

        if($width > $height) {
          if($width > 1280) {
            $image->resizeToWidth(1280);

            $image->save($file,$image_type);
          }

          $image->resizeToWidth(250);

          $image->save($image_home_default,$image_type);

        }
        else {
          if($height > 1280) {
            $image->resizeToHeight(1280);

            $image->save($file,$image_type);
          }

          $image->resizeToHeight(250);

          $image->save($image_home_default,$image_type);
        }
      
      } //if($_FILES['default_product_image']['error'] != 4)
      
      if(isset($_FILES['product_image'])) {

        foreach($_FILES['product_image']['error'] as $key_image => $error) {

          if($error != 4) {
            $product_image_tmp_name  = $_FILES['product_image']['tmp_name'][$key_image];
            $product_image_name = $_FILES['product_image']['name'][$key_image];
            $product_image_name_exploded = explode(".", $product_image_name);
            $image_name = str_replace(" ", "-", $product_image_name_exploded[0]);
            $image_exstension = mb_convert_case($product_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
            $product_image_name = "$image_name.$image_exstension";

            $pi_is_default = 0;
            $pi_sort_order = $key_image+1;

            $query_insert_img = "INSERT INTO `product_image`(`product_image_id`, 
                                                              `product_id`, 
                                                              `pi_name`, 
                                                              `pi_is_default`, 
                                                              `pi_sort_order`) 
                                                      VALUES ('',
                                                              '$product_id',
                                                              '$product_image_name',
                                                              '$pi_is_default',
                                                              '$pi_sort_order')";
            //echo $query_insert_img;
            $result_insert_img = mysqli_query($db_link, $query_insert_img);
            if(mysqli_affected_rows($db_link) <= 0) {
              echo $languages[$current_lang]['sql_error_insert']." - 6 `product_image` ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }

            if(is_uploaded_file($product_image_tmp_name)) {
              move_uploaded_file($product_image_tmp_name, $upload_path.$product_image_name);
            }
            else {
              echo $languages[$current_lang]['sql_error_insert']." - 6 image $product_image_name ($product_image_tmp_name) - ".mysqli_error($db_link);
              mysqli_query($db_link,"ROLLBACK");
              exit;
            }

            $file = $upload_path.$product_image_name;

            list($width,$height) = getimagesize($file);

            $image = new SimpleImage(); 
            $image->load($file);

            $image_home_default_name = $image_name."_home_default.".$image_exstension;
            $image_home_default = $upload_path.$image_home_default_name;

            switch($image_exstension) {
              case "gif" : $image_type = 1;
                break;
              case "jpg" : $image_type = 2;
                break;
              case "jpeg" : $image_type = 2;
                break;
              case "png" : $image_type = 3;
                break;
            }

            if($width > $height) {
              if($width > 1280) {
                $image->resizeToWidth(1280);

                $image->save($file,$image_type);
              }

              $image->resizeToWidth(250);

              $image->save($image_home_default,$image_type);

            }
            else {
              if($height > 1280) {
                $image->resizeToHeight(1280);

                $image->save($file,$image_type);
              }

              $image->resizeToHeight(250);

              $image->save($image_home_default,$image_type);
            }
          } //if($error != 4)
        } //foreach($_FILES['product_image']['error'] as $key_image => $error)
      } //if(isset($_FILES['product_image']))
      //handling the product image
    
//      echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");
    ?>
      <!--<script>window.location.href="<?php echo '/_admin/catalog/products-by-category.php?category_ids_list='.$_GET['category_ids_list'];?>"</script>-->
    <?php
      header('Location: /_admin/catalog/products-by-category.php?category_ids_list='.$_GET['category_ids_list']);
    }//if(empty($product_errors))
//    print_r($product_errors);
    
  }//if(isset($_POST['submit_product']))
  
  $page_title = $languages[$current_lang]['product_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_product_add_new'];?>
      </div>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_product_add_new'];?></h1>
      
<?php if(isset($product_errors) && !empty($product_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
      
<?php
      if(!isset($_GET['category_id'])) {
?>
      <section class="contents_options">
<!--        <a class="pageoptions" href="/_admin/catalog/products-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_product'];?>" title="<?=$languages[$current_lang]['title_add_new_product'];?>" />
          <?=$languages[$current_lang]['link_add_new_product'];?>
        </a>-->
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandCategory('all','expand')" title="<?=$languages[$current_lang]['title_expand_all_sections'];?>">
          <img src="/_admin/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_expand_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandCategory('all','collapse')" title="<?=$languages[$current_lang]['title_collapse_all_sections'];?>">
          <img src="/_admin/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_collapse_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_collapse_all_sections'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&nbsp;</th>
            <th width="5%" class="text_left">&nbsp;</th>
            <th width="60%" class="text_left"><?=$languages[$current_lang]['header_category_name'];?></th>
            <th></th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" width="16" height="16" />
      </div>
      <div id="categories_list" class="list_container">
        <table>
          <tbody>
<?php
            list_categories_for_add_product($parent_id = 0, $path_number = 0);
?>
          </tbody>
        </table>
      </div>
<?php
      }
      else {
        $current_category_id = $_GET['category_id'];
        $current_category_name = $_GET['category_name'];
?>
      <ul class="product_tabs tabs">
        <li><a href="#product_main_tab" ajax-fn="AddProductMainTab"><?=$languages[$current_lang]['header_product_main_tab'];?></a></li>
        <li><a href="#product_images_tab" class="images" ajax-fn="AddProductImagesTab"><?=$languages[$current_lang]['header_product_images_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
      <form method="post" name="add_product" id="add_product" class="input_form" action="<?=$_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
        <div>
          <button type="submit" name="submit_product" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <a href="/_admin/catalog/products-by-category.php?category_id=<?=$current_category_id;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?>
          </a>
          <input type="hidden" name="language_id" id="language_id" value="<?=$current_language_id;?>" />
          <input type="hidden" name="category_id" id="category_id" value="<?=$current_category_id;?>" />
          <input type="hidden" name="request_uri" id="request_uri" value="<?=$_SERVER['REQUEST_URI'];?>" />
          <input type="hidden" id="text_yes" value="<?=$languages[$current_lang]['yes'];?>" />
          <input type="hidden" id="text_no" value="<?=$languages[$current_lang]['no'];?>" />
          <input type="hidden" id="text_btn_delete" value="<?=$languages[$current_lang]['btn_delete'];?>" />
        </div>
        <p class="clearfix"></p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div id="product_main_tab" class="product_tab tab">

          <ul id="languages" class="language_tabs tabs">
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {

              $language_id = $row_languages['language_id'];
              $language_code = $row_languages['language_code'];
              $language_menu_name = $row_languages['language_menu_name'];
?>
              <li><a href="#<?=$language_code;?>"><img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?></a></li>
<?php
            }
          }
?>
          </ul>
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
?>
          <div id="<?=$language_code;?>" class="language_tab tab">
            <div>
              <label for="product_name" class="title"><?=$languages[$current_lang]['header_product_name'];?><span class="red">*</span></label>
              <?php
                if(isset($product_errors['pd_name'][$language_id])) {
                  echo "<div class='error'>".$product_errors['pd_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="pd_name[<?=$language_id;?>]" class="pd_name" style="width: 400px;" value="<?php if(isset($_POST['pd_name'][$language_id])) echo $_POST['pd_name'][$language_id];?>" />
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="pd_news_url" class="title"><?=$languages[$current_lang]['header_product_news_url'];?></label>
              <?php
                if(isset($product_errors['pd_news_url'][$language_id])) {
                  echo "<div class='error'>".$product_errors['pd_news_url'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="pd_news_url[<?=$language_id;?>]" id="pd_news_url_<?=$language_id;?>" style="width: 400px;" value="<?php if(isset($_POST['pd_news_url'][$language_id])) echo $_POST['pd_news_url'][$language_id];?>" />
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_title" class="title"><?=$languages[$current_lang]['header_product_meta_title'];?></label>
              <input type="text" name="pd_meta_title[<?=$language_id;?>]" id="pd_meta_title" onkeyup="CountCharacters(this,'55')" style="width: 60%;" value="<?php if(isset($_POST['pd_meta_title'][$language_id])) echo $_POST['pd_meta_title'][$language_id];?>" />
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_keywords" class="title"><?=$languages[$current_lang]['header_product_meta_keywords'];?></label>
              <input type="text" name="pd_meta_keywords[<?=$language_id;?>]" id="pd_meta_keywords" style="width: 60%;" value="<?php if(isset($_POST['pd_meta_keywords'][$language_id])) echo $_POST['pd_meta_keywords'][$language_id];?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_description" class="title"><?=$languages[$current_lang]['header_product_meta_description'];?></label>
              <textarea name="pd_meta_description[<?=$language_id;?>]" id="pd_meta_description" onkeyup="CountCharacters(this,'200')" style="width: 60%;"/><?php if(isset($_POST['pd_meta_description'][$language_id])) echo $_POST['pd_meta_description'][$language_id];?></textarea>
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="product_description" class="title"><?=$languages[$current_lang]['header_product_description'];?><span class="red">*</span></label>
              <textarea name="pd_description[<?=$language_id;?>]"><?php if(isset($_POST['pd_description'][$language_id])) echo $_POST['pd_description'][$language_id];?></textarea>
            </div>
            
            <div>
              <label for="product_overview" class="title"><?=$languages[$current_lang]['header_product_overview'];?><span class="red">*</span></label>
              <textarea name="pd_overview[<?=$language_id;?>]"><?php if(isset($_POST['pd_overview'][$language_id])) echo $_POST['pd_overview'][$language_id];?></textarea>
            </div>
            
            <div>
              <label for="product_novations" class="title"><?=$languages[$current_lang]['header_product_novations'];?></label>
              <textarea name="pd_novations[<?=$language_id;?>]"><?php if(isset($_POST['pd_novations'][$language_id])) echo $_POST['pd_novations'][$language_id];?></textarea>
            </div>
            
            <div>
              <label for="product_system_requirements" class="title"><?=$languages[$current_lang]['header_product_system_requirements'];?><span class="red">*</span></label>
              <textarea name="pd_system_requirements[<?=$language_id;?>]"><?php if(isset($_POST['pd_system_requirements'][$language_id])) echo $_POST['pd_system_requirements'][$language_id];?></textarea>
            </div>
            
            <div>
              <label for="product_downloads" class="title"><?=$languages[$current_lang]['header_product_downloads'];?></label>
              <textarea name="pd_downloads[<?=$language_id;?>]"><?php if(isset($_POST['pd_downloads'][$language_id])) echo $_POST['pd_downloads'][$language_id];?></textarea>
            </div>
            
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>
          </div>
<?php
          } //foreach($languages_array)
        } //if(!empty($languages_array))
?>
          <div>
            <label for="product_trial_url" class="title"><?=$languages[$current_lang]['header_product_trial_url'];?></label>
            <input type="text" name="product_trial_url" id="product_trial_url" style="width: 588px;" value="<?php if(isset($product_trial_url)) echo $product_trial_url;?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="product_categories" class="title"><?=$languages[$current_lang]['header_product_categories'];?><span class="red">*</span></label>
            <select name="select_categories" id="select_categories" style="width: 600px;" onChange="AddCategoryToProduct(this.value,'#categories_ids')">
              <option value="0"><?=$languages[$current_lang]['option_choose_categories_for_product'];?></option>
              <?php list_categories_in_select_for_products($parent_id = 0, $path_number = 0); ?> 
            </select>
            <ul id="categories_list" style="margin-top:6px;">
<?php
  echo "<li id='$current_category_id'><b>-$current_category_name</b> (<a onclick='RemoveCategoryFromProduct(\"$current_category_id\",\"#categories_ids\")' style='display:inline-block;color:red;'>x</a>)</li>";
?>
            </ul>
            <input type="hidden" name="categories_ids" id="categories_ids" value="<?=$current_category_id;?>," />
            <input type="hidden" id="choosen_category_already" value="<?=$languages[$current_lang]['choosen_category_already_warning'];?>" />
            <input type="hidden" id="category_is_not_choosable" value="<?=$languages[$current_lang]['category_is_not_choosable_warning'];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="product_is_active" class="title"><?=$languages[$current_lang]['header_product_is_active'];?></label>
            <input type="checkbox" name="product_is_active" id="product_is_active" <?php if(isset($product_is_active)) { if($product_is_active == 1) echo 'checked="checked"'; } else echo 'checked="checked"';?> />
          </div>
          <div class="clearfix">&nbsp;</div>
          
        </div>
        
        <div id="product_images_tab" class="product_tab tab no_padding">
          <div class="default_product_image">
            <label for="product_image" class="title"><?=$languages[$current_lang]['header_default_product_image'];?></label>
            <?php
              if(isset($product_errors['default_product_image'])) {
                echo "<div class='error'>".$product_errors['default_product_image']."</div>";
              }
            ?>
            <p><input type="file" name="default_product_image" style="width: auto;" /></p>
          </div>
          
          <div class="product_images">
            <label for="product_image" class="title"><?=$languages[$current_lang]['header_gallery_product_images'];?></label>
            <?php
              if(isset($product_errors['product_image'])) {
                foreach($product_errors['product_image'] as $product_image_error) {
                  echo "<div class='error'>".$product_image_error."</div>";
                }
              }
            ?>
            <p><input type="file" name="product_image[]" class="product_image_file" style="width: auto;" /></p>
            <p><input type="file" name="product_image[]" class="product_image_file" style="width: auto;" /></p>
            <p><input type="file" name="product_image[]" class="product_image_file" style="width: auto;" /></p>
          </div>
          <div id="more_gal_imgs_container">
            
          </div>
          <div class="margin_bottom">
            <a class="button green" onClick="ShowOneMoreImagesInput('10')">
              <i class="icon icon_plus_sign"></i>
              <?=$languages[$current_lang]['btn_add_images_inputs'];?>
            </a>
            <input type="hidden" id="more_product_images_id" value="4" />
            <input type="hidden" id="alt_delete" value="<?=$languages[$current_lang]['alt_delete'];?>" />
          </div>
        </div>
        
        <div>
          <button type="submit" name="submit_product" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <a href="/_admin/catalog/products-by-category.php?category_id=<?=$current_category_id;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?>
          </a>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
?>
          CKEDITOR.replace('pd_description[<?=$language_id;?>]');
          CKEDITOR.replace('pd_overview[<?=$language_id;?>]');
          CKEDITOR.replace('pd_novations[<?=$language_id;?>]');
          CKEDITOR.replace('pd_system_requirements[<?=$language_id;?>]');
          CKEDITOR.replace('pd_downloads[<?=$language_id;?>]');
          CKEDITOR.add
<?php
        }
      }
?>
      $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
      
      // product tab switcher
      $(".product_tabs li").removeClass("active");
      $(".product_tab").hide();
      $(".product_tabs li:first").addClass("active");
      $(".product_tab:first").show();
      $(".product_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".product_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".product_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end product tab switcher
      
      // language tab switcher
      $(".language_tabs li").removeClass("active");
      $(".language_tab").hide();
      $(".language_tabs li:first").addClass("active");
      $(".language_tab:first").show();
      $(".language_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        $(".language_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".language_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end language tab switcher
      
      // options tab switcher
      $("#product_options_tabs a").removeClass("active");
      $(".product_option_tab").hide();
      $("#product_options_tabs a:first").addClass("active");
      $(".product_option_tab:first").show();
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
    });
  </script>
<?php
      }
?>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>
<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>
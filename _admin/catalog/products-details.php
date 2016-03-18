<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  //echo "<pre>";print_r($_SERVER);EXIT;
  
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
  
  $category_ids_list = $_GET['category_ids_list'];
  $current_category_id = $_GET['category_id'];
  $current_product_id = $_GET['product_id'];
  
  $query_product = "SELECT `product_trial_url`,`product_is_active`
                    FROM `products`
                    WHERE `products`.`product_id` = '$current_product_id'";
  //echo $query_product;exit;
  $result_product = mysqli_query($db_link, $query_product);
  if(!$result_product) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_product) > 0) {
    $product_row = mysqli_fetch_assoc($result_product);
    
    $product_trial_url = $product_row['product_trial_url'];
    $product_is_active = $product_row['product_is_active'];
  }

  if(isset($_POST['edit_product'])) {
   
//    echo"<pre>";print_r($_POST);
//    $extension = $extension_array[1];
//    echo $extension;exit;
    
  }//if(isset($_POST['edit_product']))
  
  $page_title = $languages[$current_lang]['product_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
  $category_li_block = "";
  $old_categories_list = "";
  $is_there_old_categories_list = false;
  $query_categories = "SELECT `product_to_category`.`category_id`,`category_descriptions`.`cd_name`
                        FROM `product_to_category` 
                        INNER JOIN `category_descriptions` USING(`category_id`)
                        INNER JOIN `languages` USING(`language_id`)
                        WHERE `product_to_category`.`product_id` = '$current_product_id' AND `languages`.`language_is_default_backend` = '1'";
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
      $delete_warning = $languages[$current_lang]['delete_category_warning'];
      $category_li_block .= "<li id='$category_id'><b>-$cd_name</b> (<a onclick='if(confirm(\"$delete_warning\")) DeleteCategoryFromOption(\"$category_id\",\"$current_product_id\")' style='display:inline-block;color:red;'>x</a>)</li>";
      $categories_key++;
    }
  }
  else {
    $query_categories = "SELECT `category_id` FROM `product_to_category` WHERE `product_id` = '$current_product_id' AND `category_id` = '0'";
    //echo $query_categories;
    $result_categories = mysqli_query($db_link, $query_categories);
    if(!$result_categories) echo mysqli_error($db_link);
    $categories_count = mysqli_num_rows($result_categories);
    if($categories_count > 0) {
      $all_categories_to_attribute_text = $languages[$current_lang]['header_categories_to_attribute_text'];
      $delete_warning = $languages[$current_lang]['delete_category_warning'];
      $category_li_block .= "<li id='0'><b>-$all_categories_to_attribute_text</b> (<a onclick='if(confirm(\"$delete_warning\")) DeleteCategoryFromOption(\"0\",\"$current_product_id\")' style='display:inline-block;color:red;'>x</a>)</li>";
    }
  }
?>
  <main id="page_details">
    <div class="inside_container">
      <div id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/catalog/products-categories.php?category_ids_list=<?=$category_ids_list;?>" class="back_btn" title="<?=$languages[$current_lang]['title_breadcrumbs_products_categories'];?>"><?=$languages[$current_lang]['header_products_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_product_edit'];?>
      </div>
      
<?php if(isset($product_errors) && !empty($product_errors)) echo '<div class="warning">Моля проверете дали всички задължителни полета са попълнени</div>';?>
<!--      <div class="warning"></div>
      <div class="success"></div>-->
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_product_edit'];?></h1>
      
      <ul class="product_tabs tabs">
        <li><a href="#product_main_tab" ajax-fn="EditProductMainTab"><?=$languages[$current_lang]['header_product_main_tab'];?></a></li>
        <li><a href="#product_images_tab" class="images" ajax-fn="EditProductImagesTab"><?=$languages[$current_lang]['header_product_images_tab'];?></a></li>
      </ul>
      <div class="clearfix">&nbsp;</div>
      
      <form method="post" name="edit_product" id="edit_product" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <div>
          <a href="javascript:;" class="button red float_right delete_product_link" data-id="<?=$current_product_id;?>">
            <i class="icon icon_delete_sign"></i><?=$languages[$current_lang]['btn_delete'];?>
          </a>
          <a href="javascript:;" onClick="EditProductMainTab('#product_main_tab')" class="save_product_tab button green">
            <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save_tab'];?>
          </a>
<!--          <button type="submit" name="save_product_all_tabs" class="save_product_all_tabs button red">
            <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save_all_tabs'];?>
          </button>-->
          <a href="/_admin/catalog/products-categories.php?category_ids_list=<?=$category_ids_list;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?>
          </a>
          <input type="hidden" name="language_id" id="language_id" value="<?=$current_language_id;?>" />
          <input type="hidden" name="category_id" id="category_id" value="<?=$current_category_id;?>" />
          <input type="hidden" name="request_uri" id="request_uri" value="<?=$_SERVER['REQUEST_URI'];?>" />
          <input type="hidden" id="text_yes" value="<?=$languages[$current_lang]['yes'];?>" />
          <input type="hidden" id="text_no" value="<?=$languages[$current_lang]['no'];?>" />
          <input type="hidden" id="text_btn_delete" value="<?=$languages[$current_lang]['btn_delete'];?>" />
          <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages[$current_lang]['text_drag_and_drop_upload'];?>" />
        </div>
        <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure']?>">
          <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_product_warning']?></p>
        </div>
        <script>
        $(function() {
          $("#modal_confirm").dialog({
            resizable: false,
            width: 400,
            height: 200,
            autoOpen: false,
            modal: true,
            draggable: false,
            closeOnEscape: true,
            dialogClass: "modal_confirm",
            buttons: {
              "<?=$languages[$current_lang]['btn_delete'];?>": function() {
                DeleteProduct('details');
              },
              "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
                $(".delete_product_link").removeClass("active");
                $(this).dialog("close");
              }
            }
          });
          $(".delete_product_link").click(function() {
            $(".delete_product_link").removeClass("active");
            $(this).addClass("active");
            $("#modal_confirm").dialog("open");
          });
        });
        </script>
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
              
              if(isset($_POST['pd_name'][$language_id])) {
                $pd_name = $_POST['pd_name'][$language_id];
                $pd_description = $_POST['pd_description'][$language_id];
                $pd_meta_title = $_POST['pd_meta_title'][$language_id];
                $pd_meta_description = $_POST['pd_meta_description'][$language_id];
                $pd_meta_keywords = $_POST['pd_meta_keywords'][$language_id];
              }
              else {
                $query_product_desc = "SELECT `product_description`.*
                                      FROM `product_description`
                                      WHERE `product_id` = '$current_product_id' AND `language_id` = '$language_id'";
                //echo $query_product_desc;exit;
                $result_product_desc = mysqli_query($db_link, $query_product_desc);
                if(!$result_product_desc) echo mysqli_error($db_link);
                if(mysqli_num_rows($result_product_desc) > 0) {
                  $product_desc_row = mysqli_fetch_assoc($result_product_desc);

                  $pd_name = stripslashes($product_desc_row['pd_name']);
                  $pd_description = stripslashes($product_desc_row['pd_description']);
                  $pd_overview = stripslashes($product_desc_row['pd_overview']);
                  $pd_novations = stripslashes($product_desc_row['pd_novations']);
                  $pd_system_requirements = stripslashes($product_desc_row['pd_system_requirements']);
                  $pd_meta_title = $product_desc_row['pd_meta_title'];
                  $pd_meta_description = $product_desc_row['pd_meta_description'];
                  $pd_meta_keywords = $product_desc_row['pd_meta_keywords'];
                }
                else {
                  $pd_name = "";
                  $pd_description = "";
                  $pd_overview = "";
                  $pd_novations = "";
                  $pd_system_requirements = "";
                  $pd_meta_title = "";
                  $pd_meta_description = "";
                  $pd_meta_keywords = "";
                }
              }
                
?>
          <div id="<?=$language_code;?>" class="language_tab tab" data-id="<?=$language_id;?>">
            <div>
              <label for="product_name" class="title"><?=$languages[$current_lang]['header_product_name'];?><span class="red">*</span></label>
              <?php
                if(isset($product_errors['pd_name'][$language_id])) {
                  echo "<div class='error'>".$product_errors['pd_name'][$language_id]."</div>";
                }
              ?>
              <input type="text" name="pd_name[<?=$language_id;?>]" id="pd_name_<?=$language_id;?>" style="width: 400px;" value="<?=$pd_name;?>" />
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_title" class="title"><?=$languages[$current_lang]['header_product_meta_title'];?></label>
              <input type="text" name="pd_meta_title[<?=$language_id;?>]" id="pd_meta_title_<?=$language_id;?>" onkeyup="CountCharacters(this,'100')" style="width: 60%;" value="<?=$pd_meta_title;?>" />
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_title'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_keywords" class="title"><?=$languages[$current_lang]['header_product_meta_keywords'];?></label>
              <input type="text" name="pd_meta_keywords[<?=$language_id;?>]" id="pd_meta_keywords_<?=$language_id;?>" style="width: 60%;" value="<?=$pd_meta_keywords;?>" />
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_keywords'];?></i>
            </div>
            <div class="clearfix"></div>
            
            <div>
              <label for="product_meta_description" class="title"><?=$languages[$current_lang]['header_product_meta_description'];?></label>
              <textarea name="pd_meta_description[<?=$language_id;?>]" id="pd_meta_description_<?=$language_id;?>" onkeyup="CountCharacters(this,'200')" style="width: 60%;"/><?=$pd_meta_description;?></textarea>
              <span class="info"><b></b></span>
              <span class="warning red" style="display: none;"><b><?=$languages[$current_lang]['meta_characters_warning'];?></b></span>
              <div class="clearfix"></div>
              <i class="info"><?=$languages[$current_lang]['info_product_meta_description'];?></i>
            </div>
            <div class="clearfix"></div>

            <div>
              <label for="product_description" class="title"><?=$languages[$current_lang]['header_product_description'];?></label>
              <textarea name="pd_description[<?=$language_id;?>]"><?=$pd_description;?></textarea>
            </div>
            
            <div>
              <label for="product_overview" class="title"><?=$languages[$current_lang]['header_product_overview'];?><span class="red">*</span></label>
              <textarea name="pd_overview[<?=$language_id;?>]"><?=$pd_overview;?></textarea>
            </div>
            
            <div>
              <label for="product_novations" class="title"><?=$languages[$current_lang]['header_product_novations'];?></label>
              <textarea name="pd_novations[<?=$language_id;?>]"><?=$pd_novations;?></textarea>
            </div>
            
            <div>
              <label for="product_system_requirements" class="title"><?=$languages[$current_lang]['header_product_system_requirements'];?><span class="red">*</span></label>
              <textarea name="pd_system_requirements[<?=$language_id;?>]"><?=$pd_system_requirements;?></textarea>
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
            <input type="text" name="product_trial_url" id="product_trial_url" style="width: 588px;" value="<?=$product_trial_url;?>" />
          </div>
          <div class="clearfix"></div>
            
          <div>
            <label for="product_categories" class="title"><?=$languages[$current_lang]['header_product_categories'];?><span class="red">*</span></label>
            <select name="select_categories" id="select_categories" style="width: 600px;" onChange="AddCategoryToProduct(this.value,'#new_categories_ids')">
              <option value="0"><?=$languages[$current_lang]['option_choose_categories_for_product'];?></option>
              <?php list_categories_in_select_for_products($parent_id = 0, $path_number = 0); ?> 
            </select>
            <ul id="categories_list" style="margin-top:6px;">
              <?=$category_li_block;?>
            </ul>
            <input type="hidden" name="is_there_old_categories_list" id="is_there_old_categories_list" value="<?=$is_there_old_categories_list;?>" />
            <input type="hidden" name="old_categories_list" id="old_categories_list" value="<?=$old_categories_list;?>" />
            <input type="hidden" name="new_categories_ids" id="new_categories_ids" value="" />
            <input type="hidden" id="choosen_category_already" value="<?=$languages[$current_lang]['choosen_category_already_warning'];?>" />
            <input type="hidden" id="category_is_not_choosable" value="<?=$languages[$current_lang]['category_is_not_choosable_warning'];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="product_is_active" class="title"><?=$languages[$current_lang]['header_product_is_active'];?></label>
            <?php
              if(isset($product_is_active) && $product_is_active == 0) echo '<input type="checkbox" name="product_is_active" id="product_is_active" />';
              else echo '<input type="checkbox" name="product_is_active" id="product_is_active" checked="checked" />';
            ?>
          </div>
          <div class="clearfix">&nbsp;</div>
          
        </div>
        
        <div id="product_images_tab" class="product_tab tab">
<?php
        $pi_names_array = get_product_images($current_product_id);
        $pd_images_folder = "/site/images/products/";
?>
          <h2><?=$languages[$current_lang]['header_product_images'];?></h2>
          <p><i><?=$languages[$current_lang]['info_product_image_default'];?></i></p>
          <ul id="sortable">
<?php
          if(isset($pi_names_array)) {

            foreach($pi_names_array as $prod_gallery_image) {
              //echo"<pre>";print_r($prod_gallery_image);
              $gallery_img_id = $prod_gallery_image['product_image_id'];
              $gallery_image = $prod_gallery_image['pi_name'];
              $gallery_image_exploded = explode(".", $gallery_image);
              $gallery_img_name = $gallery_image_exploded[0];
              $gallery_img_exstension = $gallery_image_exploded[1];
              $gallery_img_path_small = $pd_images_folder.$gallery_img_name."_home_default.".$gallery_img_exstension;
              @$gallery_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_path_small);
              $gallery_img_dimensions = $gallery_img_params[3];
?>
              <li id="gallery_image_<?=$gallery_img_id?>" data-id="<?=$gallery_img_id?>" class="ui-state-default">
                <input type="button" class="delete_img" data-id="<?=$gallery_img_id?>" data-image="<?=$gallery_image?>" data-type="2" value="<?=$languages[$current_lang]['btn_delete'];?>">
                <a class="move_img"><?=$languages[$current_lang]['btn_move'];?></a>
                <div class="clearfix"></div>
                <img src="<?=$gallery_img_path_small?>" class="dbx-handle" />
              </li>
<?php
            }
          }
?>
          </ul>
          <div class="clearfix"></div>
          <p>&nbsp;</p>
          
          <h2><?=$languages[$current_lang]['header_add_images'];?></h2>
          
          <div id="dropzone"></div>
        </div>
        <!--product_images_tab-->
        
        <div>
          <a href="javascript:;" onClick="EditProductMainTab('#product_main_tab')" class="save_product_tab button green">
            <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save_tab'];?>
          </a>
<!--          <button type="submit" name="save_product_all_tabs" class="save_product_all_tabs button red">
            <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save_all_tabs'];?>
          </button>-->
          <a href="/_admin/catalog/products-categories.php?category_id=<?=$current_category_id;?>" class="button blue">
            <i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?>
          </a>
        </div>
        <div class="clearfix">&nbsp;</div>
        
      </form>
    
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages[$current_lang]['ajaxmessage_update_product_tab_success'];?>" />
        <input type="hidden" name="product_id" id="product_id" value="<?=$current_product_id;?>" />
      </form>
      <div class="clearfix">&nbsp;</div>
    </div>
  </main>

  <!--modal_confirm_delete_img-->
  <div style="display:none;" id="modal_confirm_delete_img" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;">Сигурни ли сте, че искате да изтриете тази снимка?</p>
  </div>

  <!--modal_confirm_delete_product_attribute-->
  <div style="display:none;" id="modal_confirm_delete_product_attribute" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_products_attribute_warning'];?></p>
  </div>

  <!--modal_confirm_delete_option_value-->
  <div style="display:none;" id="modal_confirm_delete_option_value" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_products_option_warning'];?></p>
  </div>

  <!--modal_confirm_delete_discount-->
  <div style="display:none;" id="modal_confirm_delete_discount" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure'];?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_product_discount_warning'];?></p>
  </div>
<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetProductImages(<?=$current_product_id;?>);
            }
          });
          this.on("success", function(file, responseText) {
            if(responseText == "" || responseText == " ") {
              
            }
            else {
              alert(responseText);
              this.removeFile(file);
            }
          });
        }
      };
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $row_languages) {

          $language_id = $row_languages['language_id'];
?>
          CKEDITOR.replace('pd_description[<?=$language_id;?>]');
          CKEDITOR.replace('pd_overview[<?=$language_id;?>]');
          CKEDITOR.replace('pd_novations[<?=$language_id;?>]');
          CKEDITOR.replace('pd_system_requirements[<?=$language_id;?>]');
<?php
        }
      }
?>
      $("#sortable").sortable({
        placeholder: "ui-state-highlight"
      });
      $("#sortable").disableSelection();
  
      $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" });

      // products tab switcher
      $(".product_tabs li").removeClass("active");
      $(".product_tab").hide();
      $(".product_tabs li:first").addClass("active");
      $(".product_tab:first").show();
      $(".product_tabs a").click(function() {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        var ajax_fn = this_link.attr("ajax-fn");
        if(this_link.hasClass("images")) $(".dropzone").show();
        else $(".dropzone").hide();
        $(".save_product_tab").attr("onClick",""+ajax_fn+"('"+clicked_tab+"')");
        $(".product_tabs li").removeClass("active");
        this_link.parent().addClass("active");
        $(".product_tab").hide();
        $(clicked_tab).fadeIn();
        event.preventDefault();
      });
      // end products tab switcher
      
      // languages tab switcher
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
      // end languages tab switcher
      
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
      
      $("#modal_confirm_delete_img").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm_delete_img",
        buttons: {
          "<?=$languages[$current_lang]['btn_delete'];?>": function() {
            var image_id = $(".delete_img.active").attr("data-id");
            var image_data = $(".delete_img.active").attr("data-image");
            var image_type = $(".delete_img.active").attr("data-type");
            //alert(image_data);
            DeleteProductImage(image_id,image_data,image_type);
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(this).dialog("close");
          }
        }
      });
      
      $(".delete_img").click(function() {
        $(".delete_img").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_img").dialog("open");
      });
      
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
      
      $(".delete_option_value").click(function() {
        $(".delete_option_value").removeClass("active");
        $(this).addClass("active");
        $("#modal_confirm_delete_option_value").dialog("open");
      });
    });
  </script>
</body>
</html>
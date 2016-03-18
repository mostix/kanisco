<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: clients.php');
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
  
  if(isset($_POST['edit_client'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['client_name'] as $language_id => $client_name) {
      //if(empty($client_name)) $client_errors['client_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      //if(empty($_POST['client_text'][$language_id])) $client_errors['client_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $client_names_array[$language_id] = $_POST['client_name'][$language_id];
      $client_texts_array[$language_id] = $_POST['client_text'][$language_id];
      $client_links_array[$language_id] = $_POST['client_link'][$language_id];
      $client_has_record_array[$language_id] = $_POST['client_has_record_in_gb'][$language_id];
    }
    
    $client_is_active = 0;
    if(isset($_POST['client_is_active'])) $client_is_active = 1;
    
    if(!isset($client_errors)) {
      //if there are no form errors we can insert the information
      
      $query_update_client = "UPDATE `clients` SET `client_is_active`='$client_is_active' WHERE `client_id` = '$current_client_id'";
      $all_queries .= "<br>".$query_update_client;
      $result_update_client = mysqli_query($db_link, $query_update_client);
      if(!$result_update_client) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($client_names_array as $language_id => $client_name) {
        
        $client_has_record = $client_has_record_array[$language_id];
        $client_name_db = mysqli_real_escape_string($db_link, $client_name);
        $client_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $client_texts_array[$language_id]));
        $client_link_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $client_links_array[$language_id]));
      
        if($client_has_record == 1) {
          $query_update_client_desc = "UPDATE `clients_descriptions` SET `client_name`='$client_name_db',`client_text`=$client_text_db,`client_link`=$client_link_db
                                        WHERE `client_id` = '$current_client_id' AND `language_id` = '$language_id'";
          $all_queries .= "<br>".$query_update_client_desc;
          $result_update_client_desc = mysqli_query($db_link, $query_update_client_desc);
          if(!$result_update_client_desc) {
            echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          $query_insert_client_desc = "INSERT INTO `clients_descriptions`(`client_id`,`language_id`,`client_name`,`client_text`,`client_link`) 
                                                                  VALUES ('$current_client_id','$language_id','$client_name_db',$client_text_db,$client_link_db)";
          $all_queries .= "<br>".$query_insert_client_desc;
          $result_insert_client_desc = mysqli_query($db_link, $query_insert_client_desc);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: clients.php');
      
    } //if(!isset($client_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_client_details = "SELECT `client_id`,`client_image`,`client_is_active`,`client_sort_order`
                              FROM `clients`
                              WHERE `client_id` = '$current_client_id'";
    //echo $query_client_details;exit;
    $result_client_details = mysqli_query($db_link, $query_client_details);
    if(!$result_client_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_client_details) > 0) {
      $client_details = mysqli_fetch_assoc($result_client_details);

      $client_id = $client_details['client_id'];
      $client_image = $client_details['client_image'];
      $client_image_exploded = explode(".", $client_image);
      $client_image_name = $client_image_exploded[0];
      $client_image_exstension = $client_image_exploded[1];
      $client_image_thumb = "/site/images/clients/".$client_image_name.".".$client_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
  }
  
  $page_title = $languages[$current_lang]['text_client_edit'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/clients/clients.php"><?=$languages[$current_lang]['menu_clients'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['text_client_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['text_client_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?client_id=$current_client_id";?>">
        <p class="float_right">
          <button type="submit" name="edit_client" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_code = $row_languages['language_code'];
            $language_menu_name = $row_languages['language_menu_name'];
?>
            <li>
              <a href="#<?=$language_code;?>">
                <img src="/_admin/images/flags/<?=$language_code;?>.png" title="<?=$language_menu_name;?>" /> <?=$language_menu_name;?>
              </a>
            </li>
<?php
  }
}
?>
        </ul>
<?php
      if(!empty($languages_array)) {
        foreach($languages_array as $key => $row_languages) {

          $language_id = $row_languages['language_id'];
          $language_code = $row_languages['language_code'];
          
          $query_client_desc = "SELECT `client_name`,`client_text`,`client_link`
                                FROM `clients_descriptions` 
                                WHERE `client_id` = '$current_client_id' AND `language_id` = '$language_id'";
          $result_client_desc = mysqli_query($db_link, $query_client_desc);
          if(!$result_client_desc) { echo mysqli_error($db_link); }
          if(mysqli_num_rows($result_client_desc) > 0) {
            $client_desc = mysqli_fetch_assoc($result_client_desc);
            
            $client_names_array[$language_id] = $client_desc['client_name'];
            $client_texts_array[$language_id] = $client_desc['client_text'];
            $client_links_array[$language_id] = $client_desc['client_link'];
            $client_has_record_in_gb[$language_id] = 1;
          }
          else {
            $client_has_record_in_gb[$language_id] = 0;
          }
          
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div>
            <label for="client_name" class="title"><?=$languages[$current_lang]['header_name'];?></label>
            <?php
              if(isset($client_errors['client_name'][$language_id])) {
                echo "<div class='error'>".$client_errors['client_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="client_name[<?=$language_id;?>]" class="client_name" style="width: 400px;" value="<?php if(isset($client_names_array[$language_id])) { echo $client_names_array[$language_id]; }?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="client_link" class="title"><?=$languages[$current_lang]['header_link'];?></label>
            <?php
              if(isset($client_errors['client_link'][$language_id])) {
                echo "<div class='error'>".$client_errors['client_link'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="client_link[<?=$language_id;?>]" class="client_link" style="width: 400px;" value="<?php if(isset($client_links_array[$language_id])) echo $client_links_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div class="hidden">
            <label for="client_text" class="title"><?=$languages[$current_lang]['header_text'];?></label>
            <?php
              if(isset($client_errors['client_text'][$language_id])) {
                echo "<div class='error'>".$client_errors['client_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="client_text[<?=$language_id;?>]" id="ckeditor_client_text_<?=$language_code;?>" class="default_text"><?php if(isset($client_texts_array[$language_id])) echo $client_texts_array[$language_id];?></textarea>
          </div>
          <input type="hidden" name="client_has_record_in_gb[<?=$language_id;?>]" id="client_has_record_in_gb_<?=$language_code;?>" value="<?=$client_has_record_in_gb[$language_id];?>" >
          <div class="clearfix"></div>
        </div>
<?php
    }
  }
?>
        <div>
          <label for="client_is_active" class="title"><?=$languages[$current_lang]['header_status'];?></label>
          <?php
            if(isset($client_is_active)) {
              if($client_is_active == 0) echo '<input type="checkbox" name="client_is_active" id="client_is_active" />';
              else echo '<input type="checkbox" name="client_is_active" id="client_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="client_is_active" id="client_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>
        
        <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
        <p></p>
          
        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$client_image_thumb;?>" <?=$thumb_image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_image'];?></h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_client" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages[$current_lang]['ajaxmessage_update_product_tab_success'];?>" >
        <input type="hidden" name="client_id" id="client_id" value="<?=$current_client_id;?>" >
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages[$current_lang]['text_drag_and_drop_upload'];?>" >
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

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
              GetClientImage(<?=$current_client_id;?>);
            }
            this.removeFile(file);
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
//      if(!empty($languages_array)) {
//        foreach($languages_array as $row_languages) {
//
//          $language_code = $row_languages['language_code'];
//?>
//          CKEDITOR.replace('ckeditor_client_text_');
//<?php
//        }
//      }
?>
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
    });
  </script>
</body>
</html>
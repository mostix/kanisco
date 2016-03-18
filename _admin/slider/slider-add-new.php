<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: sliders.php');
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
  
  if(isset($_POST['add_slider'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['slider_header'] as $language_id => $slider_header) {
      //if(empty($slider_header)) $slider_errors['slider_header'][$language_id] = $languages[$current_lang]['required_field_error'];
      //if(empty($_POST['slider_text'][$language_id])) $slider_errors['slider_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $slider_headers_array[$language_id] = $_POST['slider_header'][$language_id];
      $slider_texts_array[$language_id] = $_POST['slider_text'][$language_id];
      $slider_links_array[$language_id] = $_POST['slider_link'][$language_id];
    }
    
    $slider_is_active = 0;
    if(isset($_POST['slider_is_active'])) $slider_is_active = 1;
    
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $slider_image_name = "";
    
    if(isset($_FILES['slider_image'])) {
      if($_FILES['slider_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['slider_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $product_errors['slider_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['slider_image']['size'] < MAX_FILE_SIZE) && ($_FILES['slider_image']['error'] == 0)) {
          // no error

          $slider_image_tmp_name  = $_FILES['slider_image']['tmp_name'];
          $slider_image_name = $_FILES['slider_image']['name'];
          $slider_image_name_exploded = explode(".", $slider_image_name);
          $image_name = $slider_image_name_exploded[0];
          $image_exstension = mb_convert_case($slider_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        }
        elseif(($_FILES['slider_image']['size'] > MAX_FILE_SIZE) || ($_FILES['slider_image']['error'] == 1 || $_FILES['slider_image']['error'] == 2)) {
          $product_errors['slider_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['slider_image']['error'] != 4) { // error 4 means no file was uploaded
            $product_errors['slider_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      } 
    }
        
    if(!isset($slider_errors)) {
      //if there are no form errors we can insert the information
      
      $slider_sort_order = get_slider_last_order_value()+1;
      
      $query_insert_slider = "INSERT INTO `sliders`(`slider_id`, `slider_image`, `slider_is_active`, `slider_sort_order`) 
                                            VALUES ('','$slider_image_name','$slider_is_active','$slider_sort_order')";
      $all_queries .= "<br>".$query_insert_slider;
      $result_insert_slider = mysqli_query($db_link, $query_insert_slider);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $slider_id = mysqli_insert_id($db_link);
      
      foreach($slider_headers_array as $language_id => $slider_header) {
        
        $slider_header_db = mysqli_real_escape_string($db_link, $slider_header);
        $slider_text_db = mysqli_real_escape_string($db_link, $slider_texts_array[$language_id]);
        $slider_link_db = mysqli_real_escape_string($db_link, $slider_links_array[$language_id]);
      
        $query_insert_slider_desc = "INSERT INTO `sliders_descriptions`(`slider_id`, `language_id`, `slider_header`, `slider_text`, `slider_link`) 
                                                                VALUES ('$slider_id','$language_id','$slider_header_db','$slider_text_db','$slider_link_db')";
        $all_queries .= "<br>".$query_insert_slider_desc;
        $result_insert_slider_desc = mysqli_query($db_link, $query_insert_slider_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/slider/";
    
      if(is_uploaded_file($slider_image_tmp_name)) {
        move_uploaded_file($slider_image_tmp_name, $upload_path.$slider_image_name);
      }
      else {
        echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $file = $upload_path.$slider_image_name;

      list($width,$height) = getimagesize($file);

      $image = new SimpleImage();
      $image->load($file);

      $image_site_name = $image_name."_site.".$image_exstension;
      $image_site = $upload_path.$image_site_name;

      $image_admin_thumb_name = $image_name."_admin_thumb.".$image_exstension;
      $image_admin_thumb = $upload_path.$image_admin_thumb_name;

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
        $image->resizeToWidth(940);

        $image->save($image_site,$image_type);

        $image->resizeToWidth(400);

        $image->save($image_admin_thumb,$image_type);

      }
      else {
        $image->resizeToHeight(380);

        $image->save($image_site,$image_type);

        $image->resizeToHeight(120);

        $image->save($image_admin_thumb,$image_type);
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: sliders.php');
      
    } //if(!isset($slider_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages[$current_lang]['slider_details_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/slider/sliders.php"><?=$languages[$current_lang]['header_sliders'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_slider_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_slider_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_slider" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div>
            <label for="slider_header" class="title"><?=$languages[$current_lang]['header_slider_header'];?></label>
            <?php
              if(isset($slider_errors['slider_header'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_header'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_header[<?=$language_id;?>]" class="slider_header" style="width: 400px;" value="<?php if(isset($slider_headers_array[$language_id])) echo $slider_headers_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>
          
          <div>
            <label for="slider_link" class="title"><?=$languages[$current_lang]['header_slider_link'];?></label>
            <?php
              if(isset($slider_errors['slider_link'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_link'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="slider_link[<?=$language_id;?>]" class="slider_link" style="width: 400px;" value="<?php if(isset($slider_links_array[$language_id])) echo $slider_links_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="slider_text" class="title"><?=$languages[$current_lang]['header_slider_text'];?></label>
            <?php
              if(isset($slider_errors['slider_text'][$language_id])) {
                echo "<div class='error'>".$slider_errors['slider_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="slider_text[<?=$language_id;?>]" id="ckeditor_slider_text_<?=$language_code;?>" class="default_text"><?php if(isset($slider_texts_array[$language_id])) echo $slider_texts_array[$language_id];?></textarea>
          </div>
          <div class="clearfix"></div>
        </div>
<?php
    }
  }
?>
        <div>
          <label for="slider_is_active" class="title"><?=$languages[$current_lang]['header_status'];?></label>
          <?php
            if(isset($slider_is_active)) {
              if($slider_is_active == 0) echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" />';
              else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="slider_is_active" id="slider_is_active" checked="checked" />';
          ?>
        </div>
        <div class="clearfix"><p>&nbsp;</p></div>
          
        <div>
          <label for="slider_image" class="title"><?=$languages[$current_lang]['header_add_image'];?></label>
          <?php
            if(isset($product_errors['slider_image'])) {
              echo "<div class='error'>".$product_errors['slider_image']."</div>";
            }
          ?>
          <p><input type="file" name="slider_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_slider" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
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
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {
              
              $language_code = $row_languages['language_code'];
?>
              CKEDITOR.replace('ckeditor_slider_text_<?=$language_code;?>');
<?php
    }
  }
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
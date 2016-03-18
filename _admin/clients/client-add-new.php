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
  
  if(isset($_POST['add_client'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['client_name'] as $language_id => $client_name) {
      
      $client_names_array[$language_id] = $_POST['client_name'][$language_id];
      $client_texts_array[$language_id] = $_POST['client_text'][$language_id];
      $client_links_array[$language_id] = $_POST['client_link'][$language_id];
    }
    
    $client_is_active = 0;
    if(isset($_POST['client_is_active'])) $client_is_active = 1;
    
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $client_image_name = "";
    
    if(isset($_FILES['client_image'])) {
      if($_FILES['client_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['client_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $product_errors['client_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['client_image']['size'] < MAX_FILE_SIZE) && ($_FILES['client_image']['error'] == 0)) {
          // no error

          $client_image_tmp_name  = $_FILES['client_image']['tmp_name'];
          $client_image_name = $_FILES['client_image']['name'];
          $client_image_name_exploded = explode(".", $client_image_name);
          $image_name = $client_image_name_exploded[0];
          $image_exstension = mb_convert_case($client_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
        }
        elseif(($_FILES['client_image']['size'] > MAX_FILE_SIZE) || ($_FILES['client_image']['error'] == 1 || $_FILES['client_image']['error'] == 2)) {
          $product_errors['client_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['client_image']['error'] != 4) { // error 4 means no file was uploaded
            $product_errors['client_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      } 
    }
        
    if(!isset($client_errors)) {
      //if there are no form errors we can insert the information
      
      $client_sort_order = get_client_last_order_value()+1;
      
      $query_insert_client = "INSERT INTO `clients`(`client_id`, `client_image`, `client_is_active`, `client_sort_order`) 
                                            VALUES ('','$client_image_name','$client_is_active','$client_sort_order')";
      $all_queries .= "<br>".$query_insert_client;
      $result_insert_client = mysqli_query($db_link, $query_insert_client);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $client_id = mysqli_insert_id($db_link);
      
      foreach($client_names_array as $language_id => $client_name) {
        
        $client_name_db = mysqli_real_escape_string($db_link, $client_name);
        $client_text_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $client_texts_array[$language_id]));
        $client_link_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $client_links_array[$language_id]));
      
        $query_insert_client_desc = "INSERT INTO `clients_descriptions`(`client_id`, `language_id`, `client_name`,`client_text`,`client_link`) 
                                                                VALUES ('$client_id','$language_id','$client_name_db',$client_text_db,$client_link_db)";
        $all_queries .= "<br>".$query_insert_client_desc;
        $result_insert_client_desc = mysqli_query($db_link, $query_insert_client_desc);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/clients/";
    
      if(is_uploaded_file($client_image_tmp_name)) {
        move_uploaded_file($client_image_tmp_name, $upload_path.$client_image_name);
      }
      else {
        echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $file = $upload_path.$client_image_name;

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
        $image->resizeToWidth(170);

        $image->save($image_site,$image_type);

        $image->resizeToWidth(170);

        $image->save($image_admin_thumb,$image_type);

      }
      else {
        $image->resizeToHeight(90);

        $image->save($image_site,$image_type);

        $image->resizeToHeight(90);

        $image->save($image_admin_thumb,$image_type);
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: clients.php');
      
    } //if(!isset($client_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages[$current_lang]['text_client_add_new'];
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
        <?=$languages[$current_lang]['text_client_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['text_client_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_client" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
            <label for="client_name" class="title"><?=$languages[$current_lang]['header_name'];?></label>
            <?php
              if(isset($client_errors['client_name'][$language_id])) {
                echo "<div class='error'>".$client_errors['client_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="client_name[<?=$language_id;?>]" class="client_name" style="width: 400px;" value="<?php if(isset($client_names_array[$language_id])) echo $client_names_array[$language_id];?>" />
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
          
        <div>
          <label for="client_image" class="title"><?=$languages[$current_lang]['header_add_image'];?></label>
          <?php
            if(isset($product_errors['client_image'])) {
              echo "<div class='error'>".$product_errors['client_image']."</div>";
            }
          ?>
          <p><input type="file" name="client_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_client" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
//          if(!empty($languages_array)) {
//            foreach($languages_array as $row_languages) {
//              
//              $language_code = $row_languages['language_code'];
//?>
//              CKEDITOR.replace('ckeditor_client_text_');
//<?php
//    }
//  }
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
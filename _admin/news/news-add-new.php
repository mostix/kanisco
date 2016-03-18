<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: news-categories.php');
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
  
  $news_category_id = 0;
  $news_post_date_month = false;
  $news_post_date_day = false;
  $news_post_date_year = false;
  $news_post_date_hour = false;
  $news_post_date_minute = false;
  $news_post_date_second = false;
  $news_expiry_start_date_month = false;
  $news_expiry_start_date_day = false;
  $news_expiry_start_date_year = false;
  $news_expiry_start_date_hour = false;
  $news_expiry_start_date_minute = false;
  $news_expiry_start_date_second = false;
  $news_expiry_end_date_month = false;
  $news_expiry_end_date_day = false;
  $news_expiry_end_date_year = false;
  $news_expiry_end_date_hour = false;
  $news_expiry_end_date_minute = false;
  $news_expiry_end_date_second = false;
  
  if(isset($_POST['add_news'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['news_title'] as $language_id => $news_title) {
      if(empty($news_title)) $news_errors['news_title'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['news_summary'][$language_id])) $news_errors['news_summary'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['news_text'][$language_id])) $news_errors['news_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $news_titles_array[$language_id] = $_POST['news_title'][$language_id];
      $news_summaries_array[$language_id] = $_POST['news_summary'][$language_id];
      $news_texts_array[$language_id] = $_POST['news_text'][$language_id];
    }
    
    // $_POST['news_category_params'] has three parameters - news_category_id, hierarchy_ids and hierarchy_level
    // but here we need only news_caregoriy_id
    $news_category_params = explode("+", $_POST['news_category_params']);
    $news_category_id = $news_category_params[0];
    $news_is_active = $_POST['news_is_active'];
    $news_post_date_year = $_POST['news_post_date_year'];
    $news_post_date_month = $_POST['news_post_date_month'];
    $news_post_date_day = $_POST['news_post_date_day'];
    $news_post_date_hour = $_POST['news_post_date_hour'];
    $news_post_date_minute = $_POST['news_post_date_minute'];
    $news_post_date_second = $_POST['news_post_date_second'];
    $news_post_date = "$news_post_date_year-$news_post_date_month-$news_post_date_day $news_post_date_hour:$news_post_date_minute:$news_post_date_second";
    $use_expiry_info = 0;
    if(isset($_POST['use_expiry_info'])) $use_expiry_info = 1;
    if($use_expiry_info == 1) {
      $news_expiry_start_date_year = $_POST['news_expiry_start_date_year'];
      $news_expiry_start_date_month = $_POST['news_expiry_start_date_month'];
      $news_expiry_start_date_day = $_POST['news_expiry_start_date_day'];
      $news_expiry_start_date_hour = $_POST['news_expiry_start_date_hour'];
      $news_expiry_start_date_minute = $_POST['news_expiry_start_date_minute'];
      $news_expiry_start_date_second = $_POST['news_expiry_start_date_second'];
      $news_expiry_end_date_year = $_POST['news_expiry_end_date_year'];
      $news_expiry_end_date_month = $_POST['news_expiry_end_date_month'];
      $news_expiry_end_date_day = $_POST['news_expiry_end_date_day'];
      $news_expiry_end_date_hour = $_POST['news_expiry_end_date_hour'];
      $news_expiry_end_date_minute = $_POST['news_expiry_end_date_minute'];
      $news_expiry_end_date_second = $_POST['news_expiry_end_date_second'];
      $news_start_time = "'$news_expiry_start_date_year-$news_expiry_start_date_month-$news_expiry_start_date_day $news_expiry_start_date_hour:$news_expiry_start_date_minute:$news_expiry_start_date_second'";
      $news_end_time = "'$news_expiry_end_date_year-$news_expiry_end_date_month-$news_expiry_end_date_day $news_expiry_end_date_hour:$news_expiry_end_date_minute:$news_expiry_end_date_second'";
    }
    else {
      $news_expiry_start_date_month = false;
      $news_expiry_start_date_day = false;
      $news_expiry_start_date_year = false;
      $news_expiry_start_date_hour = false;
      $news_expiry_start_date_minute = false;
      $news_expiry_start_date_second = false;
      $news_expiry_end_date_month = false;
      $news_expiry_end_date_day = false;
      $news_expiry_end_date_year = false;
      $news_expiry_end_date_hour = false;
      $news_expiry_end_date_minute = false;
      $news_expiry_end_date_second = false;
      $news_start_time = "NULL";
      $news_end_time = "NULL";
    }
    
    if($_POST['news_category_params'] == "0+0+0") $news_errors['news_category_params'] = $languages[$current_lang]['required_select_field_error'];
      
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    
    if(isset($_FILES['news_image'])) {
      if($_FILES['news_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['news_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $news_errors['news_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['news_image']['size'] < MAX_FILE_SIZE) && ($_FILES['news_image']['error'] == 0)) {
          // no error

          $news_image_tmp_name  = $_FILES['news_image']['tmp_name'];
          $news_image_name = $_FILES['news_image']['name'];
          $news_image_name_exploded = explode(".", $news_image_name);
          $image_name = $news_image_name_exploded[0];
          $image_exstension = mb_convert_case($news_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
          $news_image_name = "$image_name.$image_exstension";
        }
        elseif(($_FILES['news_image']['size'] > MAX_FILE_SIZE) || ($_FILES['news_image']['error'] == 1 || $_FILES['news_image']['error'] == 2)) {
          $news_errors['news_image'] = $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['news_image']['error'] != 4) { // error 4 means no file was uploaded
            $news_errors['news_image'] = $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      }
      else $news_errors['news_image'] = $languages[$current_lang]['required_select_field_error'];
    }
    
    if(!isset($news_errors)) {
      //if there are no form errors we can insert the information
    
      $news_author_id = $_SESSION['admin']['user_id'];
      $news_extra = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_extra']));

      $query_insert_news = "INSERT INTO `news`(`news_id`, 
                                                `news_category_id`, 
                                                `news_post_date`, 
                                                `news_start_time`, 
                                                `news_end_time`, 
                                                `news_is_active`, 
                                                `news_image`, 
                                                `news_created_date`, 
                                                `news_modified_date`, 
                                                `news_author_id`, 
                                                `news_extra`)
                                        VALUES ('',
                                                '$news_category_id',
                                                '$news_post_date',
                                                $news_start_time,
                                                $news_end_time,
                                                '$news_is_active',
                                                '$news_image_name',
                                                NOW(),
                                                NOW(),
                                                '$news_author_id',
                                                $news_extra)";
      $all_queries .= "<br>".$query_insert_news;
      $result_insert_news = mysqli_query($db_link, $query_insert_news);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $news_id = mysqli_insert_id($db_link);
      
      foreach($news_titles_array as $language_id => $news_title) {
        
        $news_title_db = mysqli_real_escape_string($db_link, $news_title);
        $news_summary_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_summaries_array[$language_id]));
        $news_text_db = mysqli_real_escape_string($db_link, $news_texts_array[$language_id]);
      
        $query_insert_news_descriptions = "INSERT INTO `news_descriptions`(`news_id`, 
                                                                          `language_id`, 
                                                                          `news_title`, 
                                                                          `news_summary`, 
                                                                          `news_text`)
                                                                  VALUES ('$news_id',
                                                                          '$language_id',
                                                                          '$news_title_db',
                                                                          $news_summary_db,
                                                                          '$news_text_db')";
        $all_queries .= "<br>".$query_insert_news_descriptions;
        $result_insert_news_descriptions = mysqli_query($db_link, $query_insert_news_descriptions);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/news/";
    
      if(is_uploaded_file($news_image_tmp_name)) {
        move_uploaded_file($news_image_tmp_name, $upload_path.$news_image_name);
      }
      else {
        echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $file = $upload_path.$news_image_name;
    
      list($width,$height) = getimagesize($file);

      $image = new SimpleImage();
      $image->load($file);

      $image_thumb_name = $image_name."_thumb.".$image_exstension;
      $image_thumb = $upload_path.$image_thumb_name;

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

      $image->resizeToWidth(368);

      $image->save($image_thumb,$image_type);

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: news.php');
      
    } //if(!isset($news_errors))
      
  } //if(isset($_POST['submit'])
  
  $page_title = $languages[$current_lang]['news_categories_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/news/news-categories.php" title="<?=$languages[$current_lang]['title_breadcrumbs_news_categories'];?>"><?=$languages[$current_lang]['header_news_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_news_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_news_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <div class="float_right">
          <button type="submit" name="add_news" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div>
          <label for="news_category_params" class="title"><?=$languages[$current_lang]['header_news_category'];?><span class="red">*</span></label>
          <?php
            if(isset($news_errors['news_category_params'])) {
              echo "<div class='error'>".$news_errors['news_category_params']."</div>";
            }
          ?>
          <select name="news_category_params" id="news_category_params" style="width: 50%;">
            <option value="0+0+0"><?=$languages[$current_lang]['option_choose_parent'];?></option>
<?php
            list_news_categories_for_select($news_category_id);
?> 
          </select>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_is_active" class="title"><?=$languages[$current_lang]['header_news_status'];?><span class="red">*</span></label>
          <select name="news_is_active" id="news_is_active" style="width: 200px;">
            <option value="0"><?=$languages[$current_lang]['header_news_status_draft'];?></option>
            <option value="1" selected="selected"><?=$languages[$current_lang]['header_news_status_published'];?></option>
          </select>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_post_date" class="title"><?=$languages[$current_lang]['header_news_post_date'];?></label>
 <?php
          list_date_months_in_select("news_post_date_month",$news_post_date_month);
          list_date_days_in_select("news_post_date_day",$news_post_date_day);
          list_date_years_in_select("news_post_date_year",$news_post_date_year);
          list_date_hours_in_select("news_post_date_hour",$news_post_date_hour);
          list_date_minutes_in_select("news_post_date_minute",$news_post_date_minute);
          list_date_seconds_in_select("news_post_date_second",$news_post_date_second);
 ?>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="use_expiry_info" class="title"><?=$languages[$current_lang]['header_news_expiry_info'];?></label>
          <input type="checkbox" name="use_expiry_info" id="use_expiry_info" onclick="ToggleCollapse('expiry_info');" <?php if(isset($use_expiry_info) && $use_expiry_info == 1) echo 'checked="checked"' ;?>>
        </div>
        <p class="clearfix"></p>
        
        <div id="expiry_info" style="<?php if(isset($use_expiry_info) && $use_expiry_info == 1) echo 'display: block;'; else echo "display: none;"; ;?>">
          <div>
            <label for="news_expiry_start_date" class="title"><?=$languages[$current_lang]['header_news_expiry_start_date'];?></label>
<?php
            list_date_months_in_select("news_expiry_start_date_month",$news_expiry_start_date_month);
            list_date_days_in_select("news_expiry_start_date_day",$news_expiry_start_date_day);
            list_date_years_in_select("news_expiry_start_date_year",$news_expiry_start_date_year);
            list_date_hours_in_select("news_expiry_start_date_hour",$news_expiry_start_date_hour);
            list_date_minutes_in_select("news_expiry_start_date_minute",$news_expiry_start_date_minute);
            list_date_seconds_in_select("news_expiry_start_date_second",$news_expiry_start_date_second);
 ?>        
          </div>
          <div>
            <label for="news_expiry_end_date" class="title"><?=$languages[$current_lang]['header_news_expiry_end_date'];?></label>
<?php
            list_date_months_in_select("news_expiry_end_date_month",$news_expiry_end_date_month);
            list_date_days_in_select("news_expiry_end_date_day",$news_expiry_end_date_day);
            list_date_years_in_select("news_expiry_end_date_year",$news_expiry_end_date_year);
            list_date_hours_in_select("news_expiry_end_date_hour",$news_expiry_end_date_hour);
            list_date_minutes_in_select("news_expiry_end_date_minute",$news_expiry_end_date_minute);
            list_date_seconds_in_select("news_expiry_end_date_second",$news_expiry_end_date_second);
 ?>        
          </div>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_extra" class="title"><?=$languages[$current_lang]['header_news_extra'];?></label>
          <input type="text" name="news_extra" id="news_extra" style="width: 400px;" value="<?php if(isset($_POST['news_extra'])) echo $_POST['news_extra'];?>">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="news_image" class="title"><?=$languages[$current_lang]['header_add_image'];?><span class="red">*</span></label>
          <?php
            if(isset($news_errors['news_image'])) {
              echo "<div class='error'>".$news_errors['news_image']."</div>";
            }
          ?>
          <p><input type="file" name="news_image" style="width: auto;" /></p>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <ul id="languages" class="language_tabs tabs">
<?php
        if(!empty($languages_array)) {
          foreach($languages_array as $row_languages) {

            $language_id = $row_languages['language_id'];
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
          $language_menu_name = $row_languages['language_menu_name'];
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div>
            <label for="news_title" class="title"><?=$languages[$current_lang]['header_news_title'];?><span class="red">*</span></label>
            <?php
              if(isset($news_errors['news_title'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_title'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="news_title[<?=$language_id;?>]" class="news_title" style="width: 400px;" value="<?php if(isset($_POST['news_title'][$language_id])) echo $_POST['news_title'][$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="news_summary" class="title"><?=$languages[$current_lang]['header_news_summary'];?></label>
            <textarea name="news_summary[<?=$language_id;?>]" id="ckeditor_news_summary_<?=$language_code;?>" class="default_text"><?php if(isset($_POST['news_summary'][$language_id])) echo $_POST['news_summary'][$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>

          <div>
            <label for="news_text" class="title"><?=$languages[$current_lang]['header_news_text'];?><span class="red">*</span></label>
            <?php
              if(isset($news_errors['news_text'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="news_text[<?=$language_id;?>]" id="ckeditor_news_text_<?=$language_code;?>" class="default_text"><?php if(isset($_POST['news_text'][$language_id])) echo $_POST['news_text'][$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
<?php
    }
  }
?>
        <div>
          <button type="submit" name="add_news" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
              CKEDITOR.replace('ckeditor_news_summary_<?=$language_code;?>');
              CKEDITOR.replace('ckeditor_news_text_<?=$language_code;?>');
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
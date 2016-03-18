<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: news.php');
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
  
  $content_type_id = 7; // news
  $query_content = "SELECT `content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_pretty_url = $content_array['content_pretty_url'];
  }
  
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
  
  if(isset($_POST['edit_news'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['news_title'] as $language_id => $news_title) {
//      if(empty($news_title)) $news_errors['news_title'][$language_id] = $languages[$current_lang]['required_field_error'];
//      if(empty($_POST['news_summary'][$language_id])) $news_errors['news_summary'][$language_id] = $languages[$current_lang]['required_field_error'];
//      if(empty($_POST['news_text'][$language_id])) $news_errors['news_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
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
    
    $news_extra = $_POST['news_extra'];
    
    if($_POST['news_category_params'] == "0+0+0") $news_errors['news_category_params'] = $languages[$current_lang]['required_select_field_error'];
    
    if(!isset($news_errors)) {
      //if there are no form errors we can insert the information

      $news_extra = prepare_for_null_row(mysqli_real_escape_string($db_link, $_POST['news_extra']));
      
      $query_update_news = "UPDATE `news` SET `news_category_id`='$news_category_id',
                                              `news_post_date`='$news_post_date',
                                              `news_start_time`=$news_start_time, 
                                              `news_end_time`=$news_end_time, 
                                              `news_is_active`='$news_is_active', 
                                              `news_modified_date`=NOW(), 
                                              `news_extra`=$news_extra
                                        WHERE `news_id` = '$current_news_id'";
      $all_queries .= "<br>".$query_update_news;
      $result_update_news = mysqli_query($db_link, $query_update_news);
      if(!$result_update_news) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($news_titles_array as $language_id => $news_title) {
        
        $news_title_db = mysqli_real_escape_string($db_link, $news_title);
        $news_summary_db = prepare_for_null_row(mysqli_real_escape_string($db_link, $news_summaries_array[$language_id]));
        $news_text_db = mysqli_real_escape_string($db_link, $news_texts_array[$language_id]);
      
        $query_update_news_desc = "UPDATE `news_descriptions` SET `news_title`='$news_title_db',
                                                                  `news_summary`=$news_summary_db,
                                                                  `news_text`='$news_text_db' 
                                                            WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
        $all_queries .= "<br>".$query_update_news_desc;
        $result_update_news_desc = mysqli_query($db_link, $query_update_news_desc);
        if(!$result_update_news_desc) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: news.php');
      
    } //if(!isset($news_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_news_details = "SELECT `news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                                  `news`.`news_is_active`,`news`.`news_image`,`news`.`news_created_date`,`news`.`news_modified_date`,`news`.`news_extra`,
                                  CONCAT(`users`.`user_firstname`,' ',`users`.`user_lastname`) as `user_fullname`
                            FROM `news` 
                            INNER JOIN `users` ON `users`.`user_id` = `news`.`news_author_id`
                            WHERE `news`.`news_id` = '$current_news_id'";
    //echo $query_news_details;exit;
    $result_news_details = mysqli_query($db_link, $query_news_details);
    if(!$result_news_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_news_details) > 0) {
      $news_details = mysqli_fetch_assoc($result_news_details);

      $news_category_id = $news_details['news_category_id'];
      $news_post_date = $news_details['news_post_date'];
      $news_post_date_month = date("m", strtotime($news_post_date));
      $news_post_date_day = date("d", strtotime($news_post_date));
      $news_post_date_year = date("Y", strtotime($news_post_date));
      $news_post_date_hour = date("H", strtotime($news_post_date));
      $news_post_date_minute = date("i", strtotime($news_post_date));
      $news_post_date_second = date("s", strtotime($news_post_date));
      $news_start_time = $news_details['news_start_time'];
      if(!is_null($news_start_time)) {
        $news_expiry_start_date_month = date("m", strtotime($news_start_time));
        $news_expiry_start_date_day = date("d", strtotime($news_start_time));
        $news_expiry_start_date_year = date("Y", strtotime($news_start_time));
        $news_expiry_start_date_hour = date("H", strtotime($news_start_time));
        $news_expiry_start_date_minute = date("i", strtotime($news_start_time));
        $news_expiry_start_date_second = date("s", strtotime($news_start_time));
      }
      $news_end_time = $news_details['news_end_time'];
      if(!is_null($news_start_time)) {
        $news_expiry_end_date_month = date("m", strtotime($news_end_time));
        $news_expiry_end_date_day = date("d", strtotime($news_end_time));
        $news_expiry_end_date_year = date("Y", strtotime($news_end_time));
        $news_expiry_end_date_hour = date("H", strtotime($news_end_time));
        $news_expiry_end_date_minute = date("i", strtotime($news_end_time));
        $news_expiry_end_date_second = date("s", strtotime($news_end_time)); 
      } 
      $use_expiry_info = (is_null($news_start_time)) ? 0 : 1;
      $news_is_active = $news_details['news_is_active'];
      $news_image = "";
      $thumb_image_dimensions = "";
      $news_images_folder = "/site/images/news/";
      if(!empty($news_details['news_image'])) {
        $news_image = $news_images_folder.$news_details['news_image'];
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image);
        @$thumb_image_dimensions = $thumb_image_params[3];
      }
      $news_created_date = $news_details['news_created_date'];
      $news_modified_date = $news_details['news_modified_date'];
      $news_extra = $news_details['news_extra'];
    }
  }
  
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
        <a href="/_admin/news/news.php" title="<?=$languages[$current_lang]['title_breadcrumbs_news_categories'];?>"><?=$languages[$current_lang]['header_news_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_news_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_news_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?news_id=$current_news_id";?>">
        <p class="float_right">
          <button type="submit" name="edit_news" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </p>
        
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
            <option value="0"<?php if($news_is_active == 0) echo ' selected="selected"';?>><?=$languages[$current_lang]['header_news_status_draft'];?></option>
            <option value="1"<?php if($news_is_active == 1) echo ' selected="selected"';?>><?=$languages[$current_lang]['header_news_status_published'];?></option>
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
          <input type="text" name="news_extra" id="news_extra" style="width: 400px;" value="<?php if(isset($news_extra)) echo $news_extra;?>">
        </div>
        <p class="clearfix">&nbsp;</p>
        
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
          
          if(isset($_POST['edit_news'])) {
            $news_titles_array[$language_id] = $_POST['news_title'][$language_id];
            $news_summaries_array[$language_id] = $_POST['news_summary'][$language_id];
            $news_texts_array[$language_id] = $_POST['news_text'][$language_id];
          }
          else {
            $query_news_desc = "SELECT `news_title`, `news_summary`, `news_text` 
                                FROM `news_descriptions` 
                                WHERE `news_id` = '$current_news_id' AND `language_id` = '$language_id'";
            $result_news_desc = mysqli_query($db_link, $query_news_desc);
            if(!$result_news_desc) echo mysqli_error($db_link);
            if(mysqli_num_rows($result_news_desc) > 0) {
              $news_desc = mysqli_fetch_assoc($result_news_desc);

              $news_titles_array[$language_id] = stripslashes($news_desc['news_title']);
              $news_summaries_array[$language_id] = stripcslashes($news_desc['news_summary']);
              $news_texts_array[$language_id] = stripslashes($news_desc['news_text']);
            }
          }
          
          $news_title_url = str_replace(" ", "-", mb_convert_case($news_titles_array[$language_id], MB_CASE_LOWER, "UTF-8"));
          $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?nid=$current_news_id";
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <a href="<?=$news_details_link;?>" target="_blank" style="position: relative;top: 8px;">
            <img src="/_admin/images/view.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_view'];?>" title="<?=$languages[$current_lang]['title_view'];?>" width="16" height="16" />
          </a>
          <div>
            <label for="news_title" class="title"><?=$languages[$current_lang]['header_news_title'];?></label>
            <?php
              if(isset($news_errors['news_title'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_title'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="news_title[<?=$language_id;?>]" class="news_title" style="width: 400px;" value="<?php if(isset($news_titles_array[$language_id])) echo $news_titles_array[$language_id];?>" />
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="news_summary" class="title"><?=$languages[$current_lang]['header_news_summary'];?></label>
            <textarea name="news_summary[<?=$language_id;?>]" id="ckeditor_news_summary_<?=$language_code;?>" class="default_text"><?php if(isset($news_summaries_array[$language_id])) echo $news_summaries_array[$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>

          <div>
            <label for="news_text" class="title"><?=$languages[$current_lang]['header_news_text'];?></label>
            <?php
              if(isset($news_errors['news_text'][$language_id])) {
                echo "<div class='error'>".$news_errors['news_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="news_text[<?=$language_id;?>]" id="ckeditor_news_text_<?=$language_code;?>" class="default_text"><?php if(isset($news_texts_array[$language_id])) echo $news_texts_array[$language_id];?></textarea>
          </div>
          <div class="clearfix">
            <p>&nbsp;</p>
          </div>
        </div>
<?php
    }
  }
?>
        <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
        <p></p>
        <p><i class="info"><?=$languages[$current_lang]['info_slider_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          
        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$news_image;?>" <?=$thumb_image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_image'];?></h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_news" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages[$current_lang]['ajaxmessage_update_product_tab_success'];?>" >
        <input type="hidden" name="news_id" id="news_id" value="<?=$current_news_id;?>" >
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
              GetNewsDefaultImage(<?=$current_news_id;?>);
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
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: events.php');
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
  
  if(isset($_POST['update_event'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['event_name'] as $language_id => $event_name) {
      if(empty($event_name)) $event_errors['event_name'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['event_summary'][$language_id])) $event_errors['event_summary'][$language_id] = $languages[$current_lang]['required_field_error'];
      if(empty($_POST['event_text'][$language_id])) $event_errors['event_text'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $event_names_array[$language_id] = $_POST['event_name'][$language_id];
      $event_summaries_array[$language_id] = $_POST['event_summary'][$language_id];
      $event_texts_array[$language_id] = $_POST['event_text'][$language_id];
    }
    
    $event_date = $_POST['event_date'];
      if(empty($event_date)) $event_errors['event_date'] = $languages[$current_lang]['required_field_error'];
    $event_start_time_hour = $_POST['event_start_time_hour'];
    $event_start_time_minute = $_POST['event_start_time_minute'];
    $event_time_start = "$event_start_time_hour:$event_start_time_minute";
    $event_end_time_hour = $_POST['event_end_time_hour'];
    $event_end_time_minute = $_POST['event_end_time_minute'];
    $event_time_end = "$event_end_time_hour:$event_end_time_minute";
    $event_cost = $_POST['event_cost'];
      if(empty($event_cost)) $event_errors['event_cost'] = $languages[$current_lang]['required_field_error'];
    $event_organizer_phone = $_POST['event_organizer_phone'];
    $event_organizer_email  = $_POST['event_organizer_email'];
    $event_map_address = $_POST['event_map_address'];
      if(empty($event_map_address)) $event_errors['event_map_address'] = $languages[$current_lang]['required_field_error'];
    $event_map_lat = $_POST['event_map_lat'];
      if(empty($event_map_lat)) $event_errors['event_map_lat'] = $languages[$current_lang]['required_field_error'];
    $event_map_lng = $_POST['event_map_lng'];
      if(empty($event_map_lng)) $event_errors['event_map_lng'] = $languages[$current_lang]['required_field_error'];
    $event_phone = $_POST['event_phone'];
      if(empty($event_phone)) $event_errors['event_phone'] = $languages[$current_lang]['required_field_error'];
    $event_is_active = 0;
      if(isset($_POST['event_is_active'])) $event_is_active= 1;
    
    if(!isset($event_errors)) {
      //if there are no form errors we can insert the information
      
      $event_map_address_db = mysqli_real_escape_string($db_link, $event_map_address);

       $query_update_event = "UPDATE `events` SET `event_date`='$event_date',
                                                  `event_time_start`='$event_time_start', 
                                                  `event_time_end`='$event_time_end', 
                                                  `event_cost`='$event_cost', 
                                                  `event_organizer_phone`='$event_organizer_phone', 
                                                  `event_organizer_email`='$event_organizer_email', 
                                                  `event_map_address`='$event_map_address_db', 
                                                  `event_phone`='$event_phone', 
                                                  `event_map_lat`='$event_map_lat', 
                                                  `event_map_lng`='$event_map_lng', 
                                                  `event_is_active`='$event_is_active'
                                            WHERE `event_id` = '$current_event_id'";
      $all_queries .= "<br>".$query_update_event;
      $result_update_event = mysqli_query($db_link, $query_update_event);
      if(!$result_update_event) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $event_id = mysqli_insert_id($db_link);
      
      foreach($event_names_array as $language_id => $event_name) {
        
        $event_name_db = mysqli_real_escape_string($db_link, $event_name);
        $event_summary_db = mysqli_real_escape_string($db_link, $event_summaries_array[$language_id]);
        $event_text_db = mysqli_real_escape_string($db_link, $event_texts_array[$language_id]);
        
        $query_update_event_desc = "UPDATE `events_descriptions` SET `event_name`='$event_name_db',
                                                                    `event_summary`='$event_summary_db', 
                                                                    `event_text`='$event_text_db' 
                                                              WHERE `event_id` = '$current_event_id' AND `language_id` = '$language_id'";
        $all_queries .= "<br>".$query_update_event_desc;
        $result_update_event_desc = mysqli_query($db_link, $query_update_event_desc);
        if(!$result_update_event_desc) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: events.php');
      
    } //if(!isset($event_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_event = "SELECT `events`.*
                    FROM `events` 
                    WHERE `events`.`event_id` = '$current_event_id'";
    //echo $query_event;exit;
    $result_event = mysqli_query($db_link, $query_event);
    if(!$result_event) echo mysqli_error($db_link);
    $event_count = mysqli_num_rows($result_event);
    if($event_count > 0) {
      $event_row = mysqli_fetch_assoc($result_event);

      $event_id = $event_row['event_id'];
      $event_date = date("Y-m-d", strtotime($event_row['event_date']));
      $event_start_time_hour = date("H", strtotime($event_row['event_time_start']));
      $event_start_time_minute = date("i", strtotime($event_row['event_time_start']));
      $event_end_time_hour = date("H", strtotime($event_row['event_time_end']));
      $event_end_time_minute = date("i", strtotime($event_row['event_time_end']));
      $event_cost = $event_row['event_cost'];
      $event_organizer_phone = $event_row['event_organizer_phone'];
      $event_organizer_email = $event_row['event_organizer_email'];
      $event_map_address = $event_row['event_map_address'];
      $event_phone = $event_row['event_phone'];
      $event_map_lat = $event_row['event_map_lat'];
      $event_map_lng = $event_row['event_map_lng'];
      $event_is_active = $event_row['event_is_active'];
      $event_image = "";
      $thumb_image_dimensions = "";
      $event_images_folder = "/site/images/events/";
      if(!empty($event_row['event_image'])) {
        $event_image = $event_images_folder.$event_row['event_image'];
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$event_image);
        @$thumb_image_dimensions = $thumb_image_params[3];
      }
    }
  }
  
  $page_title = $languages[$current_lang]['event_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/events/events.php" title="<?=$languages[$current_lang]['title_breadcrumbs_event_categories'];?>"><?=$languages[$current_lang]['header_events'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_event_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_event_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?event_id=$current_event_id";?>" enctype="multipart/form-data">
        <div class="float_right">
          <button type="submit" name="update_event" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div>
          <label for="event_date" class="title"><?=$languages[$current_lang]['header_event_date'];?><span class="red">*</span></label>
          <input type="text" name="event_date" class="datepicker" value="<?php if(isset($event_date)) echo $event_date;?>" style="width: 160px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_start_time" class="title"><?=$languages[$current_lang]['header_event_start_time'];?><span class="red">*</span></label>
<?php
          list_date_hours_in_select("event_start_time_hour",$event_start_time_hour);
          list_date_minutes_in_select("event_start_time_minute",$event_start_time_minute);
?>        
        </div>
        <div>
          <label for="event_end_time" class="title"><?=$languages[$current_lang]['header_event_end_time'];?><span class="red">*</span></label>
<?php
          list_date_hours_in_select("event_end_time_hour",$event_end_time_hour);
          list_date_minutes_in_select("event_end_time_minute",$event_end_time_minute);
?>        
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_cost" class="title"><?=$languages[$current_lang]['header_event_cost'];?><span class="red">*</span></label>
          <input type="text" name="event_cost" id="event_cost" value="<?php if(isset($event_cost)) echo $event_cost;?>" style="width: 60px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_organizer_phone" class="title"><?=$languages[$current_lang]['header_event_organizer_phone'];?></label>
          <input type="text" name="event_organizer_phone" id="event_organizer_phone" value="<?php if(isset($event_organizer_phone)) echo $event_organizer_phone;?>" style="width: 200px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_organizer_email" class="title"><?=$languages[$current_lang]['header_event_organizer_email'];?></label>
          <input type="text" name="event_organizer_email" id="event_organizer_email" value="<?php if(isset($event_organizer_email)) echo $event_organizer_email;?>" style="width: 300px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_map_address" class="title"><?=$languages[$current_lang]['header_address'];?><span class="red">*</span></label>
          <input type="text" name="event_map_address" id="event_map_address" value="<?php if(isset($event_map_address)) echo $event_map_address;?>" style="width: 300px;">
          <a id="get_coords" class="button blue" onClick="SetPositionCoords()">Get location</a>
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_map_lat" class="title">Latitude<span class="red">*</span></label> 
          <input type="text" name="event_map_lat" id="event_map_lat" value="<?php if(isset($event_map_lat)) echo $event_map_lat; ?>" style="width: 300px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_map_lng" class="title">Longitude<span class="red">*</span></label>
          <input type="text" name="event_map_lng" id="event_map_lng" value="<?php if(isset($event_map_lng)) echo $event_map_lng; ?>" style="width: 300px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_phone" class="title"><?=$languages[$current_lang]['header_event_phone'];?></label>
          <input type="text" name="event_phone" id="event_phone" value="<?php if(isset($event_phone)) echo $event_phone;?>" style="width: 200px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="event_is_active" class="title"><?=$languages[$current_lang]['header_status'];?><span class="red">*</span></label>
          <?php
            if(isset($event_is_active)) {
              if($event_is_active == 0) echo '<input type="checkbox" name="event_is_active" id="event_is_active" />';
              else echo '<input type="checkbox" name="event_is_active" id="event_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="event_is_active" id="event_is_active" checked="checked" />';
          ?>
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
          
          $query_event_desc = "SELECT `event_name`,`event_summary`,`event_text` 
                              FROM `events_descriptions` 
                              WHERE `event_id` = '$current_event_id' AND `language_id` = '$language_id'";
          $result_event_desc = mysqli_query($db_link, $query_event_desc);
          if(!$result_event_desc) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_event_desc) > 0) {
            $event_desc = mysqli_fetch_assoc($result_event_desc);
            
            $event_names_array[$language_id] = $event_desc['event_name'];
            $event_summaries_array[$language_id] = $event_desc['event_summary'];
            $event_texts_array[$language_id] = $event_desc['event_text'];
          }
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div>
            <label for="event_name" class="title"><?=$languages[$current_lang]['header_event_name'];?><span class="red">*</span></label>
            <?php
              if(isset($event_errors['event_name'][$language_id])) {
                echo "<div class='error'>".$event_errors['event_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="event_name[<?=$language_id;?>]" class="event_name" value="<?php if(isset($event_names_array[$language_id])) echo $event_names_array[$language_id];?>" style="width: 400px;">
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="event_summary" class="title"><?=$languages[$current_lang]['header_event_summary'];?><span class="red">*</span></label>
            <?php
              if(isset($event_errors['event_summary'][$language_id])) {
                echo "<div class='error'>".$event_errors['event_summary'][$language_id]."</div>";
              }
            ?>
            <textarea name="event_summary[<?=$language_id;?>]" id="ckeditor_event_summary_<?=$language_code;?>" class="default_text">
              <?php if(isset($event_summaries_array[$language_id])) echo stripslashes($event_summaries_array[$language_id]);?>
            </textarea>
          </div>

          <div>
            <label for="event_text" class="title"><?=$languages[$current_lang]['header_event_text'];?><span class="red">*</span></label>
            <?php
              if(isset($event_errors['event_text'][$language_id])) {
                echo "<div class='error'>".$event_errors['event_text'][$language_id]."</div>";
              }
            ?>
            <textarea name="event_text[<?=$language_id;?>]" id="ckeditor_event_text_<?=$language_code;?>" class="default_text">
              <?php if(isset($event_texts_array[$language_id])) echo stripslashes($event_texts_array[$language_id]);?>
            </textarea>
          </div>
        </div>
<?php
        } //foreach($languages_array as $key => $row_languages)
      }
?>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
        <p></p>
        <p><i class="info"><?=$languages[$current_lang]['info_slider_image']." ".$languages[$current_lang]['btn_save'];?></i></p>

        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$event_image;?>" width="700" height="auto">
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_image'];?> (351x215px)</h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="update_event" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage" value="" >
        <input type="hidden" name="event_image" id="event_image" value="<?=$event_row['event_image'];?>" >
        <input type="hidden" name="event_id" id="event_id" value="<?=$current_event_id;?>" >
        <input type="hidden" id="text_drag_and_drop_upload" value="<?=$languages[$current_lang]['text_drag_and_drop_upload'];?>" >
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      Dropzone.options.filedrop = {
        dictDefaultMessage: $("#text_drag_and_drop_upload").val(),
        init: function () {
          this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
              GetEventImage(<?=$current_event_id;?>);
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
      
      $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" });
<?php
          if(!empty($languages_array)) {
            foreach($languages_array as $row_languages) {
              
              $language_code = $row_languages['language_code'];
?>
              CKEDITOR.replace('ckeditor_event_summary_<?=$language_code;?>');
              CKEDITOR.replace('ckeditor_event_text_<?=$language_code;?>');
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
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: courses.php');
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
  
  if(isset($_POST['update_course'])) {
    
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    foreach($_POST['cd_name'] as $language_id => $cd_name) {
      if(empty($cd_name)) $course_errors['cd_name'][$language_id] = $languages[$current_lang]['required_field_error'];
//      if(empty($_POST['cd_description'][$language_id])) $course_errors['cd_description'][$language_id] = $languages[$current_lang]['required_field_error'];
//      if(empty($_POST['cd_program'][$language_id])) $course_errors['cd_program'][$language_id] = $languages[$current_lang]['required_field_error'];
      
      $cd_names_array[$language_id] = $_POST['cd_name'][$language_id];
      $cd_descriptions_array[$language_id] = $_POST['cd_description'][$language_id];
      $cd_programs_array[$language_id] = $_POST['cd_program'][$language_id];
    }
    
    $course_date = $_POST['course_date'];
      if(empty($course_date)) $course_errors['course_date'] = $languages[$current_lang]['required_field_error'];
    $course_duration = $_POST['course_duration'];
      if(empty($course_duration)) $course_errors['course_duration'] = $languages[$current_lang]['required_field_error'];
    $course_cost = $_POST['course_cost'];
      if(empty($course_cost)) $course_errors['course_cost'] = $languages[$current_lang]['required_field_error'];
    $course_is_active = 0;
      if(isset($_POST['course_is_active'])) $course_is_active= 1;
    
    if(!isset($course_errors)) {
      //if there are no form errors we can insert the information

       $query_update_course = "UPDATE `courses` SET `course_date`='$course_date',
                                                      `course_duration`='$course_duration', 
                                                      `course_cost`='$course_cost', 
                                                      `course_is_active`='$course_is_active'
                                                WHERE `course_id` = '$current_course_id'";
      $all_queries .= "<br>".$query_update_course;
      $result_update_course = mysqli_query($db_link, $query_update_course);
      if(!$result_update_course) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $course_id = mysqli_insert_id($db_link);
      
      foreach($cd_names_array as $language_id => $cd_name) {
        
        $cd_name_db = mysqli_real_escape_string($db_link, $cd_name);
        $cd_description_db = mysqli_real_escape_string($db_link, $cd_descriptions_array[$language_id]);
        $cd_program_db = mysqli_real_escape_string($db_link, $cd_programs_array[$language_id]);
        
        $query_update_course_desc = "UPDATE `courses_descriptions` SET `cd_name`='$cd_name_db',
                                                                    `cd_description`='$cd_description_db', 
                                                                    `cd_program`='$cd_program_db' 
                                                              WHERE `course_id` = '$current_course_id' AND `language_id` = '$language_id'";
        $all_queries .= "<br>".$query_update_course_desc;
        $result_update_course_desc = mysqli_query($db_link, $query_update_course_desc);
        if(!$result_update_course_desc) {
          echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: courses.php');
      
    } //if(!isset($course_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_course = "SELECT `courses`.*
                    FROM `courses` 
                    WHERE `courses`.`course_id` = '$current_course_id'";
    //echo $query_course;exit;
    $result_course = mysqli_query($db_link, $query_course);
    if(!$result_course) echo mysqli_error($db_link);
    $course_count = mysqli_num_rows($result_course);
    if($course_count > 0) {
      $course_row = mysqli_fetch_assoc($result_course);

      $course_id = $course_row['course_id'];
      $course_date = date("Y-m-d", strtotime($course_row['course_date']));
      $course_duration = $course_row['course_duration'];
      $course_cost = $course_row['course_cost'];
      $course_is_active = $course_row['course_is_active'];
      $course_image = "";
      $thumb_image_dimensions = "";
      $course_images_folder = "/site/images/courses/";
      if(!empty($course_row['course_image'])) {
        $course_image = $course_images_folder.$course_row['course_image'];
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$course_image);
        @$thumb_image_dimensions = $thumb_image_params[3];
      }
    }
  }
  
  $page_title = $languages[$current_lang]['course_add_new_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/courses/courses.php"><?=$languages[$current_lang]['header_courses'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_course_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_course_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?course_id=$current_course_id";?>" enctype="multipart/form-data">
        <div class="float_right">
          <button type="submit" name="update_course" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div>
          <label for="course_date" class="title"><?=$languages[$current_lang]['header_date'];?><span class="red">*</span></label>
          <input type="text" name="course_date" class="datepicker" value="<?php if(isset($course_date)) echo $course_date;?>" style="width: 160px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <?php
            if(isset($product_errors['course_duration'])) {
              echo "<div class='error'>".$product_errors['course_duration']."</div>";
            }
          ?>
          <label for="course_duration" class="title"><?=$languages[$current_lang]['header_duration'];?><span class="red">*</span></label>
          <input type="text" name="course_duration" id="course_duration" value="<?php if(isset($course_duration)) echo $course_duration;?>" style="width: 600px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="course_cost" class="title"><?=$languages[$current_lang]['header_cost'];?><span class="red">*</span></label>
          <input type="text" name="course_cost" id="course_cost" value="<?php if(isset($course_cost)) echo $course_cost;?>" style="width: 60px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <label for="course_is_active" class="title"><?=$languages[$current_lang]['header_status'];?><span class="red">*</span></label>
          <?php
            if(isset($course_is_active)) {
              if($course_is_active == 0) echo '<input type="checkbox" name="course_is_active" id="course_is_active" />';
              else echo '<input type="checkbox" name="course_is_active" id="course_is_active" checked="checked" />';
            }
            else echo '<input type="checkbox" name="course_is_active" id="course_is_active" checked="checked" />';
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
          
          $query_course_desc = "SELECT `cd_name`,`cd_description`,`cd_program` 
                                  FROM `courses_descriptions` 
                                  WHERE `course_id` = '$current_course_id' AND `language_id` = '$language_id'";
          $result_course_desc = mysqli_query($db_link, $query_course_desc);
          if(!$result_course_desc) echo mysqli_error($db_link);
          if(mysqli_num_rows($result_course_desc) > 0) {
            $course_desc = mysqli_fetch_assoc($result_course_desc);
            
            $cd_names_array[$language_id] = $course_desc['cd_name'];
            $cd_descriptions_array[$language_id] = $course_desc['cd_description'];
            $cd_programs_array[$language_id] = $course_desc['cd_program'];
          }
?>
        <div id="<?=$language_code;?>" class="language_tab tab">
          <div>
            <label for="cd_name" class="title"><?=$languages[$current_lang]['header_name'];?><span class="red">*</span></label>
            <?php
              if(isset($course_errors['cd_name'][$language_id])) {
                echo "<div class='error'>".$course_errors['cd_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="cd_name[<?=$language_id;?>]" class="cd_name" value="<?php if(isset($cd_names_array[$language_id])) echo $cd_names_array[$language_id];?>" style="width: 400px;">
          </div>
          <div class="clearfix"></div>

          <div>
            <label for="cd_description" class="title"><?=$languages[$current_lang]['header_short_description'];?></label>
            <?php
              if(isset($course_errors['cd_description'][$language_id])) {
                echo "<div class='error'>".$course_errors['cd_description'][$language_id]."</div>";
              }
            ?>
            <textarea name="cd_description[<?=$language_id;?>]" id="ckeditor_cd_description_<?=$language_code;?>" class="default_text">
              <?php if(isset($cd_descriptions_array[$language_id])) echo stripslashes($cd_descriptions_array[$language_id]);?>
            </textarea>
          </div>

          <div>
            <label for="cd_program" class="title"><?=$languages[$current_lang]['header_course_program'];?></label>
            <?php
              if(isset($course_errors['cd_program'][$language_id])) {
                echo "<div class='error'>".$course_errors['cd_program'][$language_id]."</div>";
              }
            ?>
            <textarea name="cd_program[<?=$language_id;?>]" id="ckeditor_cd_program_<?=$language_code;?>" class="default_text">
              <?php if(isset($cd_programs_array[$language_id])) echo stripslashes($cd_programs_array[$language_id]);?>
            </textarea>
            <div class="clearfix">
              <p>&nbsp;</p>
            </div>
          </div>
          
          <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
          <p></p>
          <p><i class="info"><?=$languages[$current_lang]['info_slider_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          
          <div id="dropzone" style="padding-bottom: 410px;">
            <div id="current_image">
              <img src="<?=$course_image;?>" width="700" height="auto">
            </div>
            <p>&nbsp;</p>
            <h2><?=$languages[$current_lang]['header_change_image'];?></h2>
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
          <button type="submit" name="update_course" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage" value="<?=$languages[$current_lang]['ajaxmessage_course_image_update_success'];?>" >
        <input type="hidden" name="course_image" id="course_image" value="<?=$course_row['course_image'];?>" >
        <input type="hidden" name="course_id" id="course_id" value="<?=$current_course_id;?>" >
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
              GetCourseImage(<?=$current_course_id;?>);
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
              CKEDITOR.replace('ckeditor_cd_description_<?=$language_code;?>');
              CKEDITOR.replace('ckeditor_cd_program_<?=$language_code;?>');
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
        course.prcourseDefault();
      });
      // end language tab switcher
    });
  </script>
</body>
</html>
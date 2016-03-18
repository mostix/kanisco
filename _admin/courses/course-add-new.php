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
  
  if(isset($_POST['add_course'])) {
    
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
    
      
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    $course_image_name = "";
    
    if(isset($_FILES['course_image']) && $_FILES['course_image']['error'] != 4) {
      $extension_array = explode("/", $_FILES['course_image']['type']);
      $extension = $extension_array[1];
      if(!in_array($extension, $valid_formats)) {
        $product_errors['course_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
      }

      if(($_FILES['course_image']['size'] < MAX_FILE_SIZE) && ($_FILES['course_image']['error'] == 0)) {
        // no error

        $course_image_tmp_name  = $_FILES['course_image']['tmp_name'];
        $course_image_name = $_FILES['course_image']['name'];
        $course_image_name_exploded = explode(".", $course_image_name);
        $image_name = $course_image_name_exploded[0];
        $image_exstension = mb_convert_case($course_image_name_exploded[1], MB_CASE_LOWER, "UTF-8");
      }
      elseif(($_FILES['course_image']['size'] > MAX_FILE_SIZE) || ($_FILES['course_image']['error'] == 1 || $_FILES['course_image']['error'] == 2)) {
        $product_errors['course_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
      }
      else {
        if($_FILES['course_image']['error'] != 4) { // error 4 means no file was uploaded
          $product_errors['course_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
        }
      }
    }
    
    if(!isset($course_errors)) {
      //if there are no form errors we can insert the information
    
      $course_author_id = $_SESSION['admin']['user_id'];

      $query_insert_course = "INSERT INTO `courses`(`course_id`, 
                                                      `course_image`, 
                                                      `course_date`, 
                                                      `course_duration`, 
                                                      `course_cost`, 
                                                      `course_is_active`, 
                                                      `course_author_id`)
                                              VALUES ('',
                                                      '$course_image_name',
                                                      '$course_date',
                                                      '$course_duration',
                                                      '$course_cost',
                                                      '$course_is_active',
                                                      '$course_author_id')";
      $all_queries .= "<br>".$query_insert_course;
      $result_insert_course = mysqli_query($db_link, $query_insert_course);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $course_id = mysqli_insert_id($db_link);
      
      foreach($cd_names_array as $language_id => $cd_name) {
        
        $cd_name_db = mysqli_real_escape_string($db_link, $cd_name);
        $cd_description_db = mysqli_real_escape_string($db_link, $cd_descriptions_array[$language_id]);
        $cd_program_db = mysqli_real_escape_string($db_link, $cd_programs_array[$language_id]);
      
        $query_insert_courses_descriptions = "INSERT INTO `courses_descriptions`(`course_id`, 
                                                                                `language_id`, 
                                                                                `cd_name`, 
                                                                                `cd_description`, 
                                                                                `cd_program`)
                                                                        VALUES ('$course_id',
                                                                                '$language_id',
                                                                                '$cd_name_db',
                                                                                '$cd_description_db',
                                                                                '$cd_program_db')";
        $all_queries .= "<br>".$query_insert_courses_descriptions;
        $result_insert_courses_descriptions = mysqli_query($db_link, $query_insert_courses_descriptions);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      if(isset($_FILES['course_image']) && $_FILES['course_image']['error'] != 4) {
        
        $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/courses/";

        if(is_uploaded_file($course_image_tmp_name)) {
          move_uploaded_file($course_image_tmp_name, $upload_path.$course_image_name);
        }
        else {
          echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

        $file = $upload_path.$course_image_name;

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

        $image->resizeToWidth(351);

        $image->save($image_thumb,$image_type);
        
      }

      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: courses.php');
      
    } //if(!isset($course_errors))
      
  } //if(isset($_POST['submit'])
  
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
        <a href="/_admin/courses/courses.php" title="<?=$languages[$current_lang]['title_breadcrumbs_course_categories'];?>"><?=$languages[$current_lang]['header_courses'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_course_add_new'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_course_add_new'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <div class="float_right">
          <button type="submit" name="add_course" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>
        
        <div>
          <?php
            if(isset($product_errors['course_date'])) {
              echo "<div class='error'>".$product_errors['course_date']."</div>";
            }
          ?>
          <label for="course_date" class="title"><?=$languages[$current_lang]['header_date'];?><span class="red">*</span></label>
          <input type="text" name="course_date" class="datepicker" value="<?php if(isset($_POST['course_date'])) echo $_POST['course_date'];?>" style="width: 160px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <?php
            if(isset($product_errors['course_duration'])) {
              echo "<div class='error'>".$product_errors['course_duration']."</div>";
            }
          ?>
          <label for="course_duration" class="title"><?=$languages[$current_lang]['header_duration'];?><span class="red">*</span></label>
          <input type="text" name="course_duration" id="course_duration" value="<?php if(isset($_POST['course_duration'])) echo $_POST['course_duration'];?>" style="width: 600px;">
        </div>
        <p class="clearfix"></p>
        
        <div>
          <?php
            if(isset($product_errors['course_cost'])) {
              echo "<div class='error'>".$product_errors['course_cost']."</div>";
            }
          ?>
          <label for="course_cost" class="title"><?=$languages[$current_lang]['header_cost'];?><span class="red">*</span></label>
          <input type="text" name="course_cost" id="course_cost" value="<?php if(isset($_POST['course_cost'])) echo $_POST['course_cost'];?>" style="width: 60px;">
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
        <p class="clearfix"></p>
        
        <div>
          <label for="course_image" class="title"><?=$languages[$current_lang]['header_add_image'];?></label>
          <?php
            if(isset($product_errors['course_image'])) {
              echo "<div class='error'>".$product_errors['course_image']."</div>";
            }
          ?>
          <p><input type="file" name="course_image" style="width: auto;" /></p>
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
            <label for="cd_name" class="title"><?=$languages[$current_lang]['header_name'];?><span class="red">*</span></label>
            <?php
              if(isset($course_errors['cd_name'][$language_id])) {
                echo "<div class='error'>".$course_errors['cd_name'][$language_id]."</div>";
              }
            ?>
            <input type="text" name="cd_name[<?=$language_id;?>]" class="cd_name" value="<?php if(isset($_POST['cd_name'][$language_id])) echo $_POST['cd_name'][$language_id];?>" style="width: 400px;">
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
              <?php if(isset($_POST['cd_description'][$language_id])) echo $_POST['cd_description'][$language_id];?>
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
              <?php if(isset($_POST['cd_program'][$language_id])) echo $_POST['cd_program'][$language_id];?>
            </textarea>
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
          <button type="submit" name="add_course" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
  <script type="text/javascript" src="/modules/elfinder_ckeditor/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
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
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: instructors.php');
  }
  
  $training_disciplines = array();
  $query = "SELECT `training_discipline_id`, `training_discipline_name` FROM `training_disciplines`";
  $result = mysqli_query($db_link, $query);
  if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $training_disciplines[] = $row;
    }
  }
  
  $query = "SELECT `training_discipline_id` FROM `instructors_to_disciplines` WHERE `instructor_id` = '$current_instructor_id'";
  $result = mysqli_query($db_link, $query);
  if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $training_discipline_ids[] = $row['training_discipline_id'];
    }
  }
  
  if(isset($_POST['edit_instructor'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(empty($_POST['instructor_name'])) $instructor_errors['instructor_name'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_position'])) $instructor_errors['instructor_position'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_certificates'])) $instructor_errors['instructor_certificates'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_experience'])) $instructor_errors['instructor_experience'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_facebook'])) $instructor_errors['instructor_facebook'] = $languages[$current_lang]['required_field_error'];
    if(!isset($_POST['training_disciplines'])) $instructor_errors['training_disciplines'] = "Трябва да изберете поне една дисциплина";
      
    if(!isset($instructor_errors)) {
      //if there are no form errors we can insert the information
      
      $instructor_name = mysqli_real_escape_string($db_link, $_POST['instructor_name']);
      $instructor_position = mysqli_real_escape_string($db_link, $_POST['instructor_position']);
      $instructor_certificates = mysqli_real_escape_string($db_link, $_POST['instructor_certificates']);
      $instructor_experience = mysqli_real_escape_string($db_link, $_POST['instructor_experience']);
      $instructor_facebook = mysqli_real_escape_string($db_link, $_POST['instructor_facebook']);
      $training_disciplines = $_POST['training_disciplines'];
      
      $query_update_instructor = "UPDATE `instructors` SET `instructor_name`='$instructor_name',
                                                            `instructor_position`='$instructor_position',
                                                            `instructor_certificates`='$instructor_certificates',
                                                            `instructor_experience`='$instructor_experience',
                                                            `instructor_facebook`='$instructor_facebook'
                                                      WHERE `instructor_id` = '$current_instructor_id'";
      $all_queries .= "<br>".$query_update_instructor;
      $result_update_instructor = mysqli_query($db_link, $query_update_instructor);
      if(!$result_update_instructor) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $query = "DELETE FROM `instructors_to_disciplines` WHERE `instructor_id` = '$current_instructor_id'";
      $all_queries .= "<br>".$query;
      $result = mysqli_query($db_link, $query);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      foreach($training_disciplines as $training_discipline_id) {
      
        $query = "INSERT INTO `instructors_to_disciplines`(`instructor_id`, `training_discipline_id`) VALUES ('$current_instructor_id','$training_discipline_id')";
        $all_queries .= "<br>".$query;
        $result = mysqli_query($db_link, $query);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: instructors.php');
      
    } //if(!isset($instructor_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_instructor_details = "SELECT `instructor_id`, `instructor_name`, `instructor_position`, `instructor_certificates`, 
                                        `instructor_experience`, `instructor_facebook`, `instructor_image`, `instructor_sort_order`
                                FROM `instructors`
                                WHERE `instructor_id` = '$current_instructor_id'";
    //echo $query_instructor_details;exit;
    $result_instructor_details = mysqli_query($db_link, $query_instructor_details);
    if(!$result_instructor_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_instructor_details) > 0) {
      $instructor_details = mysqli_fetch_assoc($result_instructor_details);

      $instructor_id = $instructor_details['instructor_id'];
      $instructor_name = $instructor_details['instructor_name'];
      $instructor_position = $instructor_details['instructor_position'];
      $instructor_certificates = $instructor_details['instructor_certificates'];
      $instructor_experience = $instructor_details['instructor_experience'];
      $instructor_facebook = $instructor_details['instructor_facebook'];
      $instructor_image = $instructor_details['instructor_image'];
      $instructor_image_exploded = explode(".", $instructor_image);
      $instructor_image_name = $instructor_image_exploded[0];
      $instructor_image_exstension = $instructor_image_exploded[1];
      $instructor_image_thumb = "/site/images/instructors/".$instructor_image_name.".".$instructor_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$instructor_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
  }
  
  $page_title = $languages[$current_lang]['instructor_details_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/instructor/instructors.php"><?=$languages[$current_lang]['header_instructors'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_instructor_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_instructor_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?instructor_id=$current_instructor_id";?>">
        <p class="float_right">
          <button type="submit" name="edit_instructor" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>

        <div>
          <label for="instructor_name" class="title"><?=$languages[$current_lang]['header_name'];?><span class="red">*</span></label>
          <?php
            if(isset($instructor_errors['instructor_name'])) {
              echo "<div class='error'>".$instructor_errors['instructor_name']."</div>";
            }
          ?>
          <input type="text" name="instructor_name" class="instructor_name" style="width: 400px;" value="<?php if(isset($instructor_name)) echo $instructor_name;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="instructor_position" class="title"><?=$languages[$current_lang]['header_position'];?><span class="red">*</span></label>
          <?php
            if(isset($instructor_errors['instructor_position'])) {
              echo "<div class='error'>".$instructor_errors['instructor_position']."</div>";
            }
          ?>
          <input type="text" name="instructor_position" class="instructor_position" style="width: 400px;" value="<?php if(isset($instructor_position)) echo $instructor_position;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="instructor_certificates" class="title"><?=$languages[$current_lang]['header_certificates'];?><span class="red">*</span></label>
          <?php
            if(isset($instructor_errors['instructor_certificates'])) {
              echo "<div class='error'>".$instructor_errors['instructor_certificates']."</div>";
            }
          ?>
          <input type="text" name="instructor_certificates" class="instructor_certificates" style="width: 40px;" value="<?php if(isset($instructor_certificates)) echo $instructor_certificates;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="instructor_experience" class="title"><?=$languages[$current_lang]['header_experience'];?><span class="red">*</span></label>
          <?php
            if(isset($instructor_errors['instructor_experience'])) {
              echo "<div class='error'>".$instructor_errors['instructor_experience']."</div>";
            }
          ?>
          <input type="text" name="instructor_experience" class="instructor_experience" style="width: 40px;" value="<?php if(isset($instructor_experience)) echo $instructor_experience;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="instructor_facebook" class="title"><?=$languages[$current_lang]['header_facebook'];?><span class="red">*</span></label>
          <?php
            if(isset($instructor_errors['instructor_facebook'])) {
              echo "<div class='error'>".$instructor_errors['instructor_facebook']."</div>";
            }
          ?>
          <input type="text" name="instructor_facebook" class="instructor_facebook" style="width: 400px;" value="<?php if(isset($instructor_facebook)) echo $instructor_facebook;?>" />
        </div>
        <div class="clearfix"></div>
        
        <div>
          <label for="training_discipline" class="title"><?=$languages[$current_lang]['header_training_discipline'];?></label>
          <?php
            if(isset($instructor_errors['training_disciplines'])) {
              echo "<div class='error'>".$instructor_errors['training_disciplines']."</div>";
            }
          ?>
          <table>
            <tbody>
<?php
          $key = 0;
          foreach($training_disciplines as $training_discipline) {
            
            if($key == 5) $key = 0;
            
            $training_discipline_id = $training_discipline['training_discipline_id'];
            $training_discipline_name = $training_discipline['training_discipline_name'];
            $checked = (in_array($training_discipline_id, $training_discipline_ids)) ? "checked='checked'" : "";
            
            if($key == 0) echo "<tr>";
            echo "<td style='width:20%;text-align:left;'><input type='checkbox' name='training_disciplines[]' $checked value='$training_discipline_id'> $training_discipline_name</td>";
            
            if($key == 5) echo "</tr>";
            $key++;
          }
?>
              
            </tbody>
          </table>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
        <p></p>
        <p><i class="info"><?=$languages[$current_lang]['info_instructor_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          
        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$instructor_image_thumb;?>" <?=$thumb_image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_image'];?></h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_instructor" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages[$current_lang]['ajaxmessage_update_product_tab_success'];?>" >
        <input type="hidden" name="instructor_id" id="instructor_id" value="<?=$current_instructor_id;?>" >
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
              GetInstructorImage(<?=$current_instructor_id;?>);
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
    });
  </script>
</body>
</html>
<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: team-members.php');
  }
  
  if(isset($_POST['edit_team_member'])) {
    
    //echo"<pre>";print_r($_POST);
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(empty($_POST['team_member_name'])) $team_member_errors['team_member_name'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_position'])) $team_member_errors['team_member_position'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_certificates'])) $team_member_errors['team_member_certificates'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_experience'])) $team_member_errors['team_member_experience'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_facebook'])) $team_member_errors['team_member_facebook'] = $languages[$current_lang]['required_field_error'];
      
    if(!isset($team_member_errors)) {
      //if there are no form errors we can insert the information
      
      $team_member_name = mysqli_real_escape_string($db_link, $_POST['team_member_name']);
      $team_member_position = mysqli_real_escape_string($db_link, $_POST['team_member_position']);
      $team_member_certificates = mysqli_real_escape_string($db_link, $_POST['team_member_certificates']);
      $team_member_experience = mysqli_real_escape_string($db_link, $_POST['team_member_experience']);
      $team_member_facebook = mysqli_real_escape_string($db_link, $_POST['team_member_facebook']);
      
      $query_update_team_member = "UPDATE `team_members` SET `team_member_name`='$team_member_name',
                                                            `team_member_position`='$team_member_position',
                                                            `team_member_certificates`='$team_member_certificates',
                                                            `team_member_experience`='$team_member_experience',
                                                            `team_member_facebook`='$team_member_facebook'
                                                      WHERE `team_member_id` = '$current_team_member_id'";
      $all_queries .= "<br>".$query_update_team_member;
      $result_update_team_member = mysqli_query($db_link, $query_update_team_member);
      if(!$result_update_team_member) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $query = "DELETE FROM `team_members_to_disciplines` WHERE `team_member_id` = '$current_team_member_id'";
      $all_queries .= "<br>".$query;
      $result = mysqli_query($db_link, $query);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: team_members.php');
      
    } //if(!isset($team_member_errors))
      
  } //if(isset($_POST['submit'])
  else {
    $query_team_member_details = "SELECT `team_member_id`, `team_member_name`, `team_member_position`, `team_member_certificates`, 
                                        `team_member_experience`, `team_member_facebook`, `team_member_image`, `team_member_sort_order`
                                FROM `team_members`
                                WHERE `team_member_id` = '$current_team_member_id'";
    //echo $query_team_member_details;exit;
    $result_team_member_details = mysqli_query($db_link, $query_team_member_details);
    if(!$result_team_member_details) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_team_member_details) > 0) {
      $team_member_details = mysqli_fetch_assoc($result_team_member_details);

      $team_member_id = $team_member_details['team_member_id'];
      $team_member_name = $team_member_details['team_member_name'];
      $team_member_position = $team_member_details['team_member_position'];
      $team_member_certificates = $team_member_details['team_member_certificates'];
      $team_member_experience = $team_member_details['team_member_experience'];
      $team_member_facebook = $team_member_details['team_member_facebook'];
      $team_member_image = $team_member_details['team_member_image'];
      $team_member_image_exploded = explode(".", $team_member_image);
      $team_member_image_name = $team_member_image_exploded[0];
      $team_member_image_exstension = $team_member_image_exploded[1];
      $team_member_image_thumb = "/site/images/team_members/".$team_member_image_name.".".$team_member_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$team_member_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
    }
  }
  
  $page_title = $languages[$current_lang]['team_member_details_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/team/team-members.php"><?=$languages[$current_lang]['header_team_members'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_team_member_edit'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_team_member_edit'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF']."?team_member_id=$current_team_member_id";?>">
        <p class="float_right">
          <button type="submit" name="edit_team_member" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </p>
        
        <p><i class="info"><?=$languages[$current_lang]['required_fields'];?></i></p>

        <div>
          <label for="team_member_name" class="title"><?=$languages[$current_lang]['header_name'];?><span class="red">*</span></label>
          <?php
            if(isset($team_member_errors['team_member_name'])) {
              echo "<div class='error'>".$team_member_errors['team_member_name']."</div>";
            }
          ?>
          <input type="text" name="team_member_name" class="team_member_name" style="width: 400px;" value="<?php if(isset($team_member_name)) echo $team_member_name;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="team_member_position" class="title"><?=$languages[$current_lang]['header_position'];?><span class="red">*</span></label>
          <?php
            if(isset($team_member_errors['team_member_position'])) {
              echo "<div class='error'>".$team_member_errors['team_member_position']."</div>";
            }
          ?>
          <input type="text" name="team_member_position" class="team_member_position" style="width: 400px;" value="<?php if(isset($team_member_position)) echo $team_member_position;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="team_member_certificates" class="title"><?=$languages[$current_lang]['header_certificates'];?><span class="red">*</span></label>
          <?php
            if(isset($team_member_errors['team_member_certificates'])) {
              echo "<div class='error'>".$team_member_errors['team_member_certificates']."</div>";
            }
          ?>
          <input type="text" name="team_member_certificates" class="team_member_certificates" style="width: 40px;" value="<?php if(isset($team_member_certificates)) echo $team_member_certificates;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="team_member_experience" class="title"><?=$languages[$current_lang]['header_experience'];?><span class="red">*</span></label>
          <?php
            if(isset($team_member_errors['team_member_experience'])) {
              echo "<div class='error'>".$team_member_errors['team_member_experience']."</div>";
            }
          ?>
          <input type="text" name="team_member_experience" class="team_member_experience" style="width: 40px;" value="<?php if(isset($team_member_experience)) echo $team_member_experience;?>" />
        </div>
        <div class="clearfix"></div>

        <div>
          <label for="team_member_facebook" class="title"><?=$languages[$current_lang]['header_facebook'];?><span class="red">*</span></label>
          <?php
            if(isset($team_member_errors['team_member_facebook'])) {
              echo "<div class='error'>".$team_member_errors['team_member_facebook']."</div>";
            }
          ?>
          <input type="text" name="team_member_facebook" class="team_member_facebook" style="width: 400px;" value="<?php if(isset($team_member_facebook)) echo $team_member_facebook;?>" />
        </div>
        <div class="clearfix"></div>

        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <h2><?=$languages[$current_lang]['header_current_image'];?></h2>
        <p></p>
        <p><i class="info"><?=$languages[$current_lang]['info_team_member_image']." ".$languages[$current_lang]['btn_save'];?></i></p>
          
        <div id="dropzone" style="padding-bottom: 410px;">
          <div id="current_image">
            <img src="<?=$team_member_image_thumb;?>" <?=$thumb_image_dimensions;?>>
          </div>
          <p>&nbsp;</p>
          <h2><?=$languages[$current_lang]['header_change_image'];?></h2>
        </div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="edit_team_member" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      
      <form action="ajax/upload_images.php" id="filedrop" class="dropzone" style="display: block;">
        <input type="hidden" name="ajaxmessage" id="ajaxmessage_update_product_tab_success" value="<?=$languages[$current_lang]['ajaxmessage_update_product_tab_success'];?>" >
        <input type="hidden" name="team_member_id" id="team_member_id" value="<?=$current_team_member_id;?>" >
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
              GetInstructorImage(<?=$current_team_member_id;?>);
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
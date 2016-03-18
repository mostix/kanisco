<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: team-members.php');
  }
    
  if(isset($_POST['add_team_member'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);EXIT;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(empty($_POST['team_member_name'])) $team_member_errors['team_member_name'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_position'])) $team_member_errors['team_member_position'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_certificates'])) $team_member_errors['team_member_certificates'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_experience'])) $team_member_errors['team_member_experience'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['team_member_facebook'])) $team_member_errors['team_member_facebook'] = $languages[$current_lang]['required_field_error'];
      
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    
    if(isset($_FILES['team_member_image'])) {
      if($_FILES['team_member_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['team_member_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $product_errors['team_member_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['team_member_image']['size'] < MAX_FILE_SIZE) && ($_FILES['team_member_image']['error'] == 0)) {
          // no error

          $team_member_image_tmp_name  = $_FILES['team_member_image']['tmp_name'];
          $team_member_image_name = $_FILES['team_member_image']['name'];
        }
        elseif(($_FILES['team_member_image']['size'] > MAX_FILE_SIZE) || ($_FILES['team_member_image']['error'] == 1 || $_FILES['team_member_image']['error'] == 2)) {
          $product_errors['team_member_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['team_member_image']['error'] != 4) { // error 4 means no file was uploaded
            $product_errors['team_member_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      } 
    }
    
    if(!isset($team_member_errors)) {
      //if there are no form errors we can insert the information
      
      $team_member_sort_order = get_slider_last_order_value()+1;
      $team_member_name = mysqli_real_escape_string($db_link, $_POST['team_member_name']);
      $team_member_position = mysqli_real_escape_string($db_link, $_POST['team_member_position']);
      $team_member_certificates = mysqli_real_escape_string($db_link, $_POST['team_member_certificates']);
      $team_member_experience = mysqli_real_escape_string($db_link, $_POST['team_member_experience']);
      $team_member_facebook = mysqli_real_escape_string($db_link, $_POST['team_member_facebook']);
      
      $query_insert_team_member = "INSERT INTO `team_members`(`team_member_id`, 
                                                            `team_member_name`, 
                                                            `team_member_position`, 
                                                            `team_member_certificates`, 
                                                            `team_member_experience`, 
                                                            `team_member_facebook`, 
                                                            `team_member_image`, 
                                                            `team_member_sort_order`) 
                                                    VALUES ('',
                                                            '$team_member_name',
                                                            '$team_member_position',
                                                            '$team_member_certificates',
                                                            '$team_member_experience',
                                                            '$team_member_facebook',
                                                            '$team_member_image_name',
                                                            '$team_member_sort_order')";
      $all_queries .= "<br>".$query_insert_team_member;
      $result_insert_team_member = mysqli_query($db_link, $query_insert_team_member);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/team-members/";
    
      if(is_uploaded_file($team_member_image_tmp_name)) {
        move_uploaded_file($team_member_image_tmp_name, $upload_path.$team_member_image_name);
      }
      else {
        echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: team_members.php');
      
    } //if(!isset($team_member_errors))
      
  } //if(isset($_POST['submit'])
  
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
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_team_member" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
          
        <div>
          <label for="team_member_image" class="title"><?=$languages[$current_lang]['header_image'];?></label>
          <?php
            if(isset($team_member_errors['team_member_image'])) {
              echo "<div class='error'>".$team_member_errors['team_member_image']."</div>";
            }
          ?>
          <p><input type="file" name="team_member_image" style="width: auto;" /></p>
        </div>
         
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>
        
        <div>
          <button type="submit" name="add_team_member" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
   
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>
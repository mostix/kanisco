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
    
  if(isset($_POST['add_instructor'])) {
    
    //echo"<pre>";print_r($_POST);print_r($_FILES);EXIT;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if(empty($_POST['instructor_name'])) $instructor_errors['instructor_name'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_position'])) $instructor_errors['instructor_position'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_certificates'])) $instructor_errors['instructor_certificates'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_experience'])) $instructor_errors['instructor_experience'] = $languages[$current_lang]['required_field_error'];
    if(empty($_POST['instructor_facebook'])) $instructor_errors['instructor_facebook'] = $languages[$current_lang]['required_field_error'];
    if(!isset($_POST['training_disciplines'])) $instructor_errors['training_disciplines'] = "Трябва да изберете поне една дисциплина";
      
    define ("MAX_FILE_SIZE","2048000");
    $valid_formats = array("jpg", "jpeg", "png", "gif");
    
    if(isset($_FILES['instructor_image'])) {
      if($_FILES['instructor_image']['error'] != 4) {
        $extension_array = explode("/", $_FILES['instructor_image']['type']);
        $extension = $extension_array[1];
        if(!in_array($extension, $valid_formats)) {
          $product_errors['instructor_image'] = $languages[$current_lang]['image_extension_error']."$extension<br>";
        }

        if(($_FILES['instructor_image']['size'] < MAX_FILE_SIZE) && ($_FILES['instructor_image']['error'] == 0)) {
          // no error

          $instructor_image_tmp_name  = $_FILES['instructor_image']['tmp_name'];
          $instructor_image_name = $_FILES['instructor_image']['name'];
        }
        elseif(($_FILES['instructor_image']['size'] > MAX_FILE_SIZE) || ($_FILES['instructor_image']['error'] == 1 || $_FILES['instructor_image']['error'] == 2)) {
          $product_errors['instructor_image'] .= $languages[$current_lang]['image_size_error']."4MB<br>";
        }
        else {
          if($_FILES['instructor_image']['error'] != 4) { // error 4 means no file was uploaded
            $product_errors['instructor_image'] .= $languages[$current_lang]['image_uploading_error']."<br>";
          }
        }
      } 
    }
    
    if(!isset($instructor_errors)) {
      //if there are no form errors we can insert the information
      
      $instructor_sort_order = get_slider_last_order_value()+1;
      $training_disciplines = $_POST['training_disciplines'];
      $instructor_name = mysqli_real_escape_string($db_link, $_POST['instructor_name']);
      $instructor_position = mysqli_real_escape_string($db_link, $_POST['instructor_position']);
      $instructor_certificates = mysqli_real_escape_string($db_link, $_POST['instructor_certificates']);
      $instructor_experience = mysqli_real_escape_string($db_link, $_POST['instructor_experience']);
      $instructor_facebook = mysqli_real_escape_string($db_link, $_POST['instructor_facebook']);
      
      $query_insert_instructor = "INSERT INTO `instructors`(`instructor_id`, 
                                                            `instructor_name`, 
                                                            `instructor_position`, 
                                                            `instructor_certificates`, 
                                                            `instructor_experience`, 
                                                            `instructor_facebook`, 
                                                            `instructor_image`, 
                                                            `instructor_sort_order`) 
                                                    VALUES ('',
                                                            '$instructor_name',
                                                            '$instructor_position',
                                                            '$instructor_certificates',
                                                            '$instructor_experience',
                                                            '$instructor_facebook',
                                                            '$instructor_image_name',
                                                            '$instructor_sort_order')";
      $all_queries .= "<br>".$query_insert_instructor;
      $result_insert_instructor = mysqli_query($db_link, $query_insert_instructor);
      if(mysqli_affected_rows($db_link) <= 0) {
        echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }

      $instructor_id = mysqli_insert_id($db_link);
      
      foreach($training_disciplines as $training_discipline_id) {
      
        $query = "INSERT INTO `instructors_to_disciplines`(`instructor_id`, `training_discipline_id`) VALUES ('$instructor_id','$training_discipline_id')";
        $all_queries .= "<br>".$query;
        $result = mysqli_query($db_link, $query);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
      
      $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/instructors/";
    
      if(is_uploaded_file($instructor_image_tmp_name)) {
        move_uploaded_file($instructor_image_tmp_name, $upload_path.$instructor_image_name);
      }
      else {
        echo $languages[$current_lang]['sql_error_insert']." - 4 ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      
      //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;

      mysqli_query($db_link,"COMMIT");

      header('Location: instructors.php');
      
    } //if(!isset($instructor_errors))
      
  } //if(isset($_POST['submit'])
  
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
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
        <p class="float_right">
          <button type="submit" name="add_instructor" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
          <label for="instructor_image" class="title"><?=$languages[$current_lang]['header_image'];?></label>
          <?php
            if(isset($instructor_errors['instructor_image'])) {
              echo "<div class='error'>".$instructor_errors['instructor_image']."</div>";
            }
          ?>
          <p><input type="file" name="instructor_image" style="width: auto;" /></p>
        </div>
          
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
            
            if($key == 0) echo "<tr>";
            echo "<td style='width:20%;text-align:left;'><input type='checkbox' name='training_disciplines[]' value='$training_discipline_id'> $training_discipline_name</td>";
            
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
        
        <div>
          <button type="submit" name="add_instructor" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
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
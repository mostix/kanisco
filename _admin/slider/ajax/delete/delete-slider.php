<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['slider_id'])) {
    $slider_id = $_POST['slider_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";
  
  $query_slider_image = "SELECT `slider_image`
                        FROM `sliders`
                        WHERE `slider_id` = '$slider_id'";
  //echo $query_slider_image;exit;
  $result_slider_image = mysqli_query($db_link, $query_slider_image);
  if(!$result_slider_image) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_image) > 0) {
    $slider_image_row = mysqli_fetch_assoc($result_slider_image);

    $slider_image = $slider_image_row['slider_image'];
    
    $slider_image_exploded = explode(".", $slider_image);
    $current_slider_image_name = $slider_image_exploded[0];
    $current_slider_image_exstension = $slider_image_exploded[1];
    $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/slider/";

    $file = "$upload_path$current_slider_image_name.$current_slider_image_exstension";

    if(file_exists($file)) {
      unlink($file);
    }

    $image_admin_thumb_name = $current_slider_image_name."_amin_thumb.".$current_slider_image_exstension;
    $image_admin_thumb = "$upload_path$image_admin_thumb_name";

    if(file_exists($image_admin_thumb)) {
      unlink($image_admin_thumb);
    }

    $image_site_name = $current_slider_image_name."_site.".$current_slider_image_exstension;
    $image_site = "$upload_path$image_site_name";

    if(file_exists($image_site)) {
      unlink($image_site);
    }
  }
  
  $query = "DELETE FROM `sliders` WHERE `slider_id` = '$slider_id'";
  $all_queries .= "<br>".$query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `sliders_descriptions` WHERE `slider_id` = '$slider_id'";
  $all_queries .= "<br>".$query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
        
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  list_sliders();
?>

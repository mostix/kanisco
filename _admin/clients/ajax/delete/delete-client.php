<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['client_id'])) {
    $client_id = $_POST['client_id'];
  }
  
  mysqli_query($db_link,"BEGIN");
  $all_queries = "";
  
  $query_client_details = "SELECT `client_image`
                            FROM `clients`
                            WHERE `client_id` = '$client_id'";
  //echo $query_client_details;exit;
  $result_client_details = mysqli_query($db_link, $query_client_details);
  if(!$result_client_details) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_client_details) > 0) {
    $client_details = mysqli_fetch_assoc($result_client_details);

    $client_image = $client_details['client_image'];
  }
  $client_image_exploded = explode(".", $client_image);
  $current_client_image_name = $client_image_exploded[0];
  $current_client_image_exstension = $client_image_exploded[1];
  $upload_path = $_SERVER['DOCUMENT_ROOT']."/site/images/clients/";
  
  $file = $upload_path."$current_client_image_name.$current_client_image_exstension";
  
  unlink($file);

  $image_admin_thumb_name = $current_client_image_name."_admin_thumb.".$current_client_image_exstension;
  $image_admin_thumb = "$upload_path$image_admin_thumb_name";

  unlink($image_admin_thumb);
  
  $image_site_name = $current_client_image_name."_site.".$current_client_image_exstension;
  $image_site = "$upload_path$image_site_name";
  
  unlink($image_site);
  
  $query = "DELETE FROM `clients` WHERE `client_id` = '$client_id'";
  $all_queries .= "<br>".$query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `clients_descriptions` WHERE `client_id` = '$client_id'";
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
  
  list_clients();
?>

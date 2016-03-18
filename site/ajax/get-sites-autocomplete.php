<?php

  include_once '../config.php';
  
  $db_link = DB_OpenI();
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_GET['term'])) {
    $term = $_GET['term'];
  }

  $sites_array = array();
  $query_sites = "SELECT `site_id`,`site_type`, `site_name`,`site_municipality`, `site_region`, `site_postcode` FROM `sites`
                    WHERE `site_name` LIKE '%$term%'
                    ORDER BY `site_name` ASC";
  //echo $query_sites;
  $result_sites = mysqli_query($db_link, $query_sites);
  if(mysqli_num_rows($result_sites) > 0) {
    while($sites = mysqli_fetch_assoc($result_sites)) {

      $label = $sites['site_type']." ".$sites['site_name']." (обл. ".$sites['site_region'].")";
      $sites['label'] = $label;
      $sites_array[] = $sites;
    }
  }
  //print_r($sites_array);exit;
  if(!empty($sites_array)) echo json_encode($sites_array);
?>
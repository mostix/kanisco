<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['customer_address_id'])) {
    $customer_address_id =  $_POST['customer_address_id'];
  }
  
  if(!empty($customer_address_id)) {
    $query_delete_address = "DELETE FROM `customers_addresses` WHERE `customer_address_id` = '$customer_address_id'";
    $result_delete_address = mysqli_query($db_link, $query_delete_address);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
    }
  } 
?>
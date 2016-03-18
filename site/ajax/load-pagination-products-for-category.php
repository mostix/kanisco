<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';

  //echo "<pre>";print_r($_GET);
  if(isset($_POST['current_category_id'])) {
    $current_category_id =  $_POST['current_category_id'];
  }
  if(isset($_POST['cd_name'])) {
    $cd_name =  $_POST['cd_name'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  if(isset($_POST['current_cat_href'])) {
    $current_cat_href =  $_POST['current_cat_href'];
  }
  $cd_pretty_url = "";
  if(isset($_POST['cd_pretty_url'])) {
    $cd_pretty_url =  $_POST['cd_pretty_url'];
  }
  if(isset($_GET['offset'])) {
    $offset = $_GET['offset']; // content_root_id
  }
  else $offset = 0;
  if(isset($_GET['pmin'])) {
    $price_min =  $_GET['pmin'];
  }
  if(isset($_GET['pmax'])) {
    $price_max =  $_GET['pmax'];
  }
  $colors_ids = "";
  if(isset($_GET['colors'])) {
    $colors_ids =  $_GET['colors'];
  }
  $option_value_ids = array();
  if(isset($_GET['ocv'])) {
    $ocv =  $_GET['ocv']; //option_checkbox_values
    $option_value_ids = $ocv;
  }
  if(isset($_GET['option_radio_values'])) {
    foreach($_GET['option_radio_values'] as $option_radio_value) {
      $option_value_ids[] = $option_radio_value;
    }
  }
  if(isset($_POST['offset'])) {
    $offset =  $_POST['offset'];
  }
  if(isset($_POST['products_count'])) {
    $products_count =  $_POST['products_count'];
  }
  if(isset($_POST['order_by_price'])) {
    $order_by_price =  $_POST['order_by_price'];
  }

  list_products_by_option_value($current_category_id,$colors_ids,$option_value_ids,$offset,$current_cat_href,$cd_pretty_url,$products_count);
?>
  <script>
    $(function() {
      $(".php_pagination a").bind('click', function() {
        var offset = $(this).attr("data");
        LoadPaginationProductsForCategory(offset);
      });
    });
  </script>
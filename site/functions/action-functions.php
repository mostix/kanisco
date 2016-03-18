<?php

function is_active_page($page) {
  
    if(strstr($_GET['page'], $page)) {
      return true;
    }
    else {
      return false;
    }
}
  
function prepare_for_null_row($value) {

    if (empty($value) || is_null($value))
        $value = "NULL";
    else
        $value = "'$value'";

    return $value;
}

function generate_bcrypt_salt() {
  
    $rand_string = "";
    $charecters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
    for ($i = 0; $i < 22; $i++) {
        $randInt = mt_rand(0, 63);
        $rand_char = $charecters[$randInt];
        $rand_string .= $rand_char;
    }
    return $rand_string;
}

function generate_captcha() {
  
  global $db_link;

  unset($_SESSION['tyreslog']['captcha123']);
  $_SESSION['tyreslog']['captcha123'] = array();
  $rnd = rand(1,99);
  $query = "SELECT * FROM `captchas` LIMIT $rnd,1";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result)>0){

    $captcha = mysqli_fetch_assoc($result);
    $_SESSION['tyreslog']['captcha123']['img'] = $captcha['captcha_image'];
    $_SESSION['tyreslog']['captcha123']['code'] = $captcha['captcha_number'];

  }
}

function generate_strong_password($length = 8, $available_sets = 'luds') {
  $sets = array();
  if(strpos($available_sets, 'l') !== false)
          $sets[] = 'abcdefghjkmnpqrstuvwxyz';
  if(strpos($available_sets, 'u') !== false)
          $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
  if(strpos($available_sets, 'd') !== false)
          $sets[] = '23456789';
  if(strpos($available_sets, 's') !== false)
          $sets[] = '!@#$%&*?';

  $all = '';
  $password = '';
  foreach($sets as $set)
  {
          $password .= $set[array_rand(str_split($set))];
          $all .= $set;
  }

  $all = str_split($all);
  for($i = 0; $i < $length - count($sets); $i++)
          $password .= $all[array_rand($all)];

  $password = str_shuffle($password);

  return $password;
}

function check_if_users_passwords_match($user_password,$confirm_user_password) {
  global $languages;
  global $current_lang;
  if($user_password === $confirm_user_password) {
    return "";
  }
  else {
    return $languages[$current_lang]['customer_passwords_mismatch'];
  }
}

function check_if_user_email_is_valid($customer_email) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  
  if(!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    return false;
   }
   else {
     if(!empty($customer_id)) $query = "SELECT `customer_id` FROM `customers` WHERE `customer_id` <> '$customer_id' AND `customer_email` = '$customer_email'";
     else $query = "SELECT `customer_id` FROM `customers` WHERE `customer_email` = '$customer_email'";
     //echo $query;
     $result = mysqli_query($db_link, $query);
     if(!$result) echo mysqli_error($db_link);
     if(mysqli_num_rows($result) > 0) {
       return false;
     }
     else {
       return true;
     }
   }
}

function check_if_content_has_active_children($content_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` WHERE `content_parent_id` = '$content_id' AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_content_last_child($content_parent_id,$content_menu_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `content_id` FROM `contents` 
                            WHERE `content_parent_id` = '$content_parent_id' AND `content_menu_order` > '$content_menu_order'
                              AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function check_if_news_cat_has_active_children($news_category_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `news_category_id` FROM `news_categories` WHERE `news_cat_parent_id` = '$news_category_id' AND `content_show_in_menu` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_news_cat_last_child($news_category_id,$news_cat_sort_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `news_category_id` FROM `news_categories` 
                            WHERE `news_cat_parent_id` = '$news_category_id' AND `news_cat_sort_order` > '$news_cat_sort_order'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function get_product_default_image($product_id) {
  
  global $db_link;
  
  $query_pi_name = "SELECT `pi_name` 
                    FROM `product_image` 
                    WHERE `product_id` = '$product_id' AND `pi_is_default` = '1'";
  //echo $query_pi_name;
  $result_pi_name = mysqli_query($db_link, $query_pi_name);
  if(!$result_pi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pi_name) > 0) {
    $row_pi_name = mysqli_fetch_assoc($result_pi_name);
    $pi_name = $row_pi_name['pi_name'];
    mysqli_free_result($result_pi_name);
  }
  else {
    $pi_name = ""; //default picture
  }
  
  return $pi_name;
}

function get_product_images($product_id) {
  
  global $db_link;
  $pi_names_array = array();
  
  $query_pi_name = "SELECT `pi_name`,`pi_is_default` 
                    FROM `product_image` 
                    WHERE `product_id` = '$product_id'
                    ORDER BY `pi_sort_order` ASC";
  //echo $query_pi_name;
  $result_pi_name = mysqli_query($db_link, $query_pi_name);
  if(!$result_pi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pi_name) > 0) {
    while($pi_names_row = mysqli_fetch_assoc($result_pi_name)) {
      $pi_is_default = $pi_names_row['pi_is_default'];
      if($pi_is_default == 1) $pi_names_array['default'] = $pi_names_row;
      else $pi_names_array['gallery'][] = $pi_names_row;
    }
    mysqli_free_result($result_pi_name);
  }
  
  return $pi_names_array;
}

function get_category_products_min_max_price($current_category_id) {
  
  global $db_link;
  
  $query_min_max_price = "SELECT MIN(`product_price`) as `min_product_price`, MAX(`product_price`) as `max_product_price` 
                          FROM `products`
                          INNER JOIN `product_to_category` USING(`product_id`)
                          WHERE `product_to_category`.`category_id` = '$current_category_id'";
  //echo $query_min_max_price;
  $result_min_max_price = mysqli_query($db_link, $query_min_max_price);
  if(!$result_min_max_price) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_min_max_price) > 0) {
    $min_max_price_row = mysqli_fetch_assoc($result_min_max_price);
    mysqli_free_result($result_min_max_price);
  }
  
  return $min_max_price_row;
}

function get_news_category_name_by_id($news_category_id,$language_id) {
  
  global $db_link;
  
  $query_news_cat_name = "SELECT `news_cat_name` FROM `news_cat_desc` 
                            WHERE `news_category_id` = '$news_category_id' AND `language_id` = '$language_id'";
  //echo $query_news_cat_name;
  $result_news_cat_name = mysqli_query($db_link, $query_news_cat_name);
  if(!$result_news_cat_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_cat_name) > 0) {
    $news_cat_name_row = mysqli_fetch_assoc($result_news_cat_name);
    $news_cat_desc = $news_cat_name_row['news_cat_name'];
    mysqli_free_result($result_news_cat_name);
  }
  
  return $news_cat_desc;
}

function get_option_value_name($option_value_id, $current_language_id) {
  
  global $db_link;
  
  $query_ovd_name = "SELECT `ovd_name` FROM `option_value_description` WHERE `option_value_id` = '$option_value_id' AND `language_id` = '$current_language_id'";
  //echo $query_ovd_name;
  $result_ovd_name = mysqli_query($db_link, $query_ovd_name);
  if(!$result_ovd_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ovd_name) > 0) {
    $ovd_name_row = mysqli_fetch_assoc($result_ovd_name);
    $ovd_name = $ovd_name_row['ovd_name'];
    mysqli_free_result($result_ovd_name);
  }
  
  return $ovd_name;
}

function truncate($string,$length=480,$append="&hellip;") {
  $string = trim($string);

  if(strlen($string) > $length) {
    $string = wordwrap($string, $length);
    $string = explode("\n", $string, 2);
    $string = $string[0] . $append;
  }

  return $string;
}


function check_if_category_has_active_children($category_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `category_id` FROM `categories` 
                            WHERE `category_parent_id` = '$category_id' AND `category_show_in_menu` = '1' AND `category_is_active` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return true;
  }
  else return false;
}

function check_if_this_is_category_last_child($category_parent_id,$category_sort_order) {
  
  global $db_link;
  
  $query_active_children = "SELECT `category_id` FROM `categories` 
                            WHERE `category_parent_id` = '$category_parent_id' AND `category_sort_order` > '$category_sort_order'
                              AND `category_show_in_menu` = '1' AND `category_is_active` = '1'";
  //echo $query_active_children;exit;
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {

    return false;
  }
  else return true;
}

function get_random_client_ids_list($count) {
  
  global $db_link;
  
  $random_client_ids_list = "";
  $query_random_client_ids = "SELECT `client_id` FROM `clients` WHERE `client_is_active` = '1' ORDER BY RAND() LIMIT $count";
  //echo $query_random_client_ids."<br>";
  $result_random_client_ids = mysqli_query($db_link, $query_random_client_ids);
  if(!$result_random_client_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_random_client_ids) > 0) {
    $key = 0;
    while($random_client_ids_row = mysqli_fetch_assoc($result_random_client_ids)) {

      $random_client_ids_list .= ($key == 0) ? $random_client_ids_row['client_id'] : ",".$random_client_ids_row['client_id'];

      $key++;
    }
  }
  return $random_client_ids_list;
}
?>

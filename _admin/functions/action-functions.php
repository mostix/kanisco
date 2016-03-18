<?php

function include_file($file_name) {
  
  if (!defined($file_name)) {
    require($file_name);
    define($file_name, 1);
  }
}

function is_active_page($active_page) {
  
    if(strstr($_SERVER['PHP_SELF'], $active_page)) {
      return true;
    }
    else {
      return false;
    }
}

function multiexplode($delimiters,$string) {
  
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
    
}
        
function is_only_numbers($value) {

  if(preg_match("/[0-9]/", $value)) { 
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
    
  unset($_SESSION['captcha123']);
  $_SESSION['captcha123'] = array();
  $rnd = rand(1,99);
  $query = "SELECT * FROM `captchas` LIMIT $rnd,1";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if (!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result)>0) {

    $captcha = mysqli_fetch_assoc($result);
    $_SESSION['captcha123']['img'] = $captcha['captcha_image'];
    $_SESSION['captcha123']['code'] = $captcha['captcha_number'];

    mysqli_free_result($result);
  }
}

function start_page_build_time_measure() {
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $starttime = $mtime;
}

function close_page_build_time_measure($print_time = false) {
  
  global $starttime;
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $endtime = $mtime; 
  $totaltime = ($endtime - $starttime);
  if($print_time) echo "<br><p>This page was created in ".$totaltime." seconds</p>";
}

function get_user_rights($user_id) {

    global $db_link;
    global $languages;
    global $current_lang;
    
    $query_users_rights = "SELECT `users_rights_id`, `user_id`, `menu_id`,`users_rights_edit`, `users_rights_delete`
                          FROM `users_rights` 
                          WHERE `user_id` = '$user_id'";
    $result_users_rights = mysqli_query($db_link, $query_users_rights);
    if(!$result_users_rights) echo mysqli_error($db_link);
    if (mysqli_num_rows($result_users_rights) > 0) {
?>
        <table>
          <thead>
            <tr>
              <th width="5%"></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page'];?></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page_access'];?></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page_edit'];?></th>
              <th width="15%"><?=$languages[$current_lang]['header_user_rights_page_delete'];?></th>
              <th width="15%"><?=$languages[$current_lang]['header_user_rights_page_subpages'];?></th>
              <th width="5%"></th>
            </tr>
          </thead>
          <tbody>
<?php list_user_menu_rights(0, $user_id);?>
          </tbody>
        </table>
<?php
        mysqli_free_result($result_users_rights);
    }
}

function get_admin_user_rights($menu_url) {
  
  global $db_link;
  
  $user_id = $_SESSION['admin']['user_id'];
  $user_rights = array();

  $query_user_rights = "SELECT `users_rights`.*
                        FROM `menus`
                        INNER JOIN `users_rights` ON `users_rights`.`menu_id` = `menus`.`menu_id`
                        WHERE `menus`.`menu_url` = '$menu_url' AND `users_rights`.`user_id` = '$user_id'";
  //echo $query_user_rights."<br>";
  $result_user_rights = mysqli_query($db_link, $query_user_rights);
  if (!$result_user_rights) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_user_rights) > 0) {
    $user_rights = mysqli_fetch_assoc($result_user_rights);
    
    mysqli_free_result($result_user_rights);
  }
  
  return $user_rights;
}

function get_contents_hierarchy_ids_and_path($content_id) {
  
  global $db_link;
  
  $content_hierarchy_ids_and_path = array();
  $query_content_hierarchy_ids_and_path = "SELECT `content_hierarchy_ids`, `content_hierarchy_path`
                                          FROM `contents`
                                          WHERE `contents`.`content_id` = '$content_id'";
  //echo $query_content_hierarchy_ids;
  $result_content_hierarchy_ids_and_path = mysqli_query($db_link, $query_content_hierarchy_ids_and_path);
  if(!$result_content_hierarchy_ids_and_path) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_hierarchy_ids_and_path) > 0) {
    $row_content_hierarchy_ids_and_path = mysqli_fetch_assoc($result_content_hierarchy_ids_and_path);
    $content_hierarchy_ids_and_path['content_hierarchy_ids'] = $row_content_hierarchy_ids_and_path['content_hierarchy_ids'];
    $content_hierarchy_ids_and_path['content_hierarchy_path'] = $row_content_hierarchy_ids_and_path['content_hierarchy_path'];
    mysqli_free_result($result_content_hierarchy_ids_and_path);
  }
  return $content_hierarchy_ids_and_path;
}

function check_if_menu_has_active_children($menu_id,$user_id) {
  
  global $db_link;
  
  $query_active_children = "SELECT `menu_id` FROM `menus` WHERE `menu_parent_id` = '$menu_id' AND `menu_is_active` = '1'";
  //if($menu_id == 20) echo $query_active_children."<br>";
  $result_active_children = mysqli_query($db_link, $query_active_children);
  if(!$result_active_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_active_children) > 0) {
    while($menu_id_row = mysqli_fetch_assoc($result_active_children)) {
      $menu_id = $menu_id_row['menu_id'];

      $query_menus = "SELECT `users_rights_id` FROM `users_rights` WHERE `menu_id` = '$menu_id' AND `user_id` = '$user_id'";
      //if($menu_id == 22) echo $query_menus."<br>";
      $result_menus = mysqli_query($db_link, $query_menus);
      if (!$result_menus) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_menus) > 0) {

        return true;
      }
    }
  }
  return false;
}

function check_if_this_is_menu_last_child($menu_parent_id,$menu_sort_order,$user_id) {
  
  global $db_link;
 
  $query_last_child = "SELECT `menus`.`menu_id` FROM `menus` 
                            INNER JOIN `users_rights` ON `users_rights`.`menu_id` = `menus`.`menu_id`
                            WHERE `menus`.`menu_parent_id` = '$menu_parent_id' AND `users_rights`.`user_id` = '$user_id' AND `menus`.`menu_sort_order` > '$menu_sort_order'
                               AND `menus`.`menu_is_active` = '1'";
  //echo "$query_last_child<br>";
  $result_last_child = mysqli_query($db_link, $query_last_child);
  if(!$result_last_child) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_last_child) > 0) {
    
    return false;
  }
  else return true;
}

function check_if_content_has_children($content_id) {
  
  global $db_link;
  
  $query_content_has_children = "SELECT `contents`.`content_has_children`
                                  FROM `contents`
                                  WHERE `contents`.`content_id` = '$content_id'";
  //echo $query_content_has_children;
  $result_content_has_children = mysqli_query($db_link, $query_content_has_children);
  if(!$result_content_has_children) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_has_children) > 0) {
    $row_content_has_children = mysqli_fetch_assoc($result_content_has_children);
    $content_has_children = $row_content_has_children['content_has_children'];
    mysqli_free_result($result_content_has_children);
  }
  if($content_has_children == 1) return true;
  else return false;
}

function update_contents_children_hierarchy_params($content_parent_id, $content_parent_hierarchy_ids_list, $content_parent_hierarchy_level, $content_parent_hierarchy_path) {
  
  global $db_link;
  global $languages;
  global $current_lang;
  global $update_hierarchy_ids_level;
  global $update_hierarchy_path;
  
  $query_select_children_id = "SELECT `content_id`, `content_pretty_url` FROM `contents` WHERE `content_parent_id` = '$content_parent_id'";
  $result_select_children_id = mysqli_query($db_link, $query_select_children_id);
  if(!$result_select_children_id) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_select_children_id) > 0) {
    $content_hierarchy_level = $content_parent_hierarchy_level+1;
    while($row_children_ids = mysqli_fetch_assoc($result_select_children_id)) {
      $content_id = $row_children_ids['content_id'];
      $content_hierarchy_ids_list = "$content_parent_hierarchy_ids_list.$content_id";
      $content_pretty_url = $row_children_ids['content_pretty_url'];
      $content_hierarchy_path = "$content_parent_hierarchy_path/$content_pretty_url";
      
      $query_update_content = "UPDATE `contents` SET ";
      if($update_hierarchy_ids_level) {
            $query_update_content.= "`content_hierarchy_ids`='$content_hierarchy_ids_list',
                                     `content_hierarchy_level`='$content_hierarchy_level'";
      }
      if($update_hierarchy_path) {
            $query_update_content.= ($update_hierarchy_ids_level) ? "," : "";
            $query_update_content.= "`content_hierarchy_path`='$content_hierarchy_path'";
      }
            $query_update_content.= " WHERE `content_id` = '$content_id'";
      //echo $query_update_content."<br>";
      $result_update_content = mysqli_query($db_link, $query_update_content);
      if(!$result_update_content) {
        echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
       
      $content_has_children = check_if_content_has_children($content_id); // this function returns true or false
      if($content_has_children) {
        // if true we need to update the children's `content_hierarchy_ids` and `content_hierarchy_level`
        update_contents_children_hierarchy_params($content_id, $content_hierarchy_ids_list, $content_hierarchy_level, $content_hierarchy_path);
      }
    }
  }
}

function get_content_lаst_child_order_value($content_id) {
  
  global $db_link;
  
  $query_content_menu_order = "SELECT `content_menu_order` FROM `contents` WHERE `content_parent_id` = '$content_id'
                              ORDER BY `content_menu_order` DESC LIMIT 1";
  //echo $query_content_menu_order;
  $result_content_menu_order = mysqli_query($db_link, $query_content_menu_order);
  if(!$result_content_menu_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_menu_order) > 0) {
    $row_content_menu_order = mysqli_fetch_assoc($result_content_menu_order);
    $content_menu_order = $row_content_menu_order['content_menu_order'];
    mysqli_free_result($result_content_menu_order);
  }
  else {
    $content_menu_order = 0;
  }
  
  return $content_menu_order;
}

function get_news_category_hierarchy_ids($news_category_id) {
  
  global $db_link;
  
  $query_news_cat_hierarchy_ids = "SELECT `news_cat_hierarchy_ids`
                                  FROM `news_categories`
                                  WHERE `news_category_id` = '$news_category_id'";
  //echo $query_news_cat_hierarchy_ids."<br>";
  $result_news_cat_hierarchy_ids = mysqli_query($db_link, $query_news_cat_hierarchy_ids);
  if(!$result_news_cat_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_cat_hierarchy_ids) > 0) {
    $row_news_cat_hierarchy_ids = mysqli_fetch_assoc($result_news_cat_hierarchy_ids);
    $news_cat_hierarchy_ids = $row_news_cat_hierarchy_ids['news_cat_hierarchy_ids'];
    mysqli_free_result($result_news_cat_hierarchy_ids);
  }
  return $news_cat_hierarchy_ids;
}

function get_lаst_news_category_order_value($news_cat_parent_id) {
  
  global $db_link;
  
  $query_news_category_order = "SELECT `news_cat_sort_order` FROM `news_categories` WHERE `news_cat_parent_id` = '$news_cat_parent_id'
                                ORDER BY `news_cat_sort_order` DESC LIMIT 1";
  //echo $query_news_category_order;
  $result_news_category_order = mysqli_query($db_link, $query_news_category_order);
  if(!$result_news_category_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news_category_order) > 0) {
    $row_news_category_order = mysqli_fetch_assoc($result_news_category_order);
    $news_category_order = $row_news_category_order['news_cat_sort_order'];
    mysqli_free_result($result_news_category_order);
  }
  else {
    $news_category_order = 0;
  }
  
  return $news_category_order;
}

function get_categories_hierarchy_ids($category_id) {
  
  global $db_link;
  
  $query_category_hierarchy_ids = "SELECT `category_hierarchy_ids`
                                  FROM `categories`
                                  WHERE `category_id` = '$category_id'";
  //echo $query_category_hierarchy_ids."<br>";
  $result_category_hierarchy_ids = mysqli_query($db_link, $query_category_hierarchy_ids);
  if(!$result_category_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_hierarchy_ids) > 0) {
    $row_category_hierarchy_ids = mysqli_fetch_assoc($result_category_hierarchy_ids);
    $category_hierarchy_ids = $row_category_hierarchy_ids['category_hierarchy_ids'];
    mysqli_free_result($result_category_hierarchy_ids);
  }
  return $category_hierarchy_ids;
}

function get_categories_hierarchy_path($category_id,$language_id) {
  
  global $db_link;
  
  $query_cd_hierarchy_path = "SELECT `cd_hierarchy_path`
                              FROM `category_descriptions`
                              WHERE `category_id` = '$category_id' AND `language_id` = '$language_id'";
  //echo $query_cd_hierarchy_path."<br>";
  $result_cd_hierarchy_path = mysqli_query($db_link, $query_cd_hierarchy_path);
  if(!$result_cd_hierarchy_path) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_cd_hierarchy_path) > 0) {
    $row_cd_hierarchy_path = mysqli_fetch_assoc($result_cd_hierarchy_path);
    $cd_hierarchy_path = $row_cd_hierarchy_path['cd_hierarchy_path'];
    mysqli_free_result($result_cd_hierarchy_path);
  }
  return $cd_hierarchy_path;
}

function get_category_lаst_child_order_value($category_id) {
  
  global $db_link;
  
  $query_category_sort_order = "SELECT `category_sort_order` FROM `categories` WHERE `category_parent_id` = '$category_id'
                                ORDER BY `category_sort_order` DESC LIMIT 1";
  //echo $query_category_sort_order."<br>";
  $result_category_sort_order = mysqli_query($db_link, $query_category_sort_order);
  if(!$result_category_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_category_sort_order) > 0) {
    $row_category_sort_order = mysqli_fetch_assoc($result_category_sort_order);
    $category_sort_order = $row_category_sort_order['category_sort_order'];
    mysqli_free_result($result_category_sort_order);
  }
  else {
    $category_sort_order = 0;
  }
  
  return $category_sort_order;
}

function get_lаst_language_menu_order_value() {
  
  global $db_link;
  
  $query_language_menu_order = "SELECT `language_menu_order` FROM `languages` ORDER BY `language_menu_order` DESC LIMIT 1";
  //echo $query_language_menu_order;
  $result_language_menu_order = mysqli_query($db_link, $query_language_menu_order);
  if(!$result_language_menu_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_language_menu_order) > 0) {
    $row_language_menu_order = mysqli_fetch_assoc($result_language_menu_order);
    $language_menu_order = $row_language_menu_order['language_menu_order'];
    mysqli_free_result($result_language_menu_order);
  }
  else {
    $language_menu_order = 0;
  }
  
  return $language_menu_order;
}

function check_if_content_pretty_url_is_unique($content_pretty_url) {
  
  global $db_link;
  
  $query_pretty_url = "SELECT `content_id` FROM `contents` WHERE `content_pretty_url` = '$content_pretty_url'";
  //echo $query_pretty_url;
  $result_pretty_url = mysqli_query($db_link, $query_pretty_url);
  if(!$result_pretty_url) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pretty_url) > 0) {
    mysqli_free_result($result_pretty_url);
    return false;
  }
  else {
    return true;
  }
}

function check_if_cd_pretty_url_is_unique($cd_pretty_url,$category_id = NULL) {
  
  global $db_link;
  
  $query_pretty_url = "SELECT `category_id` FROM `category_descriptions` WHERE `cd_pretty_url` = '$cd_pretty_url' AND `category_id` <> $category_id";
  //echo $query_pretty_url;
  $result_pretty_url = mysqli_query($db_link, $query_pretty_url);
  if(!$result_pretty_url) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pretty_url) > 0) {
    mysqli_free_result($result_pretty_url);
    return false;
  }
  else {
    return true;
  }
}

function resize_crop_image($img_file,$extension) {
  
  $dimensions = @getimagesize($img_file);
  //print_r($dimensions);exit;
  $original_width = $dimensions[0]; //echo $img_file;exit;
  $original_height = $dimensions[1];
  if($original_width > $original_height) {
    $landscape = true;
  }
  if($landscape) {
    $width = 375;
    $height = 250;
  }
  else {
    $width = 169;
    $height = 225;
  }
  $picture_name = 'restaurant_gallery_picture';
  
  $ratio = (($original_width / $original_height) < ($width / $height)) ? $width / $original_width : $height / $original_height;
  $x = max(0, round($original_width / 2 - ($width / 2) / $ratio));
  $y = max(0, round($original_height / 2 - ($height / 2) / $ratio));
  if($extension == 'jpg' || $extension == 'jpeg') {
   $src = imagecreatefromjpeg($img_file);
  }
  elseif($extension == 'gif') {
    $src = imagecreatefromgif($img_file);
  }
  elseif($extension == 'png') {
    $src = imagecreatefrompng($img_file);
  }
  else {
    return $errors['new_picture'] = "Unknown picture format!";
  }
  if($src == false)
  {
     $error = "Unknown problem trying to open uploaded image.";
     return false;
  }
  $resized = imagecreatetruecolor($width, $height);
  $result = imagecopyresampled($resized, $src, 0, 0, $x, $y, $width, $height,
            round($width / $ratio, 0), round($height / $ratio));
  if($result == false)
  {
     $error = "Error trying to resize and crop image.";
     return false;
  }
  else
  {
    if($extension == 'jpg' || $extension == 'jpeg') {
      imagejpeg($resized, $picture_name.'.jpg', 80);
     }
     elseif($extension == 'gif') {
       imagegif($resized, $picture_name.'.gif', 80);
     }
     elseif($extension == 'png') {
       imagepng($resized, $picture_name.'.png', 2);
     }
     else {
       return $errors['new_picture'] = "Unknown picture format!";
     }
    imagedestroy($src);
    imagedestroy($resized);
  }
}

class SimpleImage {   
  var $image; 
  var $image_type;   
  
  function load($filename) {   
    $image_info = getimagesize($filename); 
    $this->image_type = $image_info[2]; 
    if( $this->image_type == IMAGETYPE_JPEG ) {   
      $this->image = imagecreatefromjpeg($filename);
    } elseif($this->image_type == IMAGETYPE_GIF ) {   
      $this->image = imagecreatefromgif($filename); 
    } elseif( $this->image_type == IMAGETYPE_PNG ) {  
      $this->image = imagecreatefrompng($filename); 
    } 
  } 
  
  function save($filename, $image_type=IMAGETYPE_JPEG, $compression=70, $permissions=null) {   
    if( $image_type == IMAGETYPE_JPEG ) { 
      imagejpeg($this->image,$filename,$compression);
    } elseif( $image_type == IMAGETYPE_GIF ) {   
      imagegif($this->image,$filename); 
    } elseif( $image_type == IMAGETYPE_PNG ) {  
      imagepng($this->image,$filename); 
    }
    
    if( $permissions != null) {   
      chmod($filename,$permissions);
    } 
    
  } 
  
  function output($image_type=IMAGETYPE_JPEG) {   
    if( $image_type == IMAGETYPE_JPEG ) { 
      imagejpeg($this->image); 
    } elseif( $image_type == IMAGETYPE_GIF ) {  
      imagegif($this->image); 
    } elseif( $image_type == IMAGETYPE_PNG ) {  
      imagepng($this->image); 
    } 

  } 
  
  function getWidth() {   
    return imagesx($this->image); 
  } 
  
  function getHeight() {   
    return imagesy($this->image); 
  } 
  
  function resizeToHeight($height) {   
    $ratio = $height / $this->getHeight(); 
    $width = $this->getWidth() * $ratio; 
    $this->resize($width,$height); 
  }
  
  function resizeToWidth($width) { 
    $ratio = $width / $this->getWidth(); 
    $height = $this->getheight() * $ratio; 
    $this->resize($width,$height); 
  }
  
  function scale($scale) { 
    $width = $this->getWidth() * $scale/100; 
    $height = $this->getheight() * $scale/100; 
    $this->resize($width,$height); 
  }
  
  function resize($width,$height) { 
    $new_image = imagecreatetruecolor($width, $height); 
    if( $this->image_type == IMAGETYPE_GIF || $this->image_type == IMAGETYPE_PNG ) {
      $current_transparent = imagecolortransparent($this->image); 
      
      if($current_transparent != -1) {
        $transparent_color = imagecolorsforindex($this->image, $current_transparent); 
        $current_transparent = imagecolorallocate($new_image, $transparent_color['red'],$transparent_color['green'], $transparent_color['blue']); 
        imagefill($new_image, 0, 0, $current_transparent); 
        imagecolortransparent($new_image, $current_transparent); 
      } elseif( $this->image_type == IMAGETYPE_PNG) { 
        imagealphablending($new_image, false); 
        $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127); 
        imagefill($new_image, 0, 0, $color); imagesavealpha($new_image, true);
      } 
    }
    
    imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight()); 
    $this->image = $new_image;
  }
}

function get_attribute_group_lаst_child_order_value() {
  
  global $db_link;
  
  $query_ag_sort_order = "SELECT `ag_sort_order` FROM `attribute_group` ORDER BY `ag_sort_order` DESC LIMIT 1";
  //echo $query_ag_sort_order;
  $result_ag_sort_order = mysqli_query($db_link, $query_ag_sort_order);
  if(!$result_ag_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ag_sort_order) > 0) {
    $row_ag_sort_order = mysqli_fetch_assoc($result_ag_sort_order);
    $ag_sort_order = $row_ag_sort_order['ag_sort_order'];
    mysqli_free_result($result_ag_sort_order);
  }
  else {
    $ag_sort_order = 0;
  }
  
  return $ag_sort_order;
}

function get_attribute_lаst_child_order_value($attribute_group_id) {
  
  global $db_link;
  
  $query_attribute_sort_order = "SELECT `attribute_sort_order` FROM `attributes` WHERE `attribute_group_id` = '$attribute_group_id' ORDER BY `attribute_sort_order` DESC LIMIT 1";
  //echo $query_attribute_sort_order;
  $result_attribute_sort_order = mysqli_query($db_link, $query_attribute_sort_order);
  if(!$result_attribute_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_attribute_sort_order) > 0) {
    $row_attribute_sort_order = mysqli_fetch_assoc($result_attribute_sort_order);
    $attribute_sort_order = $row_attribute_sort_order['attribute_sort_order'];
    mysqli_free_result($result_attribute_sort_order);
  }
  else {
    $attribute_sort_order = 0;
  }
  
  return $attribute_sort_order;
}

function get_ov_lаst_child_order_value($option_id) {
  
  global $db_link;
  
  $query_ov_sort_order = "SELECT `ov_sort_order` FROM `option_value` WHERE `option_id` = '$option_id'
                          ORDER BY `ov_sort_order` DESC LIMIT 1";
  //echo $query_ov_sort_order;
  $result_ov_sort_order = mysqli_query($db_link, $query_ov_sort_order);
  if(!$result_ov_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_ov_sort_order) > 0) {
    $row_ov_sort_order = mysqli_fetch_assoc($result_ov_sort_order);
    $ov_sort_order = $row_ov_sort_order['ov_sort_order'];
    mysqli_free_result($result_ov_sort_order);
  }
  else {
    $ov_sort_order = 0;
  }
  
  return $ov_sort_order;
}

function get_option_lаst_child_sort_order() {
  
  global $db_link;
  
  $query_option_sort_order = "SELECT `option_sort_order` FROM `options` ORDER BY `option_sort_order` DESC LIMIT 1";
  //echo $query_option_sort_order;
  $result_option_sort_order = mysqli_query($db_link, $query_option_sort_order);
  if(!$result_option_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_option_sort_order) > 0) {
    $row_option_sort_order = mysqli_fetch_assoc($result_option_sort_order);
    $option_sort_order = $row_option_sort_order['option_sort_order'];
    mysqli_free_result($result_option_sort_order);
  }
  else {
    $option_sort_order = 0;
  }
  
  return $option_sort_order;
}

function check_if_product_is_already_inserted_in_db($product_isbn) {
  
  global $db_link;
  
  $query_duplicated_product = "SELECT `product_id` FROM `products` WHERE `product_isbn` = '$product_isbn'";
  //echo $query_duplicated_product;
  $result_duplicated_product = mysqli_query($db_link, $query_duplicated_product);
  if(!$result_duplicated_product) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_duplicated_product) > 0) {
    return true;
  }
  else {
    return false;
  }
}

function get_all_subcategories($parent_id) {
  
  global $db_link;
  global $new_all_categories_ids;
  
  $query_categories = "SELECT `category_id` FROM `categories` WHERE `category_parent_id` = '$parent_id'";
  //echo $query_categories;exit;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 0) {
    
    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];

      if(!in_array($category_id, $new_all_categories_ids)) $new_all_categories_ids[] = $category_id;
      
      get_all_subcategories($category_id);
    }
  }
    
}

function get_column_names($db_name,$table_name) {
  
  global $db_link;
  $column_names_array = array();
  
  $query_column_names = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$db_name' AND TABLE_NAME = '$table_name'";
  //echo $query_column_names;exit;
  $result_column_names = mysqli_query($db_link, $query_column_names);
  if(!$result_column_names) echo mysqli_error($db_link);
  $column_names_count = mysqli_num_rows($result_column_names);
  if($column_names_count > 0) {
    while($column_names_row = mysqli_fetch_assoc($result_column_names)) {
      $column_names_array[] = $column_names_row['COLUMN_NAME'];
    }
  }
  
  return $column_names_array;
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
  
  $query_pi_name = "SELECT `product_image_id`,`pi_name` 
                    FROM `product_image` 
                    WHERE `product_id` = '$product_id'
                    ORDER BY `pi_sort_order` ASC";
  //echo $query_pi_name;
  $result_pi_name = mysqli_query($db_link, $query_pi_name);
  if(!$result_pi_name) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_pi_name) > 0) {
    while($pi_names_row = mysqli_fetch_assoc($result_pi_name)) {
      $pi_names_array[] = $pi_names_row;
    }
    mysqli_free_result($result_pi_name);
  }
  
  return $pi_names_array;
}

function get_product_last_image_order_value($product_id) {
  
  global $db_link;
  
  $query_image_sort_order = "SELECT `pi_sort_order` FROM `product_image` WHERE `product_id` = '$product_id' 
                            ORDER BY `pi_sort_order` DESC LIMIT 1";
  //echo $query_image_sort_order;exit;
  $result_image_sort_order = mysqli_query($db_link, $query_image_sort_order);
  if(!$result_image_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_image_sort_order) > 0) {
    $row_image_sort_order = mysqli_fetch_assoc($result_image_sort_order);
    $image_sort_order = $row_image_sort_order['pi_sort_order'];
    mysqli_free_result($result_image_sort_order);
  }
  else {
    $image_sort_order = 0;
  }
  
  return $image_sort_order;
}

function get_product_highest_order_value_for_category($category_id) {
  
  global $db_link;
  
  $query_highest_product_sort_order = "SELECT `products`.`product_sort_order`  
                                      FROM `products` 
                                      INNER JOIN `product_to_category` USING(`product_id`)
                                      WHERE `product_to_category`.`category_id` = '$category_id'
                                      ORDER BY `products`.`product_sort_order` DESC
                                      LIMIT 1";
  //echo $query_highest_product_sort_order;
  $result_highest_product_sort_order = mysqli_query($db_link, $query_highest_product_sort_order);
  if(!$result_highest_product_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_highest_product_sort_order) > 0) {
    $row_product_sort_order = mysqli_fetch_assoc($result_highest_product_sort_order);
    $product_sort_order = $row_product_sort_order['product_sort_order'];
    mysqli_free_result($result_highest_product_sort_order);
  }
  else {
    $product_sort_order = 0;
  }
  
  return $product_sort_order;
}

function get_slider_last_order_value() {
  
  global $db_link;
  
  $query_slider_sort_order = "SELECT `slider_sort_order` FROM `sliders` ORDER BY `slider_sort_order` DESC LIMIT 1";
  //echo $query_slider_sort_order;exit;
  $result_slider_sort_order = mysqli_query($db_link, $query_slider_sort_order);
  if(!$result_slider_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_slider_sort_order) > 0) {
    $row_slider_sort_order = mysqli_fetch_assoc($result_slider_sort_order);
    $slider_sort_order = $row_slider_sort_order['slider_sort_order'];
    mysqli_free_result($result_slider_sort_order);
  }
  else {
    $slider_sort_order = 0;
  }
  
  return $slider_sort_order;
}

function get_instructor_last_order_value() {
  
  global $db_link;
  
  $query_instructor_sort_order = "SELECT `instructor_sort_order` FROM `instructors` ORDER BY `instructor_sort_order` DESC LIMIT 1";
  //echo $query_instructor_sort_order;exit;
  $result_instructor_sort_order = mysqli_query($db_link, $query_instructor_sort_order);
  if(!$result_instructor_sort_order) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_instructor_sort_order) > 0) {
    $row_instructor_sort_order = mysqli_fetch_assoc($result_instructor_sort_order);
    $instructor_sort_order = $row_instructor_sort_order['instructor_sort_order'];
    mysqli_free_result($result_instructor_sort_order);
  }
  else {
    $instructor_sort_order = 0;
  }
  
  return $instructor_sort_order;
}

?>
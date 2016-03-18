<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  if(isset($_POST['cancel'])) {
    header('Location: administration-users.php');
  }
  
  if(isset($_POST['submit'])) {
    
    //echo"<pre>";print_r($_POST);echo"</pre>";exit;
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    $store_id = 1;
    $user_username = mysqli_real_escape_string($db_link, $_POST['user_username']);
    $user_firstname = mysqli_real_escape_string($db_link, $_POST['user_firstname']);
    $user_lastname = mysqli_real_escape_string($db_link, $_POST['user_lastname']);
    $user_password = $_POST['user_password'];
    if(!empty($user_password)) {
      $bcrypt_salt = "$2y$08$".generate_bcrypt_salt()."$";
      $bcrypt_password = crypt($user_password , $bcrypt_salt);
    }
    $user_type_id = $_POST['user_type_id'];
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_is_ip_in_use = 0;
    if(isset($_POST['user_is_ip_in_use'])) $user_is_ip_in_use = 1;
    $user_is_active = 0;
    if(isset($_POST['user_is_active'])) $user_is_active = 1;
    
    $query_insert_user = "INSERT INTO `users`(`user_id`, 
                                              `user_type_id`, 
                                              `store_id`, 
                                              `user_username`, 
                                              `user_salted_password`, 
                                              `user_firstname`, 
                                              `user_lastname`, 
                                              `user_ip`, 
                                              `user_is_ip_in_use`, 
                                              `user_is_active`)
                                        VALUES ('',
                                                '$user_type_id',
                                                '$store_id',
                                                '$user_username',
                                                '$bcrypt_password',
                                                '$user_firstname',
                                                '$user_lastname',
                                                '$user_ip',
                                                '$user_is_ip_in_use',
                                                '$user_is_active')";
    $all_queries .= "<br>\n".$query_insert_user;
    $result_insert_user = mysqli_query($db_link, $query_insert_user);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
    
    $user_id = mysqli_insert_id($db_link);
    
    $query_users_rights = "SELECT `users_rights_id`, `menu_id`, `users_rights_edit`, `users_rights_delete` 
                          FROM `users_types_rights` 
                          WHERE `user_type_id` = '$user_type_id'";
    $all_queries .= "<br>\n".$query_users_rights;
    $result_users_rights = mysqli_query($db_link, $query_users_rights);
    if(!$result_users_rights) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_users_rights) > 0) {

      while($default_rights = mysqli_fetch_assoc($result_users_rights)) {

        $menu_id = $default_rights['menu_id'];
        $users_rights_edit = $default_rights['users_rights_edit'];
        $users_rights_delete = $default_rights['users_rights_delete'];

        $query = "INSERT INTO `users_rights`(`users_rights_id`, 
                                            `user_id`, 
                                            `menu_id`,  
                                            `users_rights_edit`, 
                                            `users_rights_delete`)
                                    VALUES ('',
                                            '$user_id',
                                            '$menu_id',
                                            '$users_rights_edit',
                                            '$users_rights_delete')";
        $all_queries .= "<br>\n".$query;
        $result = mysqli_query($db_link, $query);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }

      }

    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    header('Location: administration-users.php');
  }
  //if(isset($_POST['submit'])
  
  $page_title = $languages[$current_lang]['text_add_new_user'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--navigation-->
  <main id="page_details">
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['header_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/administration/administration-users.php"><?=$languages[$current_lang]['text_users'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['text_add_new_user'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['text_add_new_user'];?></h1>
      
      <form method="post" class="input_form" action="<?=$_SERVER['PHP_SELF'];?>">

        <div>
          <p class="title"><?=$languages[$current_lang]['header_user_username'];?><span class="red">*</span></p>
          <input type="text" name="user_username" id="user_username" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_user_password'];?><span class="red">*</span></p>
          <input type="password" name="user_password" id="user_password" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_user_firstname'];?><span class="red">*</span></p>
          <input type="text" name="user_firstname" id="user_firstname" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_user_lastname'];?><span class="red">*</span></p>
          <input type="text" name="user_lastname" id="user_lastname" style="width: 400px;" />
        </div>
        <div class="clearfix"></div>

        <div>
          <p class="title"><?=$languages[$current_lang]['header_customer_group'];?><span class="red">*</span></p>
          <select name="user_type_id" id="user_type_id" style="width: 600px;">
<?php
          $query = "SELECT `user_type_id`, `user_type_name` FROM `users_types` 
                    WHERE `language_id` = '$current_language_id' 
                    ORDER BY `user_type_sort_order` ASC";
          //echo $query;
          $users_result = mysqli_query($db_link, $query);
          if (!$users_result) echo mysqli_error($db_link);
          if(mysqli_num_rows($users_result) > 0) {
            while ($user_details = mysqli_fetch_assoc($users_result)) {
              $user_type_id = $user_details['user_type_id'];
              $user_type_name = $user_details['user_type_name'];

?>
            <option value="<?=$user_type_id;?>"><?=$user_type_name;?></option>
<?php    
            }
          }
?>
          </select>
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_user_is_ip_in_use'];?></p>
          <input type="checkbox" name="user_is_ip_in_use" id="user_is_ip_in_use" checked="checked" />
        </div>
        <div class="clearfix"></div>
          
        <div>
          <p class="title"><?=$languages[$current_lang]['header_is_active'];?></p>
          <input type="checkbox" name="user_is_active" id="user_is_active" checked="checked" />
        </div>
        <div class="clearfix"></div>
        <div class="clearfix">
          <p>&nbsp;</p>
        </div>

        <div>
          <button type="submit" name="submit" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
        </div>
        <div class="clearfix"></div>
        
      </form>
      <div class="clearfix"></div>
    </div>
  </main>
<!--navigation-->

<?php
 
  print_html_admin_footer();
  
?>
</body>
</html>
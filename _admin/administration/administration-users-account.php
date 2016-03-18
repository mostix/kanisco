<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['header_administration_users_account'];
  $page_description = "E-shop администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_administration_users_account'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      <table>
        <thead>
          <tr>
            <th width="5%"><?=$languages[$current_lang]['btn_save'];?></th>
            <th width="20%"><?=$languages[$current_lang]['header_user_username'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_user_password'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_user_firstname'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_user_lastname'];?></th>
            <th></th>
          </tr>
        </thead>
      </table>
<?php
  if(isset($_SESSION['admin']['user_id'])) {
    $user_id = $_SESSION['admin']['user_id'];
  }
  
  $query = "SELECT `users`.`user_username`, `users`.`user_firstname`, `users`.`user_lastname`
            FROM `users`
            WHERE `users`.`user_id` = '$user_id'";
  $result_users = mysqli_query($db_link,$query);
  if (!$result_users) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_users) > 0) {// if (!$result_users)
    
    $user_details = mysqli_fetch_assoc($result_users);

    $user_username = $user_details['user_username'];
    $user_firstname = $user_details['user_firstname'];
    $user_lastname = $user_details['user_lastname'];
?>
      <div id="user<?=$user_id; ?>" class="row_over">
        <table>
          <tbody>
            <tr>
              <td width="5%"><button class="btn_save" onClick="EditRestrictedUser('<?=$user_id; ?>')"><?=$languages[$current_lang]['btn_save'];?></button></td>
              <td width="20%"><input type="text" id="user_username<?=$user_id; ?>" class="user_username" value="<?=$user_username; ?>" ></td>
              <td width="10%"><input type="password" id="user_password<?=$user_id; ?>" class="user_password" placeholder="******" ></td>
              <td width="15%"><input type="text" id="user_firstname<?=$user_id; ?>" class="user_firstname" value="<?=$user_firstname; ?>" ></td>
              <td width="15%"><input type="text" id="user_lastname<?=$user_id; ?>" class="user_lastname" value="<?=$user_lastname; ?>" ></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
<?php
    mysqli_free_result($result_users);
  }// if (!$result_users)
?>
      <div class="clearfix"></div>
    </div>
  </main>
<!--main-->
<?php 
    print_html_admin_footer();
?>
</body>
</html>
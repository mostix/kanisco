<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  //echo "<pre>";print_r($_SERVER);
  //start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = $_SERVER['PHP_SELF']);
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  //print_r($user_rights);
  
  if(isset($_GET['course_id'])) {
    $current_course_id = $_GET['course_id'];
    
    include_once 'course-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['current_pages_title'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--courses list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_courses'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_courses'];?></h1>
      
      <section class="options margin_bottom">
        <a class="pageoptions" href="course-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['link_add_new_course'];?>" />
          <?=$languages[$current_lang]['link_add_new_course'];?>
        </a>
      </section>

      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="30%" class="text_left"><?=$languages[$current_lang]['header_name'];?></th>
            <th width="12%"><?=$languages[$current_lang]['header_date'];?></th>
            <th width="34%"><?=$languages[$current_lang]['header_duration'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_price'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_status'];?></th>
            <th width="9%"><?=$languages[$current_lang]['header_actions'];?></th>
            <th width="5%" title="<?=$languages[$current_lang]['title_toggle_checkbox_all'];?>">
              <input id="selectall" type="checkbox" onclick="SelectAllCheckboxes(this)" />
            </th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" alt="<?=$languages[$current_lang]['alt_deactivate'];?>" title="<?=$languages[$current_lang]['title_deactivate'];?>" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" alt="<?=$languages[$current_lang]['alt_activate'];?>" title="<?=$languages[$current_lang]['title_activate'];?>" width="16" height="16" />
      </div>
      <div id="courses_list" class="list_container">
<?php
        list_courses();
?>
      </div>
    </div>
  </main>
<!--contents list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }
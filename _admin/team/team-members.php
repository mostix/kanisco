<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = $_SERVER['PHP_SELF']);
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  //print_r($user_rights);
  
  if(isset($_GET['team_member_id'])) {
    $current_team_member_id = $_GET['team_member_id'];
    
    include_once 'team-member-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['team_members_title'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_team_members'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/team/team-member-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_team_member'];?>" title="<?=$languages[$current_lang]['title_add_new_language'];?>" />
          <?=$languages[$current_lang]['link_add_new_team_member'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="30%" class="text_left"><?=$languages[$current_lang]['header_name'];?></th>
            <th width="30%" class="text_left"><?=$languages[$current_lang]['header_image'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_reorder'];?></th>
            <th width="10%" colspan="2"><?=$languages[$current_lang]['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" alt="<?=$languages[$current_lang]['alt_deactivate'];?>" title="<?=$languages[$current_lang]['title_deactivate'];?>" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" alt="<?=$languages[$current_lang]['alt_activate'];?>" title="<?=$languages[$current_lang]['title_activate'];?>" width="16" height="16" />
      </div>
      <div id="team_members_list" class="list_container">
<?php
        list_team_members();
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

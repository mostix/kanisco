<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = $_SERVER['PHP_SELF']);
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  //print_r($user_rights);
  
  if(isset($_GET['language_id'])) {
    $current_language_id = $_GET['language_id'];
    
    include_once 'language-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['languages_title'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_languages'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/language/language-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_language'];?>" title="<?=$languages[$current_lang]['title_add_new_language'];?>" />
          <?=$languages[$current_lang]['link_add_new_language'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="20%" class="text_left"><?=$languages[$current_lang]['header_language_code'];?></th>
            <th width="20%" class="text_left"><?=$languages[$current_lang]['header_language_name'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_language_is_active'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_language_is_default_frontend'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_language_is_default_backend'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_reorder_languages'];?></th>
            <th width="10%" colspan="2"><?=$languages[$current_lang]['header_actions_for_languages'];?></th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" alt="<?=$languages[$current_lang]['alt_deactivate'];?>" title="<?=$languages[$current_lang]['title_deactivate'];?>" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" alt="<?=$languages[$current_lang]['alt_activate'];?>" title="<?=$languages[$current_lang]['title_activate'];?>" width="16" height="16" />
      </div>
      <div id="languages_list" class="list_container">
<?php
        list_languages();
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

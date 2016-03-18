<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['current_pages_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_pages'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_pages'];?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="content-add-new.php" title="<?=$languages[$current_lang]['title_content_add_new'];?>">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_content_add_new'];?>" />
          <?=$languages[$current_lang]['link_content_add_new'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandContent('all','expand')" title="<?=$languages[$current_lang]['title_expand_all_sections'];?>">
          <img src="/_admin/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_expand_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandContent('all','collapse')" title="<?=$languages[$current_lang]['title_collapse_all_sections'];?>">
          <img src="/_admin/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_collapse_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_collapse_all_sections'];?>
        </a>
        <a class="pageoptions" href="content-reorder.php" title="<?=$languages[$current_lang]['title_reorder_pages'];?>">
          <img src="/_admin/images/reorder.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_reorder_pages'];?>" />
          <?=$languages[$current_lang]['menu_reorder_pages'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&nbsp;</th>
            <th width="5%" class="text_left">&nbsp;</th>
            <th width="25%" class="text_left" title="<?=$languages[$current_lang]['title_content_description'];?>"><?=$languages[$current_lang]['header_content_description'];?></th>
            <th width="15%" class="text_left" title="<?=$languages[$current_lang]['title_content_alias'];?>"><?=$languages[$current_lang]['header_content_alias'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_content_type'];?></th>
            <th width="5%" title="<?=$languages[$current_lang]['title_active_content'];?>"><?=$languages[$current_lang]['header_content_is_active'];?></th>
            <th width="5%" title="<?=$languages[$current_lang]['title_home_page_content'];?>"><?=$languages[$current_lang]['header_content_is_home_page'];?></th>
            <th width="10%" title="<?=$languages[$current_lang]['title_content_is_default'];?>"><?=$languages[$current_lang]['header_content_is_default'];?></th>
            <th width="7%" title="<?=$languages[$current_lang]['title_reorder_contents'];?>"><?=$languages[$current_lang]['header_reorder_contents'];?></th>
            <th width="12%"><?=$languages[$current_lang]['header_actions_for_contents'];?></th>
            <th width="4%" title="<?=$languages[$current_lang]['title_toggle_checkbox_all'];?>">
              <input id="selectall" type="checkbox" onclick="SelectAllCheckboxes(this)" />
            </th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" alt="<?=$languages[$current_lang]['alt_deactivate_content'];?>" title="<?=$languages[$current_lang]['title_deactivate_content'];?>" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" alt="<?=$languages[$current_lang]['alt_activate_content'];?>" title="<?=$languages[$current_lang]['title_activate_content'];?>" width="16" height="16" />
      </div>
      <div id="contents_list" class="list_container">
<?php
        list_contents($parent_id = 0, $path_number = 0);
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

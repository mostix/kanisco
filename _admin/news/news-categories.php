<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = $_SERVER['PHP_SELF']);
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  //print_r($user_rights);
  
  if(isset($_GET['news_category_id'])) {
    $current_news_category_id = $_GET['news_category_id'];
    
    include_once 'news-categories-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['news_categories_title'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_news_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/news/news-categories-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_language'];?>" title="<?=$languages[$current_lang]['title_add_new_language'];?>" />
          <?=$languages[$current_lang]['link_add_new_category'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandNewsCategory('all','expand')" title="<?=$languages[$current_lang]['title_expand_all_sections'];?>">
          <img src="/_admin/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_expand_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandNewsCategory('all','collapse')" title="<?=$languages[$current_lang]['title_collapse_all_sections'];?>">
          <img src="/_admin/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_collapse_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_collapse_all_sections'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="3%" class="text_left">&nbsp;</th>
            <th width="7%" class="text_left">&nbsp;</th>
            <th width="60%" class="text_left"><?=$languages[$current_lang]['header_news_category_name'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_reorder'];?></th>
            <th width="15%" colspan="2"><?=$languages[$current_lang]['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="news_categories_list" class="list_container">
<?php
        list_news_categories($news_cat_parent_id = 0, $path_number = 0);
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
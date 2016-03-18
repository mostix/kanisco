<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['catalog_categories_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_catalog_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/catalog/catalog-categories-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_language'];?>" title="<?=$languages[$current_lang]['title_add_new_language'];?>" />
          <?=$languages[$current_lang]['link_add_new_catalog_category'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="20%" class="text_left"><?=$languages[$current_lang]['header_language_code'];?></th>
            <th width="20%" class="text_left"><?=$languages[$current_lang]['header_language_name'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_language_is_active'];?></th>
            <th width="7%"><?=$languages[$current_lang]['header_language_is_default'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_reorder_languages'];?></th>
            <th width="10%" colspan="2"><?=$languages[$current_lang]['header_actions_for_languages'];?></th>
          </tr>
        </thead>
      </table>
      <div id="languages_list" class="list_container">
        <table>
          <tbody>
<?php
            list_languages();
?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
<!--contents list-->

<?php
 
  print_html_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
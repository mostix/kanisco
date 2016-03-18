<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['products_options_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products_options'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/catalog/products-options-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_products_options'];?>" title="<?=$languages[$current_lang]['title_add_new_products_options'];?>" />
          <?=$languages[$current_lang]['link_add_new_products_options'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="50%" class="text_left"><?=$languages[$current_lang]['header_products_option_name'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_products_option_type'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_sort_order'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_reorder'];?></th>
            <th width="15%" colspan="2"><?=$languages[$current_lang]['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="products_options_list" class="list_container">
<?php
        list_products_options();
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
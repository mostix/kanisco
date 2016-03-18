<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['products_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
?>

<!--products list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/catalog/products-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_language'];?>" title="<?=$languages[$current_lang]['title_add_new_language'];?>" />
          <?=$languages[$current_lang]['link_add_new_product'];?>
        </a>
<!--        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandproduct('all','expand')" title="<?=$languages[$current_lang]['title_expand_all_sections'];?>">
          <img src="/_admin/images/expandall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_expand_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_expand_all_sections'];?>
        </a>
        <a class="pageoptions" href="javascript:;" onclick="ToggleExpandproduct('all','collapse')" title="<?=$languages[$current_lang]['title_collapse_all_sections'];?>">
          <img src="/_admin/images/contractall.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_collapse_all_sections'];?>" />
          <?=$languages[$current_lang]['menu_collapse_all_sections'];?>
        </a>-->
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="45%" class="text_left"><?=$languages[$current_lang]['header_product_category_name'];?></th>
            <th width="10%" class="text_left"><?=$languages[$current_lang]['header_product_name'];?></th>
            <th width="15%" class="text_left"><?=$languages[$current_lang]['header_product_image'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_product_is_active'];?></th>
            <th width="11%"><?=$languages[$current_lang]['header_reorder_product'];?></th>
            <th width="14%" colspan="2"><?=$languages[$current_lang]['header_actions_for_product'];?></th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" width="16" height="16" />
      </div>
      <div id="products_list" class="list_container">
<?php
        list_products($parent_id = 0, $path_number = 0, $class = "even");
?>
      </div>
    </div>
  </main>
<!--products list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
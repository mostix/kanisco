<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['category_ids_list'])) {
    $category_ids_list = $_GET['category_ids_list'];
    if(strpos($category_ids_list, ",")) {
      $category_ids_array = explode(",", $category_ids_list);
      $ids_count = count($category_ids_array)-1;
    }
    $current_category_id = $category_ids_array[$ids_count];
  }
  else {
    exit("Error");
  }
  if(isset($_GET['category_name'])) {
    $current_category_name = $_GET['category_name'];
  }
  
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
        <a href="/_admin/catalog/products-categories.php" title="<?=$languages[$current_lang]['title_breadcrumbs_products_categories'];?>"><?=$languages[$current_lang]['header_products_categories'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/catalog/products-add-new.php?category_id=<?=$current_category_id;?>&category_name=<?=$current_category_name;?>">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_product'];?>" title="<?=$languages[$current_lang]['title_add_new_product'];?>" />
          <?=$languages[$current_lang]['link_add_new_product'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="45%" class="text_left"><?=$languages[$current_lang]['header_product_category_name'];?></th>
            <th width="15%" class="text_left"><?=$languages[$current_lang]['header_product_name'];?></th>
            <th width="16%"><?=$languages[$current_lang]['header_product_image'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_product_is_active'];?></th>
            <th width="11%" class="hidden"><?=$languages[$current_lang]['header_reorder_product'];?></th>
            <th width="12%" colspan="2"><?=$languages[$current_lang]['header_actions_for_product'];?></th>
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
        list_products($category_ids_list, $current_category_id, $first_iteration = true);
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
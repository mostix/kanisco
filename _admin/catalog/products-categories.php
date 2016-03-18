<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['products_categories_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
  if(isset($_GET['category_ids_list'])) {
    $category_ids_list = $_GET['category_ids_list'];
    if(strpos($category_ids_list, ",")) {
      $category_ids_array = explode(",", $category_ids_list);
      $ids_count = count($category_ids_array)-1;
    }
    $current_category_id = $category_ids_array[$ids_count];
  }
  else {
    $category_ids_array = array();
    $current_category_id = 0;
  }
?>

<!--categories list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_products_categories'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options" style="padding-left: 21%;display: none;">
        <a class="pageoptions" href="">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_add_new_product'];?>" title="<?=$languages[$current_lang]['title_add_new_product'];?>" />
          <?=$languages[$current_lang]['link_add_new_product'];?>
        </a>
      </section>
      
      <div id="left_column">
        <table id="choose_category" class="list_container margin_bottom level_1">
          <thead>
            <tr>
              <th><?=$languages[$current_lang]['header_choose_category'];?></th>
            </tr>
          </thead>
          <tbody>
<?php
            list_categories_for_products($category_ids_array,$parent_id = 0, $path_number = 0);
?>
          </tbody>
        </table>
        <div id="subcategories">
          
        </div>
      </div>
      
      <div id="right_column" class="list_container" <?php if($current_category_id == 0) echo 'style="display: none;"';?>>
        <table>
          <thead>
            <tr>
              <th width="15%" class="text_left"><?=$languages[$current_lang]['header_product_isbn'];?></th>
              <th width="16%"><?=$languages[$current_lang]['header_product_image'];?></th>
              <th width="10%"><?=$languages[$current_lang]['header_product_is_active'];?></th>
              <th width="11%" class="hidden"><?=$languages[$current_lang]['header_reorder_product'];?></th>
              <th width="12%" colspan="2"><?=$languages[$current_lang]['header_actions_for_product'];?></th>
            </tr>
          </thead>
        </table>
        <div id="products_list" class="list_container">
<?php
//        if($current_category_id != 0) {
//          
//          list_products($category_ids_list, $current_category_id, $first_iteration = true); 
//        }
?>
        </div>
      </div>
      
      <div class="clearfix"></div>
      
    </div>
  </main>
<!--categories list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
?>
  <script type="text/javascript">
    $(document).ready(function() {
      shortcut.add("right",function() {
        var btn = $(".btn_next_page a");
        if($(btn).length) {
          JsPaginating(btn);
        }
      });
      shortcut.add("left",function() {
        var btn = $(".btn_prev_page a");
        if($(btn).length) {
          JsPaginating(btn);
        }
      });
    });
  </script>
</body>
</html>
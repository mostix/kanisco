<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  //echo "<pre>";print_r($_SERVER);
  //start_page_build_time_measure();
  
  $user_rights = get_admin_user_rights($menu_url = $_SERVER['PHP_SELF']);
  
  $users_rights_edit = $user_rights['users_rights_edit'];
  $users_rights_delete = $user_rights['users_rights_delete'];
  //print_r($user_rights);
  
  $news_cat_id = 0;
  $filters_array = "";
  $order_by = "`news`.`news_post_date` DESC";
  $page_limit = 25;
  if(isset($_POST['submit_filter'])) {
    $filters_array = $_POST;
    
    $category = $filters_array['news_cat_parent_params'];
    if($category != "all") {
      // $_POST['news_cat_parent_params'] has three parameters - parent_id, hierarchy_ids and hierarchy_level
      $news_cat_parent_params = explode("+", $_POST['news_cat_parent_params']);
      $news_cat_id = $news_cat_parent_params[0];
      $news_cat_hierarchy_ids = $news_cat_parent_params[1];
      $news_cat_hierarchy_level = $news_cat_parent_params[2]+1;
    }
    $order_by = $filters_array['order_by'];
    $page_limit = $filters_array['page_limit'];
  }
  
  if(isset($_GET['news_id'])) {
    $current_news_id = $_GET['news_id'];
    
    include_once 'news-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['current_pages_title'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--newss list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_news'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_news'];?></h1>

      <fieldset>
      <legend><?=$languages[$current_lang]['header_filters'];?></legend>
        <form method="post" name="filter_news" action="<?=$_SERVER['PHP_SELF'];?>">
          <div>
            <label for="news_cat_parent_params" class="title"><?=$languages[$current_lang]['header_categories'];?>:</label>
            <select name="news_cat_parent_params" style="width: auto;">
              <option value="all" selected="selected"><?=$languages[$current_lang]['option_choose_categories_for_product'];?></option>
              <?php
                  list_news_categories_for_select($news_cat_id);
              ?>
            </select>
            <?=$languages[$current_lang]['text_show_children_cat'];?>: <input type="checkbox" class="cms_checkbox" name="all_categories" value="yes" />
          </div>
          <div>
            <label for="order_by" class="title"><?=$languages[$current_lang]['header_sort_order'];?>:</label>
            <select name="order_by" style="width: auto;">
              <option value="`news_post_date` DESC" <?php if($order_by == "`news_post_date` DESC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_post_date_desc'];?></option>
              <option value="`news_post_date` ASC" <?php if($order_by == "`news_post_date` ASC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_post_date_asc'];?></option>
              <option value="`news_end_time` DESC" <?php if($order_by == "`news_end_time` DESC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_expiry_date_desc'];?></option>
              <option value="`news_end_time` ASC" <?php if($order_by == "`news_end_time` DESC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_expiry_date_asc'];?></option>
              <option value="`news_title` ASC" <?php if($order_by == "`news_title` ASC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_title_asc'];?></option>
              <option value="`news_title` DESC" <?php if($order_by == "`news_title` DESC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_title_desc'];?></option>
              <option value="`news_is_active` ASC" <?php if($order_by == "`news_is_active` ASC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_status_asc'];?></option>
              <option value="`news_is_active` DESC" <?php if($order_by == "`news_is_active` DESC") echo 'selected="selected"';?>><?=$languages[$current_lang]['option_status_desc'];?></option>
            </select>
          </div>
          <div>
            <label for="page_limit" class="title"><?=$languages[$current_lang]['header_page_limit'];?>:</label>
            <select name="page_limit" style="width: auto;">
              <option value="5" <?php if($page_limit == 5) echo 'selected="selected"';?>>5</option>
              <option value="25" <?php if($page_limit == 25) echo 'selected="selected"';?>>25</option>
              <option value="50" <?php if($page_limit == 50) echo 'selected="selected"';?>>50</option>
              <option value="100" <?php if($page_limit == 100) echo 'selected="selected"';?>>100</option>
              <option value="500" <?php if($page_limit == 500) echo 'selected="selected"';?>>500</option>
              <option value="1000" <?php if($page_limit == 1000) echo 'selected="selected"';?>>1000</option>
              <option value="0" <?php if($page_limit == 0) echo 'selected="selected"';?>><?=$languages[$current_lang]['option_unlimited'];?></option>
            </select>
          </div>
          <p>&nbsp;</p>
          <div>
            <input type="submit" name="submit_filter" id="submit_filter" class="button blue" value="<?=$languages[$current_lang]['btn_filter'];?>" style="width: auto;" />
          </div>
        </form>

      </fieldset>
      
      <section class="options margin_bottom">
        <a class="pageoptions" href="news-add-new.php" title="<?=$languages[$current_lang]['title_news_add_new'];?>">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['alt_news_add_new'];?>" />
          <?=$languages[$current_lang]['link_news_add_new'];?>
        </a>
      </section>

      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="33%" class="text_left"><?=$languages[$current_lang]['header_news_title'];?></th>
            <th width="12%" class="text_left"><?=$languages[$current_lang]['header_news_post_date'];?></th>
            <th width="8%" class="text_left"><?=$languages[$current_lang]['header_news_start_time'];?></th>
            <th width="8%" class="text_left"><?=$languages[$current_lang]['header_news_end_time'];?></th>
            <th width="20%"><?=$languages[$current_lang]['header_news_category'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_news_status'];?></th>
            <th width="9%"><?=$languages[$current_lang]['header_actions'];?></th>
            <th width="4%" title="<?=$languages[$current_lang]['title_toggle_checkbox_all'];?>">
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
      <div id="news_list" class="list_container">
<?php
        list_news($filters_array);
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
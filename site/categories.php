<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);

  if(isset($_GET['cid'])) {
    $current_category_id = $_GET['cid']; // current selected category_id
  }
  if(isset($_GET['offset'])) {
    $offset = $_GET['offset']; // content_root_id
  }
  else $offset = 0;
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = $_GET['page'];
    $current_category_path = explode("/", $current_page_path_string);
    $count_category_path_elements = count($current_category_path)-1;
    $current_category_pretty_url = $current_category_path[$count_category_path_elements];
    $current_lang = $current_category_path[0];
    $current_page_pretty_url = $current_category_path[1];
    $query_where_page = "`content_pretty_url` = '$current_page_pretty_url'";
  }
  else {
    
    $query_where_page = "`content_is_default` = '1'";
  }
  
  $query_content = "SELECT `content_hierarchy_ids`,`content_show_newsletter`,`content_show_clients`
                    FROM `contents`
                    WHERE $query_where_page";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_show_newsletter = $content_array['content_show_newsletter'];
    $content_show_clients = $content_array['content_show_clients'];
  }

  $query_current_params = "SELECT `languages`.`language_id`,`contents`.`content_hierarchy_path` 
                          FROM `languages` 
                          INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                          WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $home_page_url = $row_current_params['content_hierarchy_path'];
  }
 
  $content_meta_title = "";
  $content_meta_description = "";
  $content_meta_keywords = "";
  $additional_script = '<script type="text/javascript" src="/site/js/jquery.jscrollpane.min.js"></script>';
  $additional_script = '';
//  print_html_categories_header($content_meta_title, $content_meta_description, $content_meta_keywords);
  print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_script);
   
  //echo $current_page_path_string;
  //echo"<pre>";print_r($_GET);
?>
<!-- PAGE TITLE -->
    <div class="container m-bot-15 clearfix">
      <div class="sixteen columns">
        <div class="page-title-container clearfix">
          <h1 class="page-title">Продукти</h1>
          <ul id="filter">
            <?php print_header_categories_menu($current_page_pretty_url,$current_category_id);?>
          </ul>
        </div>	
      </div>
    </div>
<!-- CONTENT -->
    <div class="container filter-portfolio clearfix">
      <div id="portfolio" class="clearfix">
<?php
  $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,`categories`.`category_image_path`,
                              `category_descriptions`.`cd_name`,`category_descriptions`.`cd_pretty_url`,`category_descriptions`.`cd_hierarchy_path`, 
                              `category_descriptions`.`cd_meta_title`,`category_descriptions`.`cd_meta_description`,`category_descriptions`.`cd_meta_keywords` 
                      FROM `categories`
                      INNER JOIN `category_descriptions` USING(`category_id`)
                      WHERE `categories`.`category_parent_id` = '$current_category_id' AND `categories`.`category_hierarchy_level` = '2' 
                        AND `categories`.`category_is_active` = '1'
                        AND `category_descriptions`.`language_id` = '$current_language_id'
                      ORDER BY `categories`.`category_sort_order` ASC";
  //echo $query_categories."<br>";
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  $categories_array = array();
  $categories_ids = array();
  if($category_count > 0) {

    $category_key = 0;
    $row_counter = 1;
    
    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $cd_name = $category_row['cd_name'];
      $cd_pretty_url = $category_row['cd_pretty_url'];
      $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
      $category_image_name = $category_row['category_image_path'];
      $image_path = "/site/images/category-thumbs/";
      $category_image_path = (!empty($category_image_name)) ? $image_path.$category_image_name : "/site/images/content/port-2-1.jpg";
      
      if($row_counter == 1) {
        echo "<div class='row'>";
      }
?>
      <div data-id="id-<?=$category_id;?>" data-type="category_<?=$category_id;?>" class="one-third column m-bot-25">
        <div class="content-container-white">
          <div class="view view-first">
            <img src="<?=$category_image_path?>" alt="<?=$cd_name;?>" width="298" height="176" >
          </div>
          <!--<div class="lw-item-caption-container">&nbsp;</div>-->
        </div>
        <div class="under-box-bg"></div>		
        <div class="accordion">
					
          <h3><a href="javascript:;"><span></span><?=$cd_name;?></a></h3>
          <div class="toggle">
            <?php list_products_by_option_value($category_id,$offset,$current_page_path_string,$cd_pretty_url);?>
          </div>

        </div><!-- End accordion -->
      </div>
<?php
      if($row_counter == 3 || $category_key == $category_count-1) {
        $row_counter = 0;
        echo "</div>";
      }
      $category_key++;
      $row_counter++;
    } //while($category_row)
    
  }
  else {
    //must not happen
  }
?>
    </div>
  </div>
<?php
        if($content_show_newsletter == 1) {
          print_newsletter_form();
        }
        
        if(!is_null($content_show_clients)) {
          list_clients($count = $content_show_clients);
        }
?>
  <script>
    $(function() {
      $(".accordion").bind('click', function() {
        if($(this).hasClass("opened")) {
          $(".accordion .toggle").hide();
          $(this).removeClass("opened");
        }
        else {
          $(".accordion .toggle").hide();
          $(".accordion .toggle").removeClass("opened");
          $(this).find(".toggle").show().end().addClass("opened");
        }
      });
      $(".php_pagination a").bind('click', function() {
        var offset = $(this).attr("data");
        LoadPaginationProductsForCategory(offset);
      });
    });
  </script>
<?php
  print_html_footer();
?>
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);

  if(isset($_GET['ncid'])) {
    $news_category_id = $_GET['ncid']; // current selected news_category_id
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
    $current_news_page_1 = "/".$current_category_path[0]."/".$current_category_path[1];
    $current_category_pretty_url = $current_category_path[$count_category_path_elements];
    $current_lang = $current_category_path[0];
  }
  
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path`
                          FROM `languages` 
                          INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                          WHERE `language_code` = '$current_lang'";
  //echo $query_current_params;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $language_root_content_id = $row_current_params['language_root_content_id'];
    $home_page_url = $row_current_params['content_hierarchy_path'];
  }

  $content_type_id = 7; // news
  $query_content = "SELECT `content_name`,`content_hierarchy_ids`,`content_show_newsletter`,`content_show_clients`,`content_attribute_2`,`content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` = '$language_root_content_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_name = $content_array['content_name'];
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_show_newsletter = $content_array['content_show_newsletter'];
    $content_show_clients = $content_array['content_show_clients'];
    $news_ads_banner = $content_array['content_attribute_2'];
    @$news_ads_banner_dimens = getimagesize($news_ads_banner);
    $content_pretty_url = $content_array['content_pretty_url'];
  }
      
  print_html_header("", "", "");
?>
  <div class="container m-bot-15 clearfix">
    <div class="sixteen columns">
      <div class="page-title-container clearfix">
        <h1 class="page-title">
          <a href="/"><?=$languages[$current_lang]['header_home'];?></a>
          / <a href="<?=$current_news_page_1;?>"><?=$languages[$current_lang]['menu_news'];?></a>
          <span class="sub-title">/ <?=get_news_category_name_by_id($news_category_id,$current_language_id);?></span>
        </h1>
      </div>	
    </div>
  </div>

<!-- CONTENT -->
  <div class="container clearfix">
    <div id="news_list" class="twelve columns m-bot-25">
      
<?php

    $page_offset = 4;
    $offset = ($offset) ? $offset : 0;

    list_news($offset);
?>
            
    </div>
    <!--<div class="twelve columns m-bot-25">-->
    
    <!-- SIDEBAR -->
    <div class="four columns">
      
      <div class="sidebar-item  m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"><?=$languages[$current_lang]['header_news_categories'];?></h3>
        </div>
        <div class="under-box-bg"></div>
        <div class="blog-categories-container">
          <ul id="news_categories" class="blog-categories">
            <?php list_news_categories($news_cat_parent_id = 0,$current_news_cat_id = false,$news_categories_count = false) ?>
          </ul>
        </div>
      </div>
      <script>
        $(function() {
          if($("li.has_parent.active").length) {
            var active_child = $("li.has_parent.active");
            active_child.closest("li.has_children").addClass("active");
          }
//          $("#news_categories li.has_children").bind('click', function() {
//            $("#news_categories li.has_children.active ul").slideUp();
//            $(this).addClass("active").find("ul").slideDown();
//          });
        });
      </script>
      
<?php
        if(!empty($news_ads_banner)) {
?>
      <div class="sidebar-item  m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"></h3>
        </div>

        <div class="under-box-bg"></div>
        
        <div class="content-container-white padding-all-15">
          
          <div class="content-container-white">
            <img src="<?=$news_ads_banner;?>" alt="Рекламен банер">
          </div>
					
        </div>
      </div>
<?php
          }
?>
    </div>
    <!--<div class="five columns">-->
  </div>
<?php
  print_html_footer();
?>
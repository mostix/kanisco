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

    $and_news_category = ($news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
    if($news_category_id == 2) {
      //products news
      $query_news_categories = "SELECT `news_category_id`
                                FROM `news_categories` 
                                WHERE `news_categories`.`news_cat_parent_id` = '$news_category_id'
                                ORDER BY `news_categories`.`news_cat_sort_order` ASC";
      $result_news_categories = mysqli_query($db_link, $query_news_categories);
      if(!$result_news_categories) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_news_categories) > 0) {
        $and_news_category = "AND `news`.`news_category_id` IN(";
        $key = 0;
        while($news_category_row = mysqli_fetch_assoc($result_news_categories)) {
          $and_news_category .= ($key == 0) ? $news_category_row['news_category_id'] : ",".$news_category_row['news_category_id'];
          $key++;
        }
        $and_news_category .= ")";
      }
    }

    $query_news = "SELECT `news`.`news_id`
                    FROM `news`
                    WHERE `news`.`news_is_active` = '1' $and_news_category";
    //echo $query_news."<br>";
    $result_news = mysqli_query($db_link, $query_news);
    if(!$result_news) echo mysqli_error($db_link);
    $news_count = mysqli_num_rows($result_news);
    
    $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                          `news`.`news_is_active`,`news`.`news_image`,`news`.`news_views`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary` 
                      FROM `news` 
                      INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                      WHERE `news`.`news_is_active` = '1' $and_news_category
                        AND `news_descriptions`.`language_id` = '$current_language_id'
                      ORDER BY `news`.`news_created_date` DESC
                      LIMIT $offset,$page_offset";
    //echo $query_news;
    $result_news = mysqli_query($db_link, $query_news);
    if(!$result_news) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_news) > 0) {

      // if the results are more then $page_offset
      // making a pagination, finding how many pages will be needed
      $current_page = ($offset/$page_offset)+1;

      if($news_count > $page_offset) {
        $page_count = ceil($news_count/$page_offset);
      }

      while($news_row = mysqli_fetch_assoc($result_news)) {

        $news_id = $news_row['news_id'];
        $news_title = stripslashes($news_row['news_title']);
        $news_title_escaped = str_replace(array('\\','?','!','.','(',')','%'), array('','','','','','',''), $news_title);
        $news_title_url = str_replace(" ", "-", mb_convert_case($news_title_escaped, MB_CASE_LOWER, "UTF-8"));
        //$news_summary = truncate($news_row['news_summary']);
        $news_summary = mb_strimwidth(stripslashes($news_row['news_summary']), 0, 551, "...");
        $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
        $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
        $news_post_date_month = $languages[$current_lang][$news_post_date_month_text];
        $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
        $news_start_time = $news_row['news_start_time'];
        $news_end_time = $news_row['news_end_time'];
        $news_images_folder = "/site/images/news/";
        $news_image = $news_images_folder.$news_row['news_image'];
        $news_image_exploded = explode(".", $news_image);
        $current_news_image_name = $news_image_exploded[0];
        $current_news_image_exstension = $news_image_exploded[1];
        $image_thumb_name = $current_news_image_name."_thumb.".$current_news_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$image_thumb_name);
        $thumb_image_dimensions = $thumb_image_params[3];
        $news_views = $news_row['news_views'];
        if(!isset($class)) $class = "even";
        $class = (($class == "odd") ? "even" : "odd");
        $news_details_link = "/$current_page_path_string/$news_title_url?nid=$news_id";
  ?>
        <div class="blog-item m-bot-35">
          <div class="content-container-white">
            <div class="view view-first">
              <a href="<?=$news_details_link;?>"><img src="<?=$news_image;?>" alt="<?=$news_title;?>" ></a>
              <div class="blog-item-date-cont clearfix">
                <div class="left"><span class="blog-item-date"><?=$news_post_date_day;?></span></div>
                <div class="right">
                  <div class="blog-item-mounth"><?=$news_post_date_month;?></div>
                  <div class="blog-item-year"><?=$news_post_date_year;?></div>
                </div>
              </div>
            </div>
            <div class="contant-container-caption">
              <a class="a-invert" href="<?=$news_details_link;?>">
                <?=$news_title;?>
              </a>
            </div>
          </div>
          <div class="content-container-white blog-info-container">
            <ul class="clearfix">
              <!--<li class="author">Admin</li>-->
              <li class="view"><?=$news_views;?> <?=$languages[$current_lang]['text_views'];?></li>
  <!--            <li class="comment">25 Comments</li>
              <li class="tag">Website Design - Responsive</li>-->
            </ul>
          </div>
          <div class="under-box-bg"></div>
          <div class="content-container-white blog-text-container">
            <p><?=$news_summary;?></p>
            <a class="right r-m-plus r-m-full" href="<?=$news_details_link;?>">
              <span class="bold"><?=$languages[$current_lang]['btn_slider_read'];?></span> <?=$languages[$current_lang]['btn_slider_more'];?>
            </a>
          </div>	
        </div>
  <?php
      } //while($news_row)
      mysqli_free_result($result_news);

      if(isset($page_count)) {
?>
      <div class="clear"></div>
      <div class="pagination-1-container">
        <ul class="pagination-1">
<?php
          $pages = 1;
          $current_offset = $offset;
          $offset = 0;

          if($current_page == 1) {
            echo '<li><a href="javascript:;" class="pag-prev" data=""></a></li>';
          }
          else {
            $prev_offset = $current_offset - $page_offset;
            echo '<li><a href="javascript:;" class="pag-prev" data="'.$prev_offset.'"></a></li>';
          }

          while($pages <= $page_count) {

            if($current_page == $pages) {
              echo "<li id='pag_$pages'><a href='javascript:;' class='pag-current'>$pages</a></li>";
            }
            else {
              echo "<li id='pag_$pages'><a href='javascript:;' class='inactive' data=\"$offset\">$pages</a></li>";
            }

            $pages++;
            $offset += $page_offset;
          }
          if($current_page == $page_count) {
            echo '<li><a href="javascript:;" class="pag-next" data="">&nbsp;</a></li>';
          }
          else {
            $next_offset = $current_offset + $page_offset;
            echo '<li><a href="javascript:;" class="pag-next" data="'.$next_offset.'">&nbsp;</a></li>';
          }
?>
        </ul>
        <input type="hidden" class="news_category_id" value="<?=$news_category_id;?>" >
        <input type="hidden" class="news_count" value="<?=$news_count;?>" >
        <input type="hidden" class="language_id" value="<?=$current_language_id;?>" >
        <input type="hidden" class="current_lang" value="<?=$current_lang;?>" >
      </div>
      <script>
        $(function() {
          $(".pagination-1 a").bind('click', function() {
            var offset = $(this).attr("data");
            LoadPaginationNews(offset);
          });
        });
      </script>
<?php
      } //if(isset($page_count))
    } //if(mysqli_num_rows($result_news) > 0)
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
      
      <div class="sidebar-item  m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"></h3>
        </div>

        <div class="under-box-bg"></div>

        <div class="content-container-white padding-all-15">
          
          <div class="content-container-white">
            <?php if(!empty($news_ads_banner)) echo "<img src='$news_ads_banner' alt=''>" ?>
          </div>
					
        </div>
      </div>
      
    </div>
    <!--<div class="five columns">-->
  </div>
<?php
  print_html_footer();
?>
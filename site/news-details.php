<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['ncid_d'])) {
    $news_category_id = $_GET['ncid_d']; // current selected news_category_id for details page
  }
  if(isset($_GET['nid'])) {
    $current_news_id = $_GET['nid']; // current selected product
  }
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = $_GET['page'];
    //echo $current_page_path_string;
    $current_page_path_exploded = explode("/", $current_page_path_string);
    $count_path_elements = count($current_page_path_exploded)-1;
    $current_news_pretty_url = $current_page_path_exploded[$count_path_elements];
    $current_lang = $current_page_path_exploded[0];
    $current_news_page_1 = "/".$current_page_path_exploded[0]."/".$current_page_path_exploded[1];
    $current_news_page_2 = "/".$current_page_path_exploded[0]."/".$current_page_path_exploded[1]."/".$current_page_path_exploded[2];
  }
  
  $query_update_news = "UPDATE `news` SET `news_views`=`news_views`+1 WHERE `news_id` = '$current_news_id'";
  $result_update_news = mysqli_query($db_link, $query_update_news);
  if(!$result_update_news) {
    echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
      
  $query_content_hierarchy_ids = "SELECT `language_root_content_id`,`language_id` FROM `languages` WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_content_hierarchy_ids = mysqli_query($db_link, $query_content_hierarchy_ids);
  if(!$result_content_hierarchy_ids) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content_hierarchy_ids) > 0) {
    $row_content_hierarchy_ids = mysqli_fetch_assoc($result_content_hierarchy_ids);
    $current_language_id = $row_content_hierarchy_ids['language_id'];
    $content_hierarchy_ids = $row_content_hierarchy_ids['language_root_content_id'];
  }
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_image`,`news_descriptions`.`news_title`,
                        `news_descriptions`.`news_summary`,`news`.`news_views`,`news_descriptions`.`news_text` 
                    FROM `news` 
                    INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                    WHERE `news`.`news_id` = '$current_news_id' AND `news_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
    $news_row = mysqli_fetch_assoc($result_news);

    $news_id = $news_row['news_id'];
    $news_category_id = $news_row['news_category_id'];
    $news_post_date_day = date("d", strtotime($news_row['news_post_date']));
    $news_post_date_month_text = "text_date_month_".date("m", strtotime($news_row['news_post_date']));
    $news_post_date_month = $languages[$current_lang][$news_post_date_month_text];
    $news_post_date_year = date("Y", strtotime($news_row['news_post_date']));
    $news_images_folder = "/site/images/news/";
    $news_image = $news_images_folder.$news_row['news_image'];
    $news_image_splitted = explode(".", $news_row['news_image']);
    $news_image_name = $news_image_splitted[0];
    $news_image_ext = $news_image_splitted[1];
    $fb_image = $_SERVER['SERVER_NAME'].$news_images_folder.$news_image_name."_thumb.".$news_image_ext;
    @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$news_image);
    $image_dimensions = @$image_params[3];
    $news_title = $news_row['news_title'];
    $news_summary = stripslashes($news_row['news_summary']);
    $news_text = stripslashes($news_row['news_text']);
    $news_views = $news_row['news_views'];
  }
  
  print_html_header($news_title, $news_summary, "");
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>

  <div class="container m-bot-15 clearfix">
    <div class="sixteen columns">
      <div class="page-title-container clearfix">
        <h1 class="page-title">
          <a href="/"><?=$languages[$current_lang]['header_home'];?></a> 
          / <a href="<?=$current_news_page_1;?>"><?=$languages[$current_lang]['menu_news'];?></a>
          / <a href="<?=$current_news_page_2;?>/?ncid=<?=$news_category_id;?>"><?=get_news_category_name_by_id($news_category_id,$current_language_id);?></a>
          <span class="sub-title">/ <?=$news_title;?></span>
        </h1>
      </div>	
    </div>
  </div>
  
  <!-- CONTENT -->
	<div class="container clearfix">
		<div class="eleven columns m-bot-25">
		<!-- BLOG ITEM -->
			<div class="blog-item m-bot-25">
				<div class="content-container-white">
					<div class="view view-first">
						<img src="<?=$news_image;?>" alt="<?=$news_title;?>" >
						<div class="blog-item-date-cont clearfix">
							<div class="left"><span class="blog-item-date"><?=$news_post_date_day;?></span></div>
							<div class="right">
								<div class="blog-item-mounth"><?=$news_post_date_month;?></div>
								<div class="blog-item-year"><?=$news_post_date_year;?></div>
							</div>
						</div>
					</div>
					<div class="contant-container-caption">
            <?=$news_title;?>
					</div>
				</div>
				<div class="content-container-white blog-info-container">
					<ul class="clearfix">
            <li class="fb_box">
              <div class="fb-like" data-href="<?=$_SERVER['SERVER_NAME'].urldecode($_SERVER['REQUEST_URI']);?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
            </li>
            <li class="linkedin_box">
              <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
              <script type="IN/Share" data-url="<?=$_SERVER['SERVER_NAME'].urldecode($_SERVER['REQUEST_URI']);?>" data-counter="right"></script>
            </li>
            <li class="view"><?=$news_views;?> <?=$languages[$current_lang]['text_views'];?></li>
            <!--<li class="tag">Website Design - Responsive</li>-->
          </ul>
				</div>
				<div class="under-box-bg"></div>
				<div class="content-container-white blog-text-container">
					<p><?=$news_text;?></p>
				</div>	
			</div>
    </div>
    
    <div class="five columns ">
			<div class="sidebar-item  m-bot-25">
				<div class="content-container-white">
					<h3 class="title-widget"><?=$languages[$current_lang]['header_news_categories'];?></h3>
				</div>
				<div class="under-box-bg"></div>
				<div class="blog-categories-container">
					<ul id="news_categories" class="blog-categories">
            <?php list_news_categories($news_cat_parent_id = 0,$news_category_id,$news_categories_count = false) ?>
					</ul>
				</div>
			</div>
    
      <div class="sidebar-item  m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"><?=$languages[$current_lang]['header_latest_news_for_category'];?></h3>
        </div>

        <div class="under-box-bg"></div>

        <div class="content-container-white padding-all-15">

          <div class="content-container-white">
            <ul class="latest-post-container">

              <?php list_latest_news_for_category($current_news_id, $news_category_id, $news_count = 5) ?>

            </ul>
          </div>

        </div>
      </div>
    
<!--			<div class="sidebar-item  m-bot-25">
				<div class="content-container-white">
					<h3 class="title-widget"><span class="bold">ACCORDION</span> WIDGET</h3>
				</div>
				
				<div class="under-box-bg"></div>
				
				<div class="content-container-white padding-all-15">
				
          <div id="accordion">
					
            <h3><a href="#">Lorem ipsum</a></h3>
            <div>
              <p>Nunc ipsum risus, bibendum quis tincidunt a, tempor quis nunc. Aenean in odio in sapien porttitor sodales.</p>
            </div>

            <h3><a href="#">Vestilum pulvinar</a></h3>
            <div>
              <p>Nunc ipsum risus, bibendum quis tincidunt a, tempor quis nunc. Aenean in odio in sapien porttitor sodales.</p>
            </div>

            <h3><a href="#">Donec sedin</a></h3>
            <div>
              <p>Nunc ipsum risus, bibendum quis tincidunt a, tempor quis nunc. Aenean in odio in sapien porttitor sodales.</p>
            </div> 
					 
          </div> End accordion 
					
				</div>
			</div>
    
			<div class="sidebar-item  m-bot-25">
				<div class="content-container-white">
					<h3 class="title-widget"><span class="bold">TAGS</span> WIDGET</h3>
				</div>
				
				<div class="under-box-bg"></div>
				
				<div class="content-container-white padding-all-15">
          <div class="tag-cloud">
            <ul class="clearfix">
              <li><a href="">HTML 5</a></li>
              <li><a href="">CSS 3</a></li>
              <li><a href="">Photoshop</a></li>
              <li><a href="">WordPress</a></li>
              <li><a href="">Joomla!</a></li>
              <li><a href="">UI</a></li>
              <li><a href="">Template</a></li>
            </ul>
          </div>
				</div>
			</div>

			<div class="sidebar-item  m-bot-25">
				<div class="content-container-white">
					<h3 class="title-widget"><span class="bold">FLICKR</span> WIDGET</h3>
				</div>
				
				<div class="under-box-bg"></div>
				
				<div class="content-container-white padding-l-t-15">
				
					<ul id="flickrfeed" class="clearfix"></ul>
					
				</div>
			</div>-->
    
		</div>
	</div>
  <script>
    $(function() {
      if($("li.has_parent.active").length) {
        var active_child = $("li.has_parent.active");
        active_child.closest("li.has_children").addClass("active");
      }
    });
  </script>
<?php
  print_html_footer();
?>
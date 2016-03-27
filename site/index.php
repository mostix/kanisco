<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
//  setcookie("cookie_policy", "", time()-3600);  /* expire in 1 hour */
  $dirname = dirname(__FILE__);
  
  if(isset($current_page_pretty_url) && $current_page_pretty_url == "logout") {
    unset($_SESSION['customer']);
    unset($_SESSION['cart']);
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
  }
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_page_path = $_GET['page'];
    
    $current_page_path_string = $_GET['page'];
    //echo $current_page_path_string;
    $current_page_path = explode("/", $current_page_path_string);
    $count_page_path_elements = count($current_page_path)-1;
    $current_page_pretty_url = $current_page_path[$count_page_path_elements];
    $current_lang = $current_page_path[0];
  
    $query_where_page = "`content_pretty_url` = '$current_page_pretty_url'";
  }
  else {
    
    $query_where_page = "`content_is_default` = '1'";
    $query_language = "SELECT `language_code`
                      FROM `languages` 
                      WHERE `language_is_default_frontend` = '1'";
    //echo $query_language;
    $result_language = mysqli_query($db_link, $query_language);
    if(!$result_language) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_language) > 0) {
      $language_array = mysqli_fetch_assoc($result_language);
      $current_lang = stripslashes($language_array['language_code']);
    }
  }
    
  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path` 
                          FROM `languages` 
                          INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                          WHERE `language_code` = '$current_lang'";
  //echo $query_content;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if(!$result_current_params) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $content_hierarchy_ids = $row_current_params['language_root_content_id'];
    $home_page_url = $row_current_params['content_hierarchy_path'];
  }
  else {
    $query_current_params = "SELECT `language_id`,`language_code`,`language_root_content_id`,`contents`.`content_hierarchy_path` 
                            FROM `languages` 
                            INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                            WHERE `language_is_default_frontend` = '1'";
    //echo $query_content_hierarchy_ids;exit;
    $result_current_params = mysqli_query($db_link, $query_current_params);
    if(!$result_current_params) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_current_params) > 0) {
      $row_current_params = mysqli_fetch_assoc($result_current_params);
      $current_language_id = $row_current_params['language_id'];
      $current_lang = $row_current_params['language_code'];
      $content_hierarchy_ids = $row_current_params['language_root_content_id'];
      $home_page_url = $row_current_params['content_hierarchy_path'];
    }
  }
  
  $content_is_home_page = 0;
  $content_type_id = 3;
  $content_meta_title = "";
  $content_meta_description = "";
  $content_meta_keywords = "";
  $content_name = "";
  $content_text = "";
    
  $query_content = "SELECT `content_id`,`content_type_id`,`content_parent_id`,`content_hierarchy_ids`,`content_is_home_page`,`content_name`,`content_show_newsletter`,
                            `content_show_clients`,`content_menu_text`,`content_meta_title`,`content_meta_keywords`,`content_meta_description`,`content_text`,
                            `content_pretty_url`,`content_attribute_2`
                    FROM `contents`
                    WHERE $query_where_page";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $current_content_id = $content_array['content_id'];
    $content_type_id = $content_array['content_type_id'];
    $content_parent_id = $content_array['content_parent_id'];
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_is_home_page = $content_array['content_is_home_page'];
    $content_name = stripslashes($content_array['content_name']);
    $content_show_newsletter = $content_array['content_show_newsletter'];
    $content_show_clients = $content_array['content_show_clients'];
    $content_menu_text = stripslashes($content_array['content_menu_text']);
    $content_meta_title = stripslashes($content_array['content_meta_title']);
    $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
    $content_meta_description = stripslashes($content_array['content_meta_description']);
    $content_text = stripslashes($content_array['content_text']);
    $content_pretty_url = $content_array['content_pretty_url'];
    $content_attribute_2 = $content_array['content_attribute_2'];
  }
  else {
    //error page
    $query_content = "SELECT `content_parent_id`,`content_hierarchy_ids`,`content_name`,`content_meta_title`,
                            `content_meta_keywords`,`content_meta_description`,`content_text`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_parent_id = $content_array['content_parent_id'];
      $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
      $content_name = stripslashes($content_array['content_name']);
      $content_meta_title = stripslashes($content_array['content_meta_title']);
      $content_meta_keywords = stripslashes($content_array['content_meta_keywords']);
      $content_meta_description = stripslashes($content_array['content_meta_description']);
      $content_text = stripslashes($content_array['content_text']);
    }
    else {
      $query_content = "SELECT `content_hierarchy_path`
                        FROM `contents`
                        WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `content_parent_id` FROM `contents` WHERE `content_is_default` = '1')";
      //echo $query_content."<br><br>";
      $result_content = mysqli_query($db_link, $query_content);
      if(!$result_content) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_content) > 0) {
        $content_array = mysqli_fetch_assoc($result_content);
        $content_hierarchy_path = $content_array['content_hierarchy_path'];

        header('Location: '.$content_hierarchy_path.'');
      }
    }
  }
    
  if(empty($content_meta_title)) $content_meta_title = $content_name;
  
  if(isset($current_page_pretty_url) && ($current_page_pretty_url == "registration")) {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, "<script src='https://www.google.com/recaptcha/api.js'></script>");
    
    $content_name = $languages[$current_lang]['header_registration'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->

  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <?php include_once 'registration.php';?>
    </div>
  </div>


<?php
  }
  elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "login")) {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, "<script src='https://www.google.com/recaptcha/api.js'></script>");
    
    $content_name = $languages[$current_lang]['header_login'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->

  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <?php include_once 'login.php';?>
    </div>
  </div>


<?php
  }
  elseif(isset($current_page_pretty_url) && $current_page_pretty_url == "confirm-account") {
    $customer_id = $current_page_path[1];
    
    if(!empty($customer_id)) {
      
      print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords);
    
      $content_name = $languages[$current_lang]['header_registration_confirm_account'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <?php include_once 'confirm-account.php'; ?>
    </div>
  </div>
 
<?php
    }
  }
  elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "контакти" || $current_page_pretty_url == "contacts")) {
    
    $body_class = "contacts";
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_script = false, $body_class);
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
    <div class="twelve columns m-bot-35">
    
      <div class="content-container-white">
        <section class="google-map-container">
          <iframe width="700" height="400" frameborder="0" scrolling="no" src="http://www.bgmaps.com/link/map/7B302D6CBECAF14A448D2FED57172825"></iframe>
        </section>
				<div class="contant-container-caption">
					<?=$languages[$current_lang]['header_inquiry_form'];?>
				</div>
			</div>		
<!-- CONTACT FORM-->
			<div class="contact-form-container">
        <p class="styled-box iconed-box success hidden"><?=$languages[$current_lang]['text_email_send_successfully'];?></p>
				<form action="/site/inquiery.php" id="contact-form" method="post" class="clearfix">			
					<fieldset class="field-1-3 left">
						<label><?=$languages[$current_lang]['text_enter_name'];?></label>
						<input type="hidden" name="current_lang" value="<?=$current_lang;?>">
						<input type="text" name="name" id="myname" placeholder="<?=$languages[$current_lang]['text_enter_name'];?>..." class="text requiredField m-bot-20" >
            <div class="styled-box iconed-box error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
					</fieldset>
					<fieldset class="field-1-3 left">
						<label><?=$languages[$current_lang]['text_enter_phone'];?></label>	
						<input type="text" name="phone" id="myphone"  placeholder="<?=$languages[$current_lang]['text_enter_phone'];?>..." class="text requiredField subject m-bot-20" >
            <div class="styled-box iconed-box error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
					</fieldset>	
					<fieldset class="field-1-3 left">
						<label><?=$languages[$current_lang]['text_enter_email'];?></label>	
						<input type="text" name="email" id="myemail" placeholder="<?=$languages[$current_lang]['text_enter_email'];?>..."  class="text requiredField email m-bot-20" >
            <div class="styled-box iconed-box error hidden"><?=$languages[$current_lang]['required_field_error'];?></div>
            <div class="styled-box iconed-box invalid_email hidden"><?=$languages[$current_lang]['error_create_customer_email_not_valid'];?></div>
					</fieldset>
					<fieldset class="field-1-1 left">
						<label><?=$languages[$current_lang]['text_enter_inquiry'];?></label>
						<textarea name="message" id="mymessage" rows="5" cols="30" class="text requiredField m-bot-15" placeholder="<?=$languages[$current_lang]['text_enter_inquiry'];?>..."></textarea>
            <div class="styled-box iconed-box error hidden" style="width: 100%;"><?=$languages[$current_lang]['required_field_error'];?></div>
					</fieldset>
					<fieldset class="left">
						<input name="Mysubmitted" id="Mysubmitted" value="<?=$languages[$current_lang]['btn_submit_inquiry'];?>" class="button gray medium" type="submit" >
					</fieldset>
				</form>
			</div>
    </div>
    
    <!-- SIDEBAR -->
    <div class="four columns m-bot-25">
      <?=$content_text;?>
    </div>
  </div>
<?php
    if($content_show_newsletter == 1) {
      print_newsletter_form();
    }

    if(!is_null($content_show_clients)) {
      list_clients($count = $content_show_clients);
    }
  }
  elseif(isset($current_page_pretty_url) && 
    ($current_page_pretty_url == "user-profile-settings" || $current_page_pretty_url == "user-profile-addresses" || $current_page_pretty_url == "user-profile-address-add" 
    || $current_page_pretty_url == "user-profile-address-edit" || $current_page_pretty_url == "user-profile-orders" || $current_page_pretty_url == "user-profile-change-password")) {
    
    $content_meta_title = "";
    $content_meta_description = "";
    $content_meta_keywords = "";
    
    $page_header = ($current_page_pretty_url == "user-profile-change-password") ? $languages[$current_lang]['header_user_profile_forgotten_password'] : $languages[$current_lang]['header_user_profile'];
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords);
    
    $content_name = $languages[$current_lang]['header_customer_profile'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
    <?php 
        if(!secured() && $current_page_pretty_url != "user-profile-change-password") echo "<h1>".$languages[$current_lang]['error_secured']."</h1>"; 
        else include_once 'users-profiles/'.$current_page_pretty_url.'.php'; 
    ?>
    </div>
  </div>

<?php
  }
  elseif(isset($current_page_pretty_url) && ($current_page_pretty_url == "карта-на-сайта")) {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords);
    
    $content_name = $languages[$current_lang]['header_sitemap'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <ul>
        <?php print_sitemap($content_parent_id = 0, $content_hierarchy_level_start = 2, $number_of_hierarchy_levels = 6); ?>
      </ul>
    </div>
  </div>

<?php
  }
  else {
    
    print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords);
    //echo "<pre>";print_r($content_hierarchy_ids);
    if($content_is_home_page == 1) {
      
      print_index_sliders($count = 3);
?>
<!--News starts here--> 
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <div class="latest-work-caption-container">
        <div class="caption-main-container clearfix">
          <div class="caption-text-container">
            <span class="bold"><?=$languages[$current_lang]['header_lastest'];?></span> <?=$languages[$current_lang]['header_lastest_news'];?>
          </div>
          <div class="carousel-navi jcarousel-scroll">
            <div class="jcarousel-prev">&nbsp;</div>
            <div class="jcarousel-next"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- jCAROUSEL -->
  <div class="jcarousel container latest-work-jc m-bot-25" >
    <ul class="ul-jcarousel">
      <!-- LATEST WORK ITEM -->
      
      <?php list_news_on_homepage("8") ?>

    </ul>
  </div>
<!--News ends here-->
  
  <?php print_newsletter_form(); ?>

  <div class="container clearfix">
		<div class="sixteen columns m-bot-35">
			<div class="latest-work-caption-container">
				<div class="caption-main-container clearfix">
					<div class="caption-text-container">
						<span class="bold"><?=$languages[$current_lang]['header_upcoming'];?></span> <?=$languages[$current_lang]['header_courses'];?>
					</div>
				</div>
			</div>
		</div>
	</div>

  <div class="container m-bot-25">
    <div class="sixteen columns">
      
    <?php list_upcoming_courses("4") ?>

    </div>
  </div>

  <div class="container clearfix">
		<div class="sixteen columns m-bot-35">
			<div class="latest-work-caption-container">
				<div class="caption-main-container clearfix">
					<div class="caption-text-container">
						<span class="bold"><?=$languages[$current_lang]['header_news'];?></span> <?=$languages[$current_lang]['header_for_products'];?>
					</div>
					<div class="carousel-navi jcarousel-scroll">
						<div class="jcarousel-prev"></div>
						<div class="jcarousel-next"></div>
					</div>
				</div>
			</div>
		</div>
	</div>	
		
  <!-- jCAROUSEL -->
  <div class="jcarousel container latest-work-jc m-bot-25" >
    <ul>
      <?php list_news_products_categories_on_index_page($news_cat_parent_id = 2) ?>
    </ul>
  </div>
      
<?php list_clients($count = 5); ?>
      
<?php
    }
    else {
      if($content_type_id == 7) {
        // ($content_type_id == 7) means news page
?>
  <div class="container m-bot-15 clearfix">
    <div class="sixteen columns">
      <div class="page-title-container clearfix">
        <h1 class="page-title">
          <a href="/"><?=$languages[$current_lang]['header_home'];?></a>
          <span class="sub-title">/ <?=$content_name;?></span>
        </h1>
      </div>	
    </div>
  </div>

<!-- CONTENT -->
	<div class="container clearfix">
		<div id="news_list" class="twelve columns m-bot-25">
      
      <?php list_news(); ?>

    </div>
    <!--<div class="twelve columns m-bot-25">-->
    
    <!-- SIDEBAR -->
    <div class="four columns">
      
      <div class="sidebar-item m-bot-25">
				<div class="content-container-white">
					<h3 class="title-widget"><?=$languages[$current_lang]['header_news_categories'];?></h3>
				</div>
				<div class="under-box-bg"></div>
				<div class="blog-categories-container">
					<ul id="news_categories" class="blog-categories">
						<?php list_news_categories($news_cat_parent_id = 0,$news_categories_count = false) ?>
					</ul>
				</div>
			</div>
      
    </div>
    <!--<div class="four columns">-->
  </div>
<?php
        if($content_show_newsletter == 1) {
          print_newsletter_form();
        }
        
        if(!is_null($content_show_clients)) {
          list_clients($count = $content_show_clients);
        }

      }
      elseif($content_type_id == 8) {
        // ($content_type_id == 8) means events page

        $content_name = $languages[$current_lang]['header_events'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <div id="tribe-events-content" class="tribe-events-list">
        <?php print_events_blocks($blocks_count = 12); ?>
      </div>
    </div>
  </div>

<?php
      }
      elseif($content_type_id == 9) {
        // ($content_type_id == 9) means courses page

        $content_name = $languages[$current_lang]['header_courses'];
?>
  <!-- breadcrumb starts here -->
  <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
  <!-- breadcrumb ends here -->
      
  <div class="container clearfix">
		<div class="sixteen columns m-bot-35">
			<div class="latest-work-caption-container">
				<div class="caption-main-container clearfix">
					<div class="caption-text-container">
						<span class="bold"><?=$languages[$current_lang]['header_upcoming'];?></span> <?=$languages[$current_lang]['header_courses'];?>
					</div>
				</div>
			</div>
		</div>
	</div>
  
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <?php list_upcoming_courses(); ?>
    </div>
  </div>
      
  <div class="container clearfix">
		<div class="sixteen columns m-bot-35">
			<div class="latest-work-caption-container">
				<div class="caption-main-container clearfix">
					<div class="caption-text-container">
						<span class="bold"><?=$languages[$current_lang]['header_all'];?></span> <?=$languages[$current_lang]['header_courses'];?>
					</div>
				</div>
			</div>
		</div>
	</div>
  
  <div class="container clearfix">
    <div class="sixteen columns m-bot-35">
      <?php list_all_courses(); ?>
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
<?php
      }
      elseif($content_type_id == 3) {
        // error page
?>
      <div class="container m-bot-25 clearfix">
        <div class="sixteen columns">
          <div class="page-title-container clearfix">
            <h1 class="page-title">
              <a href="/"><?=$languages[$current_lang]['header_home'];?></a>
              <span class="sub-title">/ <?=$content_name;?></span>
            </h1>
          </div>	
        </div>
      </div>

      <div class="container clearfix m-bot-35">
        <!-- main-content starts here -->
        <div class="sixteen columns">
          <div class="container m-bot-35">
            <?=$content_text;?>
          </div>
        </div>
        <!-- main-content ends here -->
      </div>
<?php
      }
      else {
        //regular page
?>
      <!-- breadcrumb starts here -->
      <?php print_breadcrumbs($content_hierarchy_ids,$content_name);?>
      <!-- breadcrumb ends here -->

      <div class="container clearfix m-bot-20">
        <div class="sixteen columns m-bot-25">
<?php
        //partners
        if(isset($current_content_id) && ($current_content_id == 15 || $current_content_id == 49)) {
          $query_content = "SELECT `content_id`,`content_menu_text`,`content_hierarchy_path`,`content_pretty_url`,`content_attribute_1` 
                            FROM `contents`
                            WHERE `content_parent_id` = '$current_content_id' AND `content_is_active` = '1'
                            ORDER BY `content_menu_order` ASC";
          //echo $query_content;
          $result_content = mysqli_query($db_link, $query_content);
          if(!$result_content) echo mysqli_error($db_link);
          $content_count = mysqli_num_rows($result_content);
          if($content_count > 0) {
?>
          <div class="container filter-portfolio clearfix">
            <ul id="portfolio" class="clearfix" style="margin-left: -10px;">
<?php
            while($content_row = mysqli_fetch_assoc($result_content)) {
              
              $content_id = $content_row['content_id'];
              $content_menu_text = $content_row['content_menu_text'];
              $content_hierarchy_path = $content_row['content_hierarchy_path'];
              $content_attribute_1 = $content_row['content_attribute_1'];
              $url = "/$content_hierarchy_path";
?>
              <li class="one-third column m-bot-25">
                <div class="content-container-white">
                  <div class="view view-first">
                    <a href="<?=$url;?>"><img src="<?=$content_attribute_1?>" alt="<?=$content_menu_text;?>" width="298" height="176" ></a>
                  </div>
                  <div class="lw-item-caption-container" style="border-top: 1px solid #dadada;">
                    <a class="a-invert" href="<?=$url;?>" ><?=$content_menu_text;?></a>
                  </div>
                </div>
              </li>
<?php
            } //while($content_row)
?>
            </ul>
          </div>
<?php
            } //if($content_count > 0)
          } //if(isset($current_content_id) && ($current_content_id == 15 || $current_content_id == 49))
          else {
?>
          <div class="content-container-white blog-text-container">
            <div class="under-box-bg"></div>
<?php
            echo $content_text;
?>
          </div>
<?php
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
      <div class="m-bot-20"></div>
<?php
      }
      
    } //else of if($content_is_home_page == 1)

  }
 
  print_html_footer();
?>
</body>
</html>
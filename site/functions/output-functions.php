<?php
 
function print_html_header($content_meta_title, $content_meta_description, $content_meta_keywords, $additional_script = false, $body_class = false) {

  global $db_link;
  global $languages;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $home_page_url;
  global $fb_image;

  if(!isset($fb_image)) $fb_image = "http://www.procad-bg.com/site/images/logo.png";
//    unset($_SESSION);
//    session_destroy();

  $_SESSION['captcha123'] = array();
  $_SESSION['captcha_error']['count'] = 0;
  $rnd=rand(1,99);
  $query = "SELECT `captchas`.* FROM `captchas` LIMIT $rnd,1";
  //echo $query;
  $result = mysqli_query($db_link, $query);
  if(!$result) echo mysqli_error($db_link);
  if(mysqli_num_rows($result) > 0) {

    $captcha = mysqli_fetch_assoc($result);
    $_SESSION['captcha123']['img'] = $captcha['captcha_image'];
    $_SESSION['captcha123']['code'] = $captcha['captcha_number'];

  }
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html  class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html  class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html  class="ie8"> <![endif]-->
<!--[if (gt IE 8)|!(IE)]><!--><html><!--<![endif]-->
<head>
  <title><?=$languages[$current_lang]['e_shop_cms']." - ".strip_tags($content_meta_title);?></title>
  <meta charset=utf-8 >
  <meta name="robots" content="index, follow" > 
  <meta name="description" content="<?=strip_tags($content_meta_description);?>">
  <meta name="keywords" content="<?=strip_tags($content_meta_keywords);?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="author" content="Iliyan Dimitrov">
  <meta property="og:site_name" content="<?=$languages[$current_lang]['e_shop_cms'];?>">
  <meta property="og:locale" content="bg_BG">
  <meta property="fb:app_id" content="853213731463390">
  <meta property="og:url" content="<?=urldecode($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);?>" />
  <meta property="og:type" content="AutoCAD LT Revit Civil Plateia Urbano Advance Design Advance Steel 3D Studio MAX визуализация BIM CAD курсов" />
  <meta property="og:title" content="<?=strip_tags($content_meta_title);?>" />
  <meta property="og:description" content="<?=strip_tags($content_meta_description);?>" />
  <meta property="og:image" content="<?=$fb_image;?>" />
  <link rel="shortcut icon" href="/site/images/index.ico">
  <link rel="stylesheet" type="text/css" href="/site/css/bootstrap.css" >
  <link rel="stylesheet" type="text/css" href="/site/css/style.css" >
  <link rel="stylesheet" type="text/css" href="/site/css/skeleton.css" >
  <link rel="stylesheet" type="text/css" href="/site/css/jquery.fancybox-1.3.4.css"  >
  <!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/site/css/ie-warning.css" ><![endif]-->
  <!--[if lte IE 9]><link rel="stylesheet" type="text/css" media="screen" href="/site/css/style-ie.css" /><![endif]-->
  <!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/site/css/ei8fix.css" ><![endif]-->
  <!--<link rel="stylesheet" type="text/css" href="/site/css/sequencejs-theme.modern-slide-in.css" >-->
  <!--[if lt IE 9]><link rel="stylesheet" type="text/css" media="screen" href="/site/css/sequencejs-theme.modern-slide-in.ie.css" /><![endif]-->
  <!--[if lte IE 8]><link rel="stylesheet" type="text/css" href="/site/css/sequencejs-theme.modern-slide-in.ie8.css" ><![endif]--><!-- Flexslider CSS -->
  <link rel="stylesheet" type="text/css" href="/site/css/flexslider.css" >
  <link rel="stylesheet" type="text/css" href="/site/css/yellow.css" id="template-color">
  <link rel="stylesheet" type="text/css" href="/site/css/custom.css">

  <script type="text/javascript" src="/site/js/jquery-1.8.3.min.js"></script>
  <script type="text/javascript" src="/site/js/jquery.easing.1.3.js"></script>
  <script type="text/javascript" src="/site/js/superfish.js"></script>
  <script type="text/javascript" src="/site/js/jquery-ui.min.js"></script>
  <!-- Flexslider js -->
  <script type="text/javascript" src="/site/js/jquery.flexslider.js"></script>
  <script type="text/javascript" src="/site/js/flex-slider.js"></script>
  <script type="text/javascript" src="/site/js/jquery.mousewheel.js"></script>
  <!-- end Flexslider js -->
  <script type="text/javascript" src="/site/js/jquery.jcarousel.js"></script>
  <script type="text/javascript" src="/site/js/jquery.fancybox-1.3.4.pack.js"></script>
  <script type="text/javascript" src="/site/js/jQuery.BlackAndWhite.min.js"></script>
  <!--<script type="text/javascript" src="/site/js/twitter/jquery.tweet.js"></script>-->
  <script type="text/javascript" src="/site/js/jquery.validate.min.js"></script>
  <!--<script type="text/javascript" src="/site/js/jflickrfeed.min.js"></script>-->
  <script type="text/javascript" src="/site/js/jquery.quicksand.js"></script>
  <script type="text/javascript" src="/site/js/main.js"></script>
  <script type="text/javascript" src="/site/js/bootstrap.min.js"></script>
  <?php if($additional_script) echo "$additional_script\n";?>
  <script type="text/javascript">
    WebFontConfig = {
      google: { families: [ 'Open+Sans::latin,cyrillic' ] }
    };
    (function() {
      var wf = document.createElement('script');
      wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
        '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      wf.type = 'text/javascript';
      wf.async = 'true';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(wf, s);
    })(); 
  </script>
  <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body class="<?=$body_class?>">
<?php
  if(!isset($_COOKIE['cookie_policy'])) {
?>
    <div id="cookies_policy">
      <a href="javascript:;" onclick="ConfirmCookiesPolicy()" class="pull-right button small"><?=$languages[$current_lang]['btn_cookies_agreed'];?></a>
      <p class="no_margin">
        <?=$languages[$current_lang]['text_cookies_policy'];?>
        <a href="/bg/политика-за-бисквитките" target="_blank"><?=$languages[$current_lang]['btn_cookies_policy_more'];?></a>
      </p>
    </div>
<?php } ?>
<!--[if lte IE 7]>
<div id="ie-container">
  <div id="ie-cont-close">
    <a href='#' onclick='javascript&#058;this.parentNode.parentNode.style.display="none"; return false;'><img src='images/ie-warning-close.jpg' style='border: none;' alt='Close'></a>
  </div>
  <div id="ie-cont-content">
    <div id="ie-cont-warning"><img src='images/ie-warning.jpg' alt='Warning!'></div>
    <div id="ie-cont-text">
      <div id="ie-text-bold">You are using an outdated browser</div>
      <div id="ie-text">For a better experience using this site, please upgrade to a modern web browser.</div>
    </div>
    <div id="ie-cont-brows" >
      <a href='http://www.firefox.com' target='_blank'><img src='images/ie-warning-firefox.jpg' alt='Download Firefox'></a>
      <a href='http://www.opera.com/download/' target='_blank'><img src='images/ie-warning-opera.jpg' alt='Download Opera'></a>
      <a href='http://www.apple.com/safari/download/' target='_blank'><img src='images/ie-warning-safari.jpg' alt='Download Safari'></a>
      <a href='http://www.google.com/chrome' target='_blank'><img src='images/ie-warning-chrome.jpg' alt='Download Google Chrome'></a>
    </div>
  </div>
</div>
<![endif]-->

<!-- HEADER -->
<header id="header">
  <div class="container">
    <div class="top-header sixteen columns">
      <div class="info-top col-lg-4 col-md-4 col-sm-4 col-xs-4 no_padding">
        <div id="choose_language">
          <?php print_header_language_menu();?>
        </div>
      </div>
      <div class="info-top text-right col-lg-8 col-md-8 col-sm-8 col-xs-8 no_padding">
        <ul id="social-links">
          <li><a target="_blank" title="Facebook" href="http://themeforest.net/item/optimas-responsive-multipurpose-template/4238646?ref=abcgomel"><i class="fa fa-facebook"></i></a></li>
          <li><a target="_blank" title="Youtube" href="#"><i class="fa fa-youtube-play"></i></a></li>
          <li><a target="_blank" title="Linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>
        </ul>
        <ul class="hidden">
<?php if(isset($_SESSION['customer'])) { ?>
          <li><a href="/<?=$current_lang;?>/users-profiles/user-profile-settings"><?=$languages[$current_lang]['customer_profile'];?></a></li>
          <li><a href="/<?=$current_lang;?>/logout"><?=$languages[$current_lang]['logout'];?></a></li>
<?php } else { ?>
          <li><a href="/<?=$current_lang;?>/login"><i class="fa fa-sign-in"></i><?=$languages[$current_lang]['login_sign_in'];?></a></li>
          <li><a href="/<?=$current_lang;?>/registration"><i class="fa fa-user"></i><?=$languages[$current_lang]['login_sign_up'];?></a></li>
<?php } ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="container clearfix">
    <div class="sixteen columns ">
      <div id="logo-container" class="col-lg-4 col-md-4 col-sm-4 col-xs-4">	
        <a href="/<?= $home_page_url ?>" title="<?=$languages[$current_lang]['e_shop_cms'];?>" class="logo" rel="home">
          <img src="/site/images/logo.jpg" title="<?=$languages[$current_lang]['e_shop_cms'];?>" alt="<?=$languages[$current_lang]['e_shop_cms'];?>" >
        </a>
      </div>
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 no_padding">
        <a href="/" title="<?=$languages[$current_lang]['e_shop_cms'];?>" class="logo partners" style="margin-right: -8px;" rel="home">
          <img src="/site/images/partners-logo.png" title="Autodesk" alt="Autodesk" >
        </a>
        <a href="/" title="<?=$languages[$current_lang]['e_shop_cms'];?>" class="logo partners" style="position: relative;top:-3px;" rel="home">
          <img src="/site/images/Peterschinegg-Kanisco-Logo.png" title="Peterschinegg GesmbH" alt="Peterschinegg GesmbH" >
        </a>
      </div>
    </div>
  </div>
  <div id="nav_container">
    <div class="container clearfix">
      <div class="sixteen columns nav-border">
        <!-- TOP MENU -->
        <nav id="main-nav">
          <ul class="sf-menu">
            <?php print_header_menu($content_parent_id = 0, $content_hierarchy_level_start = 2, $number_of_hierarchy_levels = 4);?>
          </ul>
        </nav>
        <div class="search-container hidden-xs hidden-sm clearfix">
          <form action="#" class="search-form">
            <input type="text" name="search-form-txt" class="search-text" onblur="if (this.value == '') this.value = 'Search';" onfocus="if (this.value == 'Search') this.value = '';" value="Search">
            <input type="submit" value="" class="search-submit" name="submit">
          </form>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" name="current_page_path_string" id="current_page_path_string" value="<?=$_SERVER['REQUEST_URI'];?>">
  <input type="hidden" name="current_lang" id="current_lang" value="<?=$current_lang;?>">
</header>
<?php
  //echo"<pre>";print_r($_COOKIE);
} //function print_html_header
  
function print_html_user_profile_menu() {
  global $current_lang;
?>
    <ul class="h4_cart">
      <li<?php if(is_active_page("user-profile-settings")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-settings">
          <img src="/site/images/ico/settings.png" class="icon-cart">Настройки
        </a>
      </li>
      <li<?php if(is_active_page("user-profile-orders")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-orders">
          <img src="/site/images/ico/user_awake_checkmark.png" class="icon-cart">Поръчки
        </a>
      </li>
      <li<?php if(is_active_page("user-profile-addresses") || is_active_page("user-profile-address-add") || is_active_page("user-profile-address-edit")) echo ' class="active"';?>>
        <a href="/<?=$current_lang;?>/user-profiles/user-profile-addresses">
          <img src="/site/images/ico/delivery.png" class="icon-cart">Адреси
        </a>
      </li>
      <hr class="featurette-divider">
    </ul>
<?php  
}
  
function print_html_footer() {
  global $languages;
  global $current_lang;
  global $current_page_pretty_url;
  global $home_page_url;
?>
  <footer>
   <div class="footer-content-bg">
     <a class="scrollup" title="<?=$languages[$current_lang]['text_scroll_to_top'];?>" href="javascript:;"><?=$languages[$current_lang]['text_scroll_to_top'];?></a>
     <div class="container clearfix">
       <div class="four columns m-bot-25">
         <h3 class="caption">KANISCO</h3>
         <p><?=$languages[$current_lang]['text_company_footer'];?></p>
         <a class="right r-m-plus" href="<?php if($current_lang == "bg") echo "/bg/за-нас/история"; else echo "/en/about-us/our-history" ?>">
           <span class="bold"><?=$languages[$current_lang]['btn_read'];?></span> <?=$languages[$current_lang]['btn_more'];?>
         </a>
       </div>
       <div class="eight columns m-bot-25">
         <h3 class="caption"><span class="bold"><?=$languages[$current_lang]['header_lastest'];?></span> <?=$languages[$current_lang]['header_lastest_news'];?></h3>
         <ul class="latest-post">
           <?php list_news_in_footer("2");?>
         </ul>
       </div>
       <div class="four columns m-bot-25">
         <h3 class="caption"><span class="bold"><?=$languages[$current_lang]['header_for_contact'];?></span> <?=$languages[$current_lang]['header_contact'];?></h3>
         <!--<p>Pellentesque tristique volutpat nunc, rhoncus augue tristique sed.</p>-->
         <div title="Location" class="icon_loc"><?=$languages[$current_lang]['text_company_address'];?></div>
         <div title="Phone" class="icon_phone">(+359 2) 983 14 10 <br>(+359 2) 988 78 80 <br>(+359 888) 11 91 85</div>
         <div title="Fax" class="icon_fax"><i class="fa fa-fax"></i>(+359 2) 988 78 80</div>
         <div title="Email" class="icon_mail"><a href="mailto:kanisco@kanisco.com">kanisco@kanisco.com</a></div>	
       </div>
     </div>
   </div>
   <div class="footer-copyright-bg">
     <div class="container clearfix">
       <div class="eight columns">
         <nav class="clearfix" id="footer-nav">
           <ul class="footer-menu">
             <?php print_footer_menu($content_parent_id = 0, $content_hierarchy_level_start = 2, $number_of_hierarchy_levels = 1) ?>
           </ul>
         </nav>
       </div>
       <div class="eight columns right-text">
         <?=$languages[$current_lang]['text_rights'];?> &copy; <?=date("Y");?>
       </div>
     </div>
   </div>
  </footer>

</body>
</html>
<?php  
}
  
function print_newsletter_form() {
  global $languages;
  global $current_lang;
?>
<!-- NEWS LETTER -->
  
  <div class="container clearfix">
		<div class="sixteen columns m-bot-35">
			<div class="latest-work-caption-container">
				<div class="caption-main-container clearfix">
					<div class="caption-text-container">
						<span class="bold"><?=$languages[$current_lang]['header_newsletter_01'];?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container m-bot-35 clearfix">
		<div class="sixteen columns">
      <div class="col-lg-6 col-md-6 col-sm-6 hidden-xs no_padding">
        <div class="content-container-white nl-img-container col-lg-2 col-md-2 col-sm-2">
          <img src="/site/images/mail-big-icon.png" width="48" height="34" alt="mail">
        </div>
        <div class="side-box-bg news-letter col-lg-1 col-md-1 col-sm-1">&nbsp;</div>
        <div class="nl-text-container part col-lg-9 col-md-9 col-sm-9">
          <div class="nl-text"><?=$languages[$current_lang]['text_newsletter'];?></div>
        </div>
      </div>
      <div class="nl-form-container col-lg-6 col-md-6 col-sm-6 col-xs-12 no_padding">
        <form name="newsletterform" method="post" action="/site/subscribe.php" class="newsletterform">
          <div class="nl-form-part-container col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <input type="email" required="required" id="newsletter_email" name="email" placeholder="<?=$languages[$current_lang]['text_enter_email'];?>">
            <input type="hidden" name="current_lang" value="<?=$current_lang;?>">
          </div>
          <div class="side-box-bg news-letter col-lg-1 col-md-1 col-sm-1 col-xs-1"></div>
          <button type="submit" class="button large col-lg-5 col-md-5 col-sm-5 col-xs-5"><?=$languages[$current_lang]['btn_newslettter'];?></button>
        </form>
      </div>
		</div>
    <div class="clearfix"></div>
    <div id="ajax_subscribe_msg" style="display: none;">
      
    </div>
	</div>
<?php  
}
  
function print_header_menu($content_parent_id, $content_hierarchy_level_start , $number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  $current_category_id = (isset($_GET['cid'])) ? $_GET['cid'] : 0;
  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  if($content_parent_id == 0) {
    if(strstr($content_hierarchy_ids, ".")) {
      $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
      $content_parent_id = $content_hierarchy_ids_array[0];
    }
    else {
      $content_parent_id = $content_hierarchy_ids;
    }
  }

  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_menu_order`, `content_pretty_url`,`content_target`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                      AND `content_show_in_menu` = '1'
                    ORDER BY `content_menu_order` ASC";
  //echo $query_content;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_pretty_url = $content_row['content_pretty_url'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path']; //content
          break;
        case 2:
          $content_hierarchy_path = "javascript:;"; //categories
          break;
        case 4:
          $content_hierarchy_path = $content_text; //redirecting link
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      $class_current = "";

      //promotions (`news_category_id` = '3')
      if($content_id == 6 || $content_id == 42) {
        $query_news_promotions = "SELECT `content_pretty_url`
                                  FROM `contents`
                                  WHERE `content_type_id` = '$content_type_id' 
                                    AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
        //echo $query_news_promotions."<br><br>";
        $result_news_promotions = mysqli_query($db_link, $query_news_promotions);
        if(!$result_news_promotions) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_news_promotions) > 0) {
          $content_array = mysqli_fetch_assoc($result_news_promotions);
          $content_pretty_url = $content_array['content_pretty_url'];
        }
        $news_category_id = 3;
        $query_news_categories = "SELECT `news_cat_desc`.`news_cat_hierarchy_path` 
                                FROM `news_cat_desc` 
                                WHERE `news_cat_desc`.`news_category_id` = '$news_category_id' AND `news_cat_desc`.`language_id` = '$current_language_id'";
        //echo $query_news_categories;exit;
        $result_news_categories = mysqli_query($db_link, $query_news_categories);
        if(!$result_news_categories) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_news_categories) > 0) {
          $news_category_row = mysqli_fetch_assoc($result_news_categories);

          $news_cat_hierarchy_path = $news_category_row['news_cat_hierarchy_path'];
          $content_hierarchy_path = "/$current_lang/$content_pretty_url/$news_cat_hierarchy_path?ncid=$news_category_id";
        }
      }

      if(in_array($content_id,$content_hierarchy_ids_array)) $class_current = ' current';

      $content_has_active_children = check_if_content_has_active_children($content_id);
      $content_is_last_child = check_if_this_is_content_last_child($content_parent_id,$content_menu_order);

      if($content_has_children == 1 && $content_hierarchy_level < $number_of_hierarchy_levels && $content_has_active_children || ($content_type_id == 2)) {
?>
      <li class="menu-item-simple-parent menu-item-depth-<?=$content_hierarchy_level-1;?> dropdown<?=$class_current;?>">
        <a href="<?=$content_hierarchy_path;?>"><?="$content_menu_text";?></a>
        <ul class="sub-menu">
<?php
        if($content_type_id == 2) {
          print_header_categories_menu($content_pretty_url,$current_category_id);
        }
        else {
          print_header_menu($content_id, $content_hierarchy_level_start = 0, $number_of_hierarchy_levels);
        }
      }
      else {
        if($content_type_id == 2) { // categories
          echo "<li class='$class_current'><a href='$content_text' $content_target>$content_menu_text</a></li>\n";
        }
        elseif($content_type_id == 4) { // redirecting link
          echo "<li class='$class_current'><a href='$content_text' $content_target>$content_menu_text</a></li>\n";
        }
        else echo "<li class='$class_current'><a href='$content_hierarchy_path' $content_target>$content_menu_text</a></li>\n";
      }

      if(($content_type_id == 2 && $content_hierarchy_level > 1) || ($content_hierarchy_level > 1 && $content_is_last_child)) echo "</ul></li>\n";

    }
  }
}

function print_header_categories_menu($content_pretty_url,$current_category_id) {

  global $db_link;
  global $current_language_id;
  global $current_lang;

  $query_categories = "SELECT `categories`.`category_id`,`categories`.`category_hierarchy_level`,
                              `categories`.`category_is_active`,`categories`.`category_has_children`,
                              `category_descriptions`.`cd_name`, `category_descriptions`.`cd_hierarchy_path` 
                      FROM `categories`
                      INNER JOIN `category_descriptions` USING(`category_id`)
                      WHERE `categories`.`category_hierarchy_level` = '1' AND `categories`.`category_is_active` = '1' 
                        AND `categories`.`category_show_in_menu` = '1'
                        AND `category_descriptions`.`language_id` = '$current_language_id'
                      ORDER BY `categories`.`category_sort_order` ASC";
  //echo $query_categories;
  $result_categories = mysqli_query($db_link, $query_categories);
  if(!$result_categories) echo mysqli_error($db_link);
  $category_count = mysqli_num_rows($result_categories);
  if($category_count > 0) {
    $key = 0;
    while($category_row = mysqli_fetch_assoc($result_categories)) {

      $category_id = $category_row['category_id'];
      $cd_name = $category_row['cd_name'];
      $cd_hierarchy_path = $category_row['cd_hierarchy_path'];
      $category_hierarchy_level = $category_row['category_hierarchy_level'];
      $category_is_active = $category_row['category_is_active'];
      $class_current = ($current_category_id == $category_id) ? ' class="current"' : "";
      
      echo "<li$class_current><a href='/$current_lang/$content_pretty_url/$cd_hierarchy_path?cid=$category_id'>$cd_name</a></li>\n";
    }
  }
}
  
function print_breadcrumbs($content_hierarchy_ids,$current_content_name) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  //echo "<pre>";print_r($content_hierarchy_ids_array);echo "</pre><br>";
  $ids_count = count($content_hierarchy_ids_array);
?>
    <!-- PAGE TITLE -->
    <div class="container m-bot-15 clearfix">
      <div class="sixteen columns">
        <div class="page-title-container clearfix">
<?php
  if($ids_count > 2) {
?>
          <h1 class="page-title"><a href="/"><?=$languages[$current_lang]['header_home'];?></a> 

<?php
    foreach($content_hierarchy_ids_array as $key => $content_id) {

      if($key != 0 && $key != ($ids_count-1)) {
        $query_content = "SELECT `content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_text`
                          FROM `contents`
                          WHERE `content_id` = '$content_id' AND `content_show_in_menu` = '1'";
        //echo "<br>$query_content<br>";
        $result_content = mysqli_query($db_link, $query_content);
        if(!$result_content) echo mysqli_error($db_link);
        if(mysqli_num_rows($result_content) > 0) {
          $content_row = mysqli_fetch_assoc($result_content);

          $content_type_id = $content_row['content_type_id'];
          $content_menu_text = stripslashes($content_row['content_menu_text']);

          switch($content_type_id) {
            case 1:
              $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
              break;
            case 2:
              $content_hierarchy_path = "javascript:;";
              break;
            case 4:
              $content_hierarchy_path = $content_row['content_text'];
              break;
            default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
              break;
          }
?>
          / <a href="<?=$content_hierarchy_path;?>"><?=$content_menu_text;?></a>
<?php
        }
      } //if($key != 0 || $key != $ids_count-1)
    } //foreach($content_hierarchy_ids_array
?>
            <span class="sub-title">/ <?=$current_content_name;?></span>
<?php
  }
  else {
?>
          <h1 class="page-title">
            <a href="/"><?=$languages[$current_lang]['header_home'];?></a>
            <span class="sub-title">/ <?=$current_content_name;?></span>
<?php
  }
?>
          </h1>
        </div>	
      </div>
    </div>
<?php
}
  
function print_footer_menu($content_parent_id, $content_hierarchy_level_start , $number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  if($content_parent_id == 0) {
    if(strstr($content_hierarchy_ids, ".")) {
      $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
      $content_parent_id = $content_hierarchy_ids_array[0];
    }
    else {
      $content_parent_id = $content_hierarchy_ids;
    }
  }

  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                      AND `content_show_in_footer` = '1'
                    ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      $class_active = "";

      if(in_array($content_id,$content_hierarchy_ids_array)) $class_active = ' current_page_item';

      echo "<li class='$class_active'><a href='$content_hierarchy_path' $content_target>$content_menu_text</a></li>\n";

    }
  }
}
  
function print_index_group_activities_menu($blocks_count) {

  global $db_link;
  global $current_language_id;
  global $current_lang;

  //`content_parent_id` = '5' - КОРПОРАТИВНИ УЕЛНЕС

  $limit = ($blocks_count == 0) ? "" : "LIMIT $blocks_count";

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '5'
                      AND `content_is_active` = '1'
                    ORDER BY `content_menu_order` ASC $limit";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    $block = 1;
    $key = 1;
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_attribute_1 = stripslashes($content_row['content_attribute_1']);
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      if($block == 4) $block = 1;
      $class_first = ($block == 1) ? "first" : "";
?>
      <div class="column dt-sc-one-third <?=$class_first;?> animate" data-animation="fadeInLeft" data-delay="100">
          <div class="dt-sc-ico-content type3">
              <a href="<?=$content_hierarchy_path;?>" class="icon">
                  <span class="fa fa-tint"> </span>
              </a>
              <h3><a href="<?=$content_hierarchy_path;?>"><?=$content_menu_text;?></a></h3>
              <p><?=$content_attribute_1;?></p>
          </div>
      </div>
<?php
      if($block == 3 && $key != $content_count) {
?>
      <div class="dt-sc-hr-invisible-small"></div>
      <div class="dt-sc-hr-invisible"></div>
<?php
      }
      $key++;
      $block++;
    }
  }
}
  
function print_activities_blocks($content_parent_id,$activities_header = false) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $languages;

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '$content_parent_id'
                      AND `content_is_active` = '1'
                    ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    if($activities_header) {
?>
    <h2 class="border-title aligncenter"><span><?=$languages[$current_lang][$activities_header];?></span></h2>   
<?php
    }
    $block = 1;
    $key = 1;
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_attribute_1 = stripslashes($content_row['content_attribute_1']);
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      if($block == 4) $block = 1;
      $class_first = ($block == 1) ? "first" : "";
      $data_animation = "";
      switch($block) {
        case 1:
          $data_animation = "fadeInLeft";
          break;
        case 2:
          $data_animation = "fadeInUp";
          break;
        case 3:
          $data_animation = "fadeInRight";
          break;
      }
?>
      <div class="dt-sc-one-third column <?=$class_first;?> animate" data-animation="<?=$data_animation;?>" data-delay="100">
        <div class="dt-sc-event">
          <div class="event-thumb"><a href="<?=$content_hierarchy_path;?>"><img alt="" src="/site/images/event-<?=$content_id;?>.jpg" title="" /></a></div>

          <div class="event-detail">
            <h2><a href="<?=$content_hierarchy_path;?>"><?=$content_menu_text;?></a></h2>

            <p><?=$content_attribute_1;?></p>
            <a class="dt-sc-button small" data-hover="<?=$languages[$current_lang]['btn_read_more'];?>" href="<?=$content_hierarchy_path;?>"><?=$languages[$current_lang]['btn_read_more'];?></a>
          </div>
        </div>
      </div>
<?php
      if($block == 3 && $key != $content_count) {
?>
      <div class="dt-sc-hr-invisible-small"></div>
      <div class="dt-sc-hr-invisible"></div>
<?php
      }
      $key++;
      $block++;
    }
  }
}
  
function print_team_members_blocks($blocks_count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $limit = ($blocks_count == 0) ? "" : "LIMIT $blocks_count";

  $query_team_members = "SELECT `team_members`.* FROM `team_members` ORDER BY `team_member_sort_order` ASC $limit";
  //echo $query_team_members;exit;
  $result_team_members = mysqli_query($db_link, $query_team_members);
  if(!$result_team_members) echo mysqli_error($db_link);
  $team_members_count = mysqli_num_rows($result_team_members);
  if($team_members_count > 0) {
    $block = 1;
    $key = 1;
    while($team_members_row = mysqli_fetch_assoc($result_team_members)) {

      $team_member_id = $team_members_row['team_member_id'];
      $team_member_name = $team_members_row['team_member_name'];
      $team_member_position = $team_members_row['team_member_position'];
      $team_member_certificates = $team_members_row['team_member_certificates'];
      $team_member_experience = $team_members_row['team_member_experience'];
      $team_member_facebook = $team_members_row['team_member_facebook'];
      $team_member_image = $team_members_row['team_member_image'];
      $team_member_image_exploded = explode(".", $team_member_image);
      $team_member_image_name = $team_member_image_exploded[0];
      $team_member_image_exstension = $team_member_image_exploded[1];
      $team_member_image_thumb = "/site/images/team_members/".$team_member_image_name.".".$team_member_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$team_member_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];

      $team_member_training_disciplines = "";

      $query_training_disc = "SELECT `training_disciplines`.`training_discipline_name`
                              FROM `training_disciplines`
                              INNER JOIN `team_members_to_disciplines` ON `team_members_to_disciplines`.`training_discipline_id` = `training_disciplines`.`training_discipline_id`
                              WHERE `team_members_to_disciplines`.`team_member_id` = '$team_member_id'";
      $result_training_disc = mysqli_query($db_link, $query_training_disc);
      if(!$result_training_disc) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_training_disc) > 0) {
        $key = 0;
        while($team_members_row = mysqli_fetch_assoc($result_training_disc)) {

          $training_discipline_name = $team_members_row['training_discipline_name'];
          $team_member_training_disciplines .= ($key == 0) ? $training_discipline_name : ", $training_discipline_name";

          $key++;
        }
      }
      if($block == 5) $block = 1;
      $class_first = ($block == 1) ? "first" : "";
      $data_animation = "";
      switch($block) {
        case 1:
          $data_animation = "fadeInLeft";
          break;
        case 2:
          $data_animation = "fadeInUp";
          break;
        case 3:
          $data_animation = "fadeInDown";
          break;
        case 4:
          $data_animation = "fadeInRight";
          break;
      }
?>
      <div class="dt-sc-one-fourth column <?=$class_first;?> animate" data-animation="<?=$data_animation;?>" data-delay="100">
        <div class="dt-sc-team type2">
            <div class="team-thumb">
                <img src="<?=$team_member_image_thumb;?>" <?=$thumb_image_dimensions;?> alt="<?=$team_member_name;?>">
                  <h3><span><?=$team_member_name;?></span></h3>
                  <div class="team-detail">
                    <h4><?=$team_member_position;?></h4>
                      <ul>
                        <li><?=$team_member_training_disciplines;?></li>
                        <li><b><?=$languages[$current_lang]['text_certificates'];?>:</b> <?=$team_member_certificates;?></li>
                        <li><b><?=$languages[$current_lang]['text_experience'];?>:</b> <?=$team_member_experience;?>+ <?=$languages[$current_lang]['text_years'];?> </li>
                      </ul>
                  </div>
              </div>
              <ul class="dt-sc-social-icons">
                <li class="facebook"><a class="fa fa-facebook" href="<?=$team_member_facebook;?>" target="_balnk"></a></li>
              </ul>
          </div>
      </div>
<?php
      if($block == 4 && $key != $team_members_count) {
?>
      <div class="dt-sc-hr-invisible-small"></div>
      <div class="dt-sc-hr-invisible"></div>
<?php
      }
      $key++;
      $block++;
    }
  }
}
  
function print_page_blocks($content_parent_id,$blocks_count, $activities_header = false) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $limit = ($blocks_count == 0) ? "" : "LIMIT $blocks_count";

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '$content_parent_id'
                      AND `content_is_active` = '1'
                    ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    if($activities_header) {
?>
    <h2 class="border-title aligncenter"><span><?=$languages[$current_lang][$activities_header];?></span></h2>   
<?php
    }
    $block = 1;
    $key = 1;
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_attribute_1 = stripslashes($content_row['content_attribute_1']);
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }
      if($block == 4) $block = 1;
      $class_first = ($block == 1) ? "first" : "";
?>
      <div class="column dt-sc-one-third <?=$class_first;?>">
        <div class="dt-sc-event type2">
          <div class="event-thumb">
            <a href="<?=$content_hierarchy_path;?>" title="<?=$content_menu_text;?>">
              <img alt="<?=$content_menu_text;?>" class="attachment-blog-threecol wp-post-image" height="300" src="/site/images/page-<?=$content_id;?>.jpg" title="<?=$content_menu_text;?>" width="439" />
            </a></div>

          <div class="event-detail">
          <h2><a href="<?=$content_hierarchy_path;?>"><?=$content_menu_text;?></a></h2>

          <p><?=$content_attribute_1;?></p>
          <a class="dt-sc-button small" href="<?=$content_hierarchy_path;?>"><span data-hover="<?=$languages[$current_lang]['btn_read_more'];?>"><?=$languages[$current_lang]['btn_read_more'];?></span></a>
          </div>
        </div>
      </div>
<?php
      if($block == 3 && $key != $content_count) {
?>
      <div class="dt-sc-hr-invisible  ">&nbsp;</div>
<?php
      }
      $key++;
      $block++;
    }
  }
}
  
function print_events_blocks($blocks_count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;

  $limit = ($blocks_count == 0) ? "" : "LIMIT $blocks_count";
  $current_date = date("Y-m-d");
  $query_event_dates = "SELECT `events`.`event_date`
                        FROM `events` 
                        INNER JOIN `events_descriptions` ON `events_descriptions`.`event_id` = `events`.`event_id`
                        WHERE `events`.`event_is_active` = '1' AND `events`.`event_date` >= '$current_date' AND `events_descriptions`.`language_id` = '$current_language_id'
                        GROUP BY `events`.`event_date`
                        ORDER BY `events`.`event_date` DESC $limit";
  //echo $query_event_dates;exit;
  $result_event_dates = mysqli_query($db_link, $query_event_dates);
  if(!$result_event_dates) echo mysqli_error($db_link);
  if($event_dates_count = mysqli_num_rows($result_event_dates) > 0) {
    while($event_date_row = mysqli_fetch_assoc($result_event_dates)) {
      
      $event_date = $event_date_row['event_date'];
      $event_date_day = date("d", strtotime($event_date_row['event_date']));
      $event_date_month_text = "text_date_month_full_".date("m", strtotime($event_date_row['event_date']));
      $event_date_month = $languages[$current_lang][$event_date_month_text];
      $event_date_year = date("Y", strtotime($event_date_row['event_date']));
      
      $query_event = "SELECT `events`.`event_id`,`events`.`event_image`,`events`.`event_date`,`events`.`event_time_start`,`events`.`event_time_end`,
                              `events`.`event_map_address`,`events`.`event_is_active`,`events_descriptions`.`event_name`,`events_descriptions`.`event_summary`
                      FROM `events` 
                      INNER JOIN `events_descriptions` ON `events_descriptions`.`event_id` = `events`.`event_id`
                      WHERE `events`.`event_is_active` = '1' AND `events`.`event_date` = '$event_date'
                        AND `events_descriptions`.`language_id` = '$current_language_id'
                      ORDER BY `events`.`event_date` ASC $limit";
      //echo $query_event;exit;
      $result_event = mysqli_query($db_link, $query_event);
      if(!$result_event) echo mysqli_error($db_link);
      $event_count = mysqli_num_rows($result_event);
      if($event_count > 0) {
?>
        <p>&nbsp;</p>
        <div class="latest-work-caption-container tribe-events-list-separator-month">
          <div class="caption-main-container clearfix">
            <div class="caption-text-container">
              <span class="bold"><?=$event_date_month;?></span> <?=$event_date_year;?>
            </div>
          </div>
        </div>
<?php
        $block = 1;
        while($event_row = mysqli_fetch_assoc($result_event)) {

          $event_id = $event_row['event_id'];
          $event_name = $event_row['event_name'];
          $event_name_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $event_name);
          $event_name_url = str_replace(" ", "-", mb_convert_case($event_name_escaped, MB_CASE_LOWER, "UTF-8"));
          $event_summary = stripslashes($event_row['event_summary']);
          $event_date_day = date("d", strtotime($event_row['event_date']));
          $event_date_month_text = "text_date_month_".date("m", strtotime($event_row['event_date']));
          $event_date_month = $languages[$current_lang][$event_date_month_text];
          $event_date_year = date("Y", strtotime($event_row['event_date']));
          $event_time_start = (!is_null($event_row['event_time_start'])) ? date("H:i", strtotime($event_row['event_time_start'])) : "";
          $event_time_end = (!is_null($event_row['event_time_end'])) ? date("H:i", strtotime($event_row['event_time_end'])) : "";
          $event_map_address = stripslashes($event_row['event_map_address']);
          $event_is_active = $event_row['event_is_active'];
          $event_image = $event_row['event_image'];
          $event_image_exploded = explode(".", $event_image);
          @$event_image_name = $event_image_exploded[0];
          @$event_image_exstension = $event_image_exploded[1];
          $event_image_thumb = "/site/images/events/".$event_image_name."_thumb.".$event_image_exstension;
          @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$event_image_thumb);
//          $thumb_image_dimensions = $thumb_image_params[3];
          $thumb_image_dimensions = 'width="351" height="215"';
          $event_details_link = $_SERVER['REDIRECT_URL']."/$event_name_url?eid=$event_id";

          $class_first = ($block == 1) ? " tribe-events-first" : "";
    ?>
          <div id="post_<?=$event_id;?>" class="event_block post-<?=$event_id;?><?=$class_first;?>" >
            <!-- Event Image -->
            <div class="tribe-events-event-image">
              <a href="<?=$event_details_link;?>">
                <img title="<?=$event_name;?>" alt="<?=$event_name;?>" src="<?=$event_image_thumb;?>" <?=$thumb_image_dimensions;?>>
              </a>
            </div>
            <div class="tribe-events-list-event-detail">
              <!-- Event Title -->
              <h2 class="tribe-events-list-event-title entry-title summary">
                <a class="url" href="<?=$event_details_link;?>" title="<?=$event_name;?>" rel="bookmark">
                  <?=$event_name;?>
                </a>
              </h2>
              <!-- Event Meta -->
              <div class="tribe-events-event-meta vcard">
                <div class="author  location">
                  <!-- Schedule & Recurrence Details -->
                  <div class="updated published time-details">
                    <?="$event_date_day $event_date_month $event_date_year @ $event_time_start - $event_time_end";?>
                  </div>
                  <!-- Venue Display Info -->
                  <div class="tribe-events-venue-details">
                    <address class="tribe-events-address">
                      <span class="adr">
                        <span class="street-address"><?=$event_map_address?></span>
                      </span>
                    </address>
                  </div> <!-- .tribe-events-venue-details -->
                </div>
              </div><!-- .tribe-events-event-meta -->
              <!-- Event Content -->
              <div class="tribe-events-list-event-description tribe-events-content description entry-summary">
                <p><?=$event_summary;?></p>
                <div class="dt-sc-hr-invisible-small"></div>
                <!-- Event Cost -->
                <a href="<?=$event_details_link;?>" class="button small" rel="bookmark"><?=$languages[$current_lang]['btn_read_more'];?></a>
              </div><!-- .tribe-events-list-event-description -->
            </div>
            <div class="clearfix"></div>
          </div><!-- .hentry .vevent -->
    <?php
          $block++;
        } //while($event_row = mysqli_fetch_assoc($result_event))
      } //if($event_count > 0)
    } //while($event_date_row = mysqli_fetch_assoc($result_event_dates))
  } //if($event_dates_count = mysqli_num_rows($result_event_dates) > 0)
  else {
?>
    <p>&nbsp;</p>
    <span class='tribe-events-list-separator-month'><span><?=$languages[$current_lang]['text_no_upcoming_events'];?></span></span>
<?php
  }
}
  
function print_index_sliders($count) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  $limit = ($count == 0) ? "" : "LIMIT $count";

  $query_sliders = "SELECT `sliders`.`slider_id`,`sliders`.`slider_image`,`sliders`.`slider_sort_order`,
                          `sliders_descriptions`.`slider_header`,`sliders_descriptions`.`slider_text` ,`sliders_descriptions`.`slider_link`
                    FROM `sliders`
                    INNER JOIN `sliders_descriptions` ON `sliders_descriptions`.`slider_id` = `sliders`.`slider_id`
                    WHERE `sliders`.`slider_is_active` = '1' AND `sliders_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `slider_sort_order` ASC $limit";
  //echo $query_sliders;exit;
  $result_sliders = mysqli_query($db_link, $query_sliders);
  if(!$result_sliders) echo mysqli_error($db_link);
  $sliders_count = mysqli_num_rows($result_sliders);
  if($sliders_count > 0) {
    $key = 1;
?>
<!-- SLIDER -->
<div id="slider-container" class="container clearfix hidden-xs">
  <div class="sixteen columns ">
    <div class="flexslider">
      <ul class="slides">
<?php
    while($slider_row = mysqli_fetch_assoc($result_sliders)) {

      $slider_id = $slider_row['slider_id'];
      $slider_image = $slider_row['slider_image'];
      $slider_header = $slider_row['slider_header'];
      $slider_header_html = (!empty($slider_header)) ? '<h3 class="flex-caption-container">'.$slider_header.'</h3>' : "";
      $slider_text = $slider_row['slider_text'];
      $slider_text_html = (!empty($slider_text)) ? '<div class="flex-caption-text-container"><p>'.$slider_text.'</p></div>' : "";
      $slider_link = $slider_row['slider_link'];
      $slider_image_exploded = explode(".", $slider_image);
      $slider_image_name = $slider_image_exploded[0];
      $slider_image_exstension = $slider_image_exploded[1];
      $slider_image_thumb = "/site/images/slider/".$slider_image_name."_site.".$slider_image_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$slider_image_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
      $btn_text_read = $languages[$current_lang]['btn_slider_read'];
      $btn_text_more = $languages[$current_lang]['btn_slider_more'];
      $link_see_more = (!empty($slider_link)) ? '<a class="right r-m-plus r-m-full" href="'.$slider_link.'"><span class="bold">'.$btn_text_read.'</span> '.$btn_text_more.'</a>' : "";
      $slider_box_current = ($key == 1) ? " slider_box_current" : "";
?>
        <li>
          <img src="<?=$slider_image_thumb;?>" alt="<?=$slider_header;?>" />
          <div class="flex-caption">
            <?=$slider_header_html;?>
            <?=$slider_text_html;?>
            <?=$link_see_more;?>
          </div>
        </li>
<?php
      $key++;
    } //while($slider_row)
?>

      </ul>
    </div>
  </div>		
</div>    
<?php
  } //if(mysqli_num_rows($result_sliders) > 0)
}
  
function print_header_language_menu() {

  global $db_link;
  global $current_language_id;
  global $current_page_pretty_url;
  global $content_hierarchy_ids; //coming from site/index.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  $content_parent_id = $content_hierarchy_ids_array[0];

  $query_languages = "SELECT `languages`.`language_id`, `languages`.`language_code`, `languages`.`language_root_content_id`,
                              `languages`.`language_menu_name`, `contents`.`content_hierarchy_path` 
                      FROM `languages`
                      INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                      WHERE `languages`.`language_is_active` = '1'
                      ORDER BY `languages`.`language_menu_order` ASC";
  //echo $query_content;exit;
  $result_languages = mysqli_query($db_link, $query_languages);
  if(!$result_languages) echo mysqli_error($db_link);
  $language_count = mysqli_num_rows($result_languages);
  if($language_count > 1) {
    while($language_row = mysqli_fetch_assoc($result_languages)) {

      $language_id = $language_row['language_id'];
      $language_code = $language_row['language_code'];
      $language_root_content_id = $language_row['language_root_content_id'];
      $content_hierarchy_path = $language_row['content_hierarchy_path'];
      $language_menu_name = stripslashes($language_row['language_menu_name']);
      $class_active = ($language_root_content_id == $content_parent_id) ? "class='active'" : "";
?>
      <a href='/<?=$content_hierarchy_path;?>' id='<?=$language_code;?>' <?=$class_active;?> onclick="createCookie('frontend_lang','<?=$language_code;?>')">
        <img src="/site/images/flags/<?=$language_code;?>.png" width="19" height="14" alt="<?=$language_menu_name;?>" >
      </a>

<?php
    }
    mysqli_free_result($result_languages);
  }
}
  
function list_products_by_option_value($category_id,$offset,$current_cat_href,$cd_pretty_url,$products_count = false) {

  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $current_category_id;

  if(!isset($order_by_price)) $order_by_price = "ASC";

  $page_offset = 50;
  $current_cat_href_final = $current_cat_href;
  $limit = "LIMIT $offset,$page_offset";
  $limit = "";

  $query_products = "SELECT `products`.`product_id`,`product_description`.`pd_name`
                      FROM `products`
                      INNER JOIN `product_to_category` ON `product_to_category`.`product_id` = `products`.`product_id`
                      INNER JOIN `product_description` ON `product_description`.`product_id` = `products`.`product_id`
                      WHERE `products`.`product_is_active` = '1' AND `product_to_category`.`category_id` = '$category_id'
                      AND `product_description`.`language_id` = '$current_language_id'
                      GROUP BY `products`.`product_id` 
                      ORDER BY `product_description`.`pd_name` ASC
                      $limit";
  //echo $query_products."<br>";
  //echo '<input type="hidden" value="'.$query_products.'" />';
  $result_products = mysqli_query($db_link, $query_products);
  if(!$result_products) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_products) > 0) {

    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($products_count > $page_offset) {
      $page_count = ceil($products_count/$page_offset);
    }

    while($product_row = mysqli_fetch_assoc($result_products)) {

      $product_id = $product_row['product_id'];
      $pd_name = $product_row['pd_name'];

      $pd_images_folder = "/site/images/products/";
      $default_img = get_product_default_image($product_id);

      if(empty($default_img)) $default_img = "no_image.jpg";
      $default_img_exploded = explode(".", $default_img);
      $default_img_name = $default_img_exploded[0];
      $default_img_cat_thumb_exstension = $default_img_exploded[1];
      $default_img_cat_thumb = $pd_images_folder.$default_img_name."_cat_thumb.".$default_img_cat_thumb_exstension;
      @$default_img_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$default_img_cat_thumb);
      $default_img_dimensions = $default_img_params[3];

      $gallery_img_path = explode(".", $default_img);
      $gallery_img_path_name = $gallery_img_path[0];
      $gallery_img_cat_thum_exstension = $gallery_img_path[1];
      $gallery_img_cat_thumb = $pd_images_folder.$gallery_img_path_name."_cat_thumb.".$gallery_img_cat_thum_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$gallery_img_cat_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
      $current_cat_href_final = (empty($cd_pretty_url)) ? $current_cat_href : "$current_cat_href/$cd_pretty_url";
?>
      <div>
        <h2>
          <a href="<?="/$current_cat_href_final?rcid=$current_category_id&pid=$product_id";?>" title="<?=$pd_name;?>" class="products">
            <?=$pd_name;?>
          </a>
        </h2>
      </div>
<?php
    } //while($product_row)

    // if the results are more then $page_offset make pagination
    if(isset($page_count)) {

?>
    <div class="col-lg-12">
      <div class="text-center">
        <ul id="pagination_<?=$category_id;?>" class="php_pagination pagination">
<?php
        $pages = 1;
        $current_offset = $offset;
        $offset = 0;

        if($current_page == 1) {
          echo '<li class="disabled btn_prev_page"><a href="javascript:;" data="">&laquo; </a></li>';
        }
        else {
          $prev_offset = $current_offset - $page_offset;
          echo '<li class="btn_prev_page"><a href="javascript:;" data="'.$prev_offset.'">&laquo; </a></li>';
        }

        while($pages <= $page_count) {


          $li_current = ($current_page == $pages) ? ' class="active"' : "";

          echo "<li id='pag_$pages' $li_current><a href='javascript:;' data=\"$offset\">$pages</a></li>";

          $pages++;
          $offset += $page_offset;
        }
        if($current_page == $page_count) {
          echo '<li class="disabled btn_next_page"><a href="javascript:;" data=""> &raquo;</a></li>';
        }
        else {
          $next_offset = $current_offset + $page_offset;
          echo '<li class="btn_next_page"><a href="javascript:;" data="'.$next_offset.'">&raquo; </a></li>';
        }
?>
        </ul>
      </div>
    </div>
<?php
    } // if(isset($page_count))
    mysqli_free_result($result_products);
  }
  else {
    
  }
}
  
function print_sitemap($content_parent_id, $content_hierarchy_level_start , $number_of_hierarchy_levels) {

  global $db_link;
  global $current_language_id;
  global $current_lang;
  global $current_page_path_string;
  global $content_hierarchy_ids; //coming from site/index.php or site/categories.php

  $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
  if($content_parent_id == 0) {
    if(strstr($content_hierarchy_ids, ".")) {
      $content_hierarchy_ids_array = explode(".", $content_hierarchy_ids);
      $content_parent_id = $content_hierarchy_ids_array[0];
    }
    else {
      $content_parent_id = $content_hierarchy_ids;
    }
  }

  $content_hierarchy_level_in_query = "";

  if($content_hierarchy_level_start == 0) {
    if($number_of_hierarchy_levels != 0) $content_hierarchy_level_in_query = " AND `content_hierarchy_level` <= '$number_of_hierarchy_levels'";
  }
  else {
    $content_hierarchy_level_in_query = " AND `content_hierarchy_level` = '$content_hierarchy_level_start'";
  }

  $query_content = "SELECT `content_id`,`content_type_id`,`content_menu_text`,`content_hierarchy_path`,`content_has_children`,
                            `content_hierarchy_level`,`content_text`,`content_menu_order`,`content_target`,`content_attribute_1` 
                    FROM `contents`
                    WHERE `content_parent_id` = '$content_parent_id' $content_hierarchy_level_in_query
                      AND (`content_show_in_menu` = '1' || `content_show_in_footer` = '1')
                    ORDER BY `content_menu_order` ASC";
  //echo $query_content;exit;
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  $content_count = mysqli_num_rows($result_content);
  if($content_count > 0) {
    while($content_row = mysqli_fetch_assoc($result_content)) {
      $content_id = $content_row['content_id'];
      $content_type_id = $content_row['content_type_id'];
      $content_menu_text = stripslashes($content_row['content_menu_text']);
      $content_has_children = $content_row['content_has_children'];
      $content_hierarchy_level = $content_row['content_hierarchy_level'];
      $content_text = $content_row['content_text'];
      $content_menu_order = $content_row['content_menu_order'];
      $content_target = (is_null($content_row['content_target'])) ? "" : "target='".$content_row['content_target']."'";
      switch($content_type_id) {
        case 1:
          $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
        case 2:
          $content_hierarchy_path = "javascript:;";
          break;
        case 4:
          $content_hierarchy_path = $content_text;
          break;
        default: $content_hierarchy_path = "/".$content_row['content_hierarchy_path'];
          break;
      }

      $content_has_active_children = check_if_content_has_active_children($content_id);
      $content_is_last_child = check_if_this_is_content_last_child($content_parent_id,$content_menu_order);

      if($content_has_children == 1 && $content_hierarchy_level < $number_of_hierarchy_levels && $content_has_active_children) {
?>
      <li class="no_padding">
        <a href="<?=$content_hierarchy_path;?>"><?="$content_menu_text";?></a>
        <ul>
<?php
        print_sitemap($content_id, $content_hierarchy_level_start = 0, $number_of_hierarchy_levels);
      }
      else {
?>
      <li><a href="<?=$content_hierarchy_path;?>" <?=$content_target;?>><?=$content_menu_text;?></a></li>
<?php
      }

      if($content_hierarchy_level > 2 && $content_is_last_child) {
?>
        </ul>
      </li>
<?php
      }

    }
  }
}

function list_upcoming_courses($count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $content_type_id = 9; // courses
  $limit = ($count) ? "LIMIT $count" : "";
  $current_date = date("Y-m-d");
  
  $query_courses = "SELECT `courses`.`course_id`,`courses`.`course_date`,`courses`.`course_cost`,`courses`.`course_duration`,
                            `courses_descriptions`.`cd_name`
                    FROM `courses` 
                    INNER JOIN `courses_descriptions` ON `courses_descriptions`.`course_id` = `courses`.`course_id`
                    WHERE `courses`.`course_date` > '$current_date' AND `courses`.`course_is_active` = '1'
                      AND `courses_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `courses`.`course_date` DESC $limit";
  //echo $query_courses;exit;
  $result_courses = mysqli_query($db_link, $query_courses);
  if(!$result_courses) echo mysqli_error($db_link);
  $courses_count = mysqli_num_rows($result_courses);
  if($courses_count > 0) {
?>
  <table style="width: 100%;">
    <thead>
      <tr>
        <th width="10%"><?=$languages[$current_lang]['header_date'];?></th>
        <th width="30%"><?=$languages[$current_lang]['header_course_name'];?></th>
        <th width="30%"><?=$languages[$current_lang]['header_duration'];?></th>
        <th width="15%"><?=$languages[$current_lang]['header_price_vat'];?></th>
        <th width="15%"><?=$languages[$current_lang]['header_registration'];?></th>
      </tr>
    </thead>
    <tbody>
<?php
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
    
    $block = 1;
    
    while($course_row = mysqli_fetch_assoc($result_courses)) {

      $course_id = $course_row['course_id'];
      $cd_name = stripslashes($course_row['cd_name']);
      $cd_name_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $cd_name);
      $cd_name_url = str_replace(" ", "-", mb_convert_case($cd_name_escaped, MB_CASE_LOWER, "UTF-8"));
      $course_date = date("d.m.Y", strtotime($course_row['course_date']));
      $course_duration = $course_row['course_duration'];
      $course_cost = $course_row['course_cost'];
      $course_subscribe_link = "/$current_lang/$content_pretty_url/$cd_name_url?coid=$course_id&scrb=1";
?>
      <tr>
        <td class="text-center"><?=$course_date;?></td>
        <td class="text-center"><?=$cd_name;?></td>
        <td class="text-center"><?=$course_duration;?></td>
        <td class="text-center"><?=$course_cost;?> лв.</td>
        <td class="text-center">
          <a href="<?=$course_subscribe_link;?>" class="button medium"><?=$languages[$current_lang]['btn_newslettter'];?></a>
        </td>
      </tr>
<?php
    }
    mysqli_free_result($result_courses);
?>
    </tbody>
  </table> 
<?php
  }
  else {
?>
    <p><?=$languages[$current_lang]['text_no_upcoming_courses'];?></p>
<?php
  }
}

function list_all_courses($count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $content_type_id = 9; // courses
  $limit = ($count) ? "LIMIT $count" : "";
  $current_date = date("Y-m-d");
  
  $query_courses = "SELECT `courses`.`course_id`,`courses`.`course_image`,`courses_descriptions`.`cd_name`
                    FROM `courses` 
                    INNER JOIN `courses_descriptions` ON `courses_descriptions`.`course_id` = `courses`.`course_id`
                    WHERE `courses`.`course_is_active` = '1' AND `courses_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `courses`.`course_date` DESC $limit";
  //echo $query_courses;exit;
  $result_courses = mysqli_query($db_link, $query_courses);
  if(!$result_courses) echo mysqli_error($db_link);
  $courses_count = mysqli_num_rows($result_courses);
  if($courses_count > 0) {
?>
  <div class="container filter-portfolio clearfix">
    <ul id="portfolio" class="clearfix" style="margin-left: -10px;">
<?php
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
    
    $block = 1;
    $course_images_folder = "/site/images/courses/";
    
    while($course_row = mysqli_fetch_assoc($result_courses)) {

      $course_id = $course_row['course_id'];
      $cd_name = stripslashes($course_row['cd_name']);
      $cd_name_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $cd_name);
      $cd_name_url = str_replace(" ", "-", mb_convert_case($cd_name_escaped, MB_CASE_LOWER, "UTF-8"));
      $course_image = $course_row['course_image'];
      $course_image_path = explode(".", $course_image);
      $course_image_path_name = $course_image_path[0];
      $course_image_cat_thum_exstension = $course_image_path[1];
      $course_image_cat_thumb = $course_images_folder.$course_image_path_name."_thumb.".$course_image_cat_thum_exstension;
      @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$course_image_cat_thumb);
      $thumb_image_dimensions = $thumb_image_params[3];
      $course_details_link = "/$current_lang/$content_pretty_url/$cd_name_url?coid=$course_id";
?>
      <li class="one-third column m-bot-25">
        <div class="content-container-white">
          <div class="view view-first">
            <a href="<?=$course_details_link;?>"><img src="<?=$course_image_cat_thumb?>" alt="<?=$cd_name;?>" width="298" height="176" ></a>
          </div>
          <div class="lw-item-caption-container" style="border-top: 1px solid #dadada;">
            <a class="a-invert" href="<?=$course_details_link;?>" ><?=$cd_name;?></a>
          </div>
        </div>
      </li>

<?php
    }
    mysqli_free_result($result_courses);
?>
    </ul>
  </div>
<?php
  }
  else {
?>
    <p><?=$languages[$current_lang]['text_no_upcoming_courses'];?></p>
<?php
  }
}

function list_news_on_homepage($count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;

  $content_type_id = 7; // news
  $limit = ($count) ? "LIMIT $count" : "";
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_category_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                        `news`.`news_is_active`,`news`.`news_image`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary` 
                    FROM `news` 
                    INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                    WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `news`.`news_created_date` DESC $limit";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news);
  if($news_count > 0) {
    
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
    
    $block = 1;
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_category_id = $news_row['news_category_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_title_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $news_title);
      $news_title_url = str_replace(" ", "-", mb_convert_case($news_title_escaped, MB_CASE_LOWER, "UTF-8"));
      //$news_summary = truncate($news_row['news_summary']);
//      $news_summary = stripslashes($news_row['news_summary']);
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
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?nid=$news_id";
      
      if($block == 4) $block = 1;
      $class_first = ($block == 1) ? " first" : "";
?>
      <li class="four columns">
        <div class="content-container-white">
          <div class="view view-first">
            <div class="date">
              <?=$news_post_date_day;?>
              <?=$news_post_date_month;?>
              <?=$news_post_date_year;?>
            </div>
            <a href="<?=$news_details_link;?>"><img src="<?=$image_thumb_name;?>" alt="<?=$news_title;?>" ></a>
          </div>
          <div class="lw-item-caption-container">
            <a class="a-invert" href="<?=$news_details_link;?>" ><?=$news_title;?></a>
          </div>
        </div>
        <div class="under-box-bg"></div>		
        <div class="content-container-white lw-item-text-container">
          <p><?=$news_summary;?></p>
          <a href="<?=$news_details_link;?>" class="r-m-plus-small">&nbsp;</a>
        </div>
      </li>
<?php
      $block++;
    }
    mysqli_free_result($result_news);
  }
}

function list_news($offset = false,$news_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $news_category_id;

  $content_type_id = 7; // news
  
  $page_offset = 4;
  $offset = ($offset) ? $offset : 0;
  
  $and_news_category = (!isset($news_category_id) || empty($news_category_id) || $news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
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
    
  if(!($news_count)) {
    $query_news = "SELECT `news`.`news_id`
                    FROM `news`
                    WHERE `news`.`news_is_active` = '1' $and_news_category";
    //echo $query_news."<br>";
    $result_news = mysqli_query($db_link, $query_news);
    if(!$result_news) echo mysqli_error($db_link);
    $news_count = mysqli_num_rows($result_news);
  }

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
    
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
    
    // if the results are more then $page_offset
    // making a pagination, finding how many pages will be needed
    $current_page = ($offset/$page_offset)+1;

    if($news_count > $page_offset) {
      $page_count = ceil($news_count/$page_offset);
    }
    
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_title_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $news_title);
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
      $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?nid=$news_id";
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
    }
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
}

function list_news_in_footer($news_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
          
  $content_type_id = 7; // news
  $limit = ($news_count) ? "LIMIT $news_count" : "";
  
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                        `news`.`news_is_active`,`news`.`news_image`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary` 
                    FROM `news` 
                    INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                    WHERE `news`.`news_is_active` = '1' AND `news_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY `news`.`news_created_date` DESC $limit";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news);
  if($news_count > 0) {
    
    $query_content = "SELECT `content_pretty_url`
                      FROM `contents`
                      WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
    //echo $query_content."<br><br>";
    $result_content = mysqli_query($db_link, $query_content);
    if(!$result_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_content) > 0) {
      $content_array = mysqli_fetch_assoc($result_content);
      $content_pretty_url = $content_array['content_pretty_url'];
    }
      
    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = $news_row['news_title'];
      $news_title_escaped = str_replace(array('\\','?','!','.','(',')'), array('','','','','',''), $news_title);
      $news_title_url = str_replace(" ", "-", mb_convert_case($news_title_escaped, MB_CASE_LOWER, "UTF-8"));
//      $news_summary = $news_row['news_summary'];
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
      if(!isset($class)) $class = "even";
      $class = (($class == "odd") ? "even" : "odd");
      $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?nid=$news_id";
?>
      <li>
        <h4 class="title-post-footer"><a href="<?=$news_details_link;?>"><?=$news_title;?></a></h4>
        <h4 class="date-post-footer"><?="$news_post_date_day $news_post_date_month $news_post_date_year";?></h4>
        <p><?=$news_summary;?></p>
      </li>
<?php
    }
    mysqli_free_result($result_news);
  }
}

function list_news_categories($news_cat_parent_id,$current_news_cat_id = false,$news_categories_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
          
  $content_type_id = 7; // news
  $limit = ($news_categories_count) ? "LIMIT $news_categories_count" : "";
  
  $current_news_cat_id = (isset($_GET['ncid'])) ? $_GET['ncid'] : ((isset($_GET['ncid_d'])) ? $_GET['ncid_d'] : $current_news_cat_id);
  //echo $current_news_cat_id;
  $query_content = "SELECT `content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_pretty_url = $content_array['content_pretty_url'];
  }
  
  $query_news_categories = "SELECT `news_categories`.`news_category_id`, `news_categories`.`news_cat_hierarchy_level`, `news_categories`.`news_cat_has_children`,
                                  `news_categories`.`news_cat_sort_order`,`news_cat_desc`.`news_cat_name`,`news_cat_desc`.`news_cat_hierarchy_path`, 
                                  `news_cat_desc`.`news_cat_long_name` 
                          FROM `news_categories` 
                          INNER JOIN `news_cat_desc` ON `news_cat_desc`.`news_category_id` = `news_categories`.`news_category_id`
                          WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC $limit";
  //echo $query_news_categories;exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news_categories);
  if($news_count > 0) {
    $key = 0;
    while($news_category_row = mysqli_fetch_assoc($result_news_categories)) {

      $news_category_id = $news_category_row['news_category_id'];
      $news_cat_hierarchy_level = $news_category_row['news_cat_hierarchy_level'];
      $news_cat_has_children = $news_category_row['news_cat_has_children'];
      $news_cat_sort_order = $news_category_row['news_cat_sort_order'];
      $news_cat_name = $news_category_row['news_cat_name'];
      $news_cat_hierarchy_path = $news_category_row['news_cat_hierarchy_path'];
      $news_cat_long_name = $news_category_row['news_cat_long_name'];
      $news_category_link = "/$current_lang/$content_pretty_url/$news_cat_hierarchy_path?ncid=$news_category_id";
      
      if($current_news_cat_id != 0) {
        $class_active = ($current_news_cat_id == $news_category_id) ? "active" : "";
      }
      else {
        $class_active = ($key == 0) ? " active" : "";
      }
      
      $class_has_parent = ($news_cat_parent_id == 0) ? "" : " has_parent";
      //$news_cat_has_active_children = check_if_news_cat_has_active_children($news_category_id);
      $news_cat_is_last_child = check_if_this_is_news_cat_last_child($news_cat_parent_id,$news_cat_sort_order);

      if($news_cat_has_children == 1) {
?>
      <li class="<?="$class_active$class_has_parent";?> has_children">
        <a href="<?=$news_category_link;?>"><span class="blog-cat-icon"></span><?=$news_cat_name;?></a>
        <ul>
<?php
        list_news_categories($news_category_id,$current_news_cat_id);
      }
      else {
?>
      <li class="<?="$class_active$class_has_parent";?>"><a href="<?=$news_category_link;?>"><span class="blog-cat-icon"></span><?=$news_cat_name;?></a></li>
<?php
      }

      if($news_cat_hierarchy_level > 1 && $news_cat_is_last_child) {
?>
        </ul>
      </li>
<?php
      }
      $key++;
    }
    mysqli_free_result($result_news_categories);
  }
?>
    
<?php
}

function list_latest_news_for_category($current_news_id, $news_category_id, $news_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
  
  $content_type_id = 7; // news
  $limit = ($news_count) ? "LIMIT $news_count" : "";
  
  $query_content = "SELECT `content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_pretty_url = $content_array['content_pretty_url'];
  }
  
  $and_news_category = ($news_category_id == 1) ? "" : "AND `news`.`news_category_id` = '$news_category_id'";
    
  $query_news = "SELECT `news`.`news_id`,`news`.`news_post_date`,`news`.`news_start_time`,`news`.`news_end_time`,
                      `news`.`news_is_active`,`news`.`news_image`,`news`.`news_views`,`news_descriptions`.`news_title`,`news_descriptions`.`news_summary` 
                  FROM `news` 
                  INNER JOIN `news_descriptions` ON `news_descriptions`.`news_id` = `news`.`news_id`
                  WHERE `news`.`news_id` <> '$current_news_id' AND `news`.`news_is_active` = '1' $and_news_category
                    AND `news_descriptions`.`language_id` = '$current_language_id'
                  ORDER BY `news`.`news_created_date` DESC $limit";
  //echo $query_news;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {

    while($news_row = mysqli_fetch_assoc($result_news)) {

      $news_id = $news_row['news_id'];
      $news_title = stripslashes($news_row['news_title']);
      $news_title_escaped = str_replace(array('\\','?','!','.',',','(',')','%',' ',' - '), array('-','','','','-','-','-','-','-','-'), $news_title);
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
      $news_details_link = "/$current_lang/$content_pretty_url/$news_title_url?ncid_d=$news_category_id&nid=$news_id";
?>
      <li class="latest-post-sidebar clearfix">
        <div>
          <a href="<?=$news_details_link;?>" ><img src="<?=$news_image;?>" alt="<?=$news_title;?>" ></a>
        </div>
        <div>
          <p class="latest-post-sidebar-title"><a href="<?=$news_details_link;?>"><?=$news_title;?></a></p>
          <p class="latest-post-sidebar-date"><?="$news_post_date_day $news_post_date_month $news_post_date_year";?></p>
          <!--<p class="latest-post-sidebar-comm view"><?=$news_views;?> <?=$languages[$current_lang]['text_views'];?></p>-->
        </div>	
      </li>
<?php
    } //while($news_row)
  }
  else {
    echo $languages[$current_lang]['text_no_other_news_in_category'];
  }
}

function list_news_products_categories_on_index_page($news_cat_parent_id,$news_categories_count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
          
  $content_type_id = 7; // news
  $limit = ($news_categories_count) ? "LIMIT $news_categories_count" : "";
  
  $query_content = "SELECT `content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` =  (SELECT `language_root_content_id` FROM `languages` WHERE `language_code` = '$current_lang')";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if(!$result_content) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_pretty_url = $content_array['content_pretty_url'];
  }
  
  $query_news_categories = "SELECT `news_categories`.`news_category_id`,`news_categories`.`news_category_image`,`news_categories`.`news_cat_hierarchy_level`,
                                  `news_categories`.`news_cat_has_children`,`news_categories`.`news_cat_sort_order`,`news_cat_desc`.`news_cat_name`,
                                  `news_cat_desc`.`news_cat_hierarchy_path`,`news_cat_desc`.`news_cat_long_name` 
                          FROM `news_categories` 
                          INNER JOIN `news_cat_desc` ON `news_cat_desc`.`news_category_id` = `news_categories`.`news_category_id`
                          WHERE `news_categories`.`news_cat_parent_id` = '$news_cat_parent_id' AND `news_cat_desc`.`language_id` = '$current_language_id'
                          ORDER BY `news_categories`.`news_cat_sort_order` ASC $limit";
  //echo $query_news_categories;exit;
  $result_news_categories = mysqli_query($db_link, $query_news_categories);
  if(!$result_news_categories) echo mysqli_error($db_link);
  $news_count = mysqli_num_rows($result_news_categories);
  if($news_count > 0) {
    
    while($news_category_row = mysqli_fetch_assoc($result_news_categories)) {

      $news_category_id = $news_category_row['news_category_id'];
      $news_cat_hierarchy_level = $news_category_row['news_cat_hierarchy_level'];
      $news_cat_has_children = $news_category_row['news_cat_has_children'];
      $news_cat_sort_order = $news_category_row['news_cat_sort_order'];
      $news_category_image = $news_category_row['news_category_image'];
      $news_cat_name = $news_category_row['news_cat_name'];
      $news_cat_hierarchy_path = $news_category_row['news_cat_hierarchy_path'];
      $news_cat_long_name = $news_category_row['news_cat_long_name'];
      $news_category_link = "/$current_lang/$content_pretty_url/$news_cat_hierarchy_path?ncid=$news_category_id";

?>
      <li class="four columns">
        <div class="content-container-white">
          <div class="view view-first">
            <a href="<?=$news_category_link;?>"><img src="<?=$news_category_image;?>" alt="<?=$news_cat_name;?>" ></a>
          </div>
          <div class="lw-item-caption-container">
            <a class="a-invert" href="<?=$news_category_link;?>" ><?=$news_cat_name;?></a>
          </div>
        </div>
        <div class="under-box-bg hidden"></div>		
        <div class="content-container-white lw-item-text-container hidden">
          <p><?=$news_cat_name;?></p>
        </div>
      </li>
<?php
    }
    mysqli_free_result($result_news_categories);
  }
}

function list_clients($count = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  
  $query_clients = "SELECT `clients`.`client_id`,`clients`.`client_image`,`clients`.`client_is_active`,`clients`.`client_sort_order`,
                            `clients_descriptions`.`client_name`,`clients_descriptions`.`client_text`,`clients_descriptions`.`client_link`
                    FROM `clients`
                    INNER JOIN `clients_descriptions` ON `clients_descriptions`.`client_id` = `clients`.`client_id`
                    WHERE `clients_descriptions`.`language_id` = '$current_language_id'
                    ORDER BY RAND() LIMIT $count";
  //echo $query_clients;
  $result_clients = mysqli_query($db_link, $query_clients);
  if(!$result_clients) echo mysqli_error($db_link);
  $clients_count = mysqli_num_rows($result_clients);
  if($clients_count > 0) {
?>
  <!-- OUR CLIENTS -->
  <div class="container clearfix">
    <div class="sixteen columns m-bot-20">
      <div class="our-clients-caption-container">
        <div class="caption-main-container clearfix">
          <div class="caption-text-container">
              <span class="bold"><?=$languages[$current_lang]['header_our'];?></span> <?=$languages[$current_lang]['header_clients'];?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container m-bot-20 clearfix">
    <ul class="our-clients-container clearfix">
<?php
    while($client_row = mysqli_fetch_assoc($result_clients)) {
      $client_id = $client_row['client_id'];
      $client_name = stripslashes($client_row['client_name']);
      $client_link = $client_row['client_link'];
      $client_link_text = (!is_null($client_link)) ? $client_link : "javascript:;";
      $client_is_active = $client_row['client_is_active'];
      $client_sort_order = $client_row['client_sort_order'];
      $set_clients = ($client_is_active == 1) ? 0 : 1;
      $client_image = $client_row['client_image'];
      if(!empty($client_image)) {
        $client_image_exploded = explode(".", $client_image);
        $client_image_name = $client_image_exploded[0];
        $client_image_exstension = $client_image_exploded[1];
        $client_image_thumb = "/site/images/clients/".$client_image_name."_site.".$client_image_exstension;
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
      else {
        $client_image_thumb = "/site/images/no_image.png";
        @$thumb_image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$client_image_thumb);
        $thumb_image_dimensions = $thumb_image_params[3];
      }
?>
      <li class="content-container-white" data-toggle="tooltip" data-placement="bottom" title="<?=$client_name;?>">
        <a href="<?=$client_link_text;?>" target="_blank">
          <div class="bw-wrapper">
            <img src="<?=$client_image_thumb;?>" alt="<?=$client_name;?>" >
          </div>
        </a>
      </li>
<?php
    }
?>
    </ul>
  </div>
<?php
    mysqli_free_result($result_clients);
  }
}

function list_email_campaigns($campaigns_limit = false) {
  
  global $db_link;
  global $current_language_id;
  global $languages;
  global $current_lang;
  global $class;
  global $page;
          
  $limit = ($campaigns_limit) ? "LIMIT $campaigns_limit" : "";
  
  $query_email_campaigns = "SELECT `campaign_id`, `campaign_name` FROM `email_campaigns` $limit";
  //echo $query_email_campaigns;exit;
  $result_email_campaigns = mysqli_query($db_link, $query_email_campaigns);
  if(!$result_email_campaigns) echo mysqli_error($db_link);
  $campaigns_count = mysqli_num_rows($result_email_campaigns);
  if($campaigns_count > 0) {
      
    while($email_campaigns_row = mysqli_fetch_assoc($result_email_campaigns)) {

      $campaign_id = $email_campaigns_row['campaign_id'];
      $campaign_name = $email_campaigns_row['campaign_name'];
?>
      <input type="checkbox" name="campaigns[]" id="campaign_<?=$campaign_id;?>" value="<?=$campaign_id;?>" checked="checked" /> <?=$campaign_name;?> &nbsp;
      <input type="hidden" name="campaign_name_<?=$campaign_id;?>" value="<?=$campaign_name;?>" />
<?php
    }
    mysqli_free_result($result_email_campaigns);
  }
}

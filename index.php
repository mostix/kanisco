<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
  
  $dirname = dirname(__FILE__);
  
  include_once 'site/config.php';
  include_once 'site/languages/languages.php';
  include_once 'site/functions/include-functions.php';

  if(isset($_GET['cid'])) {
    //category_id
    
    require_once 'site/categories.php';
  }
  elseif(isset($_GET['pid'])) {
    //product_id
    
    require_once 'site/product-details.php';
  }
  elseif(isset($_GET['ncid'])) {
    //news_category_id
    
    require_once 'site/news-by-category.php';
  }
  elseif(isset($_GET['nid'])) {
    //news_id
    
    require_once 'site/news-details.php';
  }
  elseif(isset($_GET['eid'])) {
    //event_id
    
    require_once 'site/event-details.php';
  }
  elseif(isset($_GET['coid'])) {
    //course_id
    
    require_once 'site/course-details.php';
  }
  else {
    require_once 'site/index.php';
  }
?>


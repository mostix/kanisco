<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

if (isset($_GET['rcid'])) {
  $current_category_id = $_GET['rcid']; // current selected root category id
}
if (isset($_GET['pid'])) {
  $current_product_id = $_GET['pid']; // current selected product
}

// encrease product_viewed by one
$query_update_product = "UPDATE `products` SET `product_viewed` = `product_viewed`+1 WHERE `product_id` = '$current_product_id'";
$result_update_product = mysqli_query($db_link, $query_update_product);
if (mysqli_affected_rows($db_link) <= 0) {
  echo $languages[$current_lang]['sql_error_delete'] . " - " . mysqli_error($db_link);
}

if (isset($_GET['page']) && !empty($_GET['page'])) {
  //$current_category_path = $_GET['page'];

  $current_page_path_string = $_GET['page'];
  //echo $current_page_path_string;
  $current_category_path = explode("/", $current_page_path_string);
  $count_category_path_elements = count($current_category_path) - 1;
  $current_category_pretty_url = $current_category_path[$count_category_path_elements];
  $current_lang = $current_category_path[0];
  $category_cd_name_1 = str_replace("-", " ", mb_convert_case($current_category_path[1], MB_CASE_TITLE, "UTF-8"));
  $category_cd_name_2 = str_replace("-", " ", mb_convert_case($current_category_path[2], MB_CASE_TITLE, "UTF-8"));
  $category_cd_name_url = $current_category_path[1]."/".$current_category_path[2];
  $current_cd_name = str_replace("-", " ", $current_category_path[$count_category_path_elements]);
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

$content_type_id = 2; // products
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
  $products_ads_banner = $content_array['content_attribute_2'];
  $content_pretty_url = $content_array['content_pretty_url'];
}

$query_product = "SELECT `products`.`product_trial_url`,`product_description`.`pd_name`,`product_description`.`pd_description`,`product_description`.`pd_overview`,
                          `product_description`.`pd_novations`,`product_description`.`pd_system_requirements`,`product_description`.`pd_downloads`,`product_description`.`pd_news_url`,
                          `product_description`.`pd_meta_title`,`product_description`.`pd_meta_description`,`product_description`.`pd_meta_keywords`
                    FROM `products`
                    INNER JOIN `product_description` USING(`product_id`)
                    WHERE `products`.`product_id` = '$current_product_id' AND `product_description`.`language_id` = '$current_language_id'";
//echo $query_product;
$result_product = mysqli_query($db_link, $query_product);
if (!$result_product) echo mysqli_error($db_link);
if (mysqli_num_rows($result_product) > 0) {
  $product_row = mysqli_fetch_assoc($result_product);

  $product_trial_url = stripslashes($product_row['product_trial_url']);
  $pd_name = stripslashes($product_row['pd_name']);
  $pd_description = stripslashes($product_row['pd_description']);
  $pd_overview = stripslashes($product_row['pd_overview']);
  $pd_novations = stripslashes($product_row['pd_novations']);
  $pd_system_requirements = stripslashes($product_row['pd_system_requirements']);
  $pd_downloads = stripslashes($product_row['pd_downloads']);
  $pd_news_url = $product_row['pd_news_url'];
  $pd_meta_title = $product_row['pd_meta_title'];
  $pd_meta_description = $product_row['pd_meta_description'];
  $pd_meta_keywords = $product_row['pd_meta_keywords'];
}
//echo"<pre>";print_r($product_row);

$pi_names_array = get_product_images($current_product_id);
$pd_images_folder = "/site/images/products/";
$product_image = $pd_images_folder . $pi_names_array['default']['pi_name'];
//echo"<pre>";print_r($pi_names_array);echo "<br>";

print_html_header($pd_name, $pd_meta_description, $pd_meta_keywords, $additional_script = false, $body_class = "product");
//echo "<pre>";print_r($_SERVER);
//echo "<pre>";print_r($_SESSION);
?>
<div class="container m-bot-15 clearfix">
  <div class="sixteen columns">
    <div class="page-title-container clearfix">
      <h1 class="page-title">
        <a href="/<?=$home_page_url;?>"><?=$languages[$current_lang]['header_home'];?></a> 
        <span class="sub-title">/ <?= $category_cd_name_1; ?></span> 
        <a href="/<?=$current_lang;?>/<?=$category_cd_name_url;?>?cid=<?=$current_category_id;?>">/ <?= $category_cd_name_2; ?></a> 
        <span class="sub-title">/ <?= $pd_name; ?></span>
      </h1>
      <ul class="portfolio-pagination">
<?php 
    $query_prev_next_product = "SELECT `product_id` FROM `products` 
                                WHERE ( 
                                        `product_id` = IFNULL((SELECT MIN(`product_id`) FROM `products` WHERE `product_id` > '$current_product_id'),0) 
                                        or  `product_id` = IFNULL((SELECT MAX(`product_id`) FROM `products` WHERE `product_id` < '$current_product_id'),0)
                                      )";
    //echo $query_prev_next_product;
    $result_prev_next_product = mysqli_query($db_link, $query_prev_next_product);
    if (!$result_prev_next_product) echo mysqli_error($db_link);
    if (mysqli_num_rows($result_prev_next_product) > 0) {
      $key = 0;
      while($prev_next_product_row = mysqli_fetch_assoc($result_prev_next_product)) {
        $product_id = $prev_next_product_row['product_id'];
        if($key == 0 && $product_id < $current_product_id) {
?>
          <li>
            <a class="pag-prev" href="<?="/$current_page_path_string?pid=$product_id";?>"></a>
          </li>
<?php
        }
        else {
?>
          <li>
            <a class="pag-next" href="<?="/$current_page_path_string?pid=$product_id";?>"></a>
          </li>
<?php
        }
        $key++;
      }
    }
?>
      </ul>
    </div>	
  </div>
</div>
<!-- CONTENT -->
<div class="container clearfix">
  <div class="twelve columns ">
    <!-- PORTFOLIO ITEM -->
    <div class="blog-item m-bot-25">
      <div class="content-container-white">
        <div class="view view-first">
          <img src="<?= $product_image; ?>" alt="<?= $pd_name ?>" >
        </div>
        <div class="contant-container-caption">
          <a class="a-invert" href="javascript:;"><?= $languages[$current_lang]['header_products_overview']; ?></a>
        </div>
      </div>
      <div class="under-box-bg"></div>

      <div id="pd_overview" class="content-container-white blog-text-container">
        <div><?= $pd_overview; ?></div>
      </div>	
<?php if(!is_null($pd_novations) && !empty($pd_novations)) { ?>
      <div id="pd_novations" class="content-container-white blog-text-container">
        <div><?= $pd_novations; ?></div>
      </div>
<?php } ?>
      <div id="pd_system_requirements" class="content-container-white blog-text-container">
        <div><?= $pd_system_requirements; ?></div>
      </div>
<?php if(!is_null($pd_downloads) && !empty($pd_downloads)) { ?>
      <div id="pd_downloads" class="content-container-white blog-text-container">
        <div><?= $pd_downloads; ?></div>
      </div>
<?php } ?>
    </div>
  </div>
  <!-- SIDEBAR -->
  <div class="four columns ">
    <!-- WIDGET -->	
    <div class="sidebar-item  m-bot-25">
      <div class="content-container-white">
        <h3 class="title-widget"><?= $languages[$current_lang]['header_products_work_activities']; ?></h3>
      </div>

      <div class="under-box-bg"></div>

      <div class="content-container-white padding-l-r-15">
        <p><?= $pd_description; ?></p>
      </div>
    </div>

    <!-- WIDGET -->
    <div class="sidebar-item  m-bot-25">
      <div class="content-container-white">
        <h3 class="title-widget"><?= $languages[$current_lang]['header_products_software_details']; ?></h3>
      </div>
      <div class="under-box-bg"></div>
      <div class="content-container-white padding-l-r-15">
        <ul class="project-details">
          <li>
            <a href="#pd_overview" data-caption="<?= $languages[$current_lang]['header_products_overview']; ?>">
              <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_products_overview']; ?>
            </a>
          </li>
<?php if(!is_null($pd_novations) && !empty($pd_novations)) { ?>
          <li>
            <a href="#pd_novations" data-caption="<?= $languages[$current_lang]['header_products_novations']; ?>">
              <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_products_novations']; ?>
            </a>
          </li>
<?php } ?>
          <li>
            <a href="#pd_system_requirements" data-caption="<?= $languages[$current_lang]['header_products_system_requirements']; ?>">
              <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_products_system_requirements']; ?>
            </a>
          </li>
<?php if(!is_null($pd_downloads) && !empty($pd_downloads)) { ?>
          <li>
            <a href="#pd_downloads" data-caption="<?= $languages[$current_lang]['header_pd_downloads']; ?>">
              <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_pd_downloads']; ?>
            </a>
          </li>
<?php } ?>
        </ul>
      </div>
    </div>	

    <!-- WIDGET -->	
    <div class="sidebar-item  m-bot-25">
      <div class="center-text">
      <?php
        if(!empty($product_trial_url)) {
      ?>
        <a class="button large" style="width: 100%;" target="_blank" href="<?=$product_trial_url;?>"><?= $languages[$current_lang]['btn_product_trial_version']; ?></a>
      <?php
        }
      ?>
      </div>
      
      <p>&nbsp;</p>
      
      <div class="center-text">
      <?php
        if(!empty($pd_news_url)) {
      ?>
        <a class="button large" style="width: 100%;" target="_blank" href="<?=$pd_news_url;?>"><?= $languages[$current_lang]['menu_news']; ?></a>
      <?php
        }
      ?>
      </div>
    </div>			

    <!-- WIDGET -->	
    <div class="sidebar-item  m-bot-25">
      <div class="content-container-white hidden">
        <h3 class="title-widget"><span class="bold">TAGS</span> WIDGET</h3>
      </div>

      <div class="tag-cloud">
        <?php if(!empty($products_ads_banner)) echo "<img src='$products_ads_banner' alt=''>" ?>
      </div>

    </div>

  </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    // tab switcher
    $(".project-details li").removeClass("active");
    $(".project-details li:first").addClass("active");
    $(".blog-text-container").hide();
    $(".blog-text-container:first").show();
    $(".project-details a").click(function (event) {
      var this_link = $(this);
      var clicked_tab = this_link.attr("href");
      var caption = this_link.attr("data-caption");
      $(".project-details li").removeClass("active");
      this_link.parent().addClass("active");
      $(".blog-text-container").hide();
      $(".contant-container-caption a").html(caption);
      $(clicked_tab).fadeIn();
      event.preventDefault();
    });
    // end tab switcher
  });
</script>
<?php
print_html_footer();
?>
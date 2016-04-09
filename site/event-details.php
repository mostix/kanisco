<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if(isset($_GET['eid'])) {
    $current_event_id = $_GET['eid']; // current selected product
  }
  
  if(isset($_GET['page']) && !empty($_GET['page'])) {
    //$current_category_path = $_GET['page'];
    
    $current_page_path_string = $_GET['page'];
    //echo $current_page_path_string;
    $current_page_path_exploded = explode("/", $current_page_path_string);
    $count_path_elements = count($current_page_path_exploded)-1;
    $current_event_pretty_url = $current_page_path_exploded[$count_path_elements];
    $current_lang = $current_page_path_exploded[0];
    $current_event_page = "/".$current_page_path_exploded[0]."/".$current_page_path_exploded[1];
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
  
  $content_type_id = 8; // events
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
    $trainings_ads_banner = $content_array['content_attribute_2'];
    $content_pretty_url = $content_array['content_pretty_url'];
  }

  $query_event = "SELECT `events`.*,`events_descriptions`.`event_name`,`events_descriptions`.`event_summary`,`events_descriptions`.`event_text` 
                    FROM `events` 
                    INNER JOIN `events_descriptions` ON `events_descriptions`.`event_id` = `events`.`event_id`
                    WHERE `events`.`event_id` = '$current_event_id' AND `events_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_event;exit;
  $result_event = mysqli_query($db_link, $query_event);
  if(!$result_event) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_event) > 0) {
    $event_row = mysqli_fetch_assoc($result_event);

    $event_id = $event_row['event_id'];
    $event_name = stripslashes($event_row['event_name']);
    $event_summary = stripslashes($event_row['event_summary']);
    $event_text = stripslashes($event_row['event_text']);
    $event_date_day = date("d", strtotime($event_row['event_date']));
    $event_date_month_text = "text_date_month_".date("m", strtotime($event_row['event_date']));
    $event_date_month = $languages[$current_lang][$event_date_month_text];
    $event_date_year = date("Y", strtotime($event_row['event_date']));
    $event_time_start = (!is_null($event_row['event_time_start'])) ? date("H:i", strtotime($event_row['event_time_start'])) : "";
    $event_time_end = (!is_null($event_row['event_time_end'])) ? date("H:i", strtotime($event_row['event_time_end'])) : "";
    $event_cost = $event_row['event_cost'];
    $event_map_lat = $event_row['event_map_lat'];
    $event_map_lng = $event_row['event_map_lng'];
    $event_images_folder = "/site/images/events/";
    $event_image = $event_images_folder.$event_row['event_image'];
    @$image_params = getimagesize($_SERVER['DOCUMENT_ROOT'].$event_image);
    $image_dimensions = @$image_params[3];
  }
  
  $body_class = "event_details postid_$event_id";
  print_html_header($event_name, $event_summary, "",$additional_script = false, $body_class);
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
<div class="container m-bot-15 clearfix">
  <div class="sixteen columns">
    <div class="page-title-container clearfix">
      <h1 class="page-title">
        <a href="/<?=$home_page_url;?>"><?=$languages[$current_lang]['header_home'];?></a> 
        /<a href="/<?= $current_lang; ?>/<?= $content_pretty_url; ?>"> <?=$languages[$current_lang]['menu_events'];?></a> 
        <span class="sub-title">/ <?= $event_name; ?></span>
      </h1>
    </div>	
  </div>
</div>

<!-- CONTENT -->
<div class="container clearfix">
  <div class="sixteen columns m-bot-25">
    <!-- BLOG ITEM -->
    <div class="blog-item m-bot-25">
        
      <section id="primary" class="content-full-width">
        <article>
          <div id="tribe-events" class="tribe-no-js" data-live_ajax="1" data-datepicker_format="0" data-category="">
              <div class="entry-thumb">
                  <a href="#"><img src="<?=$event_image;?>" class="img-responsive" alt="<?=$event_name;?>" title="<?=$event_name;?>"></a>
  <!--                <div class="entry-meta">
                      <div class="date">
                          <span><?=$event_date_day;?></span>
                          <?=$event_date_month;?><br>
                          <?=$event_date_year;?>
                      </div>
                  </div>-->
              </div>
              <div class="entry-metadata">
                <h2 class="border-title"><span><?=$event_name;?></span></h2>

                <p><?=$event_text;?></p>
              </div>
          </div>
        </article>
        <div id="map"></div>
<!--        <a href="<?=$current_event_page;?>">
          &laquo; <?=$languages[$current_lang]['btn_back_to_all_events'];?>
        </a>-->
      </section>
        
    </div>
  </div>
  <!-- main-content ends here -->
</div>
<script>
  function initMap() {
    var myLatLng = {lat: <?=$event_map_lat?>, lng: <?=$event_map_lng?>};

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 17,
      center: myLatLng
    });

    var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
//        icon: 'http://www.procad-bg.com/site/images/apple-touch-icon.png',
      title: '<?=$event_name;?>'
    });
//      showInfoWindow(marker);
  }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBRtep5nzeJd-3HwTOnJiIBnY5BQfKagw&callback=initMap"></script>
<?php
  print_html_footer();
?>
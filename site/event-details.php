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
  
  $body_class = "single single-tribe_events postid-$event_id tribe-filter-live  tribe-events-uses-geolocation events-single tribe-events-style-full tribe-theme-fitnesszone page-template-tpl-events-php singular no-js";
  print_html_header($event_name, $event_summary, "",$additional_script = false, $body_class);
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
<div class="container m-bot-15 clearfix">
  <div class="sixteen columns">
    <div class="page-title-container clearfix">
      <h1 class="page-title">
        <a href="/<?=$home_page_url;?>"><?=$languages[$current_lang]['header_home'];?></a> 
        /<a href="/<?= $current_lang; ?>/<?= $content_pretty_url; ?>"> <?=$languages[$current_lang]['menu_event'];?></a> 
        <span class="sub-title">/ <?= $event_name; ?></span>
      </h1>
    </div>	
  </div>
</div>

  <div id="main">
    <!-- main-content starts here -->
    <div id="main-content">
      <div class="container">
        <div class="dt-sc-hr-invisible"></div>
        <section id="primary" class="content-full-width">
<!--          <article id="post-<?=$event_id;?>" class="post-<?=$event_id;?> page type-page status-draft hentry">
            <div id="tribe-events" class="tribe-no-js" data-live_ajax="1" data-datepicker_format="0" data-category="">
              <div class="tribe-events-before-html"></div>
              <div id="tribe-events-content" class="tribe-events-single vevent hentry">
                <p class="tribe-events-back">
                  <a href="/bg/събития"> &laquo; <?=$languages[$current_lang]['btn_back_to_all_events'];?></a>
                </p>
                 Notices 
                 Event featured image, but exclude link 
                <div class="tribe-events-event-image">
                  <img src="<?=$event_image;?>" class="attachment-full wp-post-image" alt="<?=$event_name;?>" title="<?=$event_name;?>">
                  <h2 class="border-title"><span><?=$event_name;?></span></h2>
                  <div class="tribe-events-schedule updated published tribe-clearfix">
                    <h3>
                      <span class="date-start dtstart">February 28, 2016 @ 8:00 am<span class="value-title" title="2016-02-28UTC08:00"></span></span> 
                        - 
                      <span class="date-end dtend">March 1, 2016 @ 5:00 pm<span class="value-title" title="2016-03-01UTC05:00"></span></span>
                    </h3><span class="tribe-events-divider"> | </span><span class="tribe-events-cost"><?=$event_cost;?>лв.</span>
                  </div>
                <div id="post-246" class="post-246 tribe_events type-tribe_events status-publish has-post-thumbnail tag-valley tribe_events_cat-destiny cat_destiny">
                   Event content 
                  <div class="tribe-events-single-event-description tribe-events-content entry-content description">
                    <?=$event_text;?>
                  </div>
                   .tribe-events-single-event-description 
                  <div class="tribe-events-cal-links"><a class="tribe-events-gcal tribe-events-button" href="http://www.google.com/calendar/event?action=TEMPLATE&#038;text=Avalanche+Trip&#038;dates=20160228T080000/20160301T170000&#038;details=Maecenas+convallis+quam+eget+urna+imperdiet%2C+eget+gravida+erat+rutrum.+Pellentesque+rhoncus+odio+et+lorem+pharetra+dictum.+Nam+blandit+metus+diam%2C+ut+maximus+arcu+commodo+in.+Integer+auctor+sapien+enim%2C+viverra+vulputate+orci+posuere+eu.+Nulla+pretium+elit+orci%2C+eu+facilisis+ligula+hendrerit+ut.+Suspendisse+malesuada+eros+ut+euismod+euismod.+Fusce+vestibulum+ornare+augue%2C+eu+mollis+orci+volutpat+non.+Pellentesque+habitant+morbi+tristique+senectus+et+netus+et+malesuada+fames+ac+turpis+egestas.+Nulla+leo+lorem%2C+bibendum+et+sapien+vel%2C+venenatis+ultrices+libero.+%0ASuspendisse+arcu+odio%2C+elementum+et+nisl+nec%2C+maximus+consectetur+purus.+Maecenas+lacus+magna%2C+malesuada+et+gravida+sit+amet%2C+rutrum+non+nisl.+Sed+non+suscipit+nunc%2C+ac+varius+sapien.+Vestibulum+euismod+vehicula+mauris.+Ut+et+faucibus+dui.+Cras+ornare+nulla+eget+massa+semper%2C+a+viverra+lectus+aliquam.+Mauris+sollicitudin+turpis+eget+est+eleifend%2C+et+pretium+nunc+feugiat.+Aenean+tristique+velit+magna.+Curabitur+a+turpis+orci.++%28View+Full+Event+Description+Here%3A+http%3A%2F%2Fwedesignthemes.com%2Fthemes%2Ffitness-zone%2Fevent%2Favalanche-trip%2F%29&#038;location=Avalanche%2C+Ooty%2C+87455%2C+India&#038;sprop=website:http://wedesignthemes.com/themes/fitness-zone&#038;trp=false" title="Add to Google Calendar">+ Google Calendar</a><a class="tribe-events-ical tribe-events-button" href="http://wedesignthemes.com/themes/fitness-zone/events/?ical=1" title="Download .ics file" >+ iCal Export</a></div> .tribe-events-cal-links 
                   Event meta 
                  <div class="tribe-events-single-section tribe-events-event-meta primary tribe-clearfix">
                    <div class="tribe-events-meta-group tribe-events-meta-group-details">
                      <h3 class="tribe-events-single-section-title"> Details </h3>
                      <dl>
                        <dt> Start: </dt>
                        <dd>
                          <abbr class="tribe-events-abbr updated published dtstart" title="2016-02-28"> February 28, 2016 @ 8:00 am </abbr>
                        </dd>
                        <dt> End: </dt>
                        <dd>
                          <abbr class="tribe-events-abbr dtend" title="2016-03-01"> March 1, 2016 @ 5:00 pm </abbr>
                        </dd>
                        <dt> Cost: </dt>
                        <dd class="tribe-events-event-cost"> $30 </dd>
                        <dt>Event Category:</dt> <dd class="tribe-events-event-categories"><a href="http://wedesignthemes.com/themes/fitness-zone/events/category/destiny/" rel="tag">destiny</a></dd>
                        <dt>Event Tags:</dt><dd class="tribe-event-tags"><a href="http://wedesignthemes.com/themes/fitness-zone/tag/valley/" rel="tag">valley</a></dd>
                        <dt> Website: </dt>
                        <dd class="tribe-events-event-url"> <a href="http://iamdesigning.com" target="self">http://iamdesigning.com</a> </dd>
                      </dl>
                    </div>
                    <div class="tribe-events-meta-group tribe-events-meta-group-organizer">
                      <h3 class="tribe-events-single-section-title">Organizer</h3>
                      <dl>
                        <dd class="fn org">
                          <a href="http://wedesignthemes.com/themes/fitness-zone/organizer/ram/">Ram</a>
                        </dd>
                        <dt>Phone:</dt>
                        <dd class="tel">125478963</dd>
                        <dt>Email:</dt>
                        <dd class="email">http://iamdesigning.com</dd>
                        <dt>Website:</dt>
                        <dd class="url">
                          <a href="http://iamdesigning.com" target="self">http://iamdesigning.com</a>
                        </dd>
                      </dl>
                    </div>
                  </div>
                  <div class="tribe-events-single-section tribe-events-event-meta secondary tribe-clearfix">
                    <div class="tribe-events-meta-group tribe-events-meta-group-venue">
                      <h3 class="tribe-events-single-section-title"> Venue </h3>
                      <dl>
                        <dd class="author fn org"> <a href="http://wedesignthemes.com/themes/fitness-zone/venue/resort/">Resort</a> </dd>
                        <dd class="location">
                          <address class="tribe-events-address">
                            <span class="adr">
                              <span class="street-address">Avalanche</span>
                              <br>
                              <span class="locality">Ooty</span><span class="delimiter">,</span>
                              <span class="postal-code">87455</span>
                              <span class="country-name">India</span>
                            </span>
                            <a class="tribe-events-gmap" href="http://maps.google.com/maps?f=q&#038;source=s_q&#038;hl=en&#038;geocode=&#038;q=Avalanche+Ooty+87455+India" title="Click to view a Google Map" target="_blank">+ Google Map</a>									</address>
                        </dd>
                        <dt> Phone: </dt>
                        <dd class="tel"> 459876213 </dd>
                        <dt> Website: </dt>
                        <dd class="url"> <a href="http://iamdesigning.com" target="self">http://iamdesigning.com</a> </dd>
                      </dl>
                    </div>
                    <div class="tribe-events-venue-map">
                      <div id="tribe-events-gmap-0" style="height: 350px; width: 100%"></div> #tribe-events-gmap-0 
                    </div>
                  </div>
                  <div class="dt-sc-hr-invisible-small"></div>
                </div>  #post-x 
               Event footer 
              <div id="tribe-events-footer">
                 Navigation 
                <h3 class="tribe-events-visuallyhidden">Event Navigation</h3>
                <ul class="tribe-events-sub-nav">
                  <li class="tribe-events-nav-previous"></li>
                  <li class="tribe-events-nav-next"><a href="http://wedesignthemes.com/themes/fitness-zone/event/yoga-in-office/">Yoga in office <span>&raquo;</span></a></li>
                </ul>
                 .tribe-events-sub-nav 
              </div>
               #tribe-events-footer 
              </div> #tribe-events-content 
              <div class="tribe-events-after-html"></div>
            </div> #tribe-events 
          </article>-->
          <article id="post-<?=$event_id;?>" class="post-<?=$event_id;?> page type-page status-draft hentry">
              <div id="tribe-events" class="tribe-no-js" data-live_ajax="1" data-datepicker_format="0" data-category="">
                  <div class="entry-thumb">
                      <a href="#"><img src="<?=$event_image;?>" alt="<?=$event_name;?>" title="<?=$event_name;?>"></a>
<!--                      <div class="entry-meta">
                          <div class="date">
                              <span><?=$event_date_day;?></span>
                              <?=$event_date_month;?><br>
                              <?=$event_date_year;?>
                          </div>
                      </div>-->
                  </div>
                  <div class="entry-metadata">
<!--                    <div class="post-meta">
                        <p class="author"><a href="#"> <span class="fa fa-user"> </span> ram</a></p>
                        <p><a title="" href="#"><span class="fa fa-comment"> </span>0</a></p>
                    </div>
                    <p class="tags"><a href="#">Workout </a> / <a href="#"> Diet</a></p>-->
                    <h2 class="border-title"><span><?=$event_name;?></span></h2>

                    <p><?=$event_text;?></p>
                  </div>
              </div>
          </article>
          <div id="map"></div>
          <a href="<?=$current_event_page;?>">
            &laquo; <?=$languages[$current_lang]['btn_back_to_all_events'];?>
          </a>
        </section>
        <div class="dt-sc-hr-invisible-large"></div>
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
        icon: 'http://www.procad-bg.com/site/images/apple-touch-icon.png',
        title: '<?=$event_name;?>'
      });
//      showInfoWindow(marker);
    }
  </script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhmGbiJIya3wRteCUJR0EL5a4E1ayAPPY&callback=initMap"></script>
  
<?php
  print_html_footer();
?>
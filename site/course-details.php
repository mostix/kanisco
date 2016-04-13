<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  if (isset($_GET['coid'])) {
    $current_course_id = $_GET['coid']; // current selected course
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
    $current_cd_name = str_replace("-", " ", $current_category_path[$count_category_path_elements]);
  }

  $query_current_params = "SELECT `languages`.`language_id`,`languages`.`language_root_content_id`,`contents`.`content_hierarchy_path`
                          FROM `languages` 
                          INNER JOIN `contents` ON `contents`.`content_id` = `languages`.`language_default_content_id`
                          WHERE `language_code` = '$current_lang'";
  //echo $query_current_params;exit;
  $result_current_params = mysqli_query($db_link, $query_current_params);
  if (!$result_current_params)
    echo mysqli_error($db_link);
  if (mysqli_num_rows($result_current_params) > 0) {
    $row_current_params = mysqli_fetch_assoc($result_current_params);
    $current_language_id = $row_current_params['language_id'];
    $language_root_content_id = $row_current_params['language_root_content_id'];
    $home_page_url = $row_current_params['content_hierarchy_path'];
  }

  $content_type_id = 9; // courses
  $query_content = "SELECT `content_name`,`content_hierarchy_ids`,`content_show_newsletter`,`content_show_clients`,`content_attribute_2`,`content_pretty_url`
                    FROM `contents`
                    WHERE `content_type_id` = '$content_type_id' AND `content_parent_id` = '$language_root_content_id'";
  //echo $query_content."<br><br>";
  $result_content = mysqli_query($db_link, $query_content);
  if (!$result_content) echo mysqli_error($db_link);
  if (mysqli_num_rows($result_content) > 0) {
    $content_array = mysqli_fetch_assoc($result_content);
    $content_name = $content_array['content_name'];
    $content_hierarchy_ids = $content_array['content_hierarchy_ids'];
    $content_show_newsletter = $content_array['content_show_newsletter'];
    $content_show_clients = $content_array['content_show_clients'];
    $courses_ads_banner = $content_array['content_attribute_2'];
    $content_pretty_url = $content_array['content_pretty_url'];
  }

  $query_course = "SELECT `courses`.`course_image`,`courses`.`course_date`,`courses_descriptions`.`cd_name`,
                            `courses_descriptions`.`cd_description`,`courses_descriptions`.`cd_program`
                      FROM `courses`
                      INNER JOIN `courses_descriptions` USING(`course_id`)
                      WHERE `courses`.`course_id` = '$current_course_id' AND `courses_descriptions`.`language_id` = '$current_language_id'";
  //echo $query_course;
  $result_course = mysqli_query($db_link, $query_course);
  if (!$result_course) echo mysqli_error($db_link);
  if (mysqli_num_rows($result_course) > 0) {
    $course_row = mysqli_fetch_assoc($result_course);
    //echo"<pre>";print_r($course_row);

    $pd_images_folder = "/site/images/courses/";
    $course_image = $pd_images_folder . $course_row['course_image'];
    $course_date = $course_row['course_date'];
    $current_date = date("Y-m-d");
    $cd_name = stripslashes($course_row['cd_name']);
    $cd_description = stripslashes($course_row['cd_description']);
    $cd_program = stripslashes($course_row['cd_program']);
  }

  $sign_up_now = false;
  $course_available_for_sign_up = false;
  if (isset($_GET['scrb'])) {
    $sign_up_now = true; // sign_up for course
  }
  else {
    if($course_date > $current_date) {
      $course_available_for_sign_up = true; // sign_up for course
    }
  }
  
  print_html_header($cd_name, $cd_description, $pd_meta_keywords = "", $additional_script = false, $body_class = "courses");
  //echo "<pre>";print_r($_SERVER);
  //echo "<pre>";print_r($_SESSION);
?>
  <div class="container m-bot-15 clearfix">
    <div class="sixteen columns">
      <div class="page-title-container clearfix">
        <h1 class="page-title">
          <a href="/<?= $home_page_url; ?>"><?= $languages[$current_lang]['header_home']; ?></a> 
          /<a href="/<?= $current_lang; ?>/<?= $content_pretty_url; ?>"> <?= $content_name; ?></a> 
          <span class="sub-title">/ <?= $cd_name; ?></span>
        </h1>
      </div>	
    </div>
  </div>
  <!-- CONTENT -->
  <div class="container clearfix">
    <div class="twelve columns">
      <!-- PORTFOLIO ITEM -->
      <div class="blog-item m-bot-25">
        <div class="content-container-white">
          <div class="view view-first">
            <img src="<?= $course_image; ?>" alt="<?= $cd_name ?>" >
          </div>
          <div class="contant-container-caption">
            <a class="a-invert" href="javascript:;"><?= ($sign_up_now) ? $languages[$current_lang]['header_course_sign_up'] : $languages[$current_lang]['header_course_program']; ?></a>
          </div>
        </div>
        <div class="under-box-bg"></div>

        <div id="cd_program" class="content-container-white blog-text-container details_box">
          <p><?= $cd_program; ?></p>
        </div>
        <div id="cd_sign_up" class="details_box">
          <p class="styled-box iconed-box success hidden"><?= $languages[$current_lang]['text_course_sign_up_send_successfully']; ?></p>
          <form action="/site/course-sign-up.php" id="course-sign-up-form" method="post" class="clearfix">			
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_customer_firstname']; ?></label>
              <input type="hidden" name="course_id" value="<?= $current_course_id; ?>">
              <input type="hidden" name="cd_name" value="<?= $cd_name; ?>">
              <input type="hidden" name="current_lang" value="<?= $current_lang; ?>">
              <input type="text" name="firstname" placeholder="<?= $languages[$current_lang]['header_customer_firstname']; ?>..." class="text requiredField m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_customer_lastname']; ?></label>	
              <input type="text" name="lastname" placeholder="<?= $languages[$current_lang]['header_customer_lastname']; ?>..." class="text requiredField m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_company']; ?></label>	
              <input type="text" name="company" placeholder="<?= $languages[$current_lang]['header_company']; ?>..."  class="text m-bot-20" >
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_customer_email']; ?></label>	
              <input type="text" name="email" id="myemail" placeholder="<?= $languages[$current_lang]['header_customer_email']; ?>..."  class="text requiredField email m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
              <div class="styled-box iconed-box invalid_email hidden"><?= $languages[$current_lang]['error_create_customer_email_not_valid']; ?></div>
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_customer_phone']; ?></label>	
              <input type="text" name="phone" id="myphone" placeholder="<?= $languages[$current_lang]['header_customer_phone']; ?>..." class="text requiredField m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['header_participants_count']; ?></label>	
              <input type="text" name="participants_count" placeholder="<?= $languages[$current_lang]['header_participants_count']; ?>..." class="text requiredField m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="left">
              <input name="submit_course_sign_up" id="submit_course_sign_up" value="<?=$languages[$current_lang]['btn_newslettter'];?>" class="button gray medium" type="submit" >
            </fieldset>
          </form>
        </div>
        <div id="cd_make_inquiery" class="details_box">
          <p class="styled-box iconed-box success hidden"><?= $languages[$current_lang]['text_course_inquiery_send_successfully']; ?></p>
          <form action="/site/course-inquiery.php" id="course-inquiery-form" method="post" class="clearfix">			
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['text_enter_name']; ?></label>
              <input type="hidden" name="cd_name" value="<?= $cd_name; ?>">
              <input type="hidden" name="current_lang" value="<?= $current_lang; ?>">
              <input type="text" name="name" id="myname" placeholder="<?= $languages[$current_lang]['text_enter_name']; ?>..." class="text requiredField m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['text_enter_phone']; ?></label>	
              <input type="text" name="phone" id="myphone"  placeholder="<?= $languages[$current_lang]['text_enter_phone']; ?>..." class="text requiredField subject m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>	
            <fieldset class="field-1-3 left">
              <label><?= $languages[$current_lang]['text_enter_email']; ?></label>	
              <input type="text" name="email" id="myemail" placeholder="<?= $languages[$current_lang]['text_enter_email']; ?>..."  class="text requiredField email m-bot-20" >
              <div class="styled-box iconed-box error hidden"><?= $languages[$current_lang]['required_field_error']; ?></div>
              <div class="styled-box iconed-box invalid_email hidden"><?= $languages[$current_lang]['error_create_customer_email_not_valid']; ?></div>
            </fieldset>
            <fieldset class="field-1-1 left">
              <label><?= $languages[$current_lang]['text_enter_inquiry']; ?></label>
              <textarea name="message" id="mymessage" rows="5" cols="30" class="text requiredField m-bot-15" placeholder="<?= $languages[$current_lang]['text_enter_inquiry']; ?>..."></textarea>
              <div class="styled-box iconed-box error hidden" style="width: 100%;"><?= $languages[$current_lang]['required_field_error']; ?></div>
            </fieldset>
            <fieldset class="left">
              <input name="submit_course_inquiery" id="submit_course_inquiery" value="<?= $languages[$current_lang]['btn_submit_inquiry']; ?>" class="button gray medium" type="submit" >
            </fieldset>
          </form>
        </div>
      </div>
    </div>
    <!-- SIDEBAR -->
    <div class="four columns ">
      <!-- WIDGET -->	
      <div class="sidebar-item m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"><?= $languages[$current_lang]['header_short_description']; ?></h3>
        </div>

        <div class="under-box-bg"></div>

        <div class="content-container-white padding-all-15">
          <p class="no_margin"><?= $cd_description; ?></p>
        </div>
      </div>

      <!-- WIDGET -->
      <div class="sidebar-item  m-bot-25">
        <div class="content-container-white">
          <h3 class="title-widget"><?= $languages[$current_lang]['header_course_details']; ?></h3>
        </div>
        <div class="under-box-bg"></div>
        <div class="content-container-white padding-l-r-15">
          <ul class="course-details">
            <li>
              <a href="#cd_program" data-caption="<?= $languages[$current_lang]['header_course_program']; ?>">
                <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_course_program']; ?>
              </a>
            </li>
<?php
          if($sign_up_now || $course_available_for_sign_up) {
?>
            <li>
              <a href="#cd_sign_up" data-caption="<?= $languages[$current_lang]['header_course_sign_up']; ?>">
                <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_course_sign_up']; ?>
              </a>
            </li>
<?php
          }
?>
            <li>
              <a href="#cd_make_inquiery" data-caption="<?= $languages[$current_lang]['header_course_make_inquiery']; ?>">
                <span class="port-cat-icon"></span><?= $languages[$current_lang]['header_course_make_inquiery']; ?>
              </a>
            </li>
          </ul>
        </div>
      </div>	

      <!-- WIDGET -->	
      <div class="sidebar-item  m-bot-25">
        <div class="center-text">
<?php
          if (!empty($course_trial_url)) {
?>
            <a class="button large" style="width: 100%;" target="_blank" href="<?= $course_trial_url; ?>"><?= $languages[$current_lang]['btn_course_trial_version']; ?></a>
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
  <?php if (!empty($courses_ads_banner)) echo "<img src='$courses_ads_banner' alt=''>" ?>
        </div>

      </div>

    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function () {
      // tab switcher
<?php
    if($sign_up_now) {
?>
      $(".course-details li").removeClass("active");
      $(".course-details li:nth-child(2)").addClass("active");
      $(".twelve.columns .details_box").hide();
      $("#cd_sign_up").show();
      console.log($(".course-details li.acitve a").attr("href"));
<?php
    }
    else {
?>
      $(".course-details li").removeClass("active");
      $(".course-details li:first").addClass("active");
      $(".twelve.columns .details_box").hide();
      $(".twelve.columns .details_box:first").show();
<?php
    }
?>
      $(".course-details a").click(function (event) {
        var this_link = $(this);
        var clicked_tab = this_link.attr("href");
        var caption = this_link.attr("data-caption");
        $(".course-details li").removeClass("active");
        this_link.parent().addClass("active");
        $(".details_box").hide();
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
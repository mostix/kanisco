<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="shortcut icon" href="assets/ico/favicon.png">
<title>ART 93 Online Store<?php echo $current_page; ?></title>
<link href="assets/css/bootstrap.min.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,300,400,700,800&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<link rel="stylesheet" href="assets/css/carousel.css">
<link rel="stylesheet" href="assets/css/sass-compiled.css" />
<link rel="stylesheet" href="assets/css/label.css">
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/functions.js"></script>
<!--Sortable-->
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<!--Sortable--> 
<style>
html * {
  font-family: 'Open Sans', sans-serif !important;
}
.container-body {
  padding-top: 102px;
}
.ui-state-default {
  float:left;
  padding:5px;
  vertical-align: bottom !important;
}
.ui-state-default img {
  vertical-align: bottom !important;
}
h2.green {
  margin-bottom: 20px;
  text-align: center;
  color: green;
  font-size: 20px;
}
a {
  cursor: pointer;
}
.tabs {
  display: block;
  float: left;
  padding: 10px 16px;
  font-size: 18px;
  line-height: 1.33;
  color: #FFF;
  background-color: #428BCA;
  border-right: 1px solid #3972A3;
  border-left: 1px solid #4B98DB;
}
.tabs.active {
  background-color: #88C4F7;
}
</style>
</head>

<body>
<div class=" eterra-nav" role="navigation">
  <ul class="nav nav-pills pull-right">
    <li><a href="#">Вход</a></li>
    <li><a href="#">Профил</a></li>
    <li><a href="#"><img src="assets/ico/cart.png" style="padding:2px; width:30px; height:20px;"></a></li>
    <li></li>
  </ul>
</div>
<div class="container-body">
  <div class="navbar-wrapper">
    <div>
      <div class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <?php /*?>              <li><img src="assets/img/art93-logo.jpg" style="width:25%; height:auto; z-index:1000;"></li>
              <li><img src="assets/img/art93-logo-text.png" style="width:95%; margin-top:5%;"></li>
<?php */?>
              <li class="active"><a href="index.php">НАЧАЛО</a></li>
              <li><a href="#about">НОВИНИ</a></li>
              <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">ПРОДУКТИ<b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li class="dropdown-submenu"><a href="gloves.php">РЪКАВИЦИ</a></li>
                  <li><a href="#">ПОРТФЕЙЛИ</a></li>
                  <li><a href="#">КОЖЕНИ АКСЕСОАРИ</a></li>
                  <li><a href="#">КУТИИ ЗА БИЖУТА</a></li>
                  <li><a href="#">ЧАНТИ</a></li>
                  <li><a href="#">КУФАРИ</a></li>
                  <li><a href="#">КОЛАНИ</a></li>
                  <li><a href="#">ЧАДЪРИ</a></li>
                  <li><a href="#">ВРАТОВРЪЗКИ</a></li>
                  <li><a href="#">ТИРАНТИ</a></li>
                  <li><a href="#">ПРОДУКТИ С ВАШЕТО ЛОГО</a></li>
                  <li><a href="#">ПОДАРЪЧНИ КУТИИ</a></li>
                </ul>
              </li>
              <li><a href="#contact">МАГАЗИНИ</a></li>
              <li><a href="#contact">ЗА НАС</a></li>
              <li><a href="#contact">КОНТАКТ</a></li>
            </ul>
            <ul class="nav nav-pills pull-right">
              <li><a href="#"><img src="assets/img/art93-logo.jpg" style="width:17%; height:auto; z-index:1000; float:right; margin-bottom:5px; padding-top:5px;"></a></li>
              <li><a href="#"><img src="assets/img/art93-logo-text.png" style="width:100%; height:auto; z-index:1000; float:right; padding-top:10px;"></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  function DB_OpenI() {

    $db_name = "puzzle_slider_test";
    $db_user = "wwwUSER";
    $db_password = "684vcJ2T4m63Vbw5";

    $mysqli = new mysqli("localhost", $db_user, $db_password, $db_name);

    /* check connection */
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    /* change character set to utf8 */
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
    } else {
        //printf("Current character set: %s\n", $mysqli->character_set_name());
    }

    return $mysqli;
  }

  function DB_CloseI($link) {
    mysqli_close($link);
  }
  
  $link = DB_OpenI();
  
  if(isset($_POST['submit_puzzle_images'])) {
    $puzzle_image_ids = $_POST['puzzle_image_ids'];
    if(isset($_POST['puzzle_image_slider'])) {
      $current_puzzle_image_slider = $_POST['puzzle_image_slider'];
    }
    //echo"<pre>";print_r($_POST);exit;
    
    mysqli_query($link, "START TRANSACTION");
    
    foreach($puzzle_image_ids as $key => $puzzle_image_id) {
      
      $puzzle_image_order = $key;
      
      $query_update = "UPDATE `puzzle_images` SET `puzzle_image_order` = '$puzzle_image_order' WHERE `puzzle_image_id` = '$puzzle_image_id'";
      //echo $query_update."<br>";
      $result_update = mysqli_query($link, $query_update);
      if(!$result_update) {
        echo "Възникна грешка, моля опитайте отново";
        echo mysqli_error($link);
        mysqli_query($link, "ROLLBACK");
        exit;
      }
    }
    
    mysqli_query($link, "COMMIT");
    $changes_saved_text = "Промените бяха записани успешно";
  }
  
  if(isset($changes_saved_text)) echo "<h2 class='green'>$changes_saved_text</h2>";
  
  $query_image_sliders = "SELECT DISTINCT `puzzle_image_slider` FROM `puzzle_images` ORDER BY `puzzle_image_slider` ASC";
  $result_image_sliders = mysqli_query($link, $query_image_sliders);
  if(mysqli_num_rows($result_image_sliders) > 0) {
    while($puzzle_sliders= mysqli_fetch_assoc($result_image_sliders)) {
      
      $puzzle_image_slider = $puzzle_sliders['puzzle_image_slider'];
      $class_current = "";
      if(isset($current_puzzle_image_slider)) {
        if($current_puzzle_image_slider == $puzzle_image_slider) {
          $class_current = " active";
        }
      }
      else {
        if($puzzle_image_slider == 1) {
          $class_current = " active";
          $current_puzzle_image_slider = 1;
        }
      }
      
      echo "<a data-id='$puzzle_image_slider' class='tabs$class_current'>Слайдер $puzzle_image_slider</a>";
    }
  }
?>
    <div style="clear:left;"></div>
    
    <!-- Puzzel-like Slider -->
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
      <ul id="sortable" style="width: 1170px;height: 400px;background: #88C4F7;">
<?php
  $query = "SELECT `puzzle_images`.* FROM `puzzle_images` WHERE `puzzle_image_slider` = '1' ORDER BY `puzzle_image_order` ASC";
  $result = mysqli_query($link, $query);
  if(mysqli_num_rows($result) > 0) {
    while($puzzle_images = mysqli_fetch_assoc($result)) {

      $puzzle_image_id = $puzzle_images['puzzle_image_id'];
      $puzzle_image_slider = $puzzle_images['puzzle_image_slider'];
      $puzzle_image_dir = stripslashes($puzzle_images['puzzle_image_dir']);
      $puzzle_image_name = stripslashes($puzzle_images['puzzle_image_name']);

?>
      <li class="ui-state-default">
        <img class="puzzle_image" src="<?php echo "$puzzle_image_dir$puzzle_image_name";?>" alt="Generic placeholder image">
        <input type="hidden" name="puzzle_image_ids[]" value="<?php echo $puzzle_image_id;?>">
      </li>
<?php
    }
  }
  DB_CloseI($link);
?>
      </ul>
      <div style="clear:left;">&nbsp;</div>
      <input type="submit" name="submit_puzzle_images" class="btn btn-lg btn-primary button outline-outward btn-art-lg" value="Запази">
      <input type="hidden" name="puzzle_image_slider" value="<?php echo $current_puzzle_image_slider;?>">
    </form>
  </div>
  <div style="clear:left;"></div>
  <!-- Puzzel-like Slider -->
  
  <div class="container marketing"> 
    <hr class="featurette-divider">
    <footer>
      <div id="topcontrol" title="Scroll Back to Top" style="position: fixed; bottom: 5px; right: 5px; opacity: 1; cursor: pointer;">
        <img src="assets/img/up-2.png" style="width:51px; height:42px">
      </div>
      <img src="assets/img/footer.png"  style="margin-left:6%;" >
      <p></p>
      <p style="float:right;">&copy; 2014 Art 93, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
    </footer>
  </div><!--<div class="container marketing">-->
  </div><!-- <!--<div class="container body">-->
  
  <!-- JavaScript -->
  <script src="assets/js/functions.js"></script>
  <script type="text/javascript">
    //start slider
    $( document ).ready(function() {
      $( "#sortable" ).sortable({placeholder: "ui-state-highlight"});
      $( "#sortable" ).sortable({ opacity: 0.6 });
      $( "#sortable" ).disableSelection();
      
      $(".tabs").click(function() {
        $(".tabs").removeClass("active");
        $(this).addClass("active");
        GetPuzzelSliderImages();
      });
    });
    //end slider
  </script>
  </div>
</body>
</html>

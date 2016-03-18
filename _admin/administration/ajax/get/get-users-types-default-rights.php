<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf_in_reports();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['user_type_id'])) {
    $user_type_id = $_POST['user_type_id'];
  }

  list_users_type_default_rights($menu_parent_id = 0, $user_type_id);
?>
  <script>
    $(function() {
      $(".access_rights").click(function() {
          var user_id = $(this).attr("button-id");
          if($(".details"+user_id).hasClass("access_rights_edit")) {
            $(".users_details").removeClass("access_rights_edit");
          } else {
            $(".users_details").removeClass("access_rights_edit");
            $(".details"+user_id).addClass("access_rights_edit");
          }
        });
        $(".menu_header").click(function() {
          if($(this).hasClass("active_header")) {
            var header_id = $(this).attr("button-id");
            $(this).html("+");
            $(this).removeClass("active_header")
            $(".children"+header_id).hide();
          }
          else {
            $(".menu_header").removeClass("active_header");
            $(this).addClass("active_header");
            $(this).html("-");
            var header_id = $(this).attr("button-id");
            $(".children").hide();
            $(".children"+header_id).show();
          }
        });
    });
  </script>
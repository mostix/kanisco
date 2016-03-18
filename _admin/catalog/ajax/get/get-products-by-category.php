<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['category_ids_list'])) {
    $category_ids_list =  $_POST['category_ids_list'];
  }
  if(isset($_POST['category_id'])) {
    $category_id =  $_POST['category_id'];
  }
  if(isset($_POST['category_name'])) {
    $category_name =  $_POST['category_name'];
  }
  if(isset($_POST['category_hierarchy_level'])) {
    $category_hierarchy_level =  $_POST['category_hierarchy_level']+1;
  }
  
  if(!empty($category_id)) {

    list_products($category_ids_list,$category_id, $first_iteration = true);

  }
?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".row_over *").click(function() {
        $(".row_over").removeClass("row_over_edit");
        $(this).closest(".row_over").addClass("row_over_edit");
      });
      $("tr.even,tr.odd").mouseenter(function() {
        var me = $(this);
        me.addClass("hover");
      });
      $("tr.even,tr.odd").mouseleave(function() {
        var me = $(this);
        me.removeClass("hover");
      });
      $(".js_pagination a").bind('click', function() {
        var pag_id = $(this).attr("data");
        if(pag_id == "" || pag_id == undefined) return;
        var page_count = $("#products_list .page_count").val();
        var prev_page = "";
        var next_page = "";
        if(pag_id == "1") {
          $(".js_pagination .btn_prev_page").addClass("disabled");
          $(".js_pagination .btn_prev_page a").attr("data","");
          $(".js_pagination .btn_next_page").removeClass("disabled");
          $(".js_pagination .btn_next_page a").attr("data","2");
        }
        else if(pag_id == page_count) {
          prev_page = parseInt(pag_id)-1;
          $(".js_pagination .btn_prev_page").removeClass("disabled");
          $(".js_pagination .btn_prev_page a").attr("data",prev_page);
          $(".js_pagination .btn_next_page").addClass("disabled");
          $(".js_pagination .btn_next_page a").attr("data","");
        }
        else {
          prev_page = parseInt(pag_id)-1;
          next_page = parseInt(pag_id)+1;
          $(".js_pagination .btn_prev_page").removeClass("disabled");
          $(".js_pagination .btn_prev_page a").attr("data",prev_page);
          $(".js_pagination .btn_next_page").removeClass("disabled");
          $(".js_pagination .btn_next_page a").attr("data",next_page);
        }
        if($(this).parent().hasClass("active")) {
          // do nothing
        }
        else {
          $(".js_pagination li").removeClass("active");
          $(".js_pagination #pag_"+pag_id).addClass("active");
          $("#products_list table").hide();
          $("#products_list table.row_"+pag_id).show();
        }
        event.preventDefault();
      });
    });
  </script>

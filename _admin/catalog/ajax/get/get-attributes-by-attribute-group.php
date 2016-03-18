<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['attribute_group_id'])) {
    $attribute_group_id =  $_POST['attribute_group_id'];
  }
  
  if(!empty($attribute_group_id)) {

  list_attributes_by_attribute_group($attribute_group_id);

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
    });
  </script>
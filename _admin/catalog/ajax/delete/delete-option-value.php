<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['option_value_id'])) {
    $option_value_id = $_POST['option_value_id'];
  }
  if(isset($_POST['step'])) {
    $step = $_POST['step'];
  }
  
  /*
   * we gonna use a variable step, wich if equals to first
   * we gonna check if some products have this option value and
   * if so tell the user and then if is sure to delete it (second step)
   * delete the option value with it's records for the products
   */
  
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  
  if($step == "first") {
    $query_pov = "SELECT `product_option_value_id` FROM `product_option_value` WHERE `option_value_id` = '$option_value_id'";
    //echo $query_pov;
    $result_pov = mysqli_query($db_link, $query_pov);
    if(!$result_pov) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_pov) > 0) {
  ?>
    <div style="display:none;" id="modal_confirm_delete_option_value" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;">
        <?=$languages[$current_lang]['warning_option_value_is_assigned_to_products'];?>
      </p>
    </div>
    <input type="hidden" name="current_option_value_id" class="delete_option_value_link active" data-id="<?=$option_value_id;?>" >
    <script>
    $(function() {
      $("#modal_confirm_delete_option_value").dialog({
        resizable: false,
        width: 400,
        height: 200,
        autoOpen: false,
        modal: true,
        draggable: false,
        closeOnEscape: true,
        dialogClass: "modal_confirm",
        buttons: {
          "<?=$languages[$current_lang]['btn_delete'];?>": function() {
            DeleteOptionValue('second');
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(".delete_option_value_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $("#modal_confirm_delete_option_value").dialog("open");
    });
    </script>
  <?php
      exit;
    }
  }
  else {
    $query = "DELETE FROM `product_option_value` WHERE `option_value_id` = '$option_value_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  $query = "DELETE FROM `option_value` WHERE `option_value_id` = '$option_value_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_value_description` WHERE `option_value_id` = '$option_value_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
?>
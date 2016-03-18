<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['option_id'])) {
    $option_id = $_POST['option_id'];
  }
  if(isset($_POST['step'])) {
    $step = $_POST['step'];
  }
  
  /*
   * we gonna use a variable step, wich if equals to first
   * we gonna check if some products have this option and
   * if so tell the user and then if is sure to delete it (second step)
   * delete the option with it's values and records for the products
   */
 
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  
  if($step == "first") {
    $query_product_option = "SELECT `product_option_id` FROM `product_option` WHERE `option_id` = '$option_id'";
    //echo $query_product_option;
    $result_product_option = mysqli_query($db_link, $query_product_option);
    if(!$result_product_option) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_product_option) > 0) {
  ?>
    <div style="display:none;" id="modal_confirm_delete_option" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;">
        <?=$languages[$current_lang]['warning_option_is_assigned_to_products'];?>
      </p>
    </div>
    <input type="hidden" name="current_option_id" class="delete_option_link active" data-id="<?=$option_id;?>" >
    <script>
    $(function() {
      $("#modal_confirm_delete_option").dialog({
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
            DeleteOption('second');
          },
          "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
            $(".delete_option_link").removeClass("active");
            $(this).dialog("close");
          }
        }
      });
      $("#modal_confirm_delete_option").dialog("open");
    });
    </script>
  <?php
      list_products_options();

      exit;
    }
  }
  else {
    $query = "DELETE FROM `product_option` WHERE `option_id` = '$option_id'";
    $all_queries .= $query."\n<br>";
    //echo $query;exit;
    $result = mysqli_query($db_link, $query);
    if(mysqli_affected_rows($db_link) <= 0) {
      echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
      mysqli_query($db_link,"ROLLBACK");
      exit;
    }
  }
  
  $query = "DELETE FROM `options` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_description` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_to_category` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_value` WHERE `option_id` = '$option_id'";
  $all_queries .= $query."\n<br>";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }
  
  $query = "DELETE FROM `option_value_description` WHERE `option_id` = '$option_id'";
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

  list_products_options();
?>
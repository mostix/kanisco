<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['language_id'])) {
    $language_id = $_POST['language_id'];
  }
  if(isset($_POST['step'])) {
    $step = $_POST['step'];
  }
  
  /*
   * we gonna use a variable step, wich if equals to first
   * we gonna check if the language is in use and has contents(pages)
   * if so tell the user that she needs to delete the contents first
   */
 
  mysqli_query($db_link,"BEGIN");
  $all_queries= "";
  
  if($step == "first") {
    $query_language_content = "SELECT `contents`.`content_id` 
                                FROM `contents` 
                                INNER JOIN `languages` ON `languages`.`language_root_content_id` = `contents`.`content_id`
                                WHERE `languages`.`language_id` = '$language_id'";
    $all_queries .= $query_language_content."\n<br>";
    $result_language_content = mysqli_query($db_link, $query_language_content);
    if(!$result_language_content) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_language_content) > 0) {
      
      $content_id_row = mysqli_fetch_assoc($result_language_content);
      $content_id = $content_id_row['content_id'];
      
      $query_contents_children = "SELECT `content_id` 
                                  FROM `contents` 
                                  WHERE `content_parent_id` = '$content_id'";
      $all_queries .= $query_contents_children."\n<br>";
      $result_contents_children = mysqli_query($db_link, $query_contents_children);
      if(!$result_contents_children) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_contents_children) > 0) {

?>
  <div class="warning" style="margin-top: 10px;">
    <?=$languages[$current_lang]['error_delete_language_content'];?>
    <a href="javascript:;" class="close_btn" onClick="this.parentNode.remove();">
      <span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span>
    </a>
  </div>
<?php
      list_languages();

      exit;
      
      } //if(mysqli_num_rows($result_contents_children) > 0)
      else {
        $query = "DELETE FROM `contents` WHERE `content_id` = '$content_id'";
        $all_queries .= $query."\n<br>";
        //echo $query;exit;
        $result = mysqli_query($db_link, $query);
        if(mysqli_affected_rows($db_link) <= 0) {
          echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
          mysqli_query($db_link,"ROLLBACK");
          exit;
        }
      }
    } //if(mysqli_num_rows($result_language_content) > 0)
  } //if($step == "first")
  
  $query = "DELETE FROM `languages` WHERE `language_id` = '$language_id'";
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
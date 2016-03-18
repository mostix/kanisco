<?php
  
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['news_category_id'])) {
    $news_category_id = $_POST['news_category_id'];
  }
  
  /*
   * we have to check first if the news category contains
   * some news and if so, tell the user to delete them first
   */
  
  $query_news = "SELECT `news`.`news_id` FROM `news` 
                  INNER JOIN `news_categories` ON `news_categories`.`news_category_id` = `news`.`news_category_id`
                  WHERE `news`.`news_category_id` = '$news_category_id' LIMIT 1";
  //echo $query_news;exit;
  $result_news = mysqli_query($db_link, $query_news);
  if(!$result_news) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_news) > 0) {
?>
  <div class="warning" style="margin-top: 10px;">
    <?=$languages[$current_lang]['text_delete_news_first'];?>
    
    <a href="javascript:;" class="close_btn" onClick="this.parentNode.remove();">
      <span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span>
    </a>
  </div>
<?php
    list_news_categories($news_cat_parent_id = 0, $path_number = 0);
    
    exit;
  }
  
  mysqli_query($db_link,"BEGIN");
  
  $query = "DELETE FROM `news_categories` WHERE `news_category_id` = '$news_category_id'";
  $all_queries = $query."\n";
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    exit;
  }
  
  $query = "DELETE FROM `news_cat_desc` WHERE `news_category_id` = '$news_category_id'";
  $all_queries .= $query."\n";
  //echo $query;exit;
  $result = mysqli_query($db_link, $query);
  if(mysqli_affected_rows($db_link) <= 0) {
    echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
    mysqli_query($db_link,"ROLLBACK");
    exit;
  }

  //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
  mysqli_query($db_link,"COMMIT");
  
  list_news_categories($news_cat_parent_id = 0, $path_number = 0);
?>

<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_ajax_request();
  
//  echo "<pre>";print_r($_POST);
  
  if(isset($_POST['category_ids_list'])) {
    $category_ids_list = $_POST['category_ids_list'];
    if(strpos($category_ids_list, ",")) {
      $category_ids_array = explode(",", $category_ids_list);
      $ids_count = count($category_ids_array)-1;
    }
  }
  else {
    $category_ids_array = array();
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
 
?>
    <table class="list_container margin_bottom level_<?=$category_hierarchy_level;?>" level="<?=$category_hierarchy_level;?>">
      <thead>
        <tr>
          <th><?=$languages[$current_lang]['header_choose_subcategory']." ".$category_name;?></th>
        </tr>
      </thead>
      <tbody>
<?php list_categories_for_products($category_ids_array, $parent_id = $category_id, $path_number = 0); ?>
      </tbody>
    </table>
<?php

  }
?>
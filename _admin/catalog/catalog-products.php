<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
  start_page_build_time_measure();
  
  $page_title = $languages[$current_lang]['current_pages_title'];
  $page_description = "";
  
  print_html_admin_header($page_title, $page_description);
  
?>

<!--pages list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['home'];?>"><?=$languages[$current_lang]['home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['products'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['products'];?></h1>
      
      <table id="products_list">
      
<?php
//      $active_parent = NULL;
//      $active_parent_id = NULL;
      $query_content = "SELECT `contents`.`content_id`, `contents_types`.`content_type`, `contents`.`content_name`, `contents`.`content_collapsed`, 
                              `contents`.`content_pretty_url`, `contents`.`content_is_active`, `contents_types`.`content_type`
                        FROM `contents`
                        INNER JOIN `contents_types` ON `contents_types`.`content_type_id` = `contents`.`content_type_id`
                        WHERE `contents`.`content_hierarchy_level` = '1'
                        ORDER BY `content_menu_order` ASC";
      //echo $query;
      $result_content = mysqli_query($db_link, $query_content);
      if(!$result_content) echo mysqli_error($db_link);
      $content_count = mysqli_num_rows($result_content);
      
?>
      </table>
    </div>
  </main>
<!--pages list-->

<?php
 
  print_html_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
  <script type="text/javascript">
    $(document).ready(function() {
//      CKEDITOR.replace('ckeditor');
    });
  </script>
</body>
</html>
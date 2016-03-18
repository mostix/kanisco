<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_POST['cancel'])) {
    header('Location: contents.php');
  }
  if(isset($_POST['revert_changes'])) {
    header('Location: content-reorder.php');
  }
  
  if(isset($_POST['submit'])) {
    
  }
  
  $page_title = $languages[$current_lang]['reorder_pages_title'];
  $page_description = "";
  $additional_script = '<script src="../js/jquery.mjs.nestedSortable.js" type="text/javascript"></script>';
  
  print_html_admin_header($page_title, $page_description, $additional_script);
  
?>

<!--contents list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <a href="/_admin/contents.php" title="<?=$languages[$current_lang]['title_breadcrumbs_pages'];?>"><?=$languages[$current_lang]['header_pages'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_reorder_pages'];?>
      </section>
      
      <h1 id="pagetitle"><?=$languages[$current_lang]['header_reorder_pages'];?></h1>
      
      <form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" onClick="ReorderContent()" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages[$current_lang]['btn_revert_changes'];?></button>
        </div>
        <div class="clearfix"></div>

        <div id="reorder_pages">
<?php
          list_contents_for_reorder($parent_id = 0, $path_number = 0);
?>
        </div>

        <div class="reorder_pages_buttons">
          <button type="submit" name="submit" onClick="ReorderContent()" class="button green"><i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?></button>
          <button type="submit" name="cancel" class="button blue"><i class="icon icon_cancel_sign"></i><?=$languages[$current_lang]['btn_cancel'];?></button>
          <button type="submit" name="revert_changes" class="button red"><?=$languages[$current_lang]['btn_revert_changes'];?></button>
        </div>
        <div class="clearfix"></div>
      </form>
    </div>
  </main>
<!--contents list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
<script type="text/javascript">
  $(document).ready(function() {
    
    $('ul.sortable').nestedSortable({	
      disableNesting: 'no-nest',
      forcePlaceholderSize: true,
      handle: 'div',
      items: 'li',
      opacity: .6,
      placeholder: 'placeholder',
      startCollapsed : true,
      tabSize: 25,
      tolerance: 'pointer',
      listType: 'ul',
      toleranceElement: '> div'
    });
    $('.disclose').on('click', function() {
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
        $(this).toggleClass('ui-icon-plusthick').toggleClass('ui-icon-minusthick');
    });

  });
</script>
</body>
</html>
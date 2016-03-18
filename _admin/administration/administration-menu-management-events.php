<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['menu_events'];
  $page_description = "E-shop администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_events'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
<?php
    // we use $first_iteration in list_menu function as a global variable
    $first_iteration = true;
    $menu_id = 66; 
    $main_path_number = 0;
    do_menu_management_page($first_iteration,$menu_id,$main_path_number);
?>
    </div>
  </main>
<!--main-->
<?php 
    print_html_admin_footer();
?>
</body>
</html>
  
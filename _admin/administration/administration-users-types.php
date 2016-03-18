<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['text_users_types'];
  $page_description = $languages[$current_lang]['e_shop_cms']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['text_users_types'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
      
      <section class="contents_options">
        <a class="pageoptions" href="/_admin/administration/administration-users-types-add-new.php">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['text_add_new_users_type'];?>" />
          <?=$languages[$current_lang]['text_add_new_users_type'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="60%" class="text_left"><?=$languages[$current_lang]['header_name'];?></th>
            <th width="20%"><?=$languages[$current_lang]['header_reorder'];?></th>
            <th width="20%" colspan="2"><?=$languages[$current_lang]['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div id="users_types_list" class="list_container">
<?php
        list_users_types($current_language_id);
?>
      </div>
    </div>
  </main>
<?php 
    print_html_admin_footer();
?>
</body>
</html>
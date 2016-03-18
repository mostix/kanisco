<?php
  
  include_once '../../config.php';
  include_once '../functions/include-functions.php';
  
//  start_page_build_time_measure();
  
  if(isset($_GET['client_id'])) {
    $current_client_id = $_GET['client_id'];
    
    include_once 'client-details.php';
  }
  else {
    
    $page_title = $languages[$current_lang]['menu_clients'];
    $page_description = "";

    print_html_admin_header($page_title, $page_description);
  
?>

<!--clients list-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/_admin/index.php" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['menu_clients'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>
 
      <section class="options">
        <a class="pageoptions" href="client-add-new.php" title="<?=$languages[$current_lang]['text_client_add_new'];?>">
          <img src="/_admin/images/newobject.gif" class="systemicon" width="16" height="16" alt="<?=$languages[$current_lang]['text_client_add_new'];?>" />
          <?=$languages[$current_lang]['text_client_add_new'];?>
        </a>
      </section>
      
      <table>
        <thead>
          <tr>
            <th width="2%" class="text_left">&num;</th>
            <th width="43%" class="text_left"><?=$languages[$current_lang]['header_image'];?></th>
            <th width="30%" class="text_left"><?=$languages[$current_lang]['header_name'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_status'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_reorder'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_actions'];?></th>
          </tr>
        </thead>
      </table>
      <div class="hidden images_act_inact">
        <!--we gonna use this one only to grab one or the other picture when seting the content active or inactive below-->
        <img src="/_admin/images/true.gif" class="systemicon img_active" alt="<?=$languages[$current_lang]['alt_deactivate'];?>" title="<?=$languages[$current_lang]['title_deactivate'];?>" width="16" height="16" />
        <img src="/_admin/images/false.gif" class="systemicon img_inactive" alt="<?=$languages[$current_lang]['alt_activate'];?>" title="<?=$languages[$current_lang]['title_activate'];?>" width="16" height="16" />
      </div>
      <div id="clients_list" class="list_container">
<?php
        list_clients();
?>
      </div>
    </div>
  </main>
<!--clients list-->

<?php
 
  print_html_admin_footer();
  
//  close_page_build_time_measure($print_time = true);
  
?>
</body>
</html>
<?php
 
  }
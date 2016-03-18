  <?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['text_users'];
  $page_description = $languages[$current_lang]['e_shop_cms']." администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['text_users'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>

      <!--begin of left_col-->
      <div id="left_column">
        <table id="choose_user_type" class="list_container margin_bottom">
          <thead>
            <tr><th><?=$languages[$current_lang]['header_choose_user_group'];?></th></tr>
          </thead>
          <tbody>
<?php
          $query = "SELECT `user_type_id`, `user_type_name` FROM `users_types` 
                    WHERE `language_id` = '$current_language_id' 
                    ORDER BY `user_type_sort_order` ASC";
          //echo $query;
          $users_result = mysqli_query($db_link, $query);
          if(!$users_result) echo mysqli_error($db_link);
          if(mysqli_num_rows($users_result) > 0) {
            while ($user_details = mysqli_fetch_assoc($users_result)) {
              $user_type_id = $user_details['user_type_id'];
              $user_type_name = $user_details['user_type_name'];

              echo "<tr><td class='text_left'><a data-id='$user_type_id' class='red_link'>$user_type_name</a></td></tr>";
            }
          }
          else {   
?>
            <tr><td><?=$languages[$current_lang]['no_user_types_yet'];?></td></tr>
<?php    
          }
?>
          </tbody>
        </table>

      </div>
      <!--end of left_col-->

      <div id="right_column" class="list_container">
        <table id="users_type_default_rights">
          <thead>
            <tr>
              <th width="5%"></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page'];?></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page_access'];?></th>
              <th width="20%"><?=$languages[$current_lang]['header_user_rights_page_edit'];?></th>
              <th width="15%"><?=$languages[$current_lang]['header_user_rights_page_delete'];?></th>
              <th width="15%"><?=$languages[$current_lang]['header_user_rights_page_subpages'];?></th>
              <th width="5%"></th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        <p>&nbsp;</p>
        <a href="javascript:;" onclick="" class="users_type_default_rights button green" style="display: none;">
          <i class="icon icon_save_sign"></i><?=$languages[$current_lang]['btn_save'];?>
        </a>
      </div>
      
      <div class="clearfix"></div>
      <script type="text/javascript">
        $(document).ready(function() {
          $("#choose_user_type a").click(function() {
            $("#choose_user_type td").removeClass("selected_user_type")
            $(this).parent().addClass("selected_user_type");
            GetUsersTypesDefaultRights();
          });
        });
      </script>
    </div>
  </main>
<!--main-->
<?php 
    print_html_admin_footer();
?>
</body>
</html>
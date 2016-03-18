<div id="left_col">
  <div class="left_col">
    <table id="menu_links_level_0">
      <thead>
        <tr><td><?=$laguages[$default_lang]['first_level_menu_thead'];?></td></tr>
      </thead>
      <tbody>
<?php
    $query_menus = "SELECT `menu_id`, `menu_name` FROM `menus` WHERE `menu_parent_id` = '0' AND `menu_level` = '0' ORDER BY `menu_sort_order` ASC";
    $result_menus= mysqli_query($db_link, $query_menus);
    if (!$result_menus) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_menus) > 0) {
      while($row_menus = mysqli_fetch_assoc($result_menus)) {

        $menu_id = $row_menus['menu_id'];
        $menu_name = $row_menus['menu_name'];
        $menu_name = $laguages[$default_lang][$menu_name];

        echo '<tr><td><a data="'.$menu_id.'">'.$menu_name.'</a></td></tr>';
        
      }
    }

?>
      </tbody>
    </table>
  
    <div id="menu_links_level_1">
      
    </div>
    <div id="menu_links_level_2">
      
    </div>
    
  </div>
  
  <div class="right_col">
    <div id="choose_language">
      <table>
        <thead>
          <tr><td><?=$laguages[$default_lang]['language_thead'];?></td></tr>
        </thead>
        <tbody>
<?php
      $query_languages = "SELECT `languages`.* FROM `languages`";
      $result_languages= mysqli_query($db_link, $query_languages);
      if (!$result_languages) echo mysqli_error($db_link);
      if(mysqli_num_rows($result_languages) > 0) {
        $key = 0;
        while($row_languages = mysqli_fetch_assoc($result_languages)) {
          $language_id = $row_languages['language_id'];
          $language_name = $row_languages['language_name'];
          $language_name = $laguages[$default_lang][$language_name];
          $class = ($key == 0) ? ' class="selected_language"' : "";
          echo '<tr><td'.$class.'><a data="'.$language_id.'">'.$language_name.'</a></td></tr>';
          $key++;
        }
      }
?>
        </tbody>
      </table>
    </div>
  </div>
  
</div>
<div id="right_col">
  <table>
    <thead>
      <tr>
        <td width="5%"><?=$laguages[$default_lang]['btn_save'];?></td>
        <td width="90%"><?=$laguages[$default_lang]['note_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['btn_delete'];?></td>
      </tr>
    </thead>
  </table>
  <div id="menu_link_note">

  </div>
  <div id="add_new_menu_link_note">

  </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#menu_links_level_0 a").click(function() {
      $("#menu_links_level_0 td").removeClass("selected_menu_link_level_0");
      $(this).parent().addClass("selected_menu_link_level_0");
      $("#menu_links_level_2").html("");
      GetMenuLinkChildren('1');
    });
    $("#choose_language a").click(function() {
      $("#choose_language td").removeClass("selected_language");
      $(this).parent().addClass("selected_language");
      GetMenuLinkNote();
    });
  });
</script>
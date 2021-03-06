<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  if(isset($_POST['menu_id'])) {
    $menu_id = $_POST['menu_id'];
  }
  if(isset($_POST['language_id'])) {
    $language_id = $_POST['language_id'];
  }
  
  if(!empty($menu_id) && !empty($language_id)) {
    
    $query_menus_notes = "SELECT `menu_note` FROM `menus_notes` WHERE `menu_id` = '$menu_id' AND `language_id` = '$language_id'";
    //echo $query;
    $result_menus_notes = mysqli_query($db_link, $query_menus_notes);
    if (!$result_menus_notes) echo mysqli_error($db_link);
    if(mysqli_num_rows($result_menus_notes) > 0) {

      $row = mysqli_fetch_assoc($result_menus_notes);
      $menu_note = stripslashes($row['menu_note']);
   
?>
    <div id="menu_link_note_<?php echo "$menu_id$language_id";?>" class="row_over">
      <table>
        <tr class="even">
          <td width="5%"><button class="btn_save" onClick="EditMenuLinkNote('<?=$menu_id;?>','<?=$language_id;?>')">Save</button></td>
          <td width="90%">
            <textarea name="ckeditor" id="ckeditor" class="menu_link_note" style="width:96%;height:250px;"><?php if (!empty($menu_note)) echo $menu_note; ?></textarea>
          </td>
          <td width="5%">
            <a href="javascript:;" class="delete_menu_link_note" data-menu-id="<?=$menu_id;?>" data-lang-id="<?=$language_id;?>">
              <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_delete'];?>" title="<?=$languages[$current_lang]['title_delete'];?>" width="16" height="16" />
            </a>
          </td>
        </tr>
      </table>
    </div>
    <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure']?>">
      <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_menu_link_note_warning']?></p>
    </div>
<?php
    }
    else {
?>
    <div class="add_new_form" class="row_over"> 
      <table>
        <tr class="even">
          <td width="5%"><button class="btn_save" onClick="AddMenuLinkNote()"><?=$languages[$current_lang]['btn_save'];?></button></td>
          <td width="90%">
            <textarea name="ckeditor" id="ckeditor" style="width:96%;height:250px;"></textarea>
          </td>
          <td></td>
        </tr>
      </table>
    </div>
<?php
    }
  }
?>
  <script>
  tinymce.init({
  selector: 'textarea',
  height: 500,
  theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools'
  ],
  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '//fast.fonts.net/cssapi/e6dc9b99-64fe-4292-ad98-6974f93cd2a2.css',
    '//www.tinymce.com/css/codepen.min.css'
  ]
 });
  </script>
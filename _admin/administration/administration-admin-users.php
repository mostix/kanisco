<?php

  include_once '../../config.php';
  include_once '../functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['header_administration_users'];
  $page_description = "E-shop администрация";
  
  print_html_admin_header($page_title, $page_description);
?>
<!--main-->
  <main>
    <div class="inside_container">
      <section id="breadcrumbs">
        <a href="/" title="<?=$languages[$current_lang]['title_breadcrumbs_homepage'];?>"><?=$languages[$current_lang]['menu_home'];?></a>
        <span>&raquo;</span>
        <?=$languages[$current_lang]['header_administration_users'];?>
      </section>
      
      <h1 id="pagetitle"><?=$page_title;?></h1>

      <table>
        <thead>
          <tr>
            <th width="5%"><?=$languages[$current_lang]['btn_save'];?></th>
            <th width="15%"><?=$languages[$current_lang]['header_user_username'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_user_password'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_user_firstname'];?></th>
            <th width="10%"><?=$languages[$current_lang]['header_user_lastname'];?></th>
            <th width="9%"><?=$languages[$current_lang]['header_user_rights'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_user_is_active'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_user_logs'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_user_is_ip_in_use'];?></th>
            <th width="5%"><?=$languages[$current_lang]['header_user_reset_ip'];?></th>
            <th width="5%"><?=$languages[$current_lang]['btn_delete']; ?></th>
          </tr>
        </thead>
      </table>
      <div id="users_list">
<?php
      // we gonna select only `users`.`user_type_id` = 1 - administrator
      $query = "SELECT `users`.`user_id`, `users`.`user_username`, `users`.`user_is_active`, `users`.`user_is_ip_in_use`, 
                      `users`.`user_firstname`,`users`.`user_lastname`
                FROM `users`
                WHERE `users`.`user_type_id` = '1' AND `users`.`user_is_active` = '1'
                ORDER BY `users`.`user_firstname` ASC";
      //echo $query;
      $users_result = mysqli_query($db_link, $query);
      if (!$users_result) echo mysqli_error($db_link);
      if(mysqli_num_rows($users_result) > 0) {
      $key = 0;
        while ($user_details = mysqli_fetch_assoc($users_result)) {
          $user_id = $user_details['user_id'];
          $user_username = $user_details['user_username'];
          $user_firstname = $user_details['user_firstname'];
          $user_lastname = $user_details['user_lastname'];
          $user_is_ip_in_use = $user_details['user_is_ip_in_use'];
          $user_is_active = $user_details['user_is_active'];
          if(!isset($class)) $class = "even";
          $class = (($class == "odd") ? " even" : " odd");
          $class = ((($key % 2) == 1) ? " even" : " odd");
?>
          <div id="user<?php echo $user_id; ?>">
            <table>
              <tbody>
                <tr class="row_over<?=$class;?>">
                  <td width="5%"><button class="btn_save" onClick="EditUserJQ('<?php echo $user_id; ?>')"><?=$languages[$current_lang]['btn_save'];?></button></td>
                  <td width="15%"><input type="text" name="user_username<?php echo $user_id; ?>" id="user_username<?php echo $user_id; ?>" class="user_username" value="<?php echo $user_username; ?>" ></td>
                  <td width="10%"><input type="password" name="user_password<?php echo $user_id; ?>" id="user_password<?php echo $user_id; ?>" class="user_password" placeholder="******" ></td>
                  <td width="10%"><?php echo $user_firstname; ?></td>
                  <td width="10%"><?php echo $user_lastname; ?></td>
                  <td width="9%"><button class="access_rights button blue" button-id="<?php echo $user_id; ?>">Access rights</button></td>
                  <td width="5%">
                    <div class="checkbox<?php if ($user_is_active == 1) echo ' checkbox_checked'; ?>">
                      <input type="checkbox" id="active<?php echo $user_id; ?>" onClick="Checkbox(this)" <?php if ($user_is_active == 1) echo 'checked="checked"'; ?> />
                    </div>
                  </td>
                  <td width="5%"><button class="get_user_log button blue" button-id="<?php echo $user_id; ?>" onclick="GetUserLog(<?php echo $user_id; ?>)">Check</button></td>
                  <td width="5%">
                    <div class="checkbox<?php if ($user_is_ip_in_use == 1) echo ' checkbox_checked'; ?>">
                      <input type="checkbox" id="ip_in_use<?php echo $user_id; ?>" onClick="Checkbox(this)" <?php if ($user_is_ip_in_use == 1) echo 'checked="checked"'; ?> />
                    </div>
                  </td>
                  <td width="5%"><button class="reset_ip button blue" button-id="<?php echo $user_id; ?>" onclick="ResetIP(<?php echo $user_id; ?>)">Reset</button></td>
                  <td width="5%">
                    <a href="javascript:;" class="delete_user_link" data-id="<?=$user_id;?>">
                      <img src="/_admin/images/delete.gif" class="systemicon" alt="<?=$languages[$current_lang]['alt_delete'];?>" title="<?=$languages[$current_lang]['title_delete'];?>" width="16" height="16" />
                    </a>
                    <!--<button class="remove" onClick="DeleteUser('<?php echo $user_id; ?>')"><?=$languages[$current_lang]['btn_delete']; ?></button>-->
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="users_details details<?php echo $user_id; ?>">
            <?php 
              get_user_rights($user_id);
            ?>
            </div>
          </div>
<?php
          $key++;
        }
        mysqli_free_result($users_result);
      }
?>
      </div>
  
<!--  <div class="add_new_form">
    <h3><?=$languages[$current_lang]['form_add_new']; ?></h3>
    <table>
      <tbody>
        <tr class="row_over">
          <td width="5%"><button class="btn_save" onClick="AddUser('1')" title="Update"><?=$languages[$current_lang]['btn_save'];?></button></td>
          <td width="15%"><input type="text" id="add_user_username"></td>
          <td width="10%"><input type="password" id="add_user_password" placeholder="******" ></td>
          <td width="10%"><input type="text" id="add_user_firstname"></td>
          <td width="10%"><input type="text" id="add_user_lastname"></td>
          <td width="9%"></td>
          <td width="5%">
            <div class="checkbox">
              <input type="checkbox" id="add_user_is_active" onClick="Checkbox(this)" />
            </div>
          </td>
          <td width="5%"></td>
          <td width="5%">
            <div class="checkbox">
              <input type="checkbox" id="add_ip_in_use" onClick="Checkbox(this)" />
            </div>
          </td>
          <td width="5%"></td>
          <td width="5%"></td>
        </tr>
      </tbody>
    </table>
  </div>-->

<!--  <div class="search_form">
    <h3><?php echo SEARCH_AREA; ?></h3>
    <table>
      <tbody>
        <tr class="row_over">
          <td width="5%"><button class="button btn_save" onClick="SearchUsers('1')"><?php echo BTN_SEARCH; ?></button></td>
          <td width="15%"><input type="text" id="search_user_username"></td>
          <td width="10%">&nbsp;</td>
          <td width="10%"><input type="text" id="search_user_first_name"></td>
          <td width="10%"><input type="text" id="search_user_last_name"></td>
          <td width="10%">
            <select name="add_group_department_id" id="user_group_department_id">
              <option></option>
<?php
              $departmentResult = mysqli_query($theConn, "SELECT `department_id`, `department_name`, `department_sort` FROM `cont_department` ORDER BY `department_sort`");
              if (!$departmentResult) {
                  echo mysqli_error($theConn);
              } else {// if (!$departmentResult)
                  while ($departmentRow = mysqli_fetch_assoc($departmentResult)):
?>
                    <option value="<?php echo $departmentRow['department_id']; ?>"><?php echo $departmentRow['department_name'] ?></option>
<?php
                  endwhile;
              }// if (!$departmentResult)
?>
              </select>
            </td>
            <td width="10%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
            <td width="5%">&nbsp;</td>
          </tr>
        </tbody>
      </table>
    </div>-->
    </div>
  </main>
  <!--main-->
  <div class="clearfix"></div>
  <!--modal_confirm-->
  <div style="display:none;" id="modal_confirm" class="clearfix" title="<?=$languages[$current_lang]['are_you_sure']?>">
    <p style="padding:0;margin:0;width:100%;float:left;"><?=$languages[$current_lang]['delete_user']?></p>
    <input type="hidden" id="cannnot_delete_admin" value="<?=$languages[$current_lang]['cannnot_delete_admin']?>" />
  </div>
  <script>
  $(function() {
    $("#modal_confirm").dialog({
      resizable: false,
      width: 400,
      height: 200,
      autoOpen: false,
      modal: true,
      draggable: false,
      closeOnEscape: true,
      dialogClass: "modal_confirm",
      buttons: {
        "<?=$languages[$current_lang]['btn_delete'];?>": function() {
          DeleteUser();
        },
        "<?=$languages[$current_lang]['btn_cancel'];?>": function() {
          $(".delete_user_link").removeClass("active");
          $(this).dialog("close");
        }
      }
    });
    $(".delete_user_link").click(function() {
      $(".delete_user_link").removeClass("active");
      $(this).addClass("active");
      $("#modal_confirm").dialog("open");
    });
    $(".access_rights").click(function() {
        var user_id = $(this).attr("button-id");
        if($(".details"+user_id).hasClass("access_rights_edit")) {
          $(".users_details").removeClass("access_rights_edit");
        } else {
          $(".users_details").removeClass("access_rights_edit");
          $(".details"+user_id).addClass("access_rights_edit");
        }
      });
      $(".menu_header").click(function() {
        if($(this).hasClass("active_header")) {
          var header_id = $(this).attr("button-id");
          $(this).html("+");
          $(this).removeClass("active_header")
          $(".children"+header_id).hide();
        }
        else {
          $(".menu_header").removeClass("active_header");
          $(this).addClass("active_header");
          $(this).html("-");
          var header_id = $(this).attr("button-id");
          $(".children").hide();
          $(".children"+header_id).show();
        }
      });
  });
  </script>
<?php 
    print_html_admin_footer();
?>
</body>
</html>
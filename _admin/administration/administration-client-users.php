<div>
  <table>
    <thead>
      <tr>
        <td width="5%"><?=$laguages[$default_lang]['btn_save'];?></td>
        <td width="15%"><?=$laguages[$default_lang]['user_username_thead'];?></td>
        <td width="10%"><?=$laguages[$default_lang]['user_password_thead'];?></td>
        <td width="10%"><?=$laguages[$default_lang]['user_firstname_thead'];?></td>
        <td width="10%"><?=$laguages[$default_lang]['user_lastname_thead'];?></td>
        <td width="10%"><?=$laguages[$default_lang]['user_assigned_to_warehouse_thead'];?></td>
        <td width="9%"><?=$laguages[$default_lang]['user_rights_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['user_is_active_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['user_logs_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['user_is_ip_in_use_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['user_reset_ip_thead'];?></td>
        <td width="5%"><?=$laguages[$default_lang]['btn_delete']; ?></td>
      </tr>
    </thead>
  </table>
  <div id="users_list">
<?php
  $db_link = DB_OpenI();
  
  $all_warehouses = array();
  $query_warehouses = "SELECT `warehouses_types`.`warehouse_type_name`,`warehouses`.`warehouse_id`, `warehouses`.`warehouse_name`
                      FROM `warehouses`
                      INNER JOIN `warehouses_types` ON `warehouses_types`.`warehouse_type_id` = `warehouses`.`warehouse_type_id`
                      ORDER BY `warehouses_types`.`warehouse_type_name` ASC, `warehouses`.`warehouse_name` ASC";
  $result_warehouses = mysqli_query($db_link, $query_warehouses);
  if (!$result_warehouses) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_warehouses) > 0) {
    while($row_warehouses = mysqli_fetch_assoc($result_warehouses)) {
      $all_warehouses[] = $row_warehouses;
    }
  }
  
  // we gonna select only `users`.`user_type_id` = 3 - clients
  $query = "SELECT `users`.`user_id`, `users`.`user_username`, `users`.`user_is_active`, `users`.`user_is_ip_in_use`, 
                  `users`.`user_firstname`,`users`.`user_lastname`, `users`.`warehouse_id`
            FROM `users`
            WHERE `users`.`user_type_id` = '3' AND `users`.`user_is_active` = '1'
            ORDER BY `users`.`user_firstname` ASC";
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
      $user_warehouse_id = $user_details['warehouse_id'];
      $class = ((($key % 2) == 1) ? " even" : " odd");
?>
      <div id="user<?php echo $user_id; ?>" class="row_over<?php echo $class; ?>">
        <table>
          <tbody>
            <tr>
              <td width="5%"><button class="btn_save" onClick="EditUserJQ('<?php echo $user_id; ?>')" title="Update"><?=$laguages[$default_lang]['btn_save'];?></button></td>
              <td width="15%"><input type="text" name="user_username<?php echo $user_id; ?>" id="user_username<?php echo $user_id; ?>" class="user_username" value="<?php echo $user_username; ?>" ></td>
              <td width="10%"><input type="password" name="user_password<?php echo $user_id; ?>" id="user_password<?php echo $user_id; ?>" class="user_password" placeholder="******" ></td>
              <td width="10%"><?php echo $user_firstname; ?></td>
              <td width="10%"><?php echo $user_lastname; ?></td>
              <td width="10%">
                <select id="warehouse_id<?php echo $user_id; ?>">
                  <option value="0"></option>
              <?php
                  foreach($all_warehouses as $warehouse) {
                    
                    $warehouse_type_name = $warehouse['warehouse_type_name'];
                    $warehouse_id = $warehouse['warehouse_id'];
                    $warehouse_name = $warehouse['warehouse_name'];
                    
                    if ($user_warehouse_id == $warehouse_id)
                        $selected = 'selected="selected"';
                    else
                        $selected = "";

                    echo "<option value='$warehouse_id' $selected>$warehouse_type_name - $warehouse_name</option>";
                  }
              ?>
                </select>
              </td>
              <td width="10%"><button class="access_rights button" button-id="<?php echo $user_id; ?>">Access rights</button></td>
              <td width="5%">
                <div class="checkbox<?php if ($user_is_active == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" id="active<?php echo $user_id; ?>" onClick="Checkbox(this)" <?php if ($user_is_active == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%"><button class="get_user_log button" button-id="<?php echo $user_id; ?>" onclick="GetUserLog(<?php echo $user_id; ?>)">Check</button></td>
              <td width="5%">
                <div class="checkbox<?php if ($user_is_ip_in_use == 1) echo ' checkbox_checked'; ?>">
                  <input type="checkbox" id="ip_in_use<?php echo $user_id; ?>" onClick="Checkbox(this)" <?php if ($user_is_ip_in_use == 1) echo 'checked="checked"'; ?> />
                </div>
              </td>
              <td width="5%"><button class="reset_ip button" button-id="<?php echo $user_id; ?>" onclick="ResetIP(<?php echo $user_id; ?>)">Reset</button></td>
              <td width="5%"><button class="remove" onClick="DeleteUser('<?php echo $user_id; ?>')"><?=$laguages[$default_lang]['btn_delete']; ?></button></td>
            </tr>
          </tbody>
        </table>
        <div class="users_details details<?php echo $user_id; ?>">
<?php get_user_rights($user_id); ?>
        </div>
      </div>
<?php
      $key++;
    }
    mysqli_free_result($users_result);
  }
?>
  </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
  $(document).ready(function() {
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
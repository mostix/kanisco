<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf();
  
  //print_r($_POST);EXIT;
  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  if(isset($_POST['user_name'])) {
    $user_name = $_POST['user_name'];
  }
?>
  <table class="no_margin">
    <thead>
      <tr>
        <td width="5%"><?=$languages[$current_lang]['btn_save'];?></td>
        <td><?php echo $languages[$current_lang]['user_details_thead']." $user_name"; ?></td>
          <td width="5%"><?=$languages[$current_lang]['btn_delete']; ?></td>
      </tr>
    </thead>
  </table>
<?php
  
  $query = "SELECT `user_id`,`user_type_id`,`store_id`,`user_username`,`user_firstname`,`user_lastname`,`user_address`,`user_phone`,
                    `user_email`,`user_info`,`user_is_ip_in_use`,`user_is_active` 
            FROM `users`
            WHERE `user_id` = '$user_id'
            ORDER BY `user_firstname` ASC";
  //echo $query;
  $users_result = mysqli_query($db_link, $query);
  if(!$users_result) echo mysqli_error($db_link);
  if(mysqli_num_rows($users_result) > 0) {
      $user_details = mysqli_fetch_assoc($users_result);
      //echo "<pre>";print_r($user_details);
      $user_id = $user_details['user_id'];
      $user_username = $user_details['user_username'];
      $user_firstname = $user_details['user_firstname'];
      $user_lastname = $user_details['user_lastname'];
      $user_address = stripslashes($user_details['user_address']);
      $user_phone = $user_details['user_phone'];
      $user_email = $user_details['user_email'];
      $user_info = stripslashes($user_details['user_info']);
      $user_is_ip_in_use = $user_details['user_is_ip_in_use'];
      $user_is_active = $user_details['user_is_active'];
?>
  <div id="user_details<?php echo $user_id;?>" class="row_over" style="padding: 2px 0;">
    <table style="width:95%;float:left;">
      <tbody>
        <tr>
          <td width="5%" rowspan="7" class="no_background">
            <button class="button btn_save" onClick="EditUserFullDetails('<?php echo $user_id;?>')">Save</button>
          </td>
        </tr>
        <tr>
          <td width="15%" class="first_child"><span><?=$languages[$current_lang]['user_username_label'];?></span></td>
          <td width="33%"><input type="text" id="user_username" value="<?php echo $user_username;?>" /></td>
          <td width="15%"><span><?=$languages[$current_lang]['user_address_label'];?></span></td>
          <td width="33%"><input type="text" name="user_address" id="user_address" value='<?php echo $user_address;?>' /></td>
        </tr>
        <tr>
          <td width="15%" class="first_child"><span><?=$languages[$current_lang]['user_password_label'];?></span></td>
          <td width="33%"><input type="password" id="user_password" placeholder="******" /></td>
          <td width="15%"><span><?=$languages[$current_lang]['user_email_label'];?></span></td>
          <td width="33%"><input type="text" name="user_email" id="user_email" value="<?php echo $user_email;?>" /></td>
        </tr>
        <tr>
          <td width="15%" class="first_child"><span><?=$languages[$current_lang]['user_firstname_label'];?></span></td>
          <td width="33%"><input type="text" id="user_firstname" value="<?php echo $user_firstname;?>" /></td>
          <td width="15%"><span><?=$languages[$current_lang]['user_phone_label'];?></span></td>
          <td width="33%"><input type="text" name="user_phone" id="user_phone" value="<?php echo $user_phone;?>" /></td>
        </tr>
        <tr>
          <td width="15%" class="first_child"><span><?=$languages[$current_lang]['user_lastname_label'];?></span></td>
          <td width="33%"><input type="text" id="user_lastname" value="<?php echo $user_lastname;?>" /></td>
          <td width="15%"><span><?=$languages[$current_lang]['user_info_label'];?></span></td>
          <td width="33%"><input type="text" id="user_info" value='<?php echo $user_info;?>' /></td>
        </tr>
        <tr>
          <td width="15%"><span><?=$languages[$current_lang]['user_account_label'];?></span></td>
          <td width="33%">
            
          </td>
          <td width="15%" class="first_child"><span><?=$languages[$current_lang]['user_assigned_to_warehouse_thead'];?></span></td>
          <td width="33%">
            <select id="user_warehouse_id">
              <option value="0"></option>
<?php
 
?>
            </select>
          </td>
        </tr>

        <tr>
          <td width="15%"><span><?=$languages[$current_lang]['user_is_active_thead'];?></span></td>
          <td width="33%">
            <div class="checkbox<?php if ($user_is_active == 1) echo ' checkbox_checked'; ?>">
              <input type="checkbox" id="user_is_active" onClick="Checkbox(this)" <?php if ($user_is_active == 1) echo 'checked="checked"'; ?> />
            </div>
          </td>
          <td width="15%"><span><?=$languages[$current_lang]['user_is_ip_in_use_thead'];?></span></td>
          <td width="33%">
                <div class="checkbox<?php if ($user_is_ip_in_use == 1) echo ' checkbox_checked'; ?>">
              <input type="checkbox" id="user_is_ip_in_use" onClick="Checkbox(this)" <?php if ($user_is_ip_in_use == 1) echo 'checked="checked"'; ?> />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <table style="width:5%;float:right;">
      <tbody>
        <tr>
          <td width="3%" rowspan="7" class="no_background" style="text-align:right;">
            <button class="remove" onClick="DeleteUser('<?php echo $user_id;?>')">Delete</button>
          </td>
        </tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
        <tr><td width="2%" class="no_background" style="height: 30px;">&nbsp;</td></tr>
      </tbody>
    </table>
    <div class="clearfix"></div>
  </div>  
<?php
  }
  else {
?>
    <tr><?=$languages[$current_lang]['no_clients_yet'];?></tr>
<?php    
  }
  
  DB_CloseI($db_link);
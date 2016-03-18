<?php

  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  //echo "<pre>";print_r($_POST);exit;

  $user_type_id = $_POST['user_type_id'];
  $user_rights_array = $_POST['user_rights'];
  
  if(!empty($user_rights_array)) {
    
    $all_queries = "";
    
    mysqli_query($db_link,"BEGIN");
    
    foreach($user_rights_array as $key => $rights) {
      
      $menu_id = $rights['menu_id'];
      $menu_access_was_checked = $rights['menu_access_was_checked'];
      $rights_access = $rights['rights_access'];
      $users_rights_edit = $rights['rights_edit'];
      $users_rights_delete = $rights['rights_delete'];
      
      if($menu_access_was_checked == 0) {
        
        /*
         * there was no record for this menu
         * e.g. no rights was given for this menu
         */
        
        if($rights_access == 1) {
          
          /*
           * the menu rights was checked, so we have to make a new record for it
           */
          
          $query = "INSERT INTO `users_types_rights`(`users_rights_id`, 
                                                    `user_type_id`, 
                                                    `menu_id`, 
                                                    `users_rights_edit`, 
                                                    `users_rights_delete`) 
                                            VALUES ('',
                                                    '$user_type_id',
                                                    '$menu_id',
                                                    '$users_rights_edit',
                                                    '$users_rights_delete')";
          $all_queries .= "<br>\n".$query;
          $result = mysqli_query($db_link, $query);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_insert']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        } 
        
      }
      else {
        
        /*
         * there was a record for this menu
         * so we have to check if the rights was cancled
         * or just edited
         */
        
        if($rights_access == 1) {
          
          /*
           * just edit the rights
           */
          
          $query_update = "UPDATE `users_types_rights` 
                          SET `users_rights_edit`='$users_rights_edit',`users_rights_delete`='$users_rights_delete' 
                          WHERE `user_type_id` = '$user_type_id' AND `menu_id` = '$menu_id'";
          $all_queries .= "<br>\n".$query_update;
          $result_update = mysqli_query($db_link, $query_update);
          if(!$result_update) {
            echo $languages[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
        else {
          
          /*
           * the rights was cancled, so we gonna delete the record
           */
          
          $query_delete = "DELETE FROM `users_types_rights` WHERE `user_type_id` = '$user_type_id' AND `menu_id` = '$menu_id'";
          $all_queries .= "<br>\n".$query_delete;
          $result_delete = mysqli_query($db_link, $query_delete);
          if(mysqli_affected_rows($db_link) <= 0) {
            echo $languages[$current_lang]['sql_error_delete']." - ".mysqli_error($db_link);
            mysqli_query($db_link,"ROLLBACK");
            exit;
          }
        }
      }
      
    } //foreach($user_rights_array)
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
  }
        
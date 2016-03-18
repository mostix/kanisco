<?php

include_once("../../../config.php");
  
  check_for_csrf();

if (isset($_POST['group_type_id'])) {
    $type_id = $_POST['group_type_id'];
}
if (isset($_POST['group_department_id'])) {
    $department_id = $_POST['group_department_id'];
}
if (isset($_POST['user_rights'])) {
    $user_rights = $_POST['user_rights'];
}


if (!empty($type_id) && !empty($department_id)) {
    $theConn = logini();    
    
    mysqli_query($theConn, "START TRANSACTION;");
     
    //delete rights for group
    $theQueryDeleteRights = "DELETE FROM `users_rights_groups` WHERE `user_company_type_id` = ".$type_id." AND `user_department_id` = ".$department_id;
    $resultDeleteRights = mysqli_query($theConn, $theQueryDeleteRights);
    //mysqli_query($theConn, $theQueryDeleteRights);
    if (!$resultDeleteRights) echo "User data not changed! 2.".mysqli_error($theConn);
    
    //insert new rights
    if ( !empty($user_rights) ) {
        foreach ($user_rights as $array_rights):
            $theQueryInsertRights = "INSERT INTO `users_rights_groups` SET
                `user_company_type_id` = ".$type_id.",
                `user_department_id` = ".$department_id."
                ".(!empty($array_rights[0])?", `users_rights_groups_access` = ".$array_rights[0]:NULL)."  
                ".(!empty($array_rights[1]) && ($array_rights[1] == "edit")?", `users_rights_groups_edit` = 1":NULL)."
                ".(!empty($array_rights[1]) && ($array_rights[1] == "delete")?", `users_rights_groups_delete` = 1":NULL)."
                ".(!empty($array_rights[2])?", `users_rights_groups_delete` = 1":NULL);
            $resultInsertRights = mysqli_query($theConn, $theQueryInsertRights);
            
            if ($resultInsertRights) {
                if ( mysqli_affected_rows($theConn) <= 0) {
                    mysqli_query($theConn, "ROLLBACK;");
                    echo "User data not changed! 3. ".$theQueryInsertRights.mysqli_error($theConn);
                    exit();
                }// if ( mysqli_affected_rows($theConn) <= 0)
            } else {// if ($resultInsertRights)
                mysqli_query($theConn, "ROLLBACK;");
                echo "User data not changed! 4. ".$theQueryInsertRights.mysqli_error($theConn);
                exit();
            }// if ($resultInsertRights)
        endforeach;
    }// if ( !empty($user_rights) )
    
    mysqli_query($theConn, "COMMIT;");
    
mysqli_close($theConn); //close mysqli connection
}// if (!empty($type_id) && !empty($department_id))
?>

<?php

  include_once '../../../config.php';
  include_once '../../functions/include-functions.php';
  
  check_ajax_request();
  
  if(isset($_POST['team_member_id'])) {
    $team_member_id =  $_POST['team_member_id'];
  }
  if(isset($_POST['team_member_sort_order'])) {
    $team_member_sort_order =  $_POST['team_member_sort_order'];
  }
  if(isset($_POST['action'])) {
    $action =  $_POST['action'];
  }
  
  if(!empty($team_member_id) && !empty($action)) {
    
    mysqli_query($db_link,"BEGIN");
    
    $all_queries = "";
    
    if($action == "forward") {
      $previous_team_member_sort_order = $team_member_sort_order-1;
      $query_update_team_member_1 = "UPDATE `team_members` SET `team_member_sort_order`='$team_member_sort_order' WHERE `team_member_sort_order` = '$previous_team_member_sort_order'";
      $all_queries .= "\n".$query_update_team_member_1;
        //echo $query_update_team_member_1;
      $result_update_team_member_1 = mysqli_query($db_link, $query_update_team_member_1);
      if(!$result_update_team_member_1) {
        echo $team_members[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_team_member_2 = "UPDATE `team_members` SET `team_member_sort_order`='$previous_team_member_sort_order' WHERE `team_member_id` = '$team_member_id'";
      $all_queries .= "\n".$query_update_team_member_2;
        //echo $query_update_team_member_2;
      $result_update_team_member_2 = mysqli_query($db_link, $query_update_team_member_2);
      if(!$result_update_team_member_2) {
        echo $team_members[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    else {
      $next_team_member_sort_order = $team_member_sort_order+1;
      $query_update_team_member_1 = "UPDATE `team_members` SET `team_member_sort_order`='$team_member_sort_order' WHERE `team_member_sort_order` = '$next_team_member_sort_order'";
      $all_queries .= "\n".$query_update_team_member_1;
        //echo $query_update_team_member_1;
      $result_update_team_member_1 = mysqli_query($db_link, $query_update_team_member_1);
      if(!$result_update_team_member_1) {
        echo $team_members[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
      $query_update_team_member_2 = "UPDATE `team_members` SET `team_member_sort_order`='$next_team_member_sort_order' WHERE `team_member_id` = '$team_member_id'";
      $all_queries .= "\n".$query_update_team_member_2;
        //echo $query_update_team_member_2;
      $result_update_team_member_2 = mysqli_query($db_link, $query_update_team_member_2);
      if(!$result_update_team_member_2) {
        echo $team_members[$current_lang]['sql_error_update']." - ".mysqli_error($db_link);
        mysqli_query($db_link,"ROLLBACK");
        exit;
      }
    }
    
    //echo $all_queries;mysqli_query($db_link,"ROLLBACK");exit;
    
    mysqli_query($db_link,"COMMIT");
    
    list_team_members();

  }
?>

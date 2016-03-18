<?php
  include_once '../../../../config.php';
  include_once '../../../functions/include-functions.php';
  
  check_for_csrf_in_reports();
  
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>KITT SYSTEMS</title>
<style>
body {
  width: 100%;
  padding:0;
  font-family:Garamond, "Times New Roman",Helvetica, sans-serif;	
  font-size:12px;
  line-height: 15px;
  text-align: left;
  color:#333;
  background-color: #fff;
}
h1 {
  margin: 30px 0 8px;
  font-size:20px;
}
h2 {
  margin: 20px 0 8px;
  font-size:16px;
}
h3,li {
  margin: 0 0 4px;
  font-size:15px;
}
p {
  margin:0 0 8px;
}
small {
  font-size:10px;
}
.btn {
  margin-bottom: 1px;
  padding: 5px 10px !important;
  font-weight: bold;
  text-align: center;
  background: #fffbe2; /* Old browsers */
  background: -moz-linear-gradient(top,  #fffbe2 0%, #fffffd 23%, #fff697 85%, #fffef4 100%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fffbe2), color-stop(23%,#fffffd), color-stop(85%,#fff697), color-stop(100%,#fffef4)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* IE10+ */
  background: linear-gradient(to bottom,  #fffbe2 0%,#fffffd 23%,#fff697 85%,#fffef4 100%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fffbe2', endColorstr='#fffef4',GradientType=0 ); /* IE6-9 */
  border: 1px solid #a8a8a7;
  border-radius: 10px;
  -moz-border-radius: 10px;
  -o-border-radius: 10px;
  -webkit-border-radius: 10px;
}
</style>
</head>
<body>
<form>
  <input type="button" class="btn" value="Print" onClick="this.parentNode.style.display='none';window.print();">
 </form>
<?php
  if(isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
  }
  
  $query = "SELECT `users`.`user_username`, `users`.`user_ip`, `users`.`user_firstname`, `users`.`user_lastname` 
            FROM `users`
            WHERE `users`.`user_id` = '$user_id'";
  $result_user_log = mysqli_query($db_link,$query);
  if(!$result_user_log) echo mysqli_error($db_link);
  if(mysqli_num_rows($result_user_log) > 0) {
    
    $user_details = mysqli_fetch_assoc($result_user_log);
    
    $user_username = $user_details['user_username'];
    $user_firstname = $user_details['user_firstname'];
    $user_lastname = $user_details['user_lastname'];
    $basic_user_ip = $user_details['user_ip'];
    
  }
  $query = "SELECT `users_logs`.* FROM `users_logs` 
            WHERE `user_id` = '$user_id'
            ORDER BY `user_log_id` DESC
            LIMIT 50";
  //echo $query;exit;
  $result = mysqli_query($db_link,$query);
  if(!$result) echo mysqli_error($db_link);
  else {
    while($row = mysqli_fetch_assoc($result)) {
      $users_logs[] = $row;
    }

    if(!empty($users_logs)) {
      
      echo "<h1 style=\"text-align:center;\">$user_firstname $user_lastname logs</h1>";
      echo "<ol>";
      foreach($users_logs as $users_log) {

        $user_log_date = $users_log['user_log_date'];
        $user_location_city = (empty($users_log['user_location_city'])) ? "" : $users_log['user_location_city']." / ";
        $user_ip = (empty($users_log['user_ip'])) ? $basic_user_ip : $users_log['user_ip'];
        
        echo "<li>$user_log_date - $user_location_city / $user_ip</li>";
      }
      echo "</ol>";
      
    }
    else echo "<h2>No logs for $user_firstname $user_lastname yet</h2>";
    
  }
?>
</body>
</html>
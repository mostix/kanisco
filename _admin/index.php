<?php

  include_once '../config.php';
  include_once 'functions/include-functions.php';
 
  $page_title = $languages[$current_lang]['e_shop_cms']." администрация";
  $page_description = $languages[$current_lang]['e_shop_cms']." администрация";
  
  if(!isset($_SESSION['admin']['user_id'])) { 
    print_html_login_header("Вход :: ".$page_title, $page_description);
    
    include_once 'login.php';
  }
  else {
  
  print_html_admin_header($page_title, $page_description);
?>

<!--main-->
  <main>
    
  </main>
<!--main-->

<?php 
      print_html_admin_footer();
    }
?>
</body>
</html>
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');

  include_once '../config.php';
  include_once '../functions/include-functions.php';
  include_once '../languages/languages.php';

  if(isset($_POST['news_category_id'])) {
    $news_category_id =  $_POST['news_category_id'];
  }
  if(isset($_POST['offset'])) {
    $offset =  $_POST['offset'];
  }
  if(isset($_POST['news_count'])) {
    $news_count =  $_POST['news_count'];
  }
  if(isset($_POST['language_id'])) {
    $current_language_id =  $_POST['language_id'];
  }
  if(isset($_POST['current_lang'])) {
    $current_lang =  $_POST['current_lang'];
  }

  list_news($offset,$news_count);
?>
<?php

define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');

require BASE_PATH . 'settings.php';
require BASE_PATH . '_lib_/translator_class.php';
$action_status_message = '';
$translator = new translator($settings['lang']);

session_start();
session_cache_limiter("private_no_expire");
if ((!isset($_SESSION["password"])) || ($_SESSION["password"] != $password)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((isset($_POST["password"])) && (md5($_POST["password"]) == $password)) {
      $_SESSION["password"] = md5($_POST["password"]);
    } else {
    echo 'Wrong password?';exit();
    }
  } else {
    echo '<!doctype html><html><head><title>'.$translator->string('Login').'</title></head>
    <body><h1>'.$translator->string('Login').'</h1><p>'.$translator->string('You must be logged in to access this page!').'</p>
   <form action="" method="POST">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" class="button">
   </form>
</body></html>';exit();
  }
}
// <<<<<<<<<<<<<<<
// ADMIN ACTIONS (Available when logged in)
// >>>>>>>>>>>>>>>
if (isset($_GET['delete'])) {
  if (!is_dir($_GET['delete'])) {
    unlink($_GET['delete']);
    $action_status_message = '<p>' . $translator->string('Deleted file:') .' <b>'. $_GET['delete'] .'</b></p>';
  } else {
    rmdir($_GET['delete']);
    $action_status_message = '<p>' . $translator->string('Deleted category:') .' <b>'. $_GET['delete'] .'</b></p>';
  }
  
} elseif (isset($_POST['add_category'])) {
  if(preg_match("/^[a-zæøåÆØÅ0-9 ]+$/i", $_POST['add_category'])) {
      $add_category = $_POST['add_category'];
      $add_category = trim($_POST['add_category']);
      $add_category = space_or_dash(' ', $add_category); // Convert space to dash
      
      $add_category = BASE_PATH . $add_category;
      if (!file_exists($add_category)) {
        mkdir($add_category, 775);
        $action_status_message = '<p>' . $translator->string('Created category:') .' <b>'. $_POST['add_category'] .'</b></p>';
      } else {
        $action_status_message = '<p><b>'.$_POST['add_category'] .'</b> '. $translator->string('already exists.') . '</p>';
      }
  } else {
    $action_status_message = '<p>' . $translator->string('Invalid category name:') .' <b>'. $_POST['add_category'] .'</b></p>';
  }
}
if (!empty($action_status_message)) {
    $action_status_message = '<div id="action_status_message">' . $action_status_message . '</div>';
}


// <<<<<<<<<<<<<<<
// Show Upload Form if logged in
// >>>>>>>>>>>>>>>
$categories = list_dirs();
$select_category = '<select name="category">';
foreach ($categories as &$value) {
  if ((isset($_SESSION["selected_category"])) && ($_SESSION["selected_category"] == $value)) {
   $selected=" selected";
  } else {$selected = '';}
  if (!isset($ignored_categories_and_files["$value"])) {
      $select_category .= '<option value="'.$value.'"'.$selected.'>'.space_or_dash('-', $value).'</option>';
  }
}
$select_category .= '</select>';

$HTML_article_content = $action_status_message;
$HTML_article_content .= '
<div class="flexbox">
  <section class="column">
    <h2>'.$translator->string("Upload file:").'</h2>
    <form action="upload.php" method="post" enctype="multipart/form-data" id="uploadForm">
      <label>'.$translator->string("Upload file:").'</label>
      <input type="file" name="fileToUpload" id="fileToUpload">
      <label>'.$translator->string("Place in category:").'</label>'. $select_category.'
      <button class="button" onClick="uploadFile()" type="button">Upload</button>
    </form>
  </section>
  <section class="column">
    <h2>'.$translator->string("Create category:").'</h2>
    <form action="admin.php" method="post" enctype="multipart/form-data" id="add_categoryForm">
      <label>'.$translator->string("Category name:").'</label>
      <input type="text" name="add_category">
      <input type="submit" class="button" value="'.$translator->string("Create:").'">
    </form>
  </section>
</div>';

$HTML_navigation = '<li><a href="/">'.$translator->string('Home').'</a></li>';
$HTML_navigation .= '<li><a href="index.php">'.$translator->string('Categories').'</a></li>';
$HTML_navigation = '<ol class="flexbox">'.$HTML_navigation.'</ol>';

// :::Functions:::
function space_or_dash($replace_this='-', $in_this) {
    if ($replace_this=='-') {
      return preg_replace('/([-]+)/', ' ', $in_this);
    } elseif ($replace_this==' ') {
      return preg_replace('/([ ]+)/', '-', $in_this);
    }
}
function list_dirs() {
  $item_arr = array_diff(scandir(BASE_PATH), array('..', '.'));
  foreach ($item_arr as $key => $value) {
      if (!is_dir(BASE_PATH . '/' . $value)) {
      unset($item_arr["$key"]);
    }
  }
  return $item_arr;
}

header("Cache-Control: no cache");
require BASE_PATH . 'templates/'.$template.'/admin_template.php';

<?php
$settings = array();
$settings['install_dir'] = 'gallery/';

require $_SERVER["DOCUMENT_ROOT"] . $settings['install_dir'] . 'settings.php';

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
    echo '<!doctype html><html><head><title>Login</title></head>
    <body><h1>Login</h1><p>You must be logged in to view this page.</p>
   <form action="" method="POST">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" class="button">
   </form>
</body></html>';exit();
  }
} else {
  // If logged in

  if (isset($_GET['delete'])) {
    unlink($_GET['delete']);
    echo 'Deleted!';exit();
  }

}



// <<<<<<<<<<<<<<<
// Show Upload Form if logged in
// >>>>>>>>>>>>>>>
$categories = list_dirs($settings);
$select_category = '<select name="category">';
foreach ($categories as &$value) {
  if ((isset($_SESSION["selected_category"])) && ($_SESSION["selected_category"] == $value)) {
   $selected=" selected";
  } else {$selected = '';}
  if ($value !== 'thumbnails') {
    $select_category .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>';
  }
}
$select_category .= '</select>';

function list_dirs($settings) {
  $directory = $_SERVER["DOCUMENT_ROOT"] . $settings['install_dir'];
  $item_arr = array_diff(scandir($directory), array('..', '.'));
  foreach ($item_arr as $key => $value) {
    if (!is_dir($directory . '/' . $value)) {
      unset($item_arr["$key"]);
    }
  }
  return $item_arr;
}

header("Cache-Control: no cache");
require $_SERVER["DOCUMENT_ROOT"] . $settings['install_dir'] . 'templates/default/admin_template.php';

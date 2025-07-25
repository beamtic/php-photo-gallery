<?php

define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');

require BASE_PATH . 'includes/settings.php';
$action_status_message = '';
$translator = new translator($settings['lang']);

if ((!isset($_SESSION["password"])) || ($_SESSION["password"] !== $password)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ((isset($_POST["password"])) && (password_verify($_POST["password"], $password))) {
      $_SESSION["password"] = $password;
    } else {
    echo 'Wrong password?';exit();
    }
  } else {
    echo '<!doctype html><html><head><title>'.$translator->string('Login').'</title></head>
    <body><h1>'.$translator->string('Login').'</h1><p>'.$translator->string('You must be logged in to access this page!').'</p>
   <form action="" method="POST">
    <input type="password" placeholder="Password" name="password">
    <button type="submit" class="button">'.$translator->string('Login').'</button>
   </form>
</body></html>';exit();
  }
}
// <<<<<<<<<<<<<<<
// ADMIN ACTIONS (Available when logged in)
// >>>>>>>>>>>>>>>
if (isset($_GET['delete'])) {
  // Note. "$delete_this" must be relative
  $delete_this = $_GET['delete'];
  if (!is_dir(BASE_PATH . 'gallery/' . $delete_this)) {
    if (file_exists(BASE_PATH . 'gallery/' . $delete_this)) {
      // echo 'thumbnails/thumb-'.$_GET['delete'];exit();
      if(!simple_delete(BASE_PATH . 'gallery/' . $delete_this)) {
        $action_status_message = '<p>' . $translator->string('Possible problem with file permissions, or directory does not exist.') .'</p><p><b>'. $delete_this .'</b></p>';
      } else {
        $action_status_message = '<p>' . $translator->string('Deleted file:') .' <b>'. $delete_this .'</b></p>';
        $delete_this_thumb = str_lreplace('/', '/thumb-', $delete_this);
        simple_delete(BASE_PATH . 'thumbnails/'.$delete_this_thumb);// Delete thumbnail
      }
    } else {
      $action_status_message = '<p>' . $translator->string('File does not exist:') . ' <b>'. $delete_this .'</b></p>';
    }
  } else {
      if(!simple_delete(BASE_PATH . 'gallery/'. $delete_this)) {
        $action_status_message = '<p>' . $translator->string('Possible problem with file permissions, or directory does not exist.') .'</p><p><b>'. $delete_this .'</b></p>';
      } else {
        $action_status_message = '<p>' . $translator->string('Deleted category:') .' <b>'. $delete_this .'</b></p>';
      }
    simple_delete(BASE_PATH . 'thumbnails/'.$delete_this);
  }
  
} elseif (isset($_POST['add_category'])) {
  if(preg_match("/^[a-zæøåÆØÅ0-9 ]+$/i", $_POST['add_category'])) {
      $add_category = $_POST['add_category'];
      $add_category = trim($_POST['add_category']);
      $add_category = space_or_dash(' ', $add_category); // Convert space to dash
      $add_category = strtolower($add_category);
      
      $add_category = BASE_PATH . 'gallery/'. $add_category;
      if (!file_exists($add_category)) {
        mkdir($add_category, 0775, true);
        chmod($add_category, 0775); // We need to change permissions of the directory using chmod
                                    // after creating the directory, on some hosts
        $action_status_message = '<p>' . $translator->string('Created category:') .' <b>'. $_POST['add_category'] .'</b></p>';
      } else {
        $action_status_message = '<p><b>'.$_POST['add_category'] .'</b> '. $translator->string('already exists.') . '</p>';
      }
  } else {
    $action_status_message = '<p>' . $translator->string('Invalid category name:') .' <b>'. $_POST['add_category'] .'</b></p>';
  }
} elseif (null !== $thumbnails_path && isset($_GET['set_preview_image'])) {
    if (file_exists($thumbnails_path . $category_json_file)) {
        $category_data = json_decode(file_get_contents($thumbnails_path . $category_json_file), true);
        $category_data['preview_image'] = 'thumb-'.$_GET['set_preview_image'];
    } else {
        $category_data = array('preview_image' => 'thumb-'.$_GET['set_preview_image']);
    }
    
    $category_data = json_encode($category_data);
    file_put_contents($thumbnails_path . $category_json_file, $category_data);
    
    $action_status_message = '<p>'.$translator->string('The category preview image was changed in: ') .'<b>'.$_GET['category'].'</b></p>';
}
if (!empty($action_status_message)) {
    $action_status_message = '<div id="action_status_message">' . $action_status_message . '</div>';
}


// <<<<<<<<<<<<<<<
// Show Upload Form if logged in
// >>>>>>>>>>>>>>>
$categories = list_dirs();
$select_category = '<select name="category">';
$selected = (($_SESSION["selected_category"] ?? null) === "") ? ' selected' : '';
$select_category .= '<option value=""'.$selected.'>'.$translator->string("Gallery Root").' (/)</option>';
foreach ($categories as &$value) {
  if ((isset($_SESSION["selected_category"])) && ($_SESSION["selected_category"] == $value)) {
   $selected=" selected";
  } else {$selected = '';}
   $select_category .= '<option value="'.$value.'"'.$selected.'>'.space_or_dash('-', $value).'</option>';
}
$select_category .= '</select>';

$HTML_article_content = '';
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
      <input type="submit" class="button" value="'.$translator->string("Create").'">
    </form>
  </section>
</div>';

$HTML_navigation = '<li><a href="/">'.$translator->string('Home').'</a></li>';
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
  $item_arr = array_diff(scandir(BASE_PATH . 'gallery/'), array('..', '.'));
  foreach ($item_arr as $key => $value) {
      if (!is_dir(BASE_PATH . 'gallery/' . $value)) {
      unset($item_arr["$key"]);
    }
  }
  return $item_arr;
}
function str_lreplace($search, $replace, $subject) {
  $pos = strrpos($subject, $search);
  if($pos !== false){
    $subject = substr_replace($subject, $replace, $pos, strlen($search));
  }
  return $subject;
}
function simple_delete($file_or_dir) {
  if (is_writable($file_or_dir)) { // Check if directory/file is writeable (required to delete file)
    if (is_dir($file_or_dir)) { // If directory, check for subdirectories
      $objects = scandir($file_or_dir); // Check for contained directories and files
      foreach ($objects as $object) {
        if(($object !== '.') && ($object !== '..')) {
          if (is_dir($file_or_dir.'/'.$object)) { // Handle subdirectories too (if any)
            simple_delete($file_or_dir.'/'.$object); // If dealing with a subdirectory, perform another simple_delete()
          } else {
            if (is_writable($file_or_dir.'/'.$object)) {
              unlink($file_or_dir.'/'.$object);
            }
          }
        }
      }
      rmdir($file_or_dir);
      return true;
    } else {
      unlink($file_or_dir); // simple_delete() also works on single files
      return true;
    }
  } else {return false;}
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require BASE_PATH . 'templates/'.$template.'/admin_template.php';

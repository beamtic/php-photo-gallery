<?php
// Remove trailing slashes (if present), and add one manually.
// Note: This avoids a problem where some servers might add a trailing slash, and others not..
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');

require BASE_PATH . 'settings.php'; // Note. Include a file in same directory without slash in front of it!
require BASE_PATH . '_lib_/translator_class.php';
$translator = new translator($settings['lang']);
session_start();
// <<<<<<<<<<<<<<<<<<<<
// Validate the _GET category input for security and error handling
// >>>>>>>>>>>>>>>>>>>>
$HTML_navigation = '<li><a href="/">'.$translator->string('Home').'</a></li>';

if (isset($_GET['category'])) {
  $HTML_navigation .= '<li><a href="index.php">'.$translator->string('Categories').'</a></li>';
  if (preg_match("/^[a-zæøåÆØÅ-]+$/i", $_GET['category'])) {
    $requested_category = $_GET['category'];
    if (isset($ignored_categories_and_files["$requested_category"])) {
      header("HTTP/1.0 500 Internal Server Error");
      echo '<!doctype html><html><head></head><body><h1>'.$translator->string('Error').'</h1><p>'.$translator->string('This is not a file or category...').'</p></body></html>';
      exit();
    }
    // <<<<<<<<<<<<<<<<<<<<
    // Fetch the files in the category, and include them in an HTML ul list
    // >>>>>>>>>>>>>>>>>>>>
    $files = list_files($settings, $ignored_categories_and_files);
    if (count($files) >= 1) {
        $HTML_cup = '<ul id="images">';
        foreach ($files as &$file_name) {
            if (isset($_SESSION["password"])) {
                $delete_control = '<a href="admin.php?delete='.$requested_category .'/'. $file_name.'" class="delete"><img src="delete.png" alt="delete" style="width:30px;height:30px;"></a>';
            } else {$delete_control='';}
            $thumb_file_location = 'thumbnails/' . $requested_category . '/thumb-' . $file_name;
            $source_file_location = $requested_category . '/' . $file_name;
            $HTML_cup .= '<li><a href="viewer.php?category='.$requested_category.'&filename='.$file_name.'"><img src="'.$thumb_file_location.'" alt="'.$file_name.'"></a>'.$delete_control.'</li>';
        }
        $HTML_cup .= '</ul>';
    } else {
        $HTML_cup = '<p>'.$translator->string('There are no files in:').' <b>' . space_or_dash('-', $requested_category) . '</b></p>';
    }
  } else {
    header("HTTP/1.0 500 Internal Server Error");
    echo '<!doctype html><html><head></head><body><h1>Error</h1><p>Invalid category</p></body></html>';
    exit();
  }
} else { // If no category was requested
    // <<<<<<<<<<<<<<<<<<<<
    // Fetch categories, and include them in an HTML ul list
    // >>>>>>>>>>>>>>>>>>>>
  $requested_category = 'Categories';
  $categories = list_directories($ignored_categories_and_files);
  if (count($categories) >= 1) {
    $HTML_cup = '<ul id="categories">';
    foreach ($categories as &$category_name) {
        $category_preview_images = category_previews($category_name, $ignored_categories_and_files);
        echo $category_preview_images;exit(); // Testing category views
        $HTML_cup .= '<li><div><a href="index.php?category='.$category_name.'" class="">'.space_or_dash('-', $category_name).'</a></div></li>';
    }
    $HTML_cup .= '</ul>';
  } else {
    $HTML_cup = '<p>'.$translator->string('There are no categories yet...').'</p>';
  }
}
$HTML_navigation = '<ol class="flexbox">'.$HTML_navigation.'</ol>';

// ====================
// Functions
// ====================
function space_or_dash($replace_this='-', $in_this) {
  if ($replace_this=='-') {
    return preg_replace('/([-]+)/', ' ', $in_this);
  } elseif ($replace_this==' ') {
    return preg_replace('/([ ]+)/', '-', $in_this);
  }
}
function list_files($settings, $ignored_categories_and_files) {
  $directory = BASE_PATH . $_GET['category'];
  $thumbs_directory = BASE_PATH . 'thumbnails/' . $_GET['category'];
  $item_arr = array_diff(scandir($directory), array('..', '.'));
  foreach ($item_arr as $key => $value) {
      if ((is_dir($directory . '/' . $value)) || (isset($ignored_categories_and_files["$value"]))) {
      unset($item_arr["$key"]);
    } else {
      $path_to_file = $thumbs_directory . '/thumb-' . $value;
      if (file_exists($path_to_file) !== true) {
        createThumbnail($value, $directory, $thumbs_directory, 400, 400);
      }
    }
  }
  return $item_arr;
}
function category_previews($category, $ignored_categories_and_files) {
    $thumbs_directory = BASE_PATH . 'thumbnails/' . $category;
    $item_arr = array_diff(scandir($directory), array('..', '.'));
    $previews_html = '';
    foreach ($item_arr as $key => $value) {
      if ((is_dir($thumbs_directory . '/' . $value)) || (isset($ignored_categories_and_files["$value"]))) {
        unset($item_arr["$key"]);
      } else {
        $previews_html = '<div style="background:url('.$item_arr["$key"].');width:25%;height:25%;" class="category_preview_img"></div>';
      }
    }
    return $previews_html;
}
function list_directories($ignored_categories_and_files) {
    $item_arr = array_diff(scandir(BASE_PATH), array('..', '.'));
    foreach ($item_arr as $key => $value) {
        if ((is_dir(BASE_PATH . '/' . $value)==false) || (isset($ignored_categories_and_files["$value"]))) {unset($item_arr["$key"]);}
    }
    return $item_arr;
}

function createThumbnail($filename, $source_directory, $thumbs_directory, $max_width, $max_height) {
    $path_to_source_file = $source_directory . '/' . $filename;
    $path_to_thumb_file = $thumbs_directory . '/thumb-' . $filename;
    $source_filetype = exif_imagetype($path_to_source_file);
    if(file_exists($thumbs_directory) !== true) {
        if (!mkdir($thumbs_directory, 0777, true)) {
            echo $translator->string('Error: The thumbnails directory could not be created.');exit();
        }
    }
    // Create the thumbnail ----->>>>
    list($orig_width, $orig_height) = getimagesize($path_to_source_file);
    $width=$orig_width;$height=$orig_height;
    
    if ($height > $max_height) { // taller
      $width = ($max_height / $height) * $width;
      $height = $max_height;
    }
    if ($width > $max_width) { // wider
      $height = ($max_width / $width) * $height;
      $width = $max_width;
    }
    $image_p = imagecreatetruecolor($width, $height);
    
    switch ($source_filetype) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($path_to_source_file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                $width, $height, $orig_width, $orig_height);
            imagejpeg($image_p, $path_to_thumb_file);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($path_to_source_file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                $width, $height, $orig_width, $orig_height);
            imagepng($image_p, $path_to_thumb_file);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($path_to_source_file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                $width, $height, $orig_width, $orig_height);
            imagegif($image_p, $path_to_thumb_file);
            break;
        default:
            echo $translator->string('Unknown filetype. Supported filetypes are: JPG, PNG og GIF.');exit();
    }
}
require BASE_PATH . 'templates/'.$template.'/category_template.php';

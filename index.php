<?php
// Remove trailing slashes (if present), and add one manually.
// Note: This avoids a problem where some servers might add a trailing slash, and others not..
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');
require BASE_PATH . 'includes/settings.php'; // Note. Include a file in same directory without slash in front of it!
require BASE_PATH . 'includes/global_functions.php';

require BASE_PATH . 'includes/dependency_checker.php';

if (session_status() == PHP_SESSION_NONE) {
  session_cache_limiter("private_no_expire");
  session_start();
}

$HTML_navigation = '';
if ("/" !== $_SERVER['REQUEST_URI']) {
  $HTML_navigation = '<li><a href="/">' . $translator->string('Home') . '</a></li>';
}

// Show categories if any
$HTML_cup = '';
$categories = list_directories($gallery_path);
$cat_count = count($categories);
if ($cat_count >= 1 && $requested_category == null) {
  $HTML_cup = '<ul id="categories">';
  foreach ($categories as &$category_name) {
    if (isset($_SESSION["password"])) {
      $delete_control = '<a href="admin.php?delete=' . $category_name . '" class="delete"><img src="delete.png" alt="delete" style="width:30px;height:30px;"></a>';
    } else {
      $delete_control = '';
    }
    $category_preview_images = category_previews($category_name, $thumbnails_path, $category_json_file);
    // echo 'cats:'.$category_preview_images; // Testing category views
    $HTML_cup .= '<li><div class="preview_images">' . $category_preview_images . '</div><div class="category"><a href="/?category=' . $category_name . '" class=""><span>' . space_or_dash('-', $category_name) . '</span></a></div>' . $delete_control . '</li>';
  }
  $HTML_cup .= '</ul>';
}
// <<<<<<<<<<<<<<<<<<<<
// Fetch the files in the category, and include them in an HTML ul list
// >>>>>>>>>>>>>>>>>>>>
$files = array_values(list_files($gallery_path));
if (count($files) >= 1) {
  $HTML_cup .= '<ul id="images">';
  foreach ($files as &$file_name) {
    $category_preview_control = '';
    $delete_control = '';
    if (isset($_SESSION["password"])) {
      $delete_control = '<a href="admin.php?delete=' . $requested_category . '/' . $file_name . '" class="delete"><img src="delete.png" alt="delete" style="width:30px;height:30px;"></a>';
      if (null !== $requested_category) {
        $category_preview_control = '<a href="admin.php?category=' . $requested_category . '&set_preview_image=' . $file_name . '" class="preview"><img src="preview.png" alt="set preview image" style="width:30px;height:30px;"></a>';
      }
    }
    $public_path = $requested_category ? 'thumbnails/' . $requested_category . '/' : 'thumbnails/';
    $thumb_filename = 'thumb-' . rawurlencode($file_name);
    $thumb_file_location = $thumbnails_path . 'thumb-' . rawurlencode($file_name);
    $source_file_location = $gallery_path . $file_name;
    if (file_exists($thumb_file_location) !== true) {
      createThumbnail($source_file_location, $thumb_file_location, 400, 400);
    }
    $view_url = $requested_category ? 'viewer.php?category=' . $requested_category . '&filename=' . $file_name : 'viewer.php?filename=' . $file_name;
    $HTML_cup .= '<li><a href="' . $view_url . '"><img src="' . $public_path . $thumb_filename . '" alt="' . $file_name . '"></a>' . $delete_control . $category_preview_control . '</li>';
  }
  $HTML_cup .= '</ul>';
} elseif (($cat_count < 1 && $requested_category == null) || $requested_category !== null) {
  $HTML_cup = '<p>' . $translator->string('There are no files in:') . ' <b>' . ($requested_category ? space_or_dash('-', $requested_category) : '/') . '</b></p>';
}
$HTML_navigation = '<ol class="flexbox">' . $HTML_navigation . '</ol>';

// ====================
// Functions
// ====================
function space_or_dash(string $replace_this, string|null $in_this): string
{
  if ($replace_this === '-') {
    return preg_replace('/-+/', ' ', $in_this);
  } elseif ($replace_this === ' ') {
    return preg_replace('/\s+/', '-', $in_this);
  }
  return $in_this;
}
function category_previews(string $category, string $thumbs_path, $category_json_file)
{
  $public_path = 'thumbnails/' . $category . '/';
  $previews_html = '';
  
  if (file_exists($thumbs_path . $category)) {
    
    if (file_exists($thumbs_path . $category . '/' . $category_json_file)) {
      $category_data = json_decode(file_get_contents($thumbs_path . $category . '/' . $category_json_file), true);
    
      $previews_html = '<div style="background:url(' . $public_path . rawurlencode($category_data['preview_image']) . ');" class="category_preview_img"></div>';
    } else {
      $files_in_dir = scandir($thumbs_path . $category);
      
      // Automatically try to select preview image if none was choosen
      $item_arr = array_diff($files_in_dir, array('..', '.'));
      if (0 === count($item_arr)) {
        return '';
      }
      foreach ($item_arr as $key => $value) {
        $previews_html = '<div style="background:url(' . $public_path . rawurlencode($item_arr["$key"]) . ');" class="category_preview_img"></div>'; // add a dot in front of = to return all images
      }
      $category_data = json_encode(array('preview_image' => $item_arr["$key"]));
      file_put_contents($thumbs_path . $category . '/' . $category_json_file, $category_data);
    }
  }
  return $previews_html;
}
function list_directories(string $gallery_path)
{
  $item_arr = array_diff(scandir($gallery_path), array('..', '.'));
  foreach ($item_arr as $key => $value) {
    if (is_dir($gallery_path . $value) === false) {
      unset($item_arr["$key"]);
    }
  }
  return $item_arr;
}
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require BASE_PATH . 'templates/' . $template . '/category_template.php';

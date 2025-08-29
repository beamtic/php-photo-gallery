<?php
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');

require BASE_PATH . 'includes/global_functions.php';
require BASE_PATH . 'includes/settings.php';
$html_content = '';
$category_items = '';
$html_backlink = '';
$next_file = false;
$previous_file = false;

$files = array_values(list_files($gallery_path, $thumbnails_path, $requested_category));
$files_count = count($files);

if ($files_count >= 1) {
  $category_items = '<ul>';
  $i = 0;
  while ($i < $files_count) {
    if ($files["$i"] == $requested_file) {
      $next_i = $i + 1;
      $previous_i = $i - 1;
      if (isset($files["$previous_i"])) {
        $previous_file = $files["$previous_i"];
      }
      if (isset($files["$next_i"])) {
        $next_file = $files["$next_i"];
      }
    } else {
      $file_name = $files["$i"];
      $file_link = (null !== $requested_category) ? 'viewer.php?category=' . $requested_category . '&filename=' . $file_name : 'viewer.php?filename=' . $file_name;
      $thumb_file_location = $public_path . 'thumb-' . $file_name;
      $source_file_location = $gallery_path . $file_name;
      $category_items .= '<li><div><a href="'. $file_link .'"><img src="' . $thumb_file_location . '" alt="' . $file_name . '"></a></div></li>';
    }
    ++$i;
  }
  $category_items .= '</ul>';
} else {
  $category_items = '';
}

$path_to_file = 'gallery/' . $requested_category . '/' . $requested_file;

if ($previous_file !== false) {
  if (null !== $requested_category) {
    $prev_link = 'viewer.php?category=' . $requested_category . '&filename=' . $previous_file;
  } else {
    $prev_link = 'viewer.php?filename=' . $previous_file;
  }
  $html_content .= '<div id="previous" class="p"><a href="'.$prev_link.'">&lt;</a></div>';
}
if ($next_file !== false) {
  if (null !== $requested_category) {
    $next_link = 'viewer.php?category=' . $requested_category . '&filename=' . $next_file;
  } else {
    $next_link = 'viewer.php?filename=' . $next_file;
  }
  $html_content .= '<div id="next"><a href="'.$next_link.'">&gt;</a></div>';
}

$html_content .= '<img src="' . $path_to_file . '" alt="' . $requested_file . '">';
$back_link =  $requested_category ? 'index.php?category=' . $requested_category : 'index.php';
$html_action_controls = '<div id="action_controls"><ul>
<li><a href="'. $back_link .'">Back</a></li>
</ul>
</div>';

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require BASE_PATH . 'templates/' . $template . '/viewer_template.php';

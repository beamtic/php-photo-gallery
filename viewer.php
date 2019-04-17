<?php
define('BASE_PATH', rtrim(realpath(dirname(__FILE__)), "/") . '/');


require BASE_PATH . 'settings.php';
$requested_category='';$requested_file='';
$html_title = 'Viewer';
$html_content = '';$category_items='';$html_backlink='';
$next_file=false;$previous_file=false;

if (
    (isset($_GET['category'])) &&
    (preg_match("/^[a-zA-ZæøåÆØÅ0-9-]+$/", $_GET['category'])) &&
    ($_GET['category'] !== 'thumbnails')) {
        
    if ((isset($_GET['filename'])) && (preg_match("/^[^\/\"'<>*]+$/", $_GET['filename']))) {  
        $requested_category = $_GET['category'];$requested_file = $_GET['filename'];
        $html_title = $requested_file . ' - ' . $requested_category . ' | ' .$html_title;
        
        $files = array_values(list_files($settings));
        $files_count = count($files);
        
        if ($files_count >= 1) {
            $category_items = '<ul>';
            $i = 0;
            while($i < $files_count) {
              if ($files["$i"] == $requested_file) {
                $next_i = $i+1;$previous_i = $i-1;
                if (isset($files["$previous_i"])) {$previous_file = $files["$previous_i"];}
                if (isset($files["$next_i"])) {$next_file = $files["$next_i"];}
              } else {
                $file_name = $files["$i"];
                $thumb_file_location = 'thumbnails/' . $requested_category . '/thumb-' . $file_name;
                $source_file_location = $requested_category . '/' . $file_name;
                $category_items .= '<li><div><a href="viewer.php?category='.$requested_category.'&filename='.$file_name.'"><img src="'.$thumb_file_location.'" alt="'.$file_name.'"></a></div></li>';
              }
              ++$i;
            }
            $category_items .= '</ul>';
        } else {$category_items='';}
        
        $path_to_file = $requested_category . '/' . $requested_file;
        
        if($previous_file !== false) {$html_content .= '<div id="previous" class="p"><a href="viewer.php?category='.$requested_category.'&filename='.$previous_file.'">&lt;</a></div>';}
        if($next_file !== false) {$html_content .= '<div id="next"><a href="viewer.php?category='.$requested_category.'&filename='.$next_file.'">&gt;</a></div>';}
        
        $html_content .= '<img src="'.$path_to_file.'" alt="'.$requested_file.'">';
        $html_action_controls = '<div id="action_controls"><ul>
<li><a href="index.php?category='.$requested_category.'">Back</a></li>
</ul>
</div>'; // <li><a href="'.$path_to_file.'">Fuld størrelse</a></li>
    } else {$html_content = '<p>Invalid filename...</p>';}
} else {$html_content = '<p>Invalid category...</p>';}

// ====================
// Functions
// Note. Besides CreateThumbnail() these functions are unique to this file
// DO NOT assume they are the same as in index.php
// If you combine and move functions to a functions.php, you will need fix code differences!
// ====================

function list_files($settings) {
    $directory = BASE_PATH . $_GET['category'];
    $thumbs_directory = BASE_PATH . 'thumbnails/' . $_GET['category'];
    $item_arr = array_diff(scandir($directory), array('..', '.'));
    foreach ($item_arr as $key => $value) {
        if (is_dir($directory . '/' . $value)) {
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

function createThumbnail($filename, $source_directory, $thumbs_directory, $max_width, $max_height) {
    $path_to_source_file = $source_directory . '/' . $filename;
    $path_to_thumb_file = $thumbs_directory . '/thumb-' . $filename;
    $source_filetype = exif_imagetype($path_to_source_file);
    if(file_exists($thumbs_directory) !== true) {
        if (!mkdir($thumbs_directory, 0777, true)) {
            echo 'Error: The thumbnails directory could not be created.';exit();
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
            echo 'Unknown filetype. Supported filetypes are: JPG, PNG og GIF.';exit();
    }
}

require BASE_PATH . 'templates/'.$template.'/viewer_template.php';
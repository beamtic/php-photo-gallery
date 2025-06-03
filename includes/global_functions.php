<?php

require_once BASE_PATH . 'lib/translator_class.php';

$translator = new translator($settings['lang']);


function respond($code, $html = '', $headers = [])
{
    $default_headers = ['content-type' => 'text/html; charset=utf-8'];
    $headers = $headers + $default_headers;
    http_response_code($code);
    foreach ($headers as $key => $value) {
        header($key . ': ' . $value);
    }
    echo $html;
    exit();
}

function list_files(string $path)
{
  if (!is_dir($path)) return [];

  if(!$item_arr = array_diff(scandir($path), array('..', '.'))) return [];

  foreach ($item_arr as $key => $value) {
    if (is_dir($path . '/' . $value)) {
      unset($item_arr["$key"]);
    }
  }
  return $item_arr;
}

function createThumbnail(string $source_file_path, string $thumb_file_path, int $max_width, int $max_height)
{
  global $translator;
  $source_filetype = exif_imagetype($source_file_path);
  // Create the thumbnail ----->>>>
  list($orig_width, $orig_height) = getimagesize($source_file_path);
  $width = $orig_width;
  $height = $orig_height;

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
      $image = imagecreatefromjpeg($source_file_path);
      imagecopyresampled(
        $image_p,
        $image,
        0,
        0,
        0,
        0,
        $width,
        $height,
        $orig_width,
        $orig_height
      );
      imagejpeg($image_p, $thumb_file_path);
      break;
    case IMAGETYPE_PNG:
      $image = imagecreatefrompng($source_file_path);
      imagecopyresampled(
        $image_p,
        $image,
        0,
        0,
        0,
        0,
        $width,
        $height,
        $orig_width,
        $orig_height
      );
      imagepng($image_p, $thumb_file_path);
      break;
    case IMAGETYPE_GIF:
      $image = imagecreatefromgif($source_file_path);
      imagecopyresampled(
        $image_p,
        $image,
        0,
        0,
        0,
        0,
        $width,
        $height,
        $orig_width,
        $orig_height
      );
      imagegif($image_p, $thumb_file_path);
      break;

    case IMAGETYPE_WEBP:
      $image = imagecreatefromwebp($source_file_path);
      imagecopyresampled(
        $image_p,
        $image,
        0,
        0,
        0,
        0,
        $width,
        $height,
        $orig_width,
        $orig_height
      );
      imagewebp($image_p, $thumb_file_path);
      break;


    default:
      echo $translator->string('Unknown filetype. Supported filetypes are: JPG, PNG or GIF.');
      exit();
  }
}
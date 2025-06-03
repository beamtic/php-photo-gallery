<?php


$password = '098f6bcd4621d373cade4e832627b4f6'; // MD5 hashed password
// 1. Create new md5 from password: /stringtomd5.php?string=xxx
// 2. Update the password above. Default password is "test".

$settings = array();
$settings['lang'] = 'da'; // Used to change site language, and on the <html> lang attribute
$settings['title'] = 'PHP Photo Gallery'; // Default: "PHP Photo Gallery"
$template = 'default'; // Default: "default"

// Files allowed for upload (Check performed in upload.php)
// Note. that this assumes that the files are trusted and not messed with
$allowed_file_types_arr = array(
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp'
    // 'avif',
);

$category_json_file = 'category_data.json';

// Dynamic settings. Might break the gallery if modified!
$gallery_path = BASE_PATH . "gallery/";
$thumbnails_path = BASE_PATH . "thumbnails/";

$requested_category = isset($_GET['category']) ? trim($_GET['category']) : null;
$requested_file = isset($_GET['filename']) ? trim($_GET['filename']) : null;
if (null === $requested_category) {
    $html_title = $requested_file . ' | Viewer';
} else if (preg_match("/^[a-zA-ZæøåÆØÅ0-9_.-]+$/", $requested_category)) {
    $html_title = $requested_file . ' - ' . $requested_category . ' | ' . $html_title;
    $gallery_path = $gallery_path . $requested_category . '/';
    $thumbnails_path = $thumbnails_path . $requested_category . '/';
} else {
    header("HTTP/1.0 500 Internal Server Error");
    echo '<!doctype html><html><head></head><body><h1>Error</h1><p>Invalid category</p></body></html>';
    exit();
}
if (null !== $requested_file && !preg_match("/^[^\/\\\\:*?\"'<>|]+$/", $requested_file)) {
    header("HTTP/1.0 500 Internal Server Error");
    echo '<!doctype html><html><head></head><body><h1>Error</h1><p>Invalid filename</p></body></html>';
    exit();
}

if (file_exists($thumbnails_path) !== true) {
    if (!mkdir($thumbnails_path, 0775, true)) {
        echo $translator->string('Error: The thumbnails directory could not be created.');
        exit();
    } else {
        // On some hosts, we need to change permissions of the directory using chmod
        // after creating the directory
        chmod($thumbnails_path, 0775);
    }
}

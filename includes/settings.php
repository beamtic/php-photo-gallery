<?php
if (session_status() == PHP_SESSION_NONE) {
  session_cache_limiter("private_no_expire"); // Must be placed before
  session_start(); // Starts the session
}

require_once BASE_PATH . 'lib/translator_class.php';

$settings = array();
$settings['lang'] = 'da'; // Used to change site language, and on the <html> lang attribute
$settings['title'] = 'PHP Photo Gallery'; // Default: "PHP Photo Gallery"
$template = 'default'; // Default: "default"
$settingsFile = BASE_PATH . '.settings.json';
$translator = new translator($settings['lang']);

if (!file_exists($settingsFile)) {
    require BASE_PATH . 'includes/setup.php';
    exit();
} else {
    $customSettingsContent = file_get_contents($settingsFile);
    $settings = json_decode($customSettingsContent, true);
    $template = $settings['template'];
    $password = $settings['password'];
}

// If setup is requested
if (isset($_GET['settings']) && $_GET['settings'] == true && file_exists($settingsFile)) {
    // When the settings file exists, require authentication to make changes
    require BASE_PATH . 'includes/setup.php';
    exit();
}

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

$gallery_path = BASE_PATH . "gallery/";
$thumbnails_path = BASE_PATH . "thumbnails/";

$requested_category = isset($_GET['category']) ? trim($_GET['category']) : null;
$requested_file = isset($_GET['filename']) ? trim($_GET['filename']) : null;

if (null === $requested_category) {
    $html_title = $requested_file . ' | Viewer';
    $public_path = 'thumbnails/';
} else if (preg_match("/^[a-zA-ZæøåÆØÅ0-9_.-]+$/", $requested_category)) {
    $html_title = $requested_file . ' - ' . $requested_category . ' | ' . $html_title;
    $gallery_path = $gallery_path . $requested_category . '/';
    $thumbnails_path = $thumbnails_path . $requested_category . '/';
    $public_path = 'thumbnails/' . $requested_category . '/';
} else {
    header("HTTP/1.0 500 Internal Server Error");
    echo '<!doctype html><html><head></head><body><h1>'.$translator->string('Error').'</h1><p>'.$translator->string('Invalid category').'</p></body></html>';
    exit();
}
if (null !== $requested_file && !preg_match("/^[^\/\\\\:*?\"'<>|]+$/", $requested_file)) {
    header("HTTP/1.0 500 Internal Server Error");
    echo '<!doctype html><html><head></head><body><h1>'.$translator->string('Error').'</h1><p>'.$translator->string('Invalid filename').'</p></body></html>';
    exit();
}


foreach ([$gallery_path, $thumbnails_path] as $path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0777, true)) {
            echo $translator->string('Error: The directory could not be created: ') . htmlspecialchars($path);
            exit();
        } else {
            chmod($path, 0777);
        }
    }
}

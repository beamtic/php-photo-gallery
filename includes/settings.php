<?php


$password = '098f6bcd4621d373cade4e832627b4f6'; // MD5 hashed password
 // 1. Create new md5 from password: /stringtomd5.php?string=xxx
 // 2. Update the password above. Default password is "test".

$settings = array();
$settings['lang'] = 'en'; // Used to change site language, and on the <html> lang attribute
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
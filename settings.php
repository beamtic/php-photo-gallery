<?php


$password = '098f6bcd4621d373cade4e832627b4f6'; // MD5 hashed password
 // 1. Create new md5 from password: /stringtomd5.php?string=xxx
 // 2. Update the password above. Default password is "test".

$settings = array();
$settings['lang'] = 'en'; // Used to change site language, and on the <html> lang attribute
$settings['title'] = 'PHP Photo Gallery'; // Default: "PHP Photo Gallery"
$template = 'default'; // Default: "default"

$ignored_categories_and_files = array();

$ignored_categories_and_files['thumbnails'] = true;
$ignored_categories_and_files['templates'] = true;
$ignored_categories_and_files['.git'] = true; // Ignore the .git directory if it exists
$ignored_categories_and_files['category_data.json'] = true; // Used for category preview images and descriptions. Etc.
$ignored_categories_and_files['_lib_'] = true;
$ignored_categories_and_files['_translations_'] = true;

$category_json_file = 'category_data.json';
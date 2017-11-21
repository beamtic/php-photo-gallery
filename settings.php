<?php


$password = ''; // MD5 hashed password

$settings = array();
$settings['lang'] = 'en'; // Used to change site language, and on the <html> lang attribute
$settings['title'] = 'Beamtic PHP Gallery'; // Default: "Beamtic PHP Gallery"
$template = 'default'; // Default: "default"

$ignored_categories_and_files = array();

$ignored_categories_and_files['thumbnails'] = true;
$ignored_categories_and_files['templates'] = true;
$ignored_categories_and_files['_lib_'] = true;
$ignored_categories_and_files['_translations_'] = true;
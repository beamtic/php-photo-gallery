<?php
$password = ''; // MD5 hashed password

$settings['lang'] = 'en'; // Used on the HTML lang attribute
  // Note.. Currently there's no translations, so you can simply change the strings directly in the source files
  // (There are not that many) - if you decide to change language :-)
$settings['title'] = 'Beamtic PHP Gallery';

$ignored_categories_and_files = array();
$ignored_categories_and_files['thumbnails'] = true;
$ignored_categories_and_files['templates'] = true;

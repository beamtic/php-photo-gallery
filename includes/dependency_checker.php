<?php

if(!is_callable('imagecreatetruecolor')) {
    $html = '<p><b>imagecreatetruecolor</b> '.$translator->string('not found. Please make sure that the GD extension is installed'). '</p>';
    $html .= '<p>'.$translator->string('If you are on Debian/Ubuntu based systems, you may be able to installe it with the following command:') . '</p>';
    $html .= '<pre style="padding:0.5rem;margin-top:1rem;">sudo apt install PHP7.4-gd</pre>';
    $html .= '<p>'. $translator->string('You should make sure to install the version of GD that corresponds to your PHP version. E.g:') . '</p>';
    $html .= '<pre style="padding:0.5rem;margin-top:1rem;">sudo apt install PHP8.0-gd</pre>';
    respond(500, $html);
}


// Directories
$gallery_dir = BASE_PATH . 'gallery/';
$translations_dir = BASE_PATH . 'translations/';

if (!file_exists($gallery_dir)) {
    if(!mkdir($gallery_dir)) {
        $html = '<p>'.$translator->string('The <b>gallery</b> directory was missing, and could not be created. Please make sure the installation path is writable. As a minimum the <b>gallery</b> and <b>thumbnail</b> directories should be writable.') .'</p>';
        $html .= '<p>'. $translator->string('If using Apache on debian/Ubuntu based systems:') . '</p>';
        $html .= '<pre style="padding:0.5rem;margin-top:1rem;">sudo chmod 775 -R /var/www/my-site-name' . "\n";
        $html .= 'sudo chown www-data:www-data -R /var/www/my-site-name</pre>';
        respond(500, $html);
    }
    mkdir($gallery_dir, 0777);
    chmod($gallery_dir, 0777);
}

if (!is_writable($translations_dir)) {
        $html = '<p>'.$translator->string('The <b>translations</b> directory is not writable. Please make sure the directory is writable.') .'</p>';
        $html .= '<p>'. $translator->string('If using Apache on debian/Ubuntu based systems:') . '</p>';
        $html .= '<pre style="padding:0.5rem;margin-top:1rem;">sudo chmod 775 -R /var/www/my-site-name/translations' . "\n";
        $html .= 'sudo chown www-data:www-data -R /var/www/my-site-name/translations</pre>';
        respond(500, $html);
}
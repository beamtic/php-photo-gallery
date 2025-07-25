<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $updatedSettings = [
    'lang' => !empty($_POST['lang']) ? $_POST['lang'] : 'en',
    'title' => !empty($_POST['title']) ? $_POST['title'] : 'Gallery',
    'template' => !empty($_POST['template']) ? $_POST['template'] : 'default'
  ];

  // If file does not exist, require a password
  if (!file_exists(BASE_PATH . '.settings.json')) {
    if (empty($_POST['password']) || strlen($_POST['password']) < 4) {
      echo 'Please provide a password of at least 4 characters in length.';
      exit();
    } else {
      $updatedSettings['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
    }
    $settings = $updatedSettings;
  } else {
    // If the file exists, password will remain the same when not provided
    $customSettingsContent = file_get_contents(BASE_PATH . '.settings.json');
    $customSettings = json_decode($customSettingsContent, true);
    $settings = $updatedSettings + $customSettings;
  }

  file_put_contents(BASE_PATH . '.settings.json', json_encode($settings));
}

$selectedLanguage = $settings['lang'];

if (file_exists(BASE_PATH . 'translations/names/' . $selectedLanguage . '.php')) {
  require BASE_PATH . 'translations/names/' . $selectedLanguage . '.php';
} else {
  require BASE_PATH . 'translations/names/en.php'; // Fallback if the translation is missing
}

$templateDir = BASE_PATH . 'templates/';
$langDir = BASE_PATH . 'translations/';

$langDirItems = scandir($langDir);
$languageList = [];

foreach ($langDirItems as $item) {
  if ($item !== '.' && $item !== '..' && !is_dir($langDir . '/' . $item)) {
    $langCode = pathinfo($langDir . '/' . $item, PATHINFO_FILENAME);
    $languageList[$langCode] = $langNamesList[$langCode];
  }
}

$templateList = array_filter(scandir($templateDir), function ($item) use ($templateDir) {
  return is_dir($templateDir . '/' . $item) && $item !== '.' && $item !== '..';
});


require BASE_PATH . 'templates/' . $template . '/setup_template.php';

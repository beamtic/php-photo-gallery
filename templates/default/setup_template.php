<!DOCTYPE html>
<html lang="<?php echo $settings['lang']; ?>">

<head>
  <title><?php echo $translator->string('Administration'); ?> | <?php echo $translator->string('Settings'); ?></title>
  <link rel="stylesheet" href="templates/default/gallery.css">
  <style>
    .setting {
      padding: 0.2rem 0;
    }
    .setting label {
      width: 12rem;
      display: inline-block
    }
  </style>
</head>

<body>
  <header id="site_header">
    <h1><?php echo $settings['title']; ?></h1>
    <div class="clear"></div>
  </header>
  <article>
    <div id="main">
      <form method="post">
        <div class="setting">
          <label for="title"><?php echo $translator->string('Gallery title:'); ?></label>
          <input type="text" name="title" id="title" value="<?php echo $settings['title']; ?>">
        </div>
        <div class="setting">
          <label for="language"><?php echo $translator->string('Language:'); ?></label>
          <select name="lang" id="language">
            <?php
            $selectedOption = '';
            foreach ($languageList as $langCode => $languageName) {
              if ($langCode == $settings['lang']) {
                $selectedOption = ' selected';
              }
              echo '<option value="' . $langCode . '"'.$selectedOption.'>' . $languageName . '</option>';
              $selectedOption = '';
            }
            ?>
          </select>
        </div>
        <div class="setting">
          <label for="template"><?php echo $translator->string('Template:'); ?></label>
          <select name="template" id="template">
            <?php
            foreach ($templateList as $templateName) {
              if ($templateName == $settings['template']) {
                $selectedOption = ' selected';
              }
              echo '<option value="' . $templateName . '"'.$selectedOption.'>' . $templateName . '</option>';
              $selectedOption = '';
            }
            ?>
          </select>
        </div>
        <div class="setting">
          <label for="password"><?php echo $translator->string('Password:'); ?></label>
          <input type="password" name="password" id="password">
        </div>
        <input type="submit" class="button" value="<?php echo $translator->string('Save'); ?>">
      </form>
    </div>
  </article>
  <footer>
    <nav>
      <ol>
        <li><a href="admin.php"><?php echo $translator->string('Administration'); ?></a></li>
      </ol>
    </nav>
  </footer>

</body>

</html>
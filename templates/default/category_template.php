<!DOCTYPE html>
<html lang="<?php echo $settings['lang']; ?>">

 <head>
  <title><?php echo $settings['title']; ?></title>
  <link rel="stylesheet" href="templates/default/gallery.css">
 </head>

 <body>
  <header id="site_header">
   <h1><?php echo $settings['title']; ?></h1>
   <nav><?php echo $HTML_navigation; ?></nav>
   <div class="clear"></div>
  </header>
  <article>
    <header><h1><?php echo $requested_category; ?></h1></header>
    <div id="main">
      <?php echo $HTML_cup; ?>
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
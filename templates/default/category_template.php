<!DOCTYPE html>
<html lang="<?php echo $settings['lang']; ?>">

 <head>
  <title><?php echo $settings['title']; ?></title>
  <link rel="stylesheet" href="templates/default/gallery.css">
 </head>

 <body>
  <header id="site_header">
   <h1><?php echo $settings['title']; ?></h1>
   <nav>
    <ol>
     <li><a href="/">HOME</a></li>
    </ol>
   </nav>
   <div class="clear"></div>
  </header>
  <article>
    <header><h1><?php echo $requested_category; ?></h1></header>
    <?php echo $HTML_cup; ?>
  </article>
  <footer>
   <nav>
    <ol>
     <li><a href="admin.php">Administration</a></li>
    </ol>
   </nav>
  </footer>
 </body>

</html>
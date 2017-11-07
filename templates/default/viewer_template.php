<!doctype html>
<html lang="da">
 <head>
   <title><?php echo $html_title; ?></title>
   <style type="text/css">
   * {margin:0;padding:0;}
   p {color:#fff;}
   body {background:rgb(5,5,5);overflow-y:scroll;}
   article,footer,header {position:relative;width:70%;margin:25px auto;max-width:1100px;min-width:400px;}
   article div, header div {position:absolute;font-weight:bold;border-radius:10px;}
   article {overflow:hidden;}
   article div {font-size:3em;}
   header div {top:1.5em;left:3em;z-index:2;}
   header div li {max-width:7em;}
   header div a {padding:0.7em;display:block;text-align:center;}
   a {text-decoration:none;}
   article div a {display:block;width:100%;height:100%;padding:0.5em;overflow:hidden;box-sizing:border-box;}
   article div a:link, header div a:link {color:rgb(200,200,200);background:rgba(140,140,140,0.6);border-radius:1vw;}
   article div a:visited, header div a:visited {color:rgb(200,200,200);}
   article div a:hover, header div a:hover {color:rgb(200,200,200);background:rgba(170,170,170,0.6);color:#fff;}
   article div a:active, header div a:active {color:rgb(200,200,200);}
   header div a {color:#fff !important;}
   article img {width:100%;}
   #next {top:3.5em;right:1%;}
   #previous {top:3.5em;left:1%;}
   ul,ol {list-style-type:none;}
   ul {display:flex;flex-wrap:wrap;flex-basis:200px;justify-content:center;}
   footer li img {width:100%;border-radius:10px;}
   footer li img:hover {filter:grayscale(65%);}
   footer li {padding:0.5em;width:200px;}
   footer li div {height:220px;overflow:hidden;border-radius:10px;}
   </style>
 </head>
 <body>
  <header><?php echo $html_action_controls; ?></header>
  <article>
    <?php echo $html_content; ?>
  </article>
  <footer>
  <?php echo $category_items; ?>
  </footer>
 </body>
</html>
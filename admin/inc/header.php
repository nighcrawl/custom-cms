<?php global $modules; ?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="fr"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>CBC Content Manager</title>
  <meta name="description" content="">
  <meta name="author" content="Ange Chierchia, CBC Informatique">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="<?=ADMIN_URL;?>css/style.css">
  <link rel="stylesheet" href="<?=ADMIN_URL;?>js/uploadify/uploadify.css">
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="<?=ADMIN_URL;?>js/modernizr-2.0.6.min.js"></script>
  <script src="<?=ADMIN_URL;?>js/ckeditor/ckeditor.js"></script>

</head>

<body>

  <div id="container">
    <header role="banner">
    	<div class="container_16">
          <p class="grid_3 admin_logo"><a href="<?=ADMIN_URL;?>">CBC Content Manager</a></p>
          <?php if(isset($_SESSION['adm'])): ?>
          <nav class="grid_8 alpha omega">
                <ul>
                    <li><a href="index.php?module=pages">Pages</a></li>

                    <?php if(in_array('produits', $modules)): ?>
                    <li><a href="index.php?module=produits">Produits</a></li>
                    <?php endif; ?>

                    <?php if(in_array('categories', $modules)): ?>
                    <li><a href="index.php?module=categories">Cat&eacute;gories</a></li>
                    <?php endif; ?>

                    <?php if(in_array('commandes', $modules)): ?>
                    <li><a href="index.php?module=commandes">Commandes</a></li>
                    <?php endif; ?>

                    <?php if(in_array('news', $modules)): ?>
                    <li><a href="index.php?module=news">Actualit&eacute;s</a></li>
                    <?php endif; ?>

                    <?php if(in_array('slideshow', $modules)): ?>
                    <li><a href="index.php?module=slideshow">Banni&egrave;re anim&eacute;e</a></li>
                    <?php endif; ?>

                    <?php if(in_array('photos', $modules)): ?>
                    <li><a href="index.php?module=photos">Galeries photos</a></li>
                    <?php endif; ?>

                    <?php if(in_array('infos', $modules)): ?>
                    <li><a href="index.php?module=infos">Infos g&eacute;n&eacute;rales</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
          <p class="grid_6 alignright" id="infos_connect">Bonjour, <span class="user"><?=$_SESSION['adm']['nom'];?></span>. <a href="<?=SITE_URL;?>">Voir le site</a> <span class="separator">|</span> <a href="<?=ADMIN_URL;?>index.php?a=logout">D&eacute;connexion.</a></p>
          <?php else : ?>
          <span class="grid_13">&nbsp;</span>
          <?php endif; ?>
        </div>
    </header>
    <div id="main" role="main" class="container_16">
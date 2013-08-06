<?php
/***
 *
 *  admin/modules/adm.dashboard.php    
 *  Dernière modification : 11/09/2012
 *
 *  Note: n'est plus utiliser comme page d'accueil du CMS.
 *        Remplacé par admin/modules/adm.pages.php
 */

<div class="grid_8 alpha">
    <h1>Bienvenue</h1>
    <p>Si vous voyez cette page, vous venez de vous connecter à votre interface d'administration. C'est depuis cette interface que vous allez pouvoir modifier les textes, images et autres contenus publiés sur votre site (<?=SITE_URL;?>).</p>
    <p>Cette interface vous permet notamment de créer/modifier/supprimer des pages, des produits et cat&eacute;gories de produit, g&eacute;rer vos commandes et vos clients en cliquant sur les différents liens du menu horizontal en haut de cette page. 
    <br />Aussi vous avez la possibilité de modifier vos informations de contact publiées sur le site.</p>
</div>
<div class="grid_6 prefix_2">
    <?php $stats = stats();
    echo "<h2>Le site en quelques chiffres</h2>
    <p>Actuellement, il y a sur le site :</p>
    <ul>
        <li>".intval($stats['public']['pages'])." pages publi&eacute;es</li>
        <li>".intval($stats['public']['produits'])." produits publi&eacute;es</li>
        <li>".intval($stats['public']['commandes'])." commandes pass&eacute;es</li>
        <li>".intval($stats['public']['clients'])." clients</li>
    </ul>";
    
    echo "<p>Brouillons :</p>
    <ul>
        <li>".intval($stats['prive']['pages'])." pages non publi&eacute;es</li>
        <li>".intval($stats['prive']['produits'])." produits en brouillon</li>
    </ul>
</div>";
?>
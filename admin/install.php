<?php 
/***
 *
 *	admin/install.php	
 *	Dernière modification : 02/08/2013
 *
 *	Script d'installation du Système de gestion de contenu.
 *	Pour installer, il suffit de renseigner les varialbes $modules,
 *	$superadmin et $client login dans le fichier config.php à la racine.
 *
 */

/* ===================================
   NE RIEN MODIFIER APRES CETTE LIGNE
   =================================*/


if(isset($_GET['wait']) && $_GET['wait'] == '1'):
	//on affiche un message pour prévenir que l'installation du CMS va commencer
	echo "<h1>L'installation du CMS va d&eacute;buter dans quelques secondes.</h1>";

	//on redirige vers install.php
	sleep(5);
	echo "<meta http-equiv='refresh' content='0;URL=".$_SERVER['PHP_SELF']."' />";

else:

	global $superadmin, $clientlogin, $modules;
	require_once('inc/functions.php');

	//création du compte super administrateur
	$query = "CREATE TABLE IF NOT EXISTS admin (
		id int(11) NOT NULL AUTO_INCREMENT,
		login varchar(50) NOT NULL,
		pwd varchar(128) NOT NULL,
		nom varchar(255) NOT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY login (login)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

	INSERT INTO admin (id, login, pwd, nom) VALUES
	(1, '".$superadmin['login']."', '".$superadmin['pwd']."', '".$superadmin['nom']."'),
	(2, '".$clientlogin['login']."', '".$clientlogin['pwd']."', '".$clientlogin['nom']."');";


	//installation du module infos
	if(in_array('infos', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS infos (
			id int(11) NOT NULL AUTO_INCREMENT,
			label varchar(50) NOT NULL,
			valeur longtext,
			PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6;

		INSERT INTO infos (id, label) VALUES
		(1, 'Adresse postale'),
		(2, '".utf8_decode('Téléphone')."'),
		(3, 'Fax'),
		(4, 'Adresse email'),
		(5, '".utf8_decode('Infos société')."');"; 

	}

	//installation du module pages
	if(in_array('pages', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS pages (
			id int(11) NOT NULL AUTO_INCREMENT,
			parent int(11) NOT NULL,
			titre varchar(255) NOT NULL,
			slug varchar(255) NOT NULL,
			contenu longtext NOT NULL,
			public tinyint(1) NOT NULL,
			nav tinyint(1) NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installationdu module projets
	if(in_array('projets', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS projets (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  nom varchar(100) NOT NULL,
		  photos longtext NOT NULL,
		  public tinyint(1) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installation du module photos
	if(in_array('photos', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS photos_albums (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  nom varchar(50) NOT NULL,
		  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  date_maj timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
		  nb_photos int(3) NOT NULL,
		  photos longtext NOT NULL,
		  public tinyint(1) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	}

	//installation du module categories
	if(in_array('categories', $modules)){
		
		$query .= "CREATE TABLE IF NOT EXISTS categories (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  nom varchar(150) NOT NULL,
		  parent int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installation du module produits
	if(in_array('produits', $modules)){
		
		$query .= "CREATE TABLE IF NOT EXISTS produits (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  nom varchar(125) NOT NULL,
		  reference varchar(50) NOT NULL,
		  photo varchar(255) NOT NULL,
		  description text NOT NULL,
		  prix float NOT NULL,
		  categories varchar(50) NOT NULL,
		  stock int(11) NOT NULL,
		  promotion tinyint(1) NOT NULL,
		  prix_promo float NOT NULL,
		  public tinyint(1) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installation du module clients
	if(in_array('clients', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS clients (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  email varchar(255) NOT NULL,
		  password varchar(255) NOT NULL,
		  nom varchar(50) NOT NULL,
		  prenom varchar(50) NOT NULL,
		  telephone varchar(20) NOT NULL,
		  adresse_facturation_preferee int(11) NOT NULL,
		  adresse_livraison_preferee int(11) NOT NULL,
		  PRIMARY KEY (id),
		  UNIQUE KEY email (email)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

		CREATE TABLE IF NOT EXISTS adresses_clients (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  adresse varchar(150) NOT NULL,
		  cp varchar(10) NOT NULL,
		  ville varchar(80) NOT NULL,
		  pays varchar(80) NOT NULL,
		  type varchar(3) NOT NULL,
		  client int(11) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installation du module commandes
	if(in_array('commandes', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS commandes (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  reference varchar(20) NOT NULL,
		  statut varchar(20) NOT NULL,
		  client int(11) NOT NULL,
		  facturation text NOT NULL,
		  livraison text NOT NULL,
		  total float NOT NULL,
		  fdp float NOT NULL,
		  colis varchar(20) NOT NULL,
		  paypal varchar(255) NOT NULL,
		  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

		CREATE TABLE IF NOT EXISTS lignes_commandes (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  commande int(11) NOT NULL,
		  produit int(11) NOT NULL,
		  prix float NOT NULL,
		  quantite int(11) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

		CREATE TABLE IF NOT EXISTS historique_traitements (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  commande int(11) NOT NULL,
		  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  statut varchar(50) NOT NULL,
		  message text NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;"

	;}


	//installation du module news
	if(in_array('news', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS news (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  titre varchar(255) NOT NULL,
		  slug varchar(255) NOT NULL,
		  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  contenu longtext NOT NULL,
		  public tinyint(1) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}

	//installation du module diaporama
	if(in_array('slideshow', $modules)){

		$query .= "CREATE TABLE IF NOT EXISTS slideshow (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  titre varchar(255) NOT NULL,
		  url int(11) NOT NULL,
		  banner varchar(255) NOT NULL,
		  public tinyint(1) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

	}


	//connexion à la base de données.
	$bdd = db();

	//préparation de la requête.
	$qa = $bdd->prepare($query);

	//execution
	$res = $qa->execute();

	if($res){
		//renommer le fichier en --installed.bkp
		echo "<h3>L'installation du CMS c'est achev&eacute;e avec succ&egrave;s.</h3>
		<p>Le CMS a &eacute;t&eacute; install&eacute; avec les modules suivants :</p>
		<ul>";
		foreach($modules as $module):
			echo "<li>".$module."</li>";
		endforeach;
		echo "</ul>
		<p>Vous allez maintenant &ecirc;tre redirig&eacute; vers l'administration du site.</p>";
		echo "<meta http-equiv='refresh' content='5;URL=index.php' />";
		rename('install.php', $renameInstallFile);
	} else {
		//affichage des erreurs s'il y en a
		print_r($bdd->errorInfo());
	}

endif;
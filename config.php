<?php
/***
 *
 *	config.php	
 *	Dernière modification : 01/08/2013
 *
 *	Configuration de base du CMS CBC.
 *	Ce fichier est utilisé par le script d'installation de 
 *	la base de données (/admin/install.php), permettant
 *	de créer les tables nécessaires au fonctionnement du CMS.
 *
 */

/*	Rensignez les différentes configs serveur
 *	qu'il est possible d'utiliser pour le CMS
 */
$configs = array(
	'local' => array( 'SITE_URL' => 'http://localhost/cbc-cms.base/', 	// Adresse du site Web
					  'SITE_ROOT' => '/cbc-cms.base/',					// Dossier racine du site, par défaut "/"
					  'DBHOST' => 'localhost', 							// Serveur de bases de données
					  'DBNAME' => 'cbc-cms', 									// Nom de la base de données à utiliser
					  'DBUSER' => 'root', 									// Login d'accès à la base de données
					  'DBPWD' => ''										// Mot de passe de la base de données
	),  
	'prod' => array( 'SITE_URL' => '',
					 'SITE_ROOT' => '/',
					 'DBHOST' => '',
					 'DBNAME' => '',
					 'DBUSER' => '',
					 'DBPWD' => ''
	) 
);

//choix de la config serveur à utiliser
$config = $configs['local']; 


/*	Choisir les modules à installer
 *
 *	Modules existants :
 *	infos, pages, produits, categories, clients, commandes, 
 *	news, photos, projets, slideshow
 */
$modules = array('infos','pages','photos','slideshow');

//Chemin vers le dossier d'uploads depuis la racine du site
define('UPLD_FOLDER', $config['SITE_ROOT'].'uploads/');

//Maximum de pages qu'il sera possible de créer via le module "Pages"
$MaxPages = 10;

//Choix des infos de connexion pour le super administrateur du CMS
$superadmin = array(
	'login' => 'admin', 
	'pwd' => pwd('passwordstring'), //pwd() crypte le mot de passe en SHA512 (128 bits)
	'nom' => 'Ange Chierchia'
);

//Choix des infos de connexion au CMS pour le client
$clientlogin = array(
	'login' => '',
	'pwd' => pwd(''), //pwd() crypte le mot de passe en SHA512 (128 bits)
	'nom' => ''
);

//Nouveau nom du fichier une fois la base de données créée.
$renameInstallFile = '--installed.bkp';


ini_set('error_reporting',E_ALL);
ini_set('display_errors', 1);

/* ===================================
   NE RIEN MODIFIER APRES CETTE LIGNE
   =================================*/

define('SITE_URL', $config['SITE_URL']);
define('SITE_ROOT', $config['SITE_ROOT']);
define('ADMIN_URL', $config['SITE_URL'].'admin/');
define("DBHOST", $config['DBHOST']);
define("DBNAME", $config['DBNAME']); 
define("DBUSER", $config['DBUSER']);
define("DBPWD", $config['DBPWD']);
define("MAX_PAGES", $MaxPages);

define("SALT", "Random string for salt"); //sel pour le cryptage des mots de passe

/*
define("PAYPAL_USER",'ange.c_1339573946_biz@gmail.com');
define("PAYPAL_URL","www.sandbox.paypal.com");
*/

setlocale(LC_TIME, 'fr_FR.utf8','fra'); // affiche la date en français (jours, mois)
setlocale(LC_MONETARY,'fr_FR.utf8','fra'); // affiche les prix au format standard français
?>

<?php 
/***
 *
 *	admin/index.php	
 *	Dernière modification : 11/09/2012
 *
 */
if(file_exists('install.php') && !file_exists('--installed.bkp')):

	header('Location:install.php?wait=1');

else:

	include('inc/functions.php');
	global $modules;
	session_start();
	get_header();

	if(isset($_SESSION['adm'])) :


	    switch(@$_GET['module']){
			/** Pages **/
			case "pages":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.pages.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			
			/** Produits **/
			case "produits":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.produits.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			
			/** Catégories de produit **/
			case "categories":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.categories.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			
			/** Clients	**/
			case "clients":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.clients.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
				
			
			/** Commandes **/
			case "commandes":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.commandes.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			
			/** Actualités **/
			case "news":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.news.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			 
			/** Galeries photos **/
			case "photos":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.photos.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;

			/** Diaporama **/
			case "slideshow":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.slideshow.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			/** Infos générales **/
			case "infos":
				if(in_array($_GET['module'], $modules)):
					include('modules/adm.infos.php');
				else:
					include('modules/adm.pages.php');
				endif;
			break;
			
			default: // accueil de l'admin
	            include('modules/adm.pages.php');
			break;
		}
	    
	    /***
	     * Déconnexion de l'administration
	     */
	    if(isset($_GET['a']) && $_GET['a'] == 'logout'):
	        unset($_SESSION['adm']); session_destroy();
	        echo "<meta http-equiv='refresh' content='0;URL=".ADMIN_URL."' />";
	    endif;   

	else : ?>
		<div id="logbox">
		<?php if(isset($_POST['submitLogin'])): //on vérifie le formulaire
			if(strlen($_POST['login']) > 0 && strlen($_POST['pwd']) > 0):
				$datas = array(
					'login' => trim($_POST['login']),
					'pwd' => trim($_POST['pwd'])
				);
				if(admConnect($datas)){
					echo "<meta http-equiv='refresh' content='0;URL=".ADMIN_URL."' />";
				} else {
					$_SESSION['logbox']['login'] = $_POST['login'];
					$_SESSION['logbox']['pwd'] = $_POST['pwd'];
					message('error',"Login et/ou mot de passe incorrect.");
					echo "<meta http-equiv='refresh' content='3;URL=".ADMIN_URL."' />";
				}
			else: 
				$_SESSION['logbox']['login'] = $_POST['login'];
				$_SESSION['logbox']['pwd'] = $_POST['pwd'];
				message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'ils ne soient pas vides.");
				echo "<meta http-equiv='refresh' content='3;URL=".ADMIN_URL."' />";
			endif;
		else: //formulaire 
			$login = isset($_SESSION['logbox']['login'])?$_SESSION['logbox']['login']:'';
			$pwd =  isset($_SESSION['logbox']['pwd'])?$_SESSION['logbox']['pwd']:''; ?>
			<form method="post">
	        	<h2>Administration</h2>
	        	<p>
	            	<label for="login">Identifiant</label>
	                <input type="text" name="login" id="login" value="<?=@$login;?>" />
	            </p>
	            <p>
	                <label for="pwd">Mot de passe</label>
	                <input type="password" name="pwd" id="pwd" value="<?=@$pwd;?>" />
	            </p>
	            <p>
	            	<input type="submit" name="submitLogin" value="S'identifier" />
	            </p>
	        </form>
	        <p align="center"><a href="<?=SITE_URL;?>">&laquo; Retour au site</a></p>
		<?php endif; ?>
		</div>
	<?php endif; 
	get_footer();

endif;?>
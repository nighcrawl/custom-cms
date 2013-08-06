<?php
/***
 *
 *	admin/modules/adm.pages.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Pages</h1>";
switch(@$_GET['action']){
	case 'add':
		echo "<h1>Cr&eacute;er une page</h1>";
		if(isset($_POST['addPage'])) : //traitement
			/* on récupère les variables du forumlaire
			   en supprimant les espaces inutiles */
			$titre = trim($_POST['titre']);
			$slug = trim($_POST['slug']);
			$contenu = trim($_POST['contenu']);
			$parent = 0;
			$public = isset($_POST['public'])?1:0;
			$nav_item = isset($_POST['nav_item'])?1:0;
			
			$_SESSION['addPage'] = array(
				'titre' => stripslashes($titre),
				'slug' => stripslashes($slug),
				'contenu' => stripslashes($contenu),
				'parent' => 0,
				'public' => $public,
				'nav_item' => $nav_item
			);
			
			if(!empty($titre) && !empty($contenu)):
				$datas = array(
					'titre' 	=> $titre,
					'slug' 		=> url_title($slug),
					'parent'	=> $parent,
					'contenu'	=> $contenu,
					'public' 	=> $public,
					'nav' 		=> $nav_item
				);
				
				if(add($datas,'pages')){
					unset($_SESSION['addPage']);
					message('success',"La page a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
				} else {
					message('error',"Une erreur est survenue pendant l'enregistrement de la page.<br />R&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages&action=add' />";
				}
			else:
				//y'a des erreurs
				message('warning',"Votre page doit obligatoirement avoir un titre et un contenu.<br />Merci de remplir ces champs.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages&action=add' />";
			endif;
		else : //formulaire ?>
			<form method="post">
				<div class="grid_12 alpha">
					<p class="grid_12 alpha omega">
						<label for="titre">Titre de la page <span class="asterisque">*</span></label>
						<input type="text" name="titre" id="titre" value="<?=@$_SESSION['addPage']['titre'];?>" class="grid_10 alpha omega" />
					</p>
                    	
                    <p class="grid_12 alpha omega">
                    	<label for="slug" class="label-inline">Permalien </label>
						<span><?php echo SITE_URL;?></span><input type="text" name="slug" id="slug" value="<?=@$_SESSION['addPage']['slug'];?>" />
                    </p>
                    
					<p class="grid_12 alpha omega">
						<label for="contenu">Contenu de la page <span class="asterisque">*</span></label>
						<textarea name="contenu" id="contenu" class="grid_11 alpha omega"><?=@$_SESSION['addPage']['contenu'];?></textarea>
						<script type="text/javascript">
						//<![CDATA[
							CKEDITOR.replace( 'contenu', {
								toolbar : 'cbc',
								extraPlugins : 'uicolor',
								uiColor : '#efefef'
							});
						//]]>
						</script>
					</p>
				</div>
				<div class="grid_4 omega">										
					<label for="public" class="grid_4 alpha omega">
						<?php $public_chk = (isset($_SESSION['addPage']['public']) && $_SESSION['addPage']['public'] == 1)?'checked':''; ?>
						<input type="checkbox" name="public" id="public" value="1" <?=$public_chk;?> /> Publier sur le site
					</label>
					
					<label for="nav_item" class="grid_4 alpha omega">
						<?php $nav_item_chk = (isset($_SESSION['addPage']['nav_item']) && $_SESSION['addPage']['nav_item'] == 1)?'checked':''; ?>
						<input type="checkbox" name="nav_item" id="nav_item" value="1" <?=$nav_item_chk;?> /> Afficher dans le menu
					</label>
				</div>
				<p class="grid_16 alpha omega"><input type="submit" name="addPage" value="Cr&eacute;er la page" /> <a href="index.php?module=pages">Retour &agrave; la liste</a></p>
			</form>
		<?php unset($_SESSION['addPage']);
		endif;
	break;
	
	case 'upd':
		echo "<h1>Modifier une page</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])) :
			$infosPage = get('*','pages',array('id =' => intval($_GET['pid'])));
			@$infos = $infosPage['reponse'][0]; 
			if(count($infos) > 0){ //on a l'id
				if(isset($_POST['updPage'])) : //traitement
					//on récupère les variables du forumlaire en supprimant les espaces inutiles
					$titre = trim($_POST['titre']);
					$slug = trim($_POST['slug']);
					$contenu = trim($_POST['contenu']);
					$parent = 0;
					$public = isset($_POST['public'])?1:0;
					$nav_item = isset($_POST['nav_item'])?1:0;
					
					$_SESSION['updPage'] = array(
						'titre' => stripslashes($titre),
						'slug' => stripslashes($slug),
						'contenu' => stripslashes($contenu),
						'parent' => $parent,
						'public' => $public,
						'nav_item' => $nav_item
					);
					
					if(!empty($titre) && !empty($contenu)):
						$datas = array(
							'titre'		=> $titre,
							'slug'		=> url_title($slug),
							'parent'	=> $parent,
							'contenu'	=> $contenu,
							'public'	=> $public,
							'nav'		=> $nav_item
						);
						
						if(add($datas,'pages',intval($_GET['pid']))){
							message('success',"La page a bien &eacute;t&eacute; modifi&eacute;e.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages&action=upd&pid=".intval($_GET['pid'])."' />";
							unset($_SESSION['updPage']);
						} else {
							message('error',"Une erreur est survenue pendant l'enregistrement des modifications.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages&action=upd&pid=".intval($_GET['pid'])."' />";
						}
					else:
						message('warning',"Votre page doit obligatoirement avoir un titre et un contenu.<br />Merci de remplir ces champs.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages&action=upd&pid=".intval($_GET['pid'])."' />";
					endif;
				else : //forumlaire 
					$titre = (isset($_SESSION['updPage']['titre']))?$_SESSION['updPage']['titre']:$infos['titre'];
					$slug = (isset($_SESSION['updPage']['slug']))?$_SESSION['updPage']['slug']:$infos['slug'];
					$contenu = (isset($_SESSION['updPage']['contenu']))?$_SESSION['updPage']['contenu']:$infos['contenu'];
					$public = (isset($_SESSION['updPage']['public']))?$_SESSION['updPage']['public']:$infos['public'];
					$nav_item = (isset($_SESSION['updPage']['nav_item']))?$_SESSION['updPage']['nav_item']:$infos['nav'];
					?>
					
					<form method="post">
						<div class="grid_12 alpha">
							<p class="grid_12 alpha omega">
								<label for="titre">Titre de la page <span class="asterisque">*</span></label>
								<input type="text" name="titre" id="titre" value='<?=stripslashes($titre);?>' class="grid_10 alpha omega" />
							</p>
                            
                            <p class="grid_12 alpha omega">
                                <label for="slug" class="label-inline">Permalien </label>
                                <span><?php echo SITE_URL;?></span><input type="text" name="slug" id="slug" value="<?=@$slug;?>" />
                            </p>
                            
							<p class="grid_12 alpha omega">
								<label for="contenu">Contenu de la page <span class="asterisque">*</span></label>
								<textarea name="contenu" id="contenu" class="grid_11 alpha omega"><?=stripslashes($contenu);?></textarea>
								<script type="text/javascript">//<![CDATA[
									CKEDITOR.replace( 'contenu', { toolbar : 'cbc', extraPlugins : 'uicolor', uiColor : '#efefef' });
								//]]></script>
							</p>
						</div>
						<div class="grid_4 omega">						
							<label for="public" class="grid_4 alpha omega">
								<?php $public_chk = (isset($public) && $public == 1)?'checked':''; ?>
								<input type="checkbox" name="public" id="public" value="1" <?=$public_chk;?> /> Publier sur le site
							</label>
							
							<label for="nav_item" class="grid_4 alpha omega">
								<?php $nav_item_chk = (isset($nav_item) && $nav_item == 1)?'checked':''; ?>
								<input type="checkbox" name="nav_item" id="nav_item" value="1" <?=$nav_item_chk;?> /> Afficher dans le menu
							</label>
						</div>
						<p class="grid_16 alpha omega"><input type="submit" name="updPage" value="Mettre &agrave; jour la page" /> <a href="index.php?module=pages">Retour &agrave; la liste</a></p>
					</form>
					
				<?php unset($_SESSION['updPage']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Cette page ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette page ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer une page</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])) : //on a l'id
			if(delete(intval($_GET['pid']),'pages')){
				message('success',"La page a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. La page n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
			}
		else : //on a pas d'id
			message('warning',"Cette page ne peut &ecirc;tre supprim&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=pages' />";
		endif;
	break;
	
	default: //liste des pages		
		$nbpp = 10;
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		
		
		$listPages = get('*','pages',null,"AND",array('id'=>'ASC'),array($_GET['d'],$nbpp));
		$pagesTotal = $listPages['total'];
		
		$listPages = $listPages['reponse'];
		
		if(count($listPages) > 0) : //on  affiche la liste ?>
			
			<?php if(count($listPages) < MAX_PAGES): ?>
			<a href="index.php?module=pages&action=add" class="button">Cr&eacute;er une nouvelle page</a>
			<?php else: ?>
			<div class="message">Vous avez atteint le maximum de pages autoris&eacute;es.</div>
			<?php endif; ?>
			
			<table width="100%" class="data">
				<thead>
					<tr>
						<th># ID</th>
						<th>Titre</th>
						<th>Page m&egrave;re</th>
						<th>Publi&eacute;e</th>
						<th>Lien de navigation</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listPages as $lp){
					echo "<tr>
						<td>".$lp['id']."</td>
						<td>".stripslashes($lp['titre'])."</td>
						<td>".getTitrePage($lp['parent'])."</td>
						<td>".isTrue($lp['public'])."</td>
						<td>".isTrue($lp['nav'])."</td>
						<td>
							<a class='button' href='index.php?module=pages&action=upd&pid=".$lp['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=pages&action=dlt&pid=".$lp['id']."'>Supprimer</a>
						</td>
					</tr>";		
				} ?></tbody>
			</table>                            
			<?php pagination($pagesTotal,$nbpp,"index.php?module=pages&d=");
		else : //pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucune page pour le moment.<br />Voulez-vous <a href='index.php?module=pages&action=add'>en cr&eacute;er une ?</a>");
		endif;
	break;
}
?>
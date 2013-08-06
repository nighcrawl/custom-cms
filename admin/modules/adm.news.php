<?php
/***
 *
 *	admin/modules/adm.news.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Actualit&eacute;s</h1>";
switch(@$_GET['action']){
	case 'add':
		echo "<h1>R&eacute;diger une actualit&eacute;</h1>";
		if(isset($_POST['addNews'])) : //traitement
			/* on récupère les variables du forumlaire
			   en supprimant les espaces inutiles */
			$titre = addslashes(trim($_POST['titre']));
			$contenu = addslashes(trim($_POST['contenu']));
			$date = (!empty($_POST['date']))?trim($_POST['date']):date('Y-m-d H:i:s');
			$public = isset($_POST['public'])?1:0;
			
			$_SESSION['addNews'] = array(
				'titre' 	=> stripslashes($titre),
				'contenu' 	=> stripslashes($contenu),
				'date' 		=> $date,
				'public' 	=> $public
			);
			
			if(!empty($titre) && !empty($contenu)):
			
				$datas = array(
					'titre' 	=> $titre,
					'slug' 		=> url_title($titre),
					'contenu' 	=> $contenu,
					'date' 		=> $date,
					'public' 	=> $public
				);
				
				if(add($datas,'news')){
					unset($_SESSION['addNews']);
					message('success',"L'actualit&eacute; a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
				} else {
					message('error',"Une erreur est survenue pendant l'enregistrement de l'actualit&eacute;.<br />R&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news&action=add' />";
				}
				
			else:
				//y'a des erreurs
				message('warning',"Votre actualit&eacute; doit obligatoirement avoir un titre et un contenu.<br />Merci de remplir ces champs.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news&action=add' />";
			endif;
		else : //formulaire ?>
			<form method="post">
				<div class="grid_12 alpha">
					<p class="grid_12 alpha omega">
						<label for="titre">Titre de l'actualite <span class="asterisque">*</span></label>
						<input type="text" name="titre" id="titre" value="<?=@$_SESSION['addNews']['titre'];?>" class="grid_10 alpha omega" />
					</p>
					<p class="grid_12 alpha omega">
						<label for="contenu">Contenu de l'actualit&eacute; <span class="asterisque">*</span></label>
						<textarea name="contenu" id="contenu" class="grid_11 alpha omega"><?=@$_SESSION['addNews']['contenu'];?></textarea>
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
					<p>
						<label for="date">Date de publication</label>
						<input type="text" name="date" id="date" value="<?=@$_SESSION['addNews']['date'];?>" class="datepicker grid_4 alpha omega" />
					</p>
					<label for="public" class="grid_4 alpha omega">
						<?php $public_chk = (isset($_SESSION['addNews']['public']) && $_SESSION['addNews']['public'] == 1)?'checked':''; ?>
						<input type="checkbox" name="public" id="public" value="1" <?=$public_chk;?> /> Publier sur le site
					</label>
				</div>
				<p class="grid_16 alpha omega"><input type="submit" name="addNews" value="Cr&eacute;er l'actualit&eacute;" /> <a href="index.php?module=news">Retour &agrave; la liste</a></p>
			</form>
		<?php unset($_SESSION['addNews']);
		endif;
	break;
	
	case 'upd':
		echo "<h1>Modifier une actualit&eacute;</h1>";
		if(isset($_GET['nid']) && is_numeric($_GET['nid'])) :
			$infosNews = get('*','news',array('id =' => intval($_GET['nid'])));
			@$infos = $infosNews['reponse'][0]; 
			if(count($infos) > 0){
				//on a l'id
				if(isset($_POST['updNews'])) : //traitement
					/* on récupère les variables du forumlaire
					   en supprimant les espaces inutiles */
					$titre = addslashes(trim($_POST['titre']));
					$contenu = addslashes(trim($_POST['contenu']));
					$date = (!empty($_POST['date']))?trim($_POST['date']):date('Y-m-d H:i:s');
					$public = isset($_POST['public'])?1:0;
					
					$_SESSION['updPage'] = array(
						'titre' => stripslashes($titre),
						'contenu' => stripslashes($contenu),
						'date' => $date,
						'public' => $public
					);
					
					if(!empty($titre) && !empty($contenu)):
						$datas = array(
							'titre'		=> $titre,
							'slug'		=> url_title($titre),
							'contenu'	=> $contenu,
							'date'		=> $date,
							'public'	=> $public
						);
						
						if(update(intval($_GET['nid']),$datas,'news')){
							unset($_SESSION['updNews']);
							message('success',"L'actualit&eacute; a bien &eacute;t&eacute; modifi&eacute;e.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news&action=upd&nid=".intval($_GET['nid'])."' />";
						} else {
							message('error',"Une erreur est survenue pendant l'enregistrement des modifications.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news&action=upd&nid=".intval($_GET['nid'])."' />";
						}
					else:
						message('warning',"Votre actualit&eacute; doit obligatoirement avoir un titre et un contenu.<br />Merci de remplir ces champs.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news&action=upd&nid=".intval($_GET['nid'])."' />";
					endif;
				else : //forumlaire 
					$titre = (isset($_SESSION['updNews']['titre']))?$_SESSION['updNews']['titre']:$infos['titre'];
					$contenu = (isset($_SESSION['updNews']['contenu']))?$_SESSION['updNews']['contenu']:$infos['contenu'];
					$date = (isset($_SESSION['updNews']['date']))?$_SESSION['updNews']['date']:$infos['date'];
					$public = (isset($_SESSION['updNews']['public']))?$_SESSION['updNews']['public']:$infos['public'];
					?>
					
					<form method="post">
						<div class="grid_12 alpha">
							<p class="grid_12 alpha omega">
								<label for="titre">Titre de l'actualite <span class="asterisque">*</span></label>
								<input type="text" name="titre" id="titre" value="<?=stripslashes($titre);?>" class="grid_10 alpha omega" />
							</p>
							<p class="grid_12 alpha omega">
								<label for="contenu">Contenu de l'actualit&eacute; <span class="asterisque">*</span></label>
								<textarea name="contenu" id="contenu" class="grid_11 alpha omega"><?=stripslashes($contenu);?></textarea>
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
							<p>
								<label for="date">Date de publication</label>
								<input type="text" name="date" id="date" value="<?=$date;?>" class="datepicker grid_4 alpha omega" />
							</p>
							<label for="public" class="grid_4 alpha omega">
								<?php $public_chk = (isset($public) && $public == 1)?'checked':''; ?>
								<input type="checkbox" name="public" id="public" value="1" <?=$public_chk;?> /> Publier sur le site
							</label>
						</div>
						<p class="grid_16 alpha omega"><input type="submit" name="updNews" value="Mettre &agrave; jour l'actualit&eacute;" /> <a href="index.php?module=news">Retour &agrave; la liste</a></p>
					</form>
					
				<?php unset($_SESSION['updNews']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Cette actualit&eacute; ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
			}
		else :
			//on a pas l'id
			message('warning',"Cette actualit&eacute; ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer une actualit&eacute;</h1>";
		if(isset($_GET['nid']) && is_numeric($_GET['nid'])) :
			//on a l'id
			if(delete(intval($_GET['nid']),'news')){
				message('success',"L'actualit&eacute; a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. L'actualit&eacute; n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette actualit&eacute; ne peut &ecirc;tre supprim&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=news' />";
		endif;
	break;
	
	default:
		//liste des actualités
		if(!isset($_GET['d'])) $_GET['d'] = 0;
		
		$nbpp = 10;
		$listNews = get('*','news',null,"AND",array('date'=>'DESC'),array($_GET['d'],$nbpp));
		$newsTotal = $listNews['total'];
		$listNews = $listNews['reponse'];
		
		if(count($listNews) > 0): //on affiche la liste ?>
			<a href="index.php?module=news&action=add" class="button">R&eacute;diger une actualit&eacute;</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th># ID</th>
						<th>Titre</th>
						<th>Date</th>
						<th>Publi&eacute;e</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listNews as $ln){
					
					echo "<tr>
						<td>".$ln['id']."</td>
						<td>".stripslashes($ln['titre'])."</td>
						<td>".date('d/m/Y H\hi',strtotime($ln['date']))."</td>
						<td>".isTrue($ln['public'])."</td>
						<td>
							<a class='button' href='index.php?module=news&action=upd&nid=".$ln['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=news&action=dlt&nid=".$ln['id']."'>Supprimer</a>
						</td>
					</tr>";	
					
				} ?></tbody>
			</table>
		<?php pagination($newsTotal,$nbpp,"index.php?module=news&d=");
		else:
			//pas d'enregistrements
			message(null,"Malheureusement, aucune actualit&eacute; n'a &eacute;t&eacute; r&eacute;dig&eacute;e pour le moment.<br />Voulez-vous <a href='index.php?module=news&action=add'>en r&eacute;diger une ?</a>");
		endif;
	break;
}
?>
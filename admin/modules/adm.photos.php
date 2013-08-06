<?php
/***
 *
 *	admin/modules/adm.photos.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Galeries photos</h1>";
switch(@$_GET['action']){
	case 'add':
		if(isset($_POST['addPhotos'])){
			//traitement du formulaire			

			if(!empty($_POST['fichiers_urls'])){
				//on créé les miniatures
				$fichiers = substr($_POST['fichiers_urls'],0,-1);
				$fichiers = explode(';',$fichiers);
			}
			
			$nom = trim($_POST['album']);
			$date = date('Y-m-d H:i:s');
			$nbphotos = count($fichiers);
			$photos = trim($_POST['fichiers_urls']);
			$public = isset($_POST['public'])?1:0;

			$_SESSION['addPhotos'] = array(
				'album' 	=> stripslashes($nom),
				'photos' 	=> stripslashes($photos),
				'public' 	=> ($public == 1)?'checked':''
			);
			
			if(!empty($nom)){
			
				$datas = array(
					'nom' 		=> $nom,
					'date' 		=> $date,
					'date_maj' 	=> $date,
					'nb_photos' => $nbphotos,
					'photos' 	=> $photos,
					'public' 	=> $public
				);
				
				if(add($datas,'photos_albums')){
					unset($_SESSION['addPhotos']);
					message('success',"L'album a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
				} else {
					message('error',"Une erreur est survenue pendant l'enregistrement de l'album.<br />R&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos&action=add' />";
				}
				
			} else {
				//y'a des erreurs
				message('warning',"Votre album photo doit obligatoirement avoir un titre.<br />Merci de remplir ce champs.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos&action=add' />";
			}
		} else { //affichage du formulaire ?>
			<form method="post" enctype="multipart/form-data">
				<p>
					<label for="album">Nom de l'album photos</label>
					<input type="text" name="album" id="album" value="<?=@$_SESSION['addPhotos']['album'];?>" />
				</p>
				
				<p>
					<label for="fichiers">Photos de l'album</label>

					<input type="file" name="fichiers" id="fichiers" multiple="multiple" />
					<div id="timestamp" style="display:none;"><?php $timestamp = time(); echo $timestamp; ?></div>
					<div id="token" style="display:none;"><?php echo md5('unique_salt' . $timestamp);?></div>
					<div id="queue"></div>

					<input type="hidden" name="fichiers_urls" id="fichiers_urls" value="<?=@$_SESSION['addPhotos']['photos'];?>" />

					<div id="photosView"><?php
					if(isset($_SESSION['addPhotos']['photos']) && !empty($_SESSION['addPhotos']['photos'])){
						$files = explode($_SESSION['addPhotos']['photos']);
						foreach($files as $f){
							echo "<img title='Cliquez pour supprimer la photo' width='80' src='../uploads/" . $f . "' />";
						}
					}
					?></div>
				</p>
				
				<p>
					<label for="public"><input type="checkbox" name="public" id="public" value="1" <?=@$_SESSION['addPhotos']['public'];?> /> Publier l'album</label>
				</p>
				
			  <p><input type="submit" name="addPhotos" value="Cr&eacute;er l'album" /></p>
			</form>
		<?php
		}
	break;
	
	case 'upd':
		echo "<h1>Modifier un album photo</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])):
			$infosPhotos = get('*','photos_albums',array('id =' => intval($_GET['pid'])));
			@$infos = $infosPhotos['reponse'][0];
			if(count($infos) >0) {
				//on a l'id
				if(isset($_POST['updPhotos'])):
					//traitement
			
					if(!empty($_POST['fichiers_urls'])){
						//on créé les miniatures
						$fichiers = substr($_POST['fichiers_urls'],0,-1);
						$fichiers = explode(';',$fichiers);
					}
					
					$album = trim($_POST['album']);
					$date = date('Y-m-d H:i:s');
					$nbphotos = count($fichiers);
					$photos = trim($_POST['fichiers_urls']);
					$public = isset($_POST['public'])?1:0;
					
					$_SESSION['updPhotos'] = array(
						'album' 	=> stripslashes($album),
						'photos' 	=> stripslashes($photos),
						'public' 	=> ($public == 1)?'checked':'',
						'nb_photos'	=> $nbphotos
					);
					
					if(!empty($album)){
						$datas = array(
							'nom' 		=> $album,
							'date_maj' 	=> $date,
							'nb_photos' => $nbphotos,
							'photos' 	=> $photos,
							'public' 	=> $public
						);
						
						if(add($datas,'photos_albums',$_GET['pid'])){
							unset($_SESSION['updPhotos']);
							message('success',"L'album a bien &eacute;t&eacute; modifi&eacute;.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
						} else {
							message('error',"Une erreur est survenue pendant l'enregistrement de l'album.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos&action=upd&pid=".$_GET['pid']."' />";
						}
					} else {
						//y'a des erreurs
						message('warning',"Votre album photo doit obligatoirement avoir un titre.<br />Merci de remplir ce champs.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos&action=upd&pid=".$_GET['pid']."' />";
					}
				else : //formulaire 
					$album = (isset($_SESSION['updPhotos']['album']))?$_SESSION['updPhotos']['album']:$infos['nom'];
					$photos = (isset($_SESSION['updPhotos']['photos']))?$_SESSION['updPhotos']['photos']:$infos['photos'];
					$public = (isset($_SESSION['updPhotos']['public']))?$_SESSION['updPhotos']['public']:$infos['public'];							
					?>
					<form method="post" enctype="multipart/form-data">
						<p>
							<label for="album">Nom de l'album photos</label>
							<input type="text" name="album" id="album" value="<?=@$album;?>" />
						</p>
						
						<p>

							<label for="fichiers">Photos de l'album</label>
							<input type="file" name="fichiers" id="fichiers" multiple="multiple" />

							<div id="timestamp" style="display:none;"><?php $timestamp = time(); echo $timestamp; ?></div>
							<div id="token" style="display:none;"><?php echo md5('unique_salt' . $timestamp);?></div>
							<div id="queue"></div>

							<input type="hidden" name="fichiers_urls" id="fichiers_urls" value="<?=@$photos;?>" />
							<div id="photosView"><?php
							if(isset($photos)){
								$photos = substr($photos,0,-1);
								$files = explode(';',$photos);
								foreach($files as $f){
									echo "<img title='Cliquez pour supprimer la photo' width='80' src='".SITE_URL."images.php?src=".SITE_ROOT."uploads/" . $f . "&w=80&h=80' />";
								}
							} ?></div>
						</p>
						
						<p>
							<label for="public"><input type="checkbox" name="public" id="public" value="1" <?php if($public == 1) echo "checked";?> /> Publier l'album</label>
						</p>
						
						<p><input type="submit" name="updPhotos" value="Modifier l'album" /></p>
					</form><?php
				endif;
			} else {
				//on a pas l'id
				message('warning',"Cet album photo ne peut &ecirc;tre modifi&eacute; car il n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
			}
		else:
			//on a pas l'id
			message('warning',"Cet album photo ne peut &ecirc;tre modifi&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer un album photos</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])) :
			//on a l'id
			if(delete(intval($_GET['pid']),'photos_albums')){
				message('success',"L'album photos a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. L'album photos n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cet album photos ne peut &ecirc;tre supprim&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=photos' />";
		endif;
	break;
	
	default:
		//liste des albums photos
		if(!isset($_GET['d'])) $_GET['d'] = 0;
		
		$nbpp = 10;
		$listPhotos = get('*','photos_albums', null, "AND",array('nom' => 'DESC'),array($_GET['d'],$nbpp));
							
		$photosTotal = $listPhotos['total'];
		$listPhotos = $listPhotos['reponse'];
		
		if(count($listPhotos) > 0): //on affiche la liste ?>
			<a href="index.php?module=photos&action=add" class="button">Cr&eacute;er un album photo</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th>Album photos</th>
						<th>Nombre de photos</th>
						<th>Derni&egrave;re mise &agrave; jour</th>
						<th>Publi&eacute;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listPhotos as $lp){
					
					echo "<tr>
						<td>".stripslashes($lp['nom'])."</td>
						<td>".$lp['nb_photos']."</td>
						<td>".date('d/m/Y H\hi',strtotime($lp['date_maj']))."</td>
						<td>".isTrue($lp['public'])."</td>
						<td>
							<a href='index.php?module=photos&action=upd&pid=".$lp['id']."' class='button'>Modifier</a> 
							<a href='index.php?module=photos&action=dlt&pid=".$lp['id']."' class='button trash'>Supprimer</a>
						</td>
					</tr>";
					
				} ?></tbody>
			</table>
			<?php pagination($photosTotal,$nbpp,"index.php?module=photos&d=");
		else:
			//pas d'enregistrements
			message(null,"Malheureusement, aucune galerie photo n'a &eacute;t&eacute; cr&eacute;&eacute;e pour le moment.<br />Voulez-vous <a href='index.php?module=photos&action=add'>en cr&eacute;er une ?</a>");
		endif;
	break;
}
?>
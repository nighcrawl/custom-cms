<?php
/***
 *
 *	admin/modules/adm.categories.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Cat&eacute;gories</h1>";
switch(@$_GET['action']){
	case 'add':
		if(isset($_POST['addCategorie'])): //traitement
			$nom = (isset($_POST['nom']) && !empty($_POST['nom']))?trim($_POST['nom']):null;
			$parent = isset($_POST['parent'])?intval($_POST['parent']):null;
			
			$_SESSION['addCategorie'] = $_POST;
			
			if(!is_null($nom) && !is_null($parent)){
				//ajout
				$datas = array(
					'nom' => addslashes($nom),
					'parent' => intval($parent)
				);
				
				if(add($datas,'categories')){
				//ça c'est bien passé
					unset($_SESSION['addCategorie']);
					message('success',"La cat&eacute;gorie a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
				} else {
					//erreur pendant la création
					message('error',"Une erreur est survenue pendant la cr&eacute;ation de la cat&eacute;gorie.<br />Merci de r&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories&action=add' />";
				}
			} else {
				//erreur
				message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'aucun champs n'est vide.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories&action=add' />";
			}
		else: //formulaire ?>
			<form method="post">
				<p>
					<label for="nom">Nom de la cat&eacute;gorie</label>
					<input type="text" name="nom" id="nom" value="<?=@$_SESSION['addCategorie']['nom'];?>" />
				</p>
				
				<p>
					<label for="parent">Cat&eacute;gorie m&egrave;re</label>
					<select name="parent" id="parent">
						<option value="0"> - </option>
						<?php $listeParents = get('*','categories',array('parent =' => 0));
						$listeParents = $listeParents['reponse'];
						foreach($listeParents as $lp): 
							if($_SESSION['addCategorie']['parent'] == $lp['id']): ?>
								<option value="<?=$lp['id'];?>" selected="selected"><?=stripslashes($lp['nom']);?></option>
							<?php else : ?>
								<option value="<?=$lp['id'];?>"><?=stripslashes($lp['nom']);?></option>
							<?php endif;
						endforeach; ?>
					</select>
				</p>
				
				<p>
					<input type="submit" name="addCategorie" value="Cr&eacute;er la cat&eacute;gorie" /> <a href="index.php?module=categories">Retour &agrave; la liste</a>
				</p>
			</form> <?php unset($_SESSION['addCategorie']);
		endif;
	break;
	
	case 'upd':
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])):
			$infosCategories = get('*','categories',array('id =' => intval($_GET['cid'])));
			@$infos = $infosCategories['reponse'][0];
			if(count($infos) > 0){
				if(isset($_POST['updCategorie'])): //traitement
					$nom = (isset($_POST['nom']) && !empty($_POST['nom']))?trim($_POST['nom']):null;
					$parent = isset($_POST['parent'])?intval($_POST['parent']):null;
					
					$_SESSION['updCategorie'] = $_POST;
					
					if(!is_null($nom) && !is_null($parent)){
						$datas = array(
							'nom' => addslashes($nom),
							'parent' => intval($parent)
						);
						
						if(add($datas,'categories',$infos['id'])){
							unset($_SESSION['updCategorie']);
							message('success',"La cat&eacute;gorie a bien &eacute;t&eacute; modifi&eacute;e.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
						} else {
							//erreur pendant la création
							message('error',"Une erreur est survenue pendant la modification de la cat&eacute;gorie.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories&action=upd&cid=".$infos['id']."' />";
						}
					} else {
						//erreur
						message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'aucun champs n'est vide.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories&action=upd&cid=".$infos['id']."' />";
					}
				else : //formulaire 
					$nom = (isset($_SESSION['updCategorie']['nom']))?$_SESSION['updCategorie']['nom']:stripslashes($infos['nom']);
					$parent_cat = (isset($_SESSION['updCategorie']['parent']))?$_SESSION['updCategorie']['parent']:intval($infos['parent']);
				?>
					<form method="post">
						<p>
							<label for="nom">Nom de la cat&eacute;gorie</label>
							<input type="text" name="nom" id="nom" value="<?=$nom;?>" />
						</p>
						
						<p>
							<label for="parent">Cat&eacute;gorie m&egrave;re</label>
							<select name="parent" id="parent">
								<option value="0"> - </option>
								<?php $listeParents = get('*','categories',array('parent =' => 0));
								$listeParents = $listeParents['reponse'];
								foreach($listeParents as $lp): 
									if($parent_cat == $lp['id']): ?>
										<option value="<?=$lp['id'];?>" selected="selected"><?=stripslashes($lp['nom']);?></option>
									<?php else : ?>
										<option value="<?=$lp['id'];?>"><?=stripslashes($lp['nom']);?></option>
									<?php endif;
								endforeach; ?>
							</select>
						</p>
						
						<p>
							<input type="submit" name="updCategorie" value="Cr&eacute;er la cat&eacute;gorie" /> <a href="index.php?module=categories">Retour &agrave; la liste</a>
						</p>
					</form> <?php unset($_SESSION['updCategorie']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Cette cat&eacute;gorie ne peut &ecirc;tre modifi&eacute;e car il n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette cat&eacute;gorie ne peut &ecirc;tre modifi&eacute;e car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer une cat&eacute;gorie</h1>";
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])) :
			//on a l'id
			if(delete(intval($_GET['cid']),'categories')){
				message('success',"La cat&eacute;gorie a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. La cat&eacute;gorie n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=categories' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette cat&eacute;gorie ne peut &ecirc;tre supprim&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
		endif;
	break;
	
	default:
		//liste des catégories
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		
		$nbpp = 10;
		$listCategories = get('*','categories',null,"AND",array("parent" => "ASC", "nom" => "ASC"),array($_GET['d'],$nbpp));
		$categoriesTotal = $listCategories['total'];
		
		$listCategories = $listCategories['reponse'];
		
		if(count($listCategories) > 0) : //on  affiche la liste ?>
			<a href="index.php?module=categories&action=add" class="button">Nouvelle cat&eacute;gorie</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th># ID</th>
						<th>Nom</th>
						<th>Cat&eacute;gorie m&egrave;re</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listCategories as $lc){
					
					$parent = get(array('nom'),'categories',array('id =' => $lc['parent']));
					$parent = @$parent['reponse'][0]['nom'];
					
					echo "<tr>
						<td>".$lc['id']."</td>
						<td>".stripslashes($lc['nom'])."</td>
						<td>".$parent."</td>
						<td>
							<a class='button' href='index.php?module=categories&action=upd&cid=".$lc['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=categories&action=dlt&cid=".$lc['id']."'>Supprimer</a>
						</td>
					</tr>";		
				} ?></tbody>
			</table>                            
			<?php pagination($categoriesTotal,$nbpp,"index.php?module=categories&d=");
		else :
			//pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucune cat&eacute;gorie pour le moment.<br />Voulez-vous <a href='index.php?module=categories&action=add'>en cr&eacute;er une ?</a>");
		endif;
	break;
}
?>
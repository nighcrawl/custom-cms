<?php
/***
 *
 *	admin/modules/adm.produits.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Produits</h1>";
switch(@$_GET['action']){
	case 'add':
		echo "<h1>Ajouter un produit</h1>";
		if(isset($_POST['addProduit'])) :
			//traitement du formulaire				
			$nom = isset($_POST['nom'])?trim($_POST['nom']):null;
			$ref = isset($_POST['reference'])?trim($_POST['reference']):null;
			$photo = isset($_FILES['photo'])?$_FILES['photo']:null;
			$description = isset($_POST['description'])?trim($_POST['description']):null;
			$categories = isset($_POST['categories'])?$_POST['categories']:null;
			$prix = (isset($_POST['prix']) && is_numeric($_POST['prix']))?$_POST['prix']:null;
			$stock = (isset($_POST['stock']) && is_numeric($_POST['stock']))?$_POST['stock']:null;
			$prix_promotion = (isset($_POST['promotion']) && is_numeric($_POST['promotion']) && $_POST['promotion'] > 0)?$_POST['promotion']:null;
			$promotion = (is_numeric($prix_promotion)&& ($prix_promotion > 0))?1:0;
			$public = isset($_POST['public'])?1:0;
									
			$_SESSION['addProduit'] = array(
				'nom' => $nom,
				'reference' => $ref,
				'description' => $description,
				'categories' => $categories,
				'prix' => $prix,
				'stock' => $stock,
				'promotion' => $prix_promotion,
				'public' => $public
			);
		
			if(!empty($nom) && !empty($ref) && !empty($description) && !is_null($prix)){
				$liste_cats = "";
				foreach($categories as $categorie){ $liste_cats .= $categorie . ";"; }
				$datas = array(
					'nom' => addslashes($nom), 
					'reference' => addslashes($ref), 
					'description' => addslashes($description), 
					'categories' => substr($liste_cats,0,-1),
					'prix' => $prix
				);
				
				if(!is_null($photo)){
					//transfère de l'image
					//enregistrement du nom
					$datas['photo'] = $photo['name'];
				}
				
				if(!is_null($stock)){ $datas['stock'] = $stock; }
				if(!is_null($prix_promotion)){ $datas['prix_promo'] = $prix_promotion; $datas['promotion'] = $promotion; }
				$datas['public'] = $public;
				
				if(add($datas,'produits')){
					unset($_SESSION['addProduit']);
					message('success',"Le produit a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
				} else {
					message('error',"Une erreur est survenue pendant l'enregistrement du produit.<br />R&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits&action=add' />";
				}							
			} else {
				//y'a des erreurs
				message('warning',"Certain champs obligatoires sont vides.<br />Merci de remplir ces champs.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits&action=add' />";
			}
		else : //formulaire ?>
			<form method="post" enctype="multipart/form-data">
				<div class="grid_12">
					<p class="grid_6 alpha">
						<label for="nom">Nom <span class="asterisque">*</span></label>
						<input type="text" name="nom" id="nom" class="grid_5 alpha omega" value="<?=@$_SESSION['addProduit']['nom'];?>" />
					</p>
					
					<p class="grid_6 omega">
						<label for="reference">R&eacute;f&eacute;rence <span class="asterisque">*</span></label>
						<input type="text" name="reference" id="reference" value="<?=@$_SESSION['addProduit']['reference'];?>" />
					</p>
					
					<p class="grid_6 suffix_6 alpha">
						<label for="photo">Photo</label>
						<input type="file" name="photo" id="photo" />
					</p>
					
					<p class="grid_11 alpha">
						<label for="description">Description <span class="asterisque">*</span></label>
						<textarea name="description" id="description" class="grid_11 alpha"><?=@$_SESSION['addProduit']['description'];?></textarea>
						<script type="text/javascript">//<![CDATA[
							CKEDITOR.replace( 'description', {
								toolbar : 'cbc',
								extraPlugins : 'uicolor',
								uiColor : '#efefef'
							});
						//]]></script>
					</p>
				</div>
				<div class="grid_4">
					<p>
						<label for="categories">Cat&eacute;gorie(s) du produit <span class="asterisque">*</span></label>
						<ul class="list" id="ul_cats">
						<?php $cats = get(array('id','nom'),'categories',array('parent =' => 0));
						foreach($cats['reponse'] as $cat): ?>
							<li><label for="cat_<?=$cat['id'];?>">
								<input type="checkbox" name="categories[]" id="cat_<?=$cat['id'];?>" value="<?=$cat['id'];?>" 
								<?php if(isset($_SESSION['addProduit']['categories']) && in_array($cat['id'],$_SESSION['addProduit']['categories'])){ echo "checked"; } ?>> <?=$cat['nom'];?></label>
								<?php $sscats = get(array('id','nom'),'categories',array('parent =' => $cat['id']));
								if(count($sscats['reponse']) > 0):
									echo "<ul>"; 
									foreach($sscats['reponse'] as $sscat): ?>
										<li><label for="cat_<?=$sscat['id'];?>">
											<input type="checkbox" name="categories[]" id="cat_<?=$sscat['id'];?>" value="<?=$sscat['id'];?>" 
											<?php if(isset($_SESSION['addProduit']['categories']) && in_array($sscat['id'],$_SESSION['addProduit']['categories'])){ echo "checked"; } ?>> <?=$sscat['nom'];?>
										</label></li>
									<?php endforeach; 
									echo "</ul>"; 
								endif; ?>
							</li>
						<?php endforeach; ?>
						<!--<li><span id="addCategorie">Cr&eacute;er une nouvelle cat&eacute;gorie</span></li>-->
						</ul>
					</p>
					
					<p class="clearfix">
						<label for="prix" class="grid_2 alpha omega">Prix <span class="asterisque">*</span></label>
						<input type="text" name="prix" id="prix" class="grid_1 omega" value="<?=@$_SESSION['addProduit']['prix'];?>" /><label class="grid_1 alpha omega">&nbsp;&euro;</label>
					</p>
					
					<p class="clearfix">
						<label for="stock" class="grid_2 alpha omega">Qt&eacute; en stock</label>
						<input type="text" name="stock" id="stock" class="grid_1 omega" value="<?=@$_SESSION['addProduit']['stock'];?>" /><label class="grid_1 alpha omega">&nbsp;unit&eacute;s</label>
					</p>
					
					<p class="clearfix">
						<label for="promotion" class="grid_2 alpha omega">Promotion</label>
						<input type="text" name="promotion" id="promotion" class="grid_1 omega" value="<?=@$_SESSION['addProduit']['promotion'];?>" /><label class="grid_1 alpha omega">&nbsp;&euro;</label>
					</p>
					
					<p class="clearfix">
						<?php $public_chk = (isset($_SESSION['addProduit']['public']) && $_SESSION['addProduit']['public'] == 1)?'checked':''; ?>
						<label for="public" class="grid_2 alpha omega"><input type="checkbox" name="public" id="public" class="grid_1 alpha omega" <?=$public_chk;?> />Publier</label>
					</p>
				</div>
				<p class="grid_16">
					<input name="addProduit" type="submit" value="Cr&eacute;er le produit" /> <a href="index.php?module=produits">Retour &agrave; la liste</a>
				</p>
			</form>
			<?php unset($_SESSION['addProduit']);
		endif;
	break;
	
	case 'upd':
		echo "<h1>Modifier un produit</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])):
			$infosProduit = get('*','produits',array('id =' => intval($_GET['pid'])));
			@$infos = $infosProduit['reponse'][0];
			if(count($infos) > 0){
				//on a l'id
				if(isset($_POST['updProduit'])) : //traitement
					$nom = isset($_POST['nom'])?trim($_POST['nom']):null;
					$ref = isset($_POST['reference'])?trim($_POST['reference']):null;
					$photo = isset($_FILES['photo'])?$_FILES['photo']:null;
					$description = isset($_POST['description'])?trim($_POST['description']):null;
					$categories = isset($_POST['categories'])?$_POST['categories']:null;
					$prix = (isset($_POST['prix']) && is_numeric($_POST['prix']))?$_POST['prix']:null;
					$stock = (isset($_POST['stock']) && is_numeric($_POST['stock']))?$_POST['stock']:null;
					$prix_promotion = (isset($_POST['promotion']) && is_numeric($_POST['promotion']) && $_POST['promotion'] > 0)?$_POST['promotion']:null;
					$promotion = (is_numeric($prix_promotion)&& ($prix_promotion > 0))?1:0;
					$public = isset($_POST['public'])?1:0;
											
					$_SESSION['updProduit'] = array(
						'nom' => $nom,
						'reference' => $ref,
						'description' => $description,
						'categories' => $categories,
						'prix' => $prix,
						'stock' => $stock,
						'promotion' => $prix_promotion,
						'public' => $public
					);
					
					if(!empty($nom) && !empty($ref) && !empty($description) && !is_null($prix)){
						$liste_cats = "";
						foreach($categories as $categorie){ $liste_cats .= $categorie . ";"; }
						$datas = array(
							'nom' => addslashes($nom), 
							'reference' => addslashes($ref), 
							'description' => addslashes($description), 
							'categories' => substr($liste_cats,0,-1),
							'prix' => $prix
						);
						
						if(!is_null($photo)){
							//transfère de l'image
							//enregistrement du nom
							$datas['photo'] = $photo['name'];
						}
						
						if(!is_null($stock)){ $datas['stock'] = $stock; }
						if(!is_null($prix_promotion)){ $datas['prix_promo'] = $prix_promotion; $datas['promotion'] = $promotion; }
						$datas['public'] = $public;
						
						if(add($datas,'produits',$infos['id'])){
							unset($_SESSION['updProduit']);
							message('success',"Le produit a bien &eacute;t&eacute; modifi&eacute;.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
						} else {
							message('error',"Une erreur est survenue pendant la modification du produit.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits&action=upd&pid=".$infos['id']."' />";
						}							
					} else {
						//y'a des erreurs
						message('warning',"Certain champs obligatoires sont vides.<br />Merci de remplir ces champs.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits&action=upd&pid=".$infos['id']."' />";
					}
				else : //formulaire
					$nom = (isset($_SESSION['updProduit']['nom']))?$_SESSION['updProduit']['nom']:$infos['nom'];
					$reference = (isset($_SESSION['updProduit']['reference']))?$_SESSION['updProduit']['reference']:$infos['reference'];
					$description = (isset($_SESSION['updProduit']['description']))?$_SESSION['updProduit']['description']:$infos['description'];
					$categories = (isset($_SESSION['updProduit']['categories']))?$_SESSION['updProduit']['categories']:(explode(';',$infos['categories']));
					$prix = (isset($_SESSION['updProduit']['prix']))?$_SESSION['updProduit']['prix']:$infos['prix'];
					$stock = (isset($_SESSION['updProduit']['stock']))?$_SESSION['updProduit']['stock']:$infos['stock'];
					$promotion = (isset($_SESSION['updProduit']['promotion']))?$_SESSION['updProduit']['promotion']:$infos['promotion'];
					$public = (isset($_SESSION['updPage']['public']))?$_SESSION['updPage']['public']:$infos['public']; ?>
					<form method="post" enctype="multipart/form-data">
						<div class="grid_12">
							<p class="grid_6 alpha">
								<label for="nom">Nom <span class="asterisque">*</span></label>
								<input type="text" name="nom" id="nom" class="grid_5 alpha omega" value="<?=@$nom;?>" />
							</p>
							
							<p class="grid_6 omega">
								<label for="reference">R&eacute;f&eacute;rence <span class="asterisque">*</span></label>
								<input type="text" name="reference" id="reference" value="<?=@$reference;?>" />
							</p>
							
							<p class="grid_6 suffix_6 alpha">
								<label for="photo">Photo</label>
								<input type="file" name="photo" id="photo" />
							</p>
							
							<p class="grid_11 alpha">
								<label for="description">Description <span class="asterisque">*</span></label>
								<textarea name="description" id="description" class="grid_11 alpha"><?=@$description;?></textarea>
								<script type="text/javascript">//<![CDATA[
									CKEDITOR.replace( 'description', {
										toolbar : 'cbc',
										extraPlugins : 'uicolor',
										uiColor : '#efefef'
									});
								//]]></script>
							</p>
						</div>
						<div class="grid_4">
							<p>
								<label for="categories">Cat&eacute;gorie(s) du produit <span class="asterisque">*</span></label>
								<ul class="list" id="ul_cats">
								<?php $cats = get(array('id','nom'),'categories',array('parent =' => 0));
								foreach($cats['reponse'] as $cat): ?>
									<li><label for="cat_<?=$cat['id'];?>">
										<input type="checkbox" name="categories[]" id="cat_<?=$cat['id'];?>" value="<?=$cat['id'];?>" 
										<?php if(isset($categories) && in_array($cat['id'],$categories)){ echo "checked"; } ?>> <?=$cat['nom'];?></label>
										<?php $sscats = get(array('id','nom'),'categories',array('parent =' => $cat['id']));
										if(count($sscats['reponse']) > 0):
											echo "<ul>"; 
											foreach($sscats['reponse'] as $sscat): ?>
												<li><label for="cat_<?=$sscat['id'];?>">
													<input type="checkbox" name="categories[]" id="cat_<?=$sscat['id'];?>" value="<?=$sscat['id'];?>" 
													<?php if(isset($categories) && in_array($sscat['id'],$categories)){ echo "checked"; } ?>> <?=$sscat['nom'];?>
												</label></li>
											<?php endforeach; 
											echo "</ul>"; 
										endif; ?>
									</li>
								<?php endforeach; ?>
								<!--<li><span id="addCategorie">Cr&eacute;er une nouvelle cat&eacute;gorie</span></li>-->
								</ul>
							</p>
							
							<p class="clearfix">
								<label for="prix" class="grid_2 alpha omega">Prix <span class="asterisque">*</span></label>
								<input type="text" name="prix" id="prix" class="grid_1 omega" value="<?=@$prix;?>" /><label class="grid_1 alpha omega">&nbsp;&euro;</label>
							</p>
							
							<p class="clearfix">
								<label for="stock" class="grid_2 alpha omega">Qt&eacute; en stock</label>
								<input type="text" name="stock" id="stock" class="grid_1 omega" value="<?=@$stock;?>" /><label class="grid_1 alpha omega">&nbsp;unit&eacute;s</label>
							</p>
							
							<p class="clearfix">
								<label for="promotion" class="grid_2 alpha omega">Promotion</label>
								<input type="text" name="promotion" id="promotion" class="grid_1 omega" value="<?=@$promotion;?>" /><label class="grid_1 alpha omega">&nbsp;&euro;</label>
							</p>
							
							<p class="clearfix">
								<?php $public_chk = (isset($public) && $public == 1)?'checked':''; ?>
								<label for="public" class="grid_2 alpha omega"><input type="checkbox" name="public" id="public" class="grid_1 alpha omega" <?=$public_chk;?> />Publier</label>
							</p>
						</div>
						<p class="grid_16">
							<input name="updProduit" type="submit"  value="Mettre &agrave; jour"/> <a href="index.php?module=produits">Retour &agrave; la liste</a>
						</p>
					</form>
					<?php unset($_SESSION['updProduit']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Ce produit ne peut &ecirc;tre modifi&eacute; car il n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
			}
		else :
			//on a pas d'id
			message('warning',"Ce produit ne peut &ecirc;tre modifi&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer un produit</h1>";
		if(isset($_GET['pid']) && is_numeric($_GET['pid'])) :
			//on a l'id
			if(delete(intval($_GET['pid']),'produits')){
				message('success',"Le produit a bien &eacute;t&eacute; supprim&eacute;.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. Le produit n'a pas pu &ecirc;tre supprim&eacute;.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
			}
		else :
			//on a pas d'id
			message('warning',"Ce produit ne peut &ecirc;tre supprim&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=produits' />";
		endif;
	break;
	
	default:
		//liste des produits
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		
		$nbpp = 10;
		$listProduits = get('*','produits',null,"AND",array('id'=>'ASC'),array($_GET['d'],$nbpp));
		$produitsTotal = $listProduits['total'];
		
		$listProduits = $listProduits['reponse'];
		
		if(count($listProduits) > 0) : //on affiche la liste ?>
			<a href="index.php?module=produits&action=add" class="button">Ajouter un produit</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th>R&eacute;f.</th>
						<th>Nom</th>
						<th>Cat&eacute;gories</th>
						<th>Prix</th>
						<th>Promotion</th>
						<th>Stock</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listProduits as $lp){
					echo "<tr>
						<td>".$lp['reference']."</td>
						<td>".$lp['nom']."</td>
						<td>"; $categories = explode(';',$lp['categories']); $cats = "";
							foreach($categories as $c):
								$ci = get(array('nom'),'categories',array('id =' => $c)); $cats .= $ci['reponse'][0]['nom'].', ';
							endforeach;
						echo substr($cats,0,-2) . "</td>
						<td>".format_price($lp['prix'])."</td>
						<td>".isTrue($lp['promotion']).(($lp['promotion'] == 1)? " (".format_price($lp['prix_promo']).")":'')."</td>
						<td>".$lp['stock']."</td>
						<td>
							<a class='button' href='index.php?module=produits&action=upd&pid=".$lp['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=produits&action=dlt&pid=".$lp['id']."'>Supprimer</a>
						</td>
					</tr>";		
				} ?></tbody>
			</table>                            
			<?php pagination($produitsTotal,$nbpp,"index.php?module=produits&d=");
		else :
			//pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucun produit pour le moment.<br />Voulez-vous <a href='index.php?module=produits&action=add'>en cr&eacute;er un ?</a>");
		endif;
	break;
}
?>
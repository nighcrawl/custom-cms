<?php
/***
 *
 *	admin/modules/adm.clients.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Clients</h1>";
switch(@$_GET['action']){
	case 'add':
		echo "<h1>Ajouter un client</h1>";
		if(isset($_POST['addClient'])):
			$_SESSION['addClient'] = $_POST;
			
			/** Client **/
			$nom = isset($_POST['nom'])?trim($_POST['nom']):null;
			$prenom = isset($_POST['prenom'])?trim($_POST['prenom']):null;
			$email = isset($_POST['email'])?trim($_POST['email']):null;
			$pwd = isset($_POST['pwd'])?trim($_POST['pwd']):null;
			$telephone = isset($_POST['telephone'])?trim($_POST['telephone']):null;
			/** Adresse facturation **/
			$adresse_fac = isset($_POST['adresse_fac'])?trim($_POST['adresse_fac']):null;
			$cp_fac = isset($_POST['cp_fac'])?trim($_POST['cp_fac']):null;
			$ville_fac = isset($_POST['ville_fac'])?trim($_POST['ville_fac']):null;
			$pays_fac = isset($_POST['pays_fac'])?trim($_POST['pays_fac']):null;
			/** Adresse livraison **/
			$adresse_liv = isset($_POST['adresse_liv'])?trim($_POST['adresse_liv']):null;
			$cp_liv = isset($_POST['cp_liv'])?trim($_POST['cp_liv']):null;
			$ville_liv = isset($_POST['ville_liv'])?trim($_POST['ville_liv']):null;
			$pays_liv = isset($_POST['pays_liv'])?trim($_POST['pays_liv']):null;
			
			if(!empty($nom) && !empty($prenom) && !empty($email) && !empty($pwd) && !empty($telephone) 
			&& !empty($adresse_fac) && !empty($cp_fac) && !empty($ville_fac) && !empty($pays_fac) 
			&& !empty($adresse_liv) && !empty($cp_liv) && !empty($ville_liv) && !empty($pays_liv)){
				
				if(!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/",$email)){
					$errors[] = "Le format de l'adresse email n'est pas valide"; 
				}
				
				if(count($errors) == 0){
					//création du client
					$datas_client = array(
						'nom' => addslashes($nom),
						'prenom' => addslashes($prenom),
						'email' => $email,
						'password' => $pwd,
						'telephone' => $telephone
					);
					$clientID = add($datas_client,'clients');
					if(is_numeric($clientID)){
						//création de l'adresse de facturation
						$datas_adr_fac = array(
							'adresse' => addslashes($adresse_fac),
							'cp' => $cp_fac,
							'ville' => addslashes($ville_fac),
							'pays' => addslashes($pays_fac),
							'type' => 'fac',
							'client' => $clientID
						);
						$adrFacCli = add($datas_adr_fac,'adresses_clients');
						//création de l'adresse de livraison
						$datas_adr_liv = array(
							'adresse' => addslashes($adresse_liv),
							'cp' => $cp_liv,
							'ville' => addslashes($ville_liv),
							'pays' => addslashes($pays_liv),
							'type' => 'liv',
							'client' => $clientID
						);
						$adrLivCli = add($datas_adr_liv,'adresses_clients');
						//on rajoute les adresses créées à l'enregistrement client
						if(add(array('adresse_facturation_preferee' => $adrFacCli, 'adresse_livraison_preferee' => $adrLivCli),'clients',$clientID)){
							//ça c'est bien passé
							unset($_SESSION['addClient']);
							message('success',"Le client a bien &eacute;t&eacute; cr&eacute;&eacute;.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
						} else {
							//erreur pendant la création
							message('error',"Une erreur est survenue pendant la cr&eacute;ation du client.<br />Merci de r&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients&action=add' />";
						}
					}
					
				} else {
					//display errors
					message('warning',$errors);
					echo "<meta http-equiv='refresh' content='5;URL=index.php?module=clients&action=add' />";	
				}
				
			} else {
				message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'aucun champs n'est vide.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients&action=add' />";							
			}
		else: //formulaire ?>
			<form method="post">
				<div class="grid_12">
					<p class="grid_5 alpha">
						<label for="nom">Nom</label>
						<input type="text" name="nom" id="nom" class="grid_4 alpha" value="<?=@$_SESSION['addClient']['nom'];?>" />
					</p>
					
					<p class="grid_5 omega suffix_2">
						<label for="prenom">Pr&eacute;nom</label>
						<input type="text" name="prenom" id="prenom" class="grid_4 alpha" value="<?=@$_SESSION['addClient']['prenom'];?>" />
					</p>
					
					<p class="grid_5 alpha">
						<label for="email">Email</label>
						<input type="text" name="email" id="email" class="grid_4 alpha" value="<?=@$_SESSION['addClient']['email'];?>" />
					</p>
					
					<p class="grid_5 omega suffix_2">
						<label for="pwd">Mot de passe</label>
						<input type="password" name="pwd" id="pwd" class="grid_4 alpha" />
					</p>
													
					<p class="clearfix">
						<label for="telephone">T&eacute;l&eacute;phone <small>(ex: +33 1 23 45 67 89)</small></label>
						<input type="text" name="telephone" id="telephone" class="grid_4 alpha" value="<?=@$_SESSION['addClient']['telephone'];?>" />
					</p>
				</div>
				
				<fieldset class="grid_12 suffix_4">
					<h3>Adresse de facturation</h3>
					<p class="grid_7 alpha suffix_5">
						<label for="adresse_fac">Adresse</label>
						<input type="text" name="adresse_fac" id="adresse_fac" class="grid_7 alpha omega" value="<?=@$_SESSION['addClient']['adresse_fac'];?>" />
					</p>
					
					<p class="grid_2 alpha">
						<label for="cp_fac">Code postal</label>
						<input type="text" name="cp_fac" id="cp_fac" style="width:85%;" value="<?=@$_SESSION['addClient']['cp_fac'];?>" />
					</p>
					
					<p class="grid_5 omega suffix_5">
						<label for="ville_fac">Ville</label>
						<input type="text" name="ville_fac" id="ville_fac" class="grid_5 alpha omega" value="<?=@$_SESSION['addClient']['ville_fac'];?>" />
					</p>
					
					<p class="grid_5 alpha">
						<label for="pays_fac">Pays</label>
						<input type="text" name="pays_fac" id="pays_fac" class="grid_5 alpha omega" value="<?=@$_SESSION['addClient']['pays_fac'];?>" />
					</p>
				</fieldset>
				
				<fieldset class="grid_12 suffix_4">
					<h3>Adresse de livraison</h3>
					<p class="grid_7 alpha suffix_5">
						<label for="adresse_liv">Adresse</label>
						<input type="text" name="adresse_liv" id="adresse_liv" class="grid_7 alpha omega" value="<?=@$_SESSION['addClient']['adresse_liv'];?>" />
					</p>
					
					<p class="grid_2 alpha">
						<label for="cp_liv">Code postal</label>
						<input type="text" name="cp_liv" id="cp_liv" style="width:85%;" value="<?=@$_SESSION['addClient']['cp_liv'];?>" />
					</p>
					
					<p class="grid_5 omega suffix_5">
						<label for="ville_liv">Ville</label>
						<input type="text" name="ville_liv" id="ville_liv" class="grid_5 alpha omega" value="<?=@$_SESSION['addClient']['ville_liv'];?>" />
					</p>
					
					<p class="grid_5 alpha">
						<label for="pays_liv">Pays</label>
						<input type="text" name="pays_liv" id="pays_liv" class="grid_5 alpha omega" value="<?=@$_SESSION['addClient']['pays_liv'];?>" />
					</p>
				</fieldset>
				
				<p class="grid_16  clearfix">
					<input type="submit" name="addClient" value="Cr&eacute;er le client" /> <a href="index.php?module=clients">Retour &agrave; la liste</a>
				</p>
			</form>
		<?php endif;
	break;
	
	case 'upd':
		echo "<h1>D&eacute;tails du client</h1>";
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])):
			$infosClient = get('*','clients',array('id =' => intval($_GET['cid'])));
			@$infos = $infosClient['reponse'][0];
			
			$infosFact = get('*','adresses_clients',array('id =' => $infos['adresse_facturation_preferee'], 'type =' => 'fac', 'client =' => $infos['id']));
			$infosFact = $infosFact['reponse'][0];
			
			$infosLivr = get('*','adresses_clients',array('id =' => $infos['adresse_livraison_preferee'], 'type =' => 'liv', 'client =' => $infos['id']));
			$infosLivr = $infosLivr['reponse'][0];
			
			if(count($infos) > 0){
				if(isset($_POST['updClient'])): //traitement
					$_SESSION['updClient'] = $_POST;
			
					/** Client **/
					$nom = isset($_POST['nom'])?trim($_POST['nom']):null;
					$prenom = isset($_POST['prenom'])?trim($_POST['prenom']):null;
					$email = isset($_POST['email'])?trim($_POST['email']):null;
					$pwd = isset($_POST['pwd'])?trim($_POST['pwd']):null;
					$telephone = isset($_POST['telephone'])?trim($_POST['telephone']):null;
					/** Adresse facturation **/
					$adresse_fac = isset($_POST['adresse_fac'])?trim($_POST['adresse_fac']):null;
					$cp_fac = isset($_POST['cp_fac'])?trim($_POST['cp_fac']):null;
					$ville_fac = isset($_POST['ville_fac'])?trim($_POST['ville_fac']):null;
					$pays_fac = isset($_POST['pays_fac'])?trim($_POST['pays_fac']):null;
					/** Adresse livraison **/
					$adresse_liv = isset($_POST['adresse_liv'])?trim($_POST['adresse_liv']):null;
					$cp_liv = isset($_POST['cp_liv'])?trim($_POST['cp_liv']):null;
					$ville_liv = isset($_POST['ville_liv'])?trim($_POST['ville_liv']):null;
					$pays_liv = isset($_POST['pays_liv'])?trim($_POST['pays_liv']):null;
					
					if(!empty($nom) && !empty($prenom) && !empty($email) && !empty($pwd) && !empty($telephone) 
					&& !empty($adresse_fac) && !empty($cp_fac) && !empty($ville_fac) && !empty($pays_fac) 
					&& !empty($adresse_liv) && !empty($cp_liv) && !empty($ville_liv) && !empty($pays_liv)){
						
						if(!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/",$email)){
							$errors[] = "Le format de l'adresse email n'est pas valide"; 
						}
						
						if(count($errors) == 0){
							
							$datas_client = array(
								'nom' => addslashes($nom),
								'prenom' => addslashes($prenom),
								'email' => $email,
								'telephone' => $telephone
							);
							
							if(isset($pwd) && !is_null($pwd)){
								$datas['password'] = $pwd;
							}
							
							//mise à jour
							$resUpdCli = add($datas,'clients',$infos['id']);
							
							$datas_adr_fac = array();
							if($infosFact['adresse'] != $adresse_fac) $datas_adr_fac['adresse'] = $adresse_fac;
							if($infosFact['cp'] != $cp_fac) $datas_adr_fac['cp'] = $cp_fac;
							if($infosFact['ville'] != $ville_fac) $datas_adr_fac['ville'] = $ville_fac;
							if($infosFact['pays'] != $pays_fac) $datas_adr_fac['pays'] = $pays_fac;
							
							if(count($datas_adr_fac) > 0){
								$resUpdFac = add($datas_adr_fac,'adresses_clients',$infos['adresse_facturation_preferee']);
							} else {
								$resUpdFac = true;
							}
							
							$datas_adr_liv = array();
							if($infosLivr['adresse'] != $adresse_liv) $datas_adr_liv['adresse'] = $adresse_liv;
							if($infosLivr['cp'] != $cp_liv) $datas_adr_liv['cp'] = $cp_liv;
							if($infosLivr['ville'] != $ville_liv) $datas_adr_liv['ville'] = $ville_liv;
							if($infosLivr['pays'] != $pays_liv) $datas_adr_liv['pays'] = $pays_liv;
							
							if(count($datas_adr_liv) > 0){
								$resUpdLiv = add($datas_adr_liv,'adresses_clients',$infos['adresse_livraison_preferee']);
							} else {
								$resUpdLiv = true;
							}
							
							if($resUpdCli && $resUpdFac && $resUpdLiv){
								unset($_SESSION['updClient']);
								message('success',"Le client a bien &eacute;t&eacute; modifi&eacute;.");
								echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
							} else {
								//erreur pendant la création
								message('error',"Une erreur est survenue pendant la modification du client.<br />Merci de r&eacute;essayez.");
								echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients&action=upd&cid=".$infos['id']."' />";
							}
							
						} else {
							message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'aucun champs n'est vide.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients&action=upd&cid=".$infos['id']."' />";
						}
						
					} else {
						message('warning',"Tous les champs sont obligatoires. Merci de v&eacute;rifier qu'aucun champs n'est vide.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients&action=upd&cid=".$infos['id']."' />";
					}
				else : //formulaire
					$nom = (isset($_SESSION['updClient']['nom']))?$_SESSION['updClient']['nom']:$infos['nom'];
					$prenom = (isset($_SESSION['updClient']['prenom']))?$_SESSION['updClient']['prenom']:$infos['prenom'];
					$email = (isset($_SESSION['updClient']['email']))?$_SESSION['updClient']['email']:$infos['email'];
					$pwd = (isset($_SESSION['updClient']['pwd']))?$_SESSION['updClient']['pwd']:$infos['password'];
					$telephone = (isset($_SESSION['updClient']['telephone']))?$_SESSION['updClient']['telephone']:$infos['telephone'];
					
					$adresse_fac = (isset($_SESSION['updClient']['adresse_fac']))?$_SESSION['updClient']['adresse_fac']:$infosFact['adresse'];
					$cp_fac = (isset($_SESSION['updClient']['cp_fac']))?$_SESSION['updClient']['cp_fac']:$infosFact['cp'];
					$ville_fac = (isset($_SESSION['updClient']['ville_fac']))?$_SESSION['updClient']['ville_fac']:$infosFact['ville'];
					$pays_fac = (isset($_SESSION['updClient']['pays_fac']))?$_SESSION['updClient']['pays_fac']:$infosFact['pays'];
					
					$adresse_liv = (isset($_SESSION['updClient']['adresse_liv']))?$_SESSION['updClient']['adresse_liv']:$infosFact['adresse'];
					$cp_liv = (isset($_SESSION['updClient']['cp_liv']))?$_SESSION['updClient']['cp_liv']:$infosFact['cp'];
					$ville_liv = (isset($_SESSION['updClient']['ville_liv']))?$_SESSION['updClient']['ville_liv']:$infosFact['ville'];
					$pays_liv = (isset($_SESSION['updClient']['pays_liv']))?$_SESSION['updClient']['pays_liv']:$infosFact['pays'];

				?>
					
					<form method="post">
						<div class="grid_12">
							<p class="grid_5 alpha">
								<label for="nom">Nom</label>
								<input type="text" name="nom" id="nom" class="grid_4 alpha" value="<?=$nom;?>" />
							</p>
							
							<p class="grid_5 omega suffix_2">
								<label for="prenom">Pr&eacute;nom</label>
								<input type="text" name="prenom" id="prenom" class="grid_4 alpha" value="<?=$prenom;?>" />
							</p>
							
							<p class="grid_5 alpha">
								<label for="email">Email</label>
								<input type="text" name="email" id="email" class="grid_4 alpha" value="<?=$email;?>" />
							</p>
							
							<p class="grid_5 omega suffix_2">
								<label for="pwd">Mot de passe</label>
								<input type="password" name="pwd" id="pwd" class="grid_4 alpha" />
							</p>
															
							<p class="clearfix">
								<label for="telephone">T&eacute;l&eacute;phone <small>(ex: +33 1 23 45 67 89)</small></label>
								<input type="text" name="telephone" id="telephone" class="grid_4 alpha" value="<?=$telephone;?>" />
							</p>
						</div>
						
						<fieldset class="grid_12 suffix_4">
							<h3>Adresse de facturation</h3>
							<p class="grid_7 alpha suffix_5">
								<label for="adresse_fac">Adresse</label>
								<input type="text" name="adresse_fac" id="adresse_fac" class="grid_7 alpha omega" value="<?=$adresse_fac;?>" />
							</p>
							
							<p class="grid_2 alpha">
								<label for="cp_fac">Code postal</label>
								<input type="text" name="cp_fac" id="cp_fac" style="width:85%;" value="<?=$cp_fac;?>" />
							</p>
							
							<p class="grid_5 omega suffix_5">
								<label for="ville_fac">Ville</label>
								<input type="text" name="ville_fac" id="ville_fac" class="grid_5 alpha omega" value="<?=$ville_fac;?>" />
							</p>
							
							<p class="grid_5 alpha">
								<label for="pays_fac">Pays</label>
								<input type="text" name="pays_fac" id="pays_fac" class="grid_5 alpha omega" value="<?=$pays_fac;?>" />
							</p>
						</fieldset>
						
						<fieldset class="grid_12 suffix_4">
							<h3>Adresse de livraison</h3>
							<p class="grid_7 alpha suffix_5">
								<label for="adresse_liv">Adresse</label>
								<input type="text" name="adresse_liv" id="adresse_liv" class="grid_7 alpha omega" value="<?=$adresse_liv;?>" />
							</p>
							
							<p class="grid_2 alpha">
								<label for="cp_liv">Code postal</label>
								<input type="text" name="cp_liv" id="cp_liv" style="width:85%;" value="<?=$cp_liv;?>" />
							</p>
							
							<p class="grid_5 omega suffix_5">
								<label for="ville_liv">Ville</label>
								<input type="text" name="ville_liv" id="ville_liv" class="grid_5 alpha omega" value="<?=$ville_liv;?>" />
							</p>
							
							<p class="grid_5 alpha">
								<label for="pays_liv">Pays</label>
								<input type="text" name="pays_liv" id="pays_liv" class="grid_5 alpha omega" value="<?=$pays_liv;?>" />
							</p>
						</fieldset>
						
						<p class="grid_16  clearfix">
							<input type="submit" name="addClient" value="Cr&eacute;er le client" /> <a href="index.php?module=clients">Retour &agrave; la liste</a>
						</p>
					</form><?php unset($_SESSION['updClient']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Ce client ne peut &ecirc;tre affich&eacute; car il n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
			}
		else:
			//on a pas d'id
			message('warning',"Ce client ne peut &ecirc;tre affich&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer un client</h1>";
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])) :
			//on a l'id
			if(delete(intval($_GET['cid']),'clients')){
				message('success',"Le client a bien &eacute;t&eacute; supprim&eacute;.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. Le client n'a pas pu &ecirc;tre supprim&eacute;.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=clients' />";
			}
		else :
			//on a pas d'id
			message('warning',"Ce client ne peut &ecirc;tre supprim&eacute; car il n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=client' />";
		endif;
	break;
	
	default:
		//liste des clients
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		$nbpp = 10;
		$listClients = get('*','clients',null,"AND",array('nom'=>'ASC'),array($_GET['d'],$nbpp));
		$clientsTotal = $listClients['total'];
		$listClients = $listClients['reponse'];
		
		if(count($listClients) > 0): //on affiche la liste ?>
			<a href="index.php?module=clients&action=add" class="button">Ajouter un client</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th># ID</th>
						<th>Nom</th>
						<th>Pr&eacute;nom</th>
						<th>Email</th>
						<th>T&eacute;&eacute;phone</th>
						<th>Total command&eacute;</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listClients as $lc){
					$cdesCli = get(array('total'),'commandes',array('client =' => $lc['id']));
					$totalCdes = 0;
					foreach($cdesCli['reponse'] as $cdes){
						$totalCdes = $totalCdes + $cdes['total'];
					}
					echo "<tr>
						<td>".$lc['id']."</td>
						<td>".$lc['nom']."</td>
						<td>".$lc['prenom']."</td>
						<td>".$lc['email']."</td>
						<td>".$lc['telephone']."</td>
						<td>".format_price($totalCdes)."</td>
						<td>
							<a class='button' href='index.php?module=clients&action=upd&cid=".$lc['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=clients&action=dlt&cid=".$lc['id']."'>Supprimer</a>
						</td>
					</tr>";
				} ?></tbody>
			</table>
			<?php pagination($clientsTotal,$nbpp,"index.php?module=clients&d=");
		else:
			//pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucun client pour le moment.<br />Voulez-vous <a href='index.php?module=clients&action=add'>en cr&eacute;er un ?</a>");
		endif;
	break;
}
?>
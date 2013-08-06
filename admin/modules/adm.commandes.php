<?php
/***
 *
 *	admin/modules/adm.commandes.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Commandes</h1>";
switch(@$_GET['action']){
	/* case 'add':
		// on génère une référence commande en utilisant 
		// la date et heure + un chiffre aléatoire entre 0 et 99
		$mt_rand = mt_rand(0,99);
		if($mt_rand < 10) $mt_rand = "0" . $mt_rand;
		$refCommande = date('Ymd-Hi-s') . $mt_rand;
		echo $refCommande;				
	break; */
	
	case 'upd':
		echo "<h2>Details de la commande</h2>";
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])):
			//on récupère les infos générales de la commande en paramètre
			$infosCommande = get('*','commandes',array('id =' => intval($_GET['cid'])));
			@$infos = $infosCommande['reponse'][0];
			
			//on récupère le détail de la commande en paramètre
			$infosLignesCde = get('*','lignes_commandes', array('commande =' => $infos['id']));
			@$lignesCde = $infosLignesCde['reponse']; 
			
			//on récupère l'historique des traitements effectués sur la commande
			$infosHistoCde = get('*','historique_traitements', array('commande =' => $infos['id']),"AND",array('date' => 'DESC', 'id' => 'DESC'));
			@$histoCde = $infosHistoCde['reponse'];
			
			//on récupère les infos client
			$infosClient = get('*','clients',array('id =' => $infos['client']));
			@$client = $infosClient['reponse'][0];
			
			$sstotal = 0;
			$tva = 0;
			
			if(isset($_POST['updCommande'])): //traitement
				$_SESSION['updCommande'] = $_POST;
				
				$statut = (isset($_POST['statut']) && !empty($_POST['statut']))?trim(strtolower($_POST['statut'])):null;
				$numcolis = (isset($_POST['numcolis']) && !empty($_POST['numcolis']))?trim($_POST['numcolis']):null;
				if(!is_null($statut)){
					$datas['statut'] = $statut;
					$datas_histo = array(
						'statut' => $statut,
						'commande' => $infos['id'],
						'date' => date('Y-m-d H:i:s')
					);
				}
				
				if(isset($datas) && count($datas) > 0){
					//on met à jour
					if($statut == 'expedition' && is_null($numcolis)) {
						message('error',"Une exp&eacute;dition ne peut pas se faire sans num&eacute;ro de colis. Merci d'en renseigner un.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=commandes&action=upd&cid=".$infos['id']."' />";
					} else {
						$datas['colis'] = $numcolis;
						
						echo "<pre>";
							print_r($datas);
							print_r($datas_histo);
						echo "</pre>";
						//exit();
						
						add($datas_histo,'historique_traitements');
					
						if(add($datas,'commandes',$infos['id'])){
							message('success',"La commande a bien &eacute;t&eacute; modifi&eacute;e.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=commandes&action=upd&cid=".$infos['id']."' />";
						} else {
							message('error',"Une erreur est survenue pendant la modification de la commande. R&eacute;essayer.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=commandes&action=upd&cid=".$infos['id']."' />";
						}
					}
				} else {
					echo "<meta http-equiv='refresh' content='0;URL=index.php?module=commandes&action=upd&cid=".$infos['id']."' />";
				}
			else : //formulaire
				echo "<div class='grid_8'>
					<p><strong>R&eacute;f. commande :</strong> ".$infos['reference']."</p>
					<p><strong>Date de la commande :</strong> ".date('d/m/Y à H\hi',strtotime($infos['date']))."</p>
				</div>";
				
				echo "<div class='grid_8'>
					<p><strong>Montant total :</strong> ".format_price($infos['total'])."</p>
					<p><strong>Statut de la commande :</strong> ".getLibelleCommande($infos['statut'])."</p>
				</div>"; ?>
                
                <?php $client = get(array('nom','prenom'),'clients',array('id =' => $infos['client']));
				$client = $client['reponse'][0]['prenom'] . " " . $client['reponse'][0]['nom'];	
				echo "<div class='grid_8'>
					<p><strong>Adresse de facturation</strong></p>
					<p>".$client."<br />".$infos['facturation']."</p>
				</div>
				
				<div class='grid_8'>
					<p><strong>Adresse de livraison</strong></p>
					<p>".$client."<br />".$infos['livraison']."</p>
				</div>";
				?>
				
				<div class="grid_16">
					<table width="100%" class="data">
						<thead>
							<tr>
								<th>R&eacute;f. produit</th>
								<th>Libell&eacute;</th>
								<th>Prix unitaire</th>
								<th>Quantit&eacute;</th>
								<th>Sous-total</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($lignesCde as $line):
												
							$r = explode('-',$line['produit']);
							$reference = $r[1].'-'.$r[2];

						
							$pdt = get('*','produits',array('reference =' => $reference));
							$pdt = $pdt['reponse'][0];
							$tva += $line['tva'];
							$sstotal += ($line['prix'] * $line['quantite']);
							echo "<tr>
								<td>".$reference."</td>
								<td>".stripslashes($pdt['nom'])." (".$r[0].")</td>
								<td>".format_price($line['prix'])."</td>
								<td>".$line['quantite']."</td>
								<td>".format_price($line['prix'] * $line['quantite'])."</td>
							</tr>";
						endforeach; ?>
						</tbody>
					</table>
				</div>
				
				<div class="prefix_10 grid_6">  
					<table class="data" align="right" style="margin-top:0;">
						<tr class="grid_6 omega">
							<td class="grid_3 alpha omega"><strong>Sous-total</strong></td>
							<td class="grid_3 alpha omega"><?=format_price($sstotal);?></td>
						</tr>
                        <tr class="grid_6 omega">
							<td class="grid_3 alpha omega"><strong>TVA</strong></td>
							<td class="grid_3 alpha omega"><?=format_price($tva);?></td>
						</tr>
						<tr class="grid_6 omega">
							<td class="grid_3 alpha omega"><strong>Frais de port</strong></td>
							<td class="grid_3 alpha omega"><?=format_price($infos['fdp']);?></td>
						</tr>
						<tr class="grid_6 omega">
							<td class="grid_3 alpha omega"><strong>Montant Total</strong></td>
							<td class="grid_3 alpha omega"><?=format_price($infos['total']);?></td>
						</tr>
					</table>       
				</div>
				
				<div class="grid_16">
					<h2>Historique des traitements effectu&eacute;s sur la commande</h2>
					<?php if(count($histoCde) > 0): $i = 0; ?>
						<table width="100%" class="data">
							<thead>
								<tr>
									<th>Date</th>
									<th>Statut de la commande</th>
									<th>Commentaire</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach($histoCde as $histo): 
								if($i <= 0) : ?>
                                    <tr class="active_row">
                                        <td><?=date('d/m/Y à H:i:s',strtotime($histo['date']));?></td>
                                        <td><?=getLibelleCommande($histo['statut']);?></td>
                                        <td><?=stripslashes($histo['message']);?></td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <td><?=date('d/m/Y à H:i:s',strtotime($histo['date']));?></td>
                                        <td><?=getLibelleCommande($histo['statut']);?></td>
                                        <td><?=stripslashes($histo['message']);?></td>
                                    </tr>
                                <?php endif;
								$i++;
							endforeach; ?>
							</tbody>
						 </table><?php 
					else:
						echo "Aucun traitement effectu&eacute; sur cette commande";
					endif;?>
				</div>
				
				<div class="grid_16">
					<h2>Modifier le statut de la commande</h2>
					<form method="post">
						<p class="grid_5 alpha">
							<label for="statut">Modifier le statut</label>
							<select name="statut" id="statut">
								<?php 
								$statut = isset($_SESSION['updCommande']['statut'])?$_SESSION['updCommande']['statut']:$infos['statut'];
								$numcolis = isset($_SESSION['updCommande']['numcolis'])?$_SESSION['updCommande']['numcolis']:$infos['colis'];
								?>
								<option value="">--</option>
                                <option <?php if($statut == 'paiement_valide') echo "selected"; ?> value="paiement_valide">Paiement valid&eacute;</option>
								<option <?php if($statut == 'refus_paiement') echo "selected"; ?> value="refus_paiement">Paiement refus&eacute;</option>
								<option <?php if($statut == 'annule') echo "selected"; ?> value="annule">Annul&eacute;e</option>
								<option <?php if($statut == 'attente') echo "selected"; ?> value="attente">En attente de validation</option>
								<option <?php if($statut == 'valide') echo "selected"; ?> value="valide">Valid&eacute;e</option>
								<option <?php if($statut == 'traitement') echo "selected"; ?> value="traitement">Traitement en cours</option>
								<option <?php if($statut == 'expedition') echo "selected"; ?> value="expedition">Exp&eacute;di&eacute;e</option>
							</select>
						</p>
						<p class="grid_5 omega">
							<label for="numcolis">N&deg; colis</label>
							<input type="text" name="numcolis" id="numcolis" value="<?=$numcolis;?>"  />
						</p>
						<p class="grid_6 alpha omega">
							<label>&nbsp;</label>
							<input type="submit" name="updCommande" value="Mettre &agrave; jour la commande" /> <a href="index.php?module=commandes">Retour &agrave; la liste</a>
						</p>
					</form>
				</div>
			<?php endif;
		else :
			//on a pas d'id
			message('warning',"Cette commandes ne peut &ecirc;tre affich&eacute; car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=commandes' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer une commande</h1>";
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])) :
			//on a l'id
			if(delete(intval($_GET['cid']),'commandes')){
				message('success',"La commande a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=commandes' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. La commande n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=commandes' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette commande ne peut &ecirc;tre supprim&eacute; car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=commandes' />";
		endif;
	break;
	
	default:
		//liste des commandes
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		
		$nbpp = 10;
		$listCommandes = get('*','commandes',null,"AND",array('date' => 'DESC'),array($_GET['d'],$nbpp));
		$commandesTotal = $listCommandes['total'];
		
		$listCommandes = $listCommandes['reponse'];
		
		if(count($listCommandes) > 0) : //on  affiche la liste ?>
			<!--<a href="index.php?module=commandes&action=add" class="button">Nouvelle commande</a>-->
			<table width="100%" class="data">
				<thead>
					<tr>
						<th>R&eacute;f.</th>
						<th>Date</th>
						<th>Client</th>
						<th>Montant Total</th>
						<th>Statut</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listCommandes as $lc){
					$client = get(array('nom','prenom'),'clients',array('id =' => $lc['client']));
					$client = $client['reponse'][0]['prenom'] . " " . $client['reponse'][0]['nom'];
					
					echo "<tr>
						<td>".$lc['reference']."</td>
						<td>".date('d/m/Y',strtotime($lc['date']))."</td>
						<td>".$client."</td>
						<td>".format_price($lc['total'])."</td>
						<td>".getLibelleCommande($lc['statut'])."</td>
						<td>
							<a class='button' href='index.php?module=commandes&action=upd&cid=".$lc['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=commandes&action=dlt&cid=".$lc['id']."'>Supprimer</a>
						</td>
					</tr>";		
				} ?></tbody>
			</table>                            
			<?php pagination($commandesTotal,$nbpp,"index.php?module=commandes&d="); 
			
			$stats_commandes = stats();
			//echo "<pre>";print_r($stats_commandes);echo "</pre>";
		else :
			//pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucune commande pour le moment.<br />Voulez-vous <a href='index.php?module=commandes&action=add'>en cr&eacute;er une ?</a>");
		endif;
	break;
}
?>
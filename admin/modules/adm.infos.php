<?php
/***
 *
 *	admin/modules/adm.infos.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Informations g&eacute;n&eacute;rales</h1>";
if(isset($_POST['updInfos'])){
	//on vérifie le formulaire
	$adresse = nl2br($_POST['adresse-postale']);
	$tel = trim($_POST['tlphone']);
	$fax = trim($_POST['fax']);
	$email = trim($_POST['adresse-email']);
					
	$sqlAdresse = get('*','infos',array('id =' => 1));
	$sqlAdresse = $sqlAdresse['reponse'][0];
	
	$sqlTel = get('*','infos',array('id =' => 2));
	$sqlTel = $sqlTel['reponse'][0];
	
	$sqlFax = get('*','infos',array('id =' => 3));
	$sqlFax = $sqlFax['reponse'][0];
	
	$sqlEmail = get('*','infos',array('id =' => 4));
	$sqlEmail = $sqlEmail['reponse'][0];
	
	//update de l'adresse
	if(add(array('valeur' => $adresse),'infos',$sqlAdresse['id'])){ $updAdresse = true; } else { $updAdresse = false; }
	//update du téléphone
	if(add(array('valeur' => $tel),'infos',$sqlTel['id'])){ $updTel = true; } else { $updTel = false; }
	//update du fax
	if(add(array('valeur' => $fax),'infos',$sqlFax['id'])){ $updFax = true; } else { $updFax = false; }	
	//update de l'email
	if(add(array('valeur' => $email),'infos',$sqlEmail['id'])){ $updEmail = true; } else { $updEmail = false; }
	
	if($updAdresse && $updEmail && $updTel && $updFax) {
		// c'est bon
		message('success',"Les informations ont bien &eacute;t&eacute;es mise &agrave; jour.");
		echo "<meta http-equiv='refresh' content='3;URL=index.php?module=infos' />";
		
	} else {
		//erreur
		message('error',"Une erreur est survenue pendant l'enregistrement des modifications.<br />R&eacute;essayez.");
		echo "<meta http-equiv='refresh' content='3;URL=index.php?module=infos' />";
	}
} else {
	//on affiche le formlaire
	$infos = get('*','infos',null,"AND",array('id' => 'ASC'));
	$infos = $infos['reponse']; ?>
	
	<form method="post">
	<?php foreach($infos as $info): ?>
		<p class="grid_12 alpha omega">
			<label for="<?=url_title($info['label']);?>"><?=stripslashes(utf8_encode($info['label']));?></label>
			<?php if($info['id'] == 1 && $info['label'] == 'Adresse postale'): ?>
				<textarea name="<?=url_title($info['label']);?>" id="<?=url_title($info['label']);?>" class="grid_6 alpha omega"><?=str_replace('<br />','',stripslashes($info['valeur']));?></textarea>
			<?php else: ?>
				<input type="text" name="<?=url_title($info['label']);?>" id="<?=url_title($info['label']);?>" value="<?=stripslashes(utf8_encode($info['valeur']));?>" />
			<?php endif; ?>
		</p>
	<?php endforeach; ?>
		<p class="grid_12 alpha omega"><input type="submit" name="updInfos" value="Mettre &agrave; jour" /></p>
	</form><?php
}
?>
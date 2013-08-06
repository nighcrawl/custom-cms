<?php 
/***
 *
 *	admin/modules/adm.slideshow.php	
 *	Dernière modification : 11/09/2012
 *
 */

echo "<h1>Gestion du diaporama</h1>";
switch(@$_GET['action']){
	case 'add':
		echo "<h1>Ajouter une slide</h1>";
		if(isset($_POST['addSlide'])):
			//traitement du formulaire
			
			$banner = (isset($_POST['banner']) && !empty($_POST['banner']))?$_POST['banner']:null;
			$public = isset($_POST['public'])?1:0;
			
			$_SESSION['addSlide'] = array(
				'public' => $public
			);
			
			if(!empty($banner)):
			
				$datas = array(
					'titre' => '',
					'url' => '',
					'banner' => $banner,
					'public' => $public
				);
				
				if(add($datas,'slideshow')){
					unset($_SESSION['addSlide']);
					message('success',"La slide a bien &eacute;t&eacute; cr&eacute;&eacute;.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
				} else {
					message('error',"Une erreur est survenue pendant l'enregistrement de la slide.<br />R&eacute;essayez.");
					echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow&action=add' />";
				}
			
			else:
				message('warning',"Votre slide doit obligatoirement avoir un titre et une banni&egrave;re.<br />Merci de remplir ces champs.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow&action=add' />";
			endif;
		else: //formulaire 
			$pagesList = get('*','pages',null,"AND",array('id'=>'ASC'));	
			$pagesList = $pagesList['reponse']; ?>
			<form method="post" enctype="multipart/form-data">
                <p>
                	<label for="banner">Banni&egrave;re de la slide</label>
                    <input type="file" name="upload" id="upload"/>
                    <input type="hidden" name="banner" id="banner" value="<?=@$_SESSION['addSlide']['banner'];?>" />

                    <div id="timestamp" style="display:none;"><?php $timestamp = time(); echo $timestamp; ?></div>
					<div id="token" style="display:none;"><?php echo md5('unique_salt' . $timestamp);?></div>
					<div id="queue"></div>
                    
                    <img id="image_banner" src="" alt="" />
                </p>
                <p><label for="public"><input type="checkbox" name="public" id="public" <?=((@$_SESSION['addSlide']['public'] == 1)?'checked':'');?> /> Publier</label></p>
            	<p><input type="submit" name="addSlide" /></p>
            </form>
		<?php unset($_SESSION['addSlide']);
        endif;
	break;
	
	case 'upd':
		echo "<h1>Modifier une slide</h1>";
		if(isset($_GET['sid']) && is_numeric($_GET['sid'])):
			@$infosSlide = get('*','slideshow',array('id =' => intval($_GET['sid'])));
			@$infos = $infosSlide['reponse'][0];
			if(count($infos) > 0){
				//on a l'id
				if(isset($_POST['updSlide'])): //traitement
					
					$banner = (isset($_POST['banner']) && !empty($_POST['banner']))?$_POST['banner']:null;
					$public = isset($_POST['public'])?1:0;
					
					$_SESSION['updSlide'] = array(
						'banner' => $banner,
						'public' => $public
					);
					
					if(!empty($banner)):
						$datas = array(
							'titre' => '',
							'url' => '',
							'public' => $public
						);
						
						if(!is_null($banner)){
							$datas['banner'] = $banner;
						}
						
						if(add($datas,'slideshow',$infos['id'])){
							unset($_SESSION['updSlide']);
							message('success',"La slide a bien &eacute;t&eacute; modifi&eacute;e.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
						} else {
							message('error',"Une erreur est survenue pendant la modification de la slide.<br />R&eacute;essayez.");
							echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow&action=upd&sid=".$infos['id']."' />";
						}
						
					else:
						message('warning',"Certain champs obligatoires sont vide<br />Merci de remplir ces champs.");
						echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow&action=upd&sid=".$infos['id']."' />";
					endif;
					
				else : //formulaire
					$pagesList = get('*','pages',null,"AND",array('id'=>'ASC'));	
					$pagesList = $pagesList['reponse'];
					
					$banner = (isset($_SESSION['updSlide']['banner']))?$_SESSION['updSlide']['banner']:$infos['banner'];
					$public = (isset($_SESSION['updSlide']['public']))?$_SESSION['updSlide']['public']:$infos['public'];
					 ?>
                    <form method="post" enctype="multipart/form-data">
                        
                        <p>
                            <label for="banner">Banni&egrave;re de la slide</label>
                            <input type="file" name="upload" id="upload"/>
                            <input type="hidden" name="banner" id="banner" value="<?=@$banner;?>" />

                            <div id="timestamp" style="display:none;"><?php $timestamp = time(); echo $timestamp; ?></div>
							<div id="token" style="display:none;"><?php echo md5('unique_salt' . $timestamp);?></div>
							<div id="queue"></div>

                            <img id="image_banner" src="../uploads/<?=$infos['banner'];?>" alt="" />
                        </p>
                        
                        <p><label for="public"><input type="checkbox" name="public" id="public" <?=((@$infos['public'] == 1)?'checked':'');?> /> Publier</label></p>
                        <p><input type="submit" name="updSlide" /></p>
                    </form>
                <?php unset($_SESSION['updSlide']);
				endif;
			} else {
				//on a pas d'id
				message('warning',"Cette slide ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
				echo"<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
			}
		else:
			//on a pas d'id
			message('warning',"Cette slide ne peut &ecirc;tre modifi&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
		endif;
	break;
	
	case 'dlt':
		echo "<h1>Supprimer une slide</h1>";
		if(isset($_GET['sid']) && is_numeric($_GET['sid'])) :
			//on a l'id
			if(delete(intval($_GET['sid']),'slideshow')){
				message('success',"La slide a bien &eacute;t&eacute; supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
			} else {
				message('error',"Une erreur est survenue pendant la suppression. La slide n'a pas pu &ecirc;tre supprim&eacute;e.");
				echo "<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
			}
		else :
			//on a pas d'id
			message('warning',"Cette slide ne peut &ecirc;tre supprim&eacute;e car elle n'existe pas.");
			echo"<meta http-equiv='refresh' content='3;URL=index.php?module=slideshow' />";
		endif;
	break;
	
	default:
		
		$nbpp = 10;
		if(!isset($_GET['d'])) $_GET['d'] = 0; //début de la pagination
		
		
		$listSlides = get('*','slideshow',null,"AND",array('id'=>'ASC'),array($_GET['d'],$nbpp));
		$slidesTotal = $listSlides['total'];
		
		$listSlides = $listSlides['reponse'];
		
		if(count($listSlides) > 0) : //on  affiche la liste ?>
			<a href="index.php?module=slideshow&action=add" class="button">Cr&eacute;er une nouvelle slide</a>
			<table width="100%" class="data">
				<thead>
					<tr>
						<th>Image</th>
						<th>Publi&eacute;e</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody><?php foreach($listSlides as $lp){
					echo "<tr>
						<td><img src='".SITE_URL."uploads/".$lp['banner']."' width='150' /></td>
						<td>".isTrue($lp['public'])."</td>
						<td>
							<a class='button' href='index.php?module=slideshow&action=upd&sid=".$lp['id']."'>Modifier</a>
							<a class='button trash' href='index.php?module=slideshow&action=dlt&sid=".$lp['id']."'>Supprimer</a>
						</td>
					</tr>";		
				} ?></tbody>
			</table>                            
			<?php pagination($slidesTotal,$nbpp,"index.php?module=slideshow&d=");
		else : //pas d'enregistrements
			message(null,"Malheureusement, il n'existe aucune slide pour le moment.<br />Voulez-vous <a href='index.php?module=slideshow&action=add'>en cr&eacute;er une ?</a>");
		endif;
	
	break;
} ?>
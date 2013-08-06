<?php
/***
 *
 *	admin/inc/functions.php	
 *	Dernière modification : 11/09/2012
 *
 */

require_once( str_replace('//','/', dirname(__FILE__).'/') .'../../config.php');

/***
 *	appel le fichier d'en-tête
 */
function get_header($dir = "admin"){
	if($dir != "admin"){
		include($dir . '/header.php');
	} else {
		include('header.php');
	}
}

/***
 *	appel le fichier de pied de page
 */
function get_footer($dir = "admin"){
	if($dir != "admin"){
		include($dir . '/footer.php');
	} else {
		include('footer.php');
	}
}

/***
 *	transforme les <br/> en \n pour les <textarea>s
 */
function br2nl($string) { return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); }

/***
 *	transforme les caractères accentués par leur équivalents
 */
function stripAccents($string){
	$pattern = array('&agrave;','&aacute;','&acirc;','&auml;','&eacute;','&egrave;','&ecirc;','&euml;','&ccedil;','&igrave;','&iacute;','&icirc;','&iuml;','&ntilde;','&ograve;','&oacute;','&ocirc;','&ouml;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&yuml;');
	$replace = array('a','a','a','a','e','e','e','e','c','i','i','i','i','n','o','o','o','o','u','u','u','u','y','y');
	
	$string = strtolower($string);
	$string = html_entity_decode($string);
	$string = str_replace($pattern,$replace,$string);	
	return $string;
}

/***
 *	réduit la chaine de caractère
 */
function excerpt($chaine,$limit,$readmore = null,$linktext = 'Lire la suite'){
	$chaine = strip_tags($chaine);
	if(strlen($chaine) >= $limit){ 
		$chaine = substr($chaine,0,$limit);
		$espace = strrpos($chaine," ");
		if($espace) { 
			$chaine = substr($chaine,0,$espace). " [&hellip;]";
			if($readmore) $chaine .= " <a href='".$readmore."'>".$linktext." &raquo;</a>";
		}
	}
	return "<p>".$chaine."</p>";
}

/***
 *	encode une chaine pour la transformer en URL
 */
function url_title($str, $separator = 'dash', $lowercase = true){
	if ($lowercase === TRUE) { $str = strtolower($str);
	}
	$str = stripAccents($str);
	if ($separator == 'dash') {
		$search		= '_';
		$replace	= '-';
	} else {
		$search		= '-';
		$replace	= '_';
	}

	$trans = array(
		'&\#\d+?;'			=> '',
		'&\S+?;'			=> '',
		'\s+'				=> $replace,
		'[^a-z0-9\-\._]'	=> '',
		$replace.'+'		=> $replace,
		$replace.'$'		=> $replace,
		'^'.$replace		=> $replace,
		'\.+$'				=> ''
	);
	$str = strip_tags($str);
	foreach ($trans as $key => $val) { $str = preg_replace("#" . $key . "#i", $val, $str); }	
	return trim(stripslashes($str));
}

/***
 *	retourne la chaine en paramètre cryptée sur 128 bits en SHA512 
 */
function pwd($str){ return hash('sha512',$str); }

/***
 *	retourne un message d'erreur, d'avertissement ou de succès
 */
function message($type = null, $message){
	if(!empty($message)){
		if(!is_null($type)){
			switch($type){
				case 'error': $title = "Erreur"; $class = 'error'; break;
				case 'warning': $title = "Attention"; $class = 'warning'; break;
				case 'success': $title = "F&eacute;licitation"; $class = 'success'; break;
			}
		} else { $title = "Information ";$class=''; }
		
		if(is_array($message)){
			echo "<div class='message " . $class . "'><h3>" . $title . "</h3><ul>";
			foreach($message as $m){
				echo "<li>" . $m . "</li>";
			}
			echo "</ul></div>";
		} else {
			echo "<div class='message " . $class . "'><h3>" . $title . "</h3><p>" . $message . "</p></div>";
		}
	} else { return false; }
}

/***
 *	Envoie la miniature d'une image vers un navigateur ou un fichier.
 *
 *	@image_src - Chemin vers l'image source.
 *	@image_dest - Le chemin de destination. S'il n'est pas défini ou s'il vaut NULL, le flux brut de l'image sera affiché directement.
 *	Pour éviter de fournir cet argument afin de fournir l'argument max_size, utilisez une valeur NULL.
 *	@max_size - La taille maximale (largeur ou hauteur) de l'image de destination. Ce paramètre optionnel a pour valeur par défaut 100.
 *	@expand - Si ce paramètre vaut TRUE, imagethumb() pourra éventuellement agrandir l'image pour atteindre la taille max_size dans le cas ou la taille de image_src est plus petite que max_size
 *	@square - Si ce paramètre vaut TRUE, la miniature générée sera carrée. 
 */
/***
 *	Envoie la miniature d'une image vers un navigateur ou un fichier.
 *
 *	@image_src - Chemin vers l'image source.
 *	@image_dest - Le chemin de destination. S'il n'est pas défini ou s'il vaut NULL, le flux brut de l'image sera affiché directement.
 *	Pour éviter de fournir cet argument afin de fournir l'argument max_size, utilisez une valeur NULL.
 *	@max_size - La taille maximale (largeur ou hauteur) de l'image de destination. Ce paramètre optionnel a pour valeur par défaut 100.
 *	@expand - Si ce paramètre vaut TRUE, imagethumb() pourra éventuellement agrandir l'image pour atteindre la taille max_size dans le cas ou la taille de image_src est plus petite que max_size
 *	@square - Si ce paramètre vaut TRUE, la miniature générée sera carrée. 
 */
function imagethumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE, $crop = FALSE, $crop_w = 940, $crop_h = 285 ){
	//if( !file_exists($image_src) ) return FALSE;

	// Récupère les infos de l'image
	$fileinfo = getimagesize($image_src);
	if( !$fileinfo ) return FALSE;

	$width     = $fileinfo[0];
	$height    = $fileinfo[1];
	$type_mime = $fileinfo['mime'];
	$type      = str_replace('image/', '', $type_mime);

	if( !$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) ) {
		// L'image est plus petite que max_size
		if($image_dest) {
			return copy($image_src, $image_dest);
		} else {
			header('Content-Type: '. $type_mime);
			return (boolean) readfile($image_src);
		}
	}

	// Calcule les nouvelles dimensions
	$ratio = $width / $height;

	if( $square ) {
		$new_width = $new_height = $max_size;

		if( $ratio > 1 ) {
			// Paysage
			$src_y = 0;
			$src_x = round( ($width - $height) / 2 );

			$src_w = $src_h = $height;
		} else {
			// Portrait
			$src_x = 0;
			$src_y = round( ($height - $width) / 2 );

			$src_w = $src_h = $width;
		}
	} else {
		$src_x = $src_y = 0;
		$src_w = $width;
		$src_h = $height;

		if ( $ratio > 1 ) {
			// Paysage
			$new_width  = $max_size;
			$new_height = round( $max_size / $ratio );
		} else {
			// Portrait
			$new_width  = $max_size;
			$new_height = round( $max_size / $ratio );
		}
	}

	// Ouvre l'image originale
	$func = 'imagecreatefrom' . $type;
	if( !function_exists($func) ) return FALSE;

	$image_src = $func($image_src);
	$new_image = imagecreatetruecolor($new_width,$new_height);

	// Gestion de la transparence pour les png
	if( $type == 'gif' || $type == 'png'){
		imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
	}
	// Redimensionnement de l'image
	if(!$crop){ // si on ne crop pas l'image
		imagecopyresampled($new_image, $image_src, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
	} elseif(isset($crop_w) && isset($crop_h)) { //sinon on doit avoir les dimensions pour le crop final
		
		$desired_ration = $crop_w/$crop_h;
		
		if ( $ratio > $desired_ration ) {
			$temp_height = $crop_h;
			$temp_width = (int)($crop_h * $ratio);
		} else {
			$temp_width = $crop_w;
			$temp_height = (int)($crop_w/$ratio);
		}
		
		//création de l'image temporaire, redimensionnée en gardant le ratio d'origine
		$temp_gdim = imagecreatetruecolor($temp_width,$temp_height);
		if( $type == 'gif' || $type == 'png'){
			imagecolortransparent($temp_gdim, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
			imagealphablending($temp_gdim, false);
			imagesavealpha($temp_gdim, true);
		}
		
		imagecopyresampled($temp_gdim,$image_src,0,0,0,0,$temp_width,$temp_height,$src_w,$src_h);
		$x0 = ($temp_width - $crop_w)/2;
		$y0 = ($temp_height - $crop_h)/2;
		
		//création de l'image finale, croppée aux dimensions voulues
		$new_image = imagecreatetruecolor($crop_w,$crop_h);
		if( $type == 'gif' || $type == 'png'){
			imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
		}
		imagecopy($new_image,$temp_gdim,0,0,$x0,$y0,$crop_w,$crop_h);
		
	} else return FALSE;

	// Enregistrement de l'image
	$func = 'image'. $type;
	if($image_dest){
		$func($new_image, $image_dest);
	} else {
		header('Content-Type: '. $type_mime);
		$func($new_image);
	}

	// Libération de la mémoire
	imagedestroy($new_image); 

	return TRUE;
}

/***
 *	Verifie si le slug passé en paramètre existe déjà dans la table @table
 */
function existSlug($slug,$table){
	$res = get(array('slug'),$table,array('slug =' => $slug));
	if($res['reponse']){
		return true;
	} else return false;
}

/***
 *	Créé un nouveau slug en se basant sur la chaine @str et la table @table en paramètres
 */
function newSlug($slug,$table){
	$res = get(array('slug'),$table,array('slug LIKE' => $slug.'%'));
	if($res['reponse']){
		return $newSlug = $res['reponse'][0]['slug']."-".($res['total']+1);
	} else return false;
}

/***
 *	Verifie si on peut se connecter à l'administration
 *	@datas array('login','password')
 */
function admConnect($datas){
	$bdd = db();
	$strSQL = "SELECT * FROM admin WHERE login = ? AND pwd = ?";
	$query = $bdd->prepare($strSQL);
	$query->execute(array($datas['login'],hash('sha512',$datas['pwd'])));
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);
	if(count($rows) == 1) {
		$_SESSION['adm']['login'] = $rows[0]['login'];
		$_SESSION['adm']['nom'] = stripslashes(utf8_encode($rows[0]['nom']));
		return true;
	} else return false;
}

/***
 *	connexion à la base de données
 */
function db(){
	try{
		$db = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME,DBUSER,DBPWD);
	} catch(Exception $e){
		echo "Erreur: ".$e->getMessage(). "</br>";
		die();
	}
	return $db;
}

/***
 *	insert des données dans la table en paramètre. Si un ID est fournit, c'est un UPDATE
 *	@datas	tableau des données à insérer dont la clé et le nom du champs dans la table
 *	@table	table dans laquelle insérer les données
 *	@id		id de l'enregistrement à modifier
 */
function add($datas,$table,$id = null){
	$bdd = db();
	foreach($datas as $key => $value){
		if(is_null($id)){
			if($key == 'slug'){
				if(existSlug($value,$table)){
					$value = newSlug($value,$table);
				}
			}
		}
		$keys[] = $key;
		$values[] = $value;
	}
	
	if(!is_null($id) && is_numeric($id)){
		//UPDATE
		$strSQL = "UPDATE ".$table." SET ";
		foreach($datas as $key => $value){
			$strSQL .= $key . " = ?,";
		} $strSQL = substr($strSQL,0,-1) . " WHERE id = ?";
		$values[] = $id;
	} else {
		//INSERT
		$strSQL = "INSERT INTO " . $table . " (";
		foreach($keys as $ky => $k){ $strSQL .= $k . ","; }
		$strSQL = substr($strSQL,0,-1) . ") VALUES(";
		foreach($values as $vl => $v){ $strSQL .= "?,"; }
		$strSQL = substr($strSQL,0,-1) . ")";
	}
	
	$query = $bdd->prepare($strSQL);
	if($query->execute($values)) {
		if(is_null($id)){ return $bdd->lastInsertId(); }
		else { return true; }
	} else return false;
}

/***
 * 	retourne le resultat d'un select
 *	@columns 	colonnes à selectionner pour la requête (ex: array('champ1','champ2') ou '*')
 *	@table 		nom de la table sur laquelle faire la requête
 *	@where 		champs sur lequels appliquer des conditions ( ex: array( 'champ1 =' => 'valeur', 'champ2 LIKE' => 'valeur%') ) 
 *	@concats 	[ AND | OR ]
 *	@order 		champs sur lequels appliquer le tri, et l'ordre pour chaque champs (ex: array('champ1' => 'ASC','champ2' => 'DESC') )
 *	@limit 		limit[0] => debut de la liste, limit[1] => nombre d'éléments dans la liste retournée (ex: array('0','20') )
 *	
 *	return @retour	: tableau contenant la requête executée, les éventuelles erreurs et le resultat de la requête
 */
function get($columns = null, $table = null, $where = null, $concats = "AND", $order = null, $limit = null, $groupby = null){
	$bdd = db();
	$retour = array(); //variable de type tableau, retournée par la fonction
	$rows = ""; $clause = ""; $sort = ""; $limitStr = ""; $group = "";
	if(!is_null($columns) && !is_null($table)){
		// si $rows est un tableau ou égale à * tout va bien.
		if(is_array($columns)){
			foreach($columns as $column) { $rows .= $column .', '; } 
			$rows = substr($rows,0,-2);
		} elseif($columns == '*'){
			$rows = '*';
		} else {
			$retour['erreur'] = "Les champs selectionn&eacute; doivent &ecirc;tre appel&eacute; depuis une variable Tableau";
		}
		
		if(!in_array(strtolower($concats),array('and','or'))){
			$retour['erreur'] = "<b>".$concats."</b> n'est pas une valeur autoris&eacute;e pour concat&eacute;ner des conditions. Utilisez 'OR' ou 'AND'.";
		}
		
		/*
		si @where est renseigné, on filtre les résultats grâce au
		tableau @where construit comme suit :
			array ('colname operateur' => 'valeur');
			ex: array('page_id =' => 5);
		sinon, on ne filtre pas les résultats
		*/
		if(!is_null($where) && is_array($where)){
			foreach($where as $k => $v){
				
				$test = explode('||',$v);
				if(count($test) > 1) {
					$str = "";
					foreach($test as $w) { 
						$str .=  $k  . " ? OR ";
						$values[] = $w;
					}
					$str = "(" . substr($str,0, -3). ") ";
					$clause .= $str . " ".$concats. " ";
				} else {
				
					$clause .= $k . " ? " . $concats . " ";
					$values[] = $v;
				}
			} 
			$clause = " WHERE ".substr($clause,0,(-(strlen($concats)+2)));
		} elseif(!is_null($where) && !is_array($where)){
			$retour['erreur'] = "La clause WHERE doit &ecirc;tre construite via une variable Tableau";
		} else {
			$clause = "";
		}
		
		//si $order est un tableau et n'est pas null
		if(!is_null($order) && is_array($order)){
			foreach($order as $k => $v){ $sort .= $k." ".$v.", "; } 
			$sort = " ORDER BY " . substr($sort,0,-2);
		} elseif(!is_null($order) && !is_array($order)) {
			$retour['erreur'] = "ORDER BY doit &ecirc;tre construit via une variable Tableau";
		} else {
			$sort = "";
		}
		
		if(!is_null($limit) && is_array($limit) && is_numeric($limit[0]) && is_numeric($limit[1])){
			$debut = $limit[0];
			$nbRows = $limit[1];
			$limitStr = " LIMIT " . $debut . "," . $nbRows;
		} elseif(!is_null($limit) && !is_array($limit)){
			$retour['erreur'] = "LIMIT doit &ecirc;tre construit via un tableau de deux entiers";
		} else {
			$limitStr = "";
		}
		
		if(!is_null($groupby) && is_array($groupby)){
			foreach($groupby as $col => $sorting){ $group .= $col . " " . $sorting . ", "; }
			$group = " GROUP BY " . substr($group,0,-2);
		} elseif(!is_null($order) && !is_array($order)) {
			$retour['erreur'] = "GROUP BY doit &ecirc;tre construit via une variable Tableau";
		} else {
			$group = "";
		}
		
		// on construit la requête
		$strSQL = "SELECT " . $rows . " FROM " . $table . $clause . $sort . $group . $limitStr;
		if(empty($retour['erreur'])){
			$query = $bdd->prepare($strSQL);
			$query->execute(@$values);
			$retour['requete'] = $strSQL;
			$retour['reponse'] = $query->fetchAll(PDO::FETCH_ASSOC);
			$sqlTotal = "SELECT COUNT(*) as total FROM " . $table . $clause . $sort . $group;
			$q = $bdd->prepare($sqlTotal);
			$q->execute(@$values);
			$tot = $q->fetchAll(PDO::FETCH_ASSOC);
			$retour['total'] = $tot[0]['total'];
		}
	} else {
		$retour['erreur'] = "Impossible de cr&eacute;er la requete, les champs &agrave; selectionner et la table sont vide";
	}
	return $retour;
}


/***
 *	supprime les données correspondant à l'ID dans la table en paramètre
 *	@id		identifiant de la ligne à supprimer
 *	@table	table sur laquelle on applique la suppression
 */
function delete($id,$table){
	$bdd = db();
	$strSQL = "DELETE FROM " . $table . " WHERE id = ?";
	$query = $bdd->prepare($strSQL);
	if($query->execute(array($id))) return true;
	else return false;
}

/***
 *	retourne un tableau contenant la liste des pages de premier niveau (maxi 10)
 *	si @child est vrai, on récupère aussi les pages 'fille' de chacune des pages de niveau 1
 */
function menu($child = false, $current = null, $active_class = null){
	$cols = array('id','titre','slug');
	$table = 'pages';
	$where = array('parent =' => 0, 'public =' => 1, 'nav =' => 1,);
	$limit = array(0,8);
	
	$menu = array();
	
	$r = get($cols,$table,$where,"AND",null,$limit);
	if(isset($r['reponse']) && count($r['reponse']) > 0){
		if($child == true){
			//on veut les sous menus
			foreach($r['reponse'] as $el) {
				$menu[$el['id']] = array(
					'id' => $el['id'],
					'titre' => $el['titre'], 
					'slug' => $el['slug']
				);
				
				$children = get($cols,$table,array('parent =' => $el['id'], 'public =' => 1));
				if(isset($children['reponse']) && count($children['reponse']) > 0){
					foreach($children['reponse'] as $child) {
						$menu[$el['id']]['children'][$child['id']] = array(
							'id' => $child['id'],
							'titre' => $child['titre'],
							'slug' => $child['slug']
						);
						
						if($current == $child['id']) {
							$menu[$el['id']]['active'] = $active_class;
						}
					}
				}
				
				if($current == $el['id']) {
					$menu[$el['id']]['active'] = $active_class;
				}
			}
		} else {
			foreach($r['reponse'] as $el) {
				$menu[$el['id']] = array(
					'id' => $el['id'],
					'titre' => $el['titre'], 
					'slug' => $el['slug']
				);
				if($current == $el['id']) {
					$menu[$el['id']]['active'] = $active_class;
				}
			}
		}		
	}
	
	$html =  "";
	foreach($menu as $m){
		if(isset($m['children'])){
			$html .= "<li class='submenu " . @$m['active'] . "'><a href='" . SITE_URL . "index.php?page_id=" . $m['id'] . "'>" . stripslashes($m['titre']) . "</a>
				<ul>";
					foreach($m['children'] as $c){
						$html .= "<li><a href='" . SITE_URL . "index.php?page_id=" . $c['id'] . "'>" . stripslashes($c['titre']) . "</a></li>";
					}
			$html .= "</ul>
			</li>";
		} else {
			$html .= "<li class='" . @$m['active'] . "'><a href='" . SITE_URL . "index.php?page_id=" . $m['id'] . "'>" . stripslashes($m['titre']) . "</a></li>";
		}
	}

	return $html;
}


/***
 *	retourne un tableau contenant la liste des pages de premier niveau (maxi 10)
 *	si @child est vrai, on récupère aussi les pages 'fille' de chacune des 
 *	pages en se limitant à @nblevels niveau
 */
function _menu($child = false, $current = null, $nblevel = null, $parent = 0, $active_class = null){
	if($child){ //on veut les pages mères et leurs enfants
	
		if(!is_null($nblevel) && is_numeric($nblevel)){
			
			$cols = array('id','titre','slug');
			$table = 'pages';
			$where = array('parent =' => $parent, 'public =' => 1, 'nav =' => 1);
			$limit = array(0,10);
			
			$menu = array();
			
			$r = get($cols, $table, $where);
			
			//niveau 0
			foreach($r['reponse'] as $el){
				$menu[$el['id']] = array(
					'id' => $el['id'],
					'titre' => $el['titre'],
					'slug' => $el['slug'],
				);
				
				for($i = 0; $i < ($nblevel-1); $i++){
					$children = _menu($child, $current, ($nblevel-1), $menu[$el['id']]['id']);
					if(count($children) > 0){
						$menu[$el['id']]['children'] = $children;
					}
				}
			
				if($current == $el['id']) { $menu[$el['id']]['active'] = $active_class; }		
			}
				
			
		} //else { die(); }
		
	} else { //on ne veut que les pages mères
	
		$cols = array('id','titre','slug');
		$table = 'pages';
		$where = array('parent =' => $parent, 'public =' => 1, 'nav =' => 1);
		$limit = array(0,10);
		
		$menu = array();
	
		$r = get($cols, $table, $where, "AND", null, $limit);
				
		foreach($r['reponse'] as $el) {
			$menu[$el['id']] = array(
				'id' => $el['id'],
				'titre' => $el['titre'], 
				'slug' => $el['slug']
			);
			if($current == $el['id']) {
				$menu[$el['id']]['active'] = $active_class;
			}
		}
	
	}
	
	return $menu;
}

function afficher_menu($menu){
	$html = "";
	foreach($menu as $mi){ //niveau 0
		if(isset($mi['children'])){ 
			$html .= "<li class='nav-item dropdown-item'><a href='".SITE_URL.$mi['slug']."'>".$mi['titre']."</a>
				<ul class='nav-submenu'>";
			foreach($mi['children'] as $mc){ //niveau 1
				if(isset($mc['children'])){ 
					$html .= "<li class='dropdown-item'><a href='".SITE_URL.$mc['slug']."'>".$mc['titre']."</a>
					<ul class='nav-submenu'>";
					foreach($mc['children'] as $mcc){ // niveau 2
						if(isset($mcc['children'])){
							$html .= "<li class='dropdown-item'><a href='".SITE_URL.$mcc['slug']."'>".$mcc['titre']."</a>
							<ul class='nav-submenu'>";
							foreach($mcc['children'] as $mccc){ //niveau 3
								$html .= "<li><a href='".SITE_URL.$mccc['slug']."'>".$mccc['titre']."</a></li>";
							}
							$html .= "</ul>
							</li>";
						} else {
							$html .= "<li><a href='".SITE_URL.$mcc['slug']."'>".$mcc['titre']."</a></li>";
						}
					}
					$html .= "</ul>
					</li>";
				} else {
					$html .="<li><a href='".SITE_URL.$mc['slug']."'>".$mc['titre']."</a></li>";
				}
			}
				$html .= "</ul>
			</li>";
		} else {
			$html .= "<li class='nav-item'><a href='".SITE_URL.$mi['slug']."'>".$mi['titre']."</a></li>";
		}
	}
	
	echo $html;
}




function nav($currentpage = null, $active_class = null, $limit = 10){
	$cols = array('id','titre','slug');
	$args = array('public =' => 1, 'parent =' => 0, 'nav =' => 1);
	$limit = array(0,$limit);

	$menu = array();
	$html = "";

	$res = get($cols,'pages',$args,"AND",null,$limit);
	if($res['reponse'] && count($res['reponse']) > 0):
		foreach($res['reponse'] as $el):
			$menu[$el['id']] = array(
				'id' => $el['id'],
				'titre' => $el['titre'],
				'slug' => $el['slug']
			);
			if($currentpage == $el['slug']):
				$menu[$el['id']]['active'] = $active_class;
			endif;
		endforeach;

	endif;

	foreach($menu as $m):
		$html .="<li><a href='" . SITE_URL . $m['slug'] . "' class='" . @$m['active'] . "''>" . stripslashes($m['titre']) . "</a></li>";
	endforeach;

	return $html;
}
 

/***
 *	retourne le titre de la page ID en paramètre
 *	si l'ID est 0, on retourne '-'
 */
function getTitrePage($page_id){
	if($page_id == 0) {
		return '-';
	} else {
		$infos = get('*','pages',array('id =' => $page_id));		
		if(count($infos['reponse']) > 0){		
			return $infos['reponse'][0]['titre'];
		} else return null;
	}
}

/***
 *	retourne 'Oui' ou 'Non' suivant la valeur de @bool
 */
function isTrue($bool){
	if($bool == true || $bool == 1){
		return 'Oui';
	} else {
		return 'Non';
	}
}

/***
 *
 */
function is_home(){
	global $PAGE;


	if($PAGE == 'home') return true;
	else return false;
}

/***
 *	génère des liens de pagination : numeros de pages, 'suivants', 'précédents'
 *	@total	nombre total d'enregistremnts à paginer
 *	@nbpp	nombre d'enregistrements à afficher par page
 *	@link	chaine qui servira à construire les liens vers les différentes pages
 */


/*
<div class="pagination">
  <ul>
  	<li class="disabled"><a href="#">Prev</a></li>
    <li class="active"><a href="#">1</a></li>
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">Next</a></li>
  </ul>
</div> */ 

function pagination($total,$nbpp,$link){
	echo "<div class='pagination pagination-centered'>
		<ul>";
		/** Pagination **/
		//calcul du nombre de pages 
		$nbLiens = ceil($total/$nbpp);
	
			//pages précédentes
			if(isset($_GET['d']) && $_GET['d'] > 0){ echo "<li><a href='" . $link . ($_GET['d'] - $nbpp) . "'>&laquo; Pr&eacute;c&eacute;dents</a></li>"; } 
			else { echo "<li class='disabled'><span>&laquo; Pr&eacute;c&eacute;dents</span></li>"; }
			
			//pages
			
			if($nbLiens > 20){
				if($_GET['d'] < 50){ //si la page courante < 6
					for($i = 0; $i < 11; $i++):
						if($_GET['d'] == ($i * $nbpp)){ echo "<li class='active'><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) ."</a></li>"; } 
						else { echo "<li><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) . "</a></li>"; }
					endfor;
				} elseif($_GET['d'] > ($nbLiens * $nbpp) - 50){ //si la page courante  < ($nbLiens - 11) 
					for($i = ($nbLiens-11); $i < ($nbLiens); $i++):
						if($_GET['d'] == ($i * $nbpp)){ echo "<li class='active'><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) ."</a></li>"; } 
						else { echo "<a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) . "</a>"; }
					endfor;
				} else { //5 pages de chaque côté de la page courante si la page > 1 et < $nbLiens
					for($i = ((($_GET['d']/$nbpp) + 1)-4); $i <= (($_GET['d']/$nbpp) + 1); $i++):
						echo "<li><a href='" . $link . (($i-2) * $nbpp) . "'>" . ($i - 1) . "</a></li>";
					endfor;
					echo "<li class='active'><a href='" . $link . ($i * $nbpp) . "'>" . (($_GET['d']/$nbpp) + 1)."</a></li>";
					for($i = (($_GET['d']/$nbpp) + 1); $i <= (($_GET['d']/$nbpp) + 1) + 4; $i++):
						echo "<li><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) . "</a></li>";
					endfor;
				}
			} else {
				for($i = 0; $i < $nbLiens; $i++):
					if($_GET['d'] == ($i * $nbpp)){ echo "<li class='active'><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) ."</a></li>"; } 
					else { echo "<li><a href='" . $link . ($i * $nbpp) . "'>" . ($i + 1) . "</a></li>"; }
				endfor;
			}
				
			// pages suivantes
			if(isset($_GET['d']) && $_GET['d'] >= 0 && $_GET['d'] < ($total - $nbpp)){ echo "<li><a href='" . $link . ($_GET['d'] + $nbpp) . "'>Suivants &raquo;</a></li>"; } 
			else { echo "<li class='disabled'><span>Suivants &raquo;</span></li>"; }

	echo "</ul>
	</div>";
}

/***
 *	retourne le nombre total de photos de l'album en paramètre
 */
function getTotalPhotosAlbum($album){
	$get = get(array('nb_photos'),'photos_albums',array('id =' => intval($album)));
	return $get['reponse'][0]['nb_photos'];
}

/***
 *	retourne les 10 dernières photos ajoutées dans les albums photos
 */
function getLatestPhotos(){
	$get = get(array('photos'),'photos_albums',array('public =' => 1),"AND",array('date_maj' => 'DESC'),array(0,10));
	//exit(print_r($get));
	$get = $get['reponse'];
	$retour = '';
	
	$photos = array();
	foreach($get as $g){
		$g['photos'] = substr($g['photos'],0,-1);
		$p = explode(';',$g['photos']);
		foreach($p as $k => $v){
			$photos[] = $v;
		}
	}
	//exit(print_r($photos));
	if(count($photos) >= 10){
		for($i = 0; $i < 10; $i++){
			$retour .= '<li><a rel="latest" href="'.SITE_URL.'uploads/'.$photos[$i].'"><img src="'.SITE_URL.'uploads/'.$photos[$i].'" alt=""></a></li>';
		}
	} else {
		for($i = 0; $i < count($photos); $i++){
			$retour .= '<li><a rel="latest" href="'.SITE_URL.'uploads/'.$photos[$i].'"><img src="'.SITE_URL.'uploads/'.$photos[$i].'" alt=""></a></li>';
		}
	}
	
	return $retour;
}

/***
 *	retourne un tableau contenant les pages, actus, albums photos publiés et non publiés
 */ /*
function stats(){
	$pages = 0; $produits = 0; $commandes = 0; $clients = 0;
	$pages_brouillons = 0; $produits_brouillons = 0;
	
	//contenus publiés
	$totalPages = get(array('id'),'pages',array('public =' => 1));
	if(isset($totalPages['reponse'])){
		$pages = $totalPages['total'];
	}
	
	$totalProduits = get(array('id'),'produits',array('public =' => 1));
	if(isset($totalProduits['reponse'])){
		$produtis = $totalProduits['total'];
	}
	
	$totalCommandes = get(array('id'),'commandes',array('statut =' => 'finie'));
	if(isset($totalCommandes['reponse'])){
		$commandes = $totalCommandes['total'];
	}
	
	$totalClients = get(array('id'),'clients');
	if(isset($totalClients['reponse'])){
		$clients = $totalClients['total'];
	}
	
	
	//contenus publiés
	$totalPages = get(array('id'),'pages',array('public =' => 0));
	if(isset($totalPages['reponse'])){
		$pages_brouillons = $totalPages['total'];
	}
	
	$totalProduits = get(array('id'),'produits',array('public =' => 0));
	if(isset($totalProduits['reponse'])){
		$produits_brouillons = $totalProduits['total'];
	}
	
	$stats = array(
		'public' => array(
			'pages' => @$pages, 
			'produits' => @$produits, 
			'commandes' => @$commandes, 
			'clients' => @$clients
		),
		'prive' => array(
			'pages' => @$pages_brouillons, 
			'produits' => @$produits_brouillons, 
		)
	);
	return $stats;
}
*/

/***
 *	retourne le nombre de commandes en attente et commandes validées
 */
function stats(){
 	$stats = get(array('statut','COUNT(id) as count'),'commandes',null,"AND",null,null,array('statut' => 'ASC'));
	return $stats;
}

/***
 *	formate un prix en séparant les euros des centimes par une vigule, 
 *	les milliers par un espace, et ajoute le sigle € en fin de chaine
 */
function format_price($prix){
	return function_exists('money_format')?money_format('%i',$prix):number_format($prix,2,',',' ')." &euro;";
}

/***
 *	Retourne l'état de la commande en toutes lettres
 */
function getLibelleCommande($statut) {
	switch($statut){
		case 'paiement_valide': $retour = "Paiement valid&eacute;"; break;
		case 'refus_paiement': $retour = "Paiement refus&eacute;"; break;
		case 'annule': $retour = "Annul&eacute;e"; break;
		case 'attente': $retour = "En attente de validation"; break;
		case 'valide': $retour = "Valid&eacute;e"; break;
		case 'traitement' : $retour = "Traitement en cours"; break;
		case 'expedition' : $retour = "Exp&eacute;di&eacute;e"; break;
	}
	return $retour;
}

function getGMapAdresse($plus=true){
	$res = get(array('valeur'),'infos',array('id =' => 1));
	$res = $res['reponse'][0]['valeur'];
	if($plus){
		$res = str_replace('<br />','+',$res);
		$res = str_replace(' ','+',$res);
	}
	return $res;
}

function getTelephoneContact(){
	$res = get(array('valeur'),'infos',array('id =' => 2));
	$res = $res['reponse'][0]['valeur'];
	return $res;
}

function getEmailContact(){
	$res = get(array('valeur'),'infos',array('id =' => 4));
	$res = $res['reponse'][0]['valeur'];
	return $res;
}

?>
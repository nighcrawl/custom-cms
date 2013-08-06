<?php include('inc/functions.php');
$nom_cat = trim($_POST['categorie']);
$cid = add(array('nom' => $nom_cat),'categories');
if($cid){
	$li = '<li><label for="cat_'.$cid.'"><input type="checkbox" name="categories[]" id="cat_'.$cid.'" value="'.$cid.'">'.$nom_cat.'</label></li>';
	echo $li;
} else return false;
?>
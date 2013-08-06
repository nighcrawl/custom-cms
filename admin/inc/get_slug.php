<?php include('functions.php');
if(isset($_POST['slug'])){
	$slug = trim($_POST['slug']);
	if(strlen($slug) > 0){
		echo url_title($slug);
	}
}
?>
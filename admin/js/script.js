/***
 *
 *	admin/js/script.js
 *	Dernière modification : 01/08/2013
 *
 */

var SITE_URL = 'http://localhost/cbc-cms.base/',	// Adresse du site Web
	SITE_ROOT = '/cbc-cms.base/';					// Dossier racine du site, par défaut "/"

jQuery(document).ready(function($){
	
	/* Affiche un bouton "Fermer" en haut à droite des boites de 
	   message d'information (erreur, avertissement, success)
	   et fait disparaitre ces dernier au clic sur ce bouton.
	*/
	$(".message").append("<a href='#' class='close' title='Faire disparaitre'>Fermer</a>");
	$("a.close").click(function(){
		$(this).parent(".message").fadeOut("slow");	
		return false;
	});
	
	/* Affiche une messageBox avant la suppression de la page,
	   afin de confirmer l'action
	*/
	$(".trash").click(function(){
		if(confirm("Vous \352tes sur le point de supprimer cet enregistrement. \312tes vous certain de vouloir le supprimer?")){
			return true;
		} else { return false; }
	});

	/*	Génère un identifiant unique (slug) pour la page
		en cours de création/modification en supprimant
		les caractères spéciaux contenus dans #titre
	*/
	$("input#titre").live('keyup',function(){
		var slug = $(this).val();			
		$.ajax({
			type: "POST",
			url: 'inc/get_slug.php',
			data: "slug="+ slug,
			success: function(msg){
				$("input#slug").val(msg);
			}
		});
	});

	/*	Utilisé avec le module "photos". 
		Télécharge les images sur le serveur.
	*/
	var strPhotos = '';
		
	if($("#fichiers_urls").val() != ""){ strPhotos = $("#fichiers_urls").val(); }

	$("#fichiers").uploadify({
		'formData': { 'timestamp' : $("#timestamp").html(), 'token': $("#token").html() },
		'fileSizeLimit' : '0',
		'successTimeout': 120,
		'swf': 'js/uploadify/uploadify.swf',
		'uploader': 'js/uploadify/uploadify.php',
		'queueID': 'queue',
		'fileTypeDesc': 'Image',
		'fileTypeExts':'*.gif; *.jpg; *.jpeg; *.png',
		'buttonText': 'Ajouter des photos...',
		'width': 150,
		'multi': true,
		'onUploadSuccess' : function(file, data, response) {
			strPhotos  = strPhotos + file.name + ';';


			$('#photosView').append('<img title="Cliquez pour supprimer la photo" width="80" src="' + SITE_URL + 'images.php?src=' + SITE_ROOT + 'uploads/' + file.name + '&w=80&h=80" />  ');
			$('#fichiers_urls').val(strPhotos);
            //alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
        }
	});

	$("#photosView img").click(function(){
		var strImg = $(this).attr('src');
		strImg = strImg.replace(SITE_URL + 'images.php?src=' + SITE_ROOT + 'uploads/','');
		strImg = strImg.replace('&w=80&h=80','');
		var photosStr = $("#fichiers_urls").val();
		photosStr = photosStr.replace(strImg + ";","");
		$("#fichiers_urls").val(photosStr);
		$(this).remove();
	});

	/*	Utilisé avec le module "slideshow". 
		Télécharge les images sur le serveur.
	*/
	var strBanner = '';
		
	if($("#banner").val() != ""){ strBanner = $("#banner").val(); }

	$("#upload").uploadify({

		'formData': { 'timestamp' : $("#timestamp").html(), 'token': $("#token").html() },
		'fileSizeLimit' : '0',
		'successTimeout': 120,
		'swf': 'js/uploadify/uploadify.swf',
		'uploader': 'js/uploadify/upldslides.php',

		'fileTypeDesc': 'Image',
		'fileTypeExts':'*.gif; *.jpg; *.jpeg; *.png',
		'buttonText': 'Ajouter une photo...',
		'width': 150,
		'multi': false,
		'onUploadSuccess' : function(file, data, response) {
			//alert(data);	
			strBanner = file.name;
			//$('#photosView').append('<img title="Cliquez pour supprimer la photo" width="80" src="http://www.cbc.lu/ange/rumelange/uploads/' + fileObj.name + '" />  ');
			$('#banner').val(strBanner);
			$('#image_banner').attr('src','../uploads/' + strBanner);
		}
	});
	
});















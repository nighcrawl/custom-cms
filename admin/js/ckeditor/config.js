/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.toolbar = 'cbc';
 
    config.toolbar_cbc =
    [
	 	['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Anchor','-','Undo','Redo','-','Maximize'],
		
		'/',
		['Format','-','Cut','Copy','Paste','PasteFromWord'],
		['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
        ['SpellChecker','Find','Replace','-','RemoveFormat','-','Source']
    ];
	
	config.extraPlugins = 'myplugin,anotherplugin';
	config.filebrowserBrowseUrl = 'js/kcfinder/browse.php?type=files&dir=uploads';
	config.filebrowserImageBrowseUrl = 'js/kcfinder/browse.php?type=images&dir=uploads';
	config.filebrowserFlashBrowseUrl = 'js/kcfinder/browse.php?type=flash&dir=uploads';
	config.filebrowserUploadUrl = 'js/kcfinder/upload.php?type=files&dir=uploads';
	config.filebrowserImageUploadUrl = 'js/kcfinder/upload.php?type=images&dir=uploads';
	config.filebrowserFlashUploadUrl = 'js/kcfinder/upload.php?type=flash&dir=uploads';
};

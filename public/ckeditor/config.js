CKEDITOR.editorConfig = function( config ) {
   config.filebrowserBrowseUrl = '/ckfinder/ckfinder.html';
   config.filebrowserImageBrowseUrl = '/ckfinder/ckfinder.html?type=Images';
   config.filebrowserUploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
   config.filebrowserImageUploadUrl = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
};
CKEDITOR.editorConfig = function( config )
{
	config.contentsCss = '/ckeditor/fonts.css';
	config.font_names = 'Archer Book;' + config.font_names;
	config.font_names = 'Helvetica;' + config.font_names;
};
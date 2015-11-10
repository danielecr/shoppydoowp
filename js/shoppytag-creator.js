
(function(){
    tinymce.PluginManager.add('tagcreator_class', function(editor, url) {
	console.log(url + '/icon.png');
	editor.addButton('tagcreator_class', {
	    title: 'Inserisci tag shoppy doo',
	    cmd: 'tagcreator_class',
	    image: url + '/icon.png',
	});
	editor.addCommand('tagcreator_class',function() {
	    editor.windowManager.open({
		width: 600,
		height: 500,
		title: 'Crea il tag shoppydoo',
		url: url+ '/tag-creator.html',
		onsubmit: function(e) {
		    console.log('submit ' + e.data.title2);
		    editor.insertContent('Title: ' + e.data.title);
		}
	    }, {
		jQuery: jQuery
	    });
	});
    });
})(jQuery);



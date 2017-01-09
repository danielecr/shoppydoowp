
(function(){
    tinymce.PluginManager.add('tagcreator_class', function(editor, url) {
	var buttonTitle = document.getElementById('sdwp-button-title').value;
	var windowTitle = document.getElementById('sdwp-window-title').value;
	editor.addButton('tagcreator_class', {
	    title: buttonTitle,
	    cmd: 'tagcreator_class',
	    image: url + '/icon.png',
	});
	editor.addCommand('tagcreator_class',function() {
	    editor.windowManager.open({
		width: 600,
		height: 500,
		title: windowTitle,
		url: url+ '/tag-creator.html',
		onsubmit: function(e) {
		    //console.log('submit ' + e.data.title2);
		    editor.insertContent('Title: ' + e.data.title);
		}
	    }, {
		jQuery: jQuery
	    });
	});
    });
})(jQuery);



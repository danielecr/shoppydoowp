
(function(){
    tinymce.PluginManager.add('earnfromsd_tagcreator_class', function(editor, url) {
	var buttonTitle = document.getElementById('sdwp-button-title').value;
	var windowTitle = document.getElementById('sdwp-window-title').value;
	editor.addButton('earnfromsd_tagcreator_class', {
	    title: buttonTitle,
	    cmd: 'earnfromsd_tagcreator_class',
	    image: url + '/icon.png',
	});
	editor.addCommand('earnfromsd_tagcreator_class',function() {
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



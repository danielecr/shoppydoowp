<?php

class TinyMCE_shoppydoo_tagcreator
{
	function __construct()
	{
		if( is_admin() ) {
		//if(function_exists('add_action') ) {
		add_action('init', array(&$this, 'setup_tinymce_plugin') );
		//}
		}
	}

	function setup_tinymce_plugin()
	{
		if( ! current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
			return;
		}

		if( get_user_option('rich_editing') !== 'true') {
			return;
		}
		add_filter('mce_external_plugins', array(&$this, 'add_tinymce_plugin') );
		add_filter('mce_buttons', array(&$this, 'add_tinymce_toolbar_button') );
	}

	function add_tinymce_plugin($plugin_array )
	{
		$plugin_array['tagcreator_class'] = plugin_dir_url(__FILE__).'js/shoppytag-creator.js';
		return $plugin_array;
	}

	function add_tinymce_toolbar_button($buttons)
	{
		array_push($buttons, 'tagcreator_class');
		//return array();
		return $buttons;
	}
}

$tinymce_shoppydoo_tagc = new TinyMCE_shoppydoo_tagcreator();

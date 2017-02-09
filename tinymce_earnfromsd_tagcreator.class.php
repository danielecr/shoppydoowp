<?php
/*
Earn from Shoppydoo is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Earn from Shoppydoo is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Earn from Shoppydoo. If not, see http://www.gnu.org/licenses/gpl-2.0.html .
*/

class TinyMCE_earnfromsd_tagcreator
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
		$plugin_array['earnfromsd_tagcreator_class'] = plugin_dir_url(__FILE__).'js/shoppytag-creator.js';
		return $plugin_array;
	}

	function add_tinymce_toolbar_button($buttons)
	{
		array_push($buttons, 'earnfromsd_tagcreator_class');
		//return array();
		return $buttons;
	}
}

$tinymce_shoppydoo_tagc = new TinyMCE_earnfromsd_tagcreator();

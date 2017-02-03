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

require_once 'earnfromsd.php';

if(function_exists('add_action')) {
	add_action( 'admin_menu', 'earnfromsd_menu' );
}

/** Step 1. */
function earnfromsd_menu() {
	add_options_page( 'Shoppydoowp options', 'Earn from Shoppydoo', 'manage_options', 'shoppydoowp-options', 'shoppydoowp_options' );
}

function shoppydoowp_first_option_setup()
{
	if(function_exists('update_option') && function_exists('get_option')) {
		$options = get_option('shoppydoowp_options');
		if(!$options && !$options['head']) {
			$options['head'] = '<ul>';
			$options['snippet'] = '<li><a href="[[url]]" target="_blank"><img src="[[smallImage]]" />[[availabilityDescr]]

[[merchant]]</a></li>';
			$options['tail'] = '</ul>';
			$options['duration'] = 3600;
			$options['partnerid'] = 'tecnomagazineit';
			update_option('shoppydoowp_options',$options);
		}
	}
}

/** Step 3. */
function shoppydoowp_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$options = get_option('shoppydoowp_options');
	if ( isset( $_POST['Expire'] ) ) {
		$storer = new shoppydoowpStorer();
		$storer->expireAllVersion($options['tmpl_version']);
	}
	if ( isset( $_POST['Submit'] ) ) {
		$curversion = 0;
		if($options['tmpl_version'] >=1) {
			$curversion = $options['tmpl_version'];
		}
		$options['partnerid'] = $_POST['partnerid'];
		$options['head'] = stripslashes($_POST['head']);
		$options['tail'] = stripslashes($_POST['tail']);
		$options['duration'] = stripslashes($_POST['duration']);

		$advance_ver = false;
		$newsnipp = stripslashes($_POST['snippet']);
		if($newsnipp != $options['snippet']) {
			$options['snippet'] = $newsnipp;
			$advance_ver = true;
		}
		if(isset($_POST['lang'])) {
			$options['lang'] = stripslashes($_POST['lang']);
			$advance_ver = true;
		}
		if($advance_ver) {
			$storer = new shoppydoowpStorer();
			$storer->expireAllVersion($curversion);
			$options['tmpl_version'] = $curversion+1;
		} else {
			$options['tmpl_version'] = $curversion;
		}
		update_option('shoppydoowp_options',$options);
	}
	$options = get_option('shoppydoowp_options');
	include "tmpl/admin-shoppydoo.php";
	
}




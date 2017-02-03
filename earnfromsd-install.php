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

if (basename($_SERVER['SCRIPT_NAME']) == 'plugins.php' && isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init','earnfromsd_install');
}

function earnfromsd_install()
{
	global $wpdb;
	$table_name = earnfromsd_get_tablename();

	$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`tagfeed_id` INT NOT NULL AUTO_INCREMENT,
				`tagstring` VARCHAR(255) NOT NULL UNIQUE,
				`tagreplacement` LONGTEXT,
				`updatetime` INT,
				`duration` INT,
				`templateversion` INT,
				PRIMARY KEY (`tagfeed_id`));";
	require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
	dbDelta( $sql );
	add_option('earnfromsd_db_version',"1.0");
	//@chmod(dirname(__FILE__).'/cache',755);
	shoppydoowp_first_option_setup();
}

function earnfromsd_get_tablename()
{
	global $wpdb;
	$table_name = $wpdb->prefix.'shoppydoowp_xml_feed';
	return $table_name;
}


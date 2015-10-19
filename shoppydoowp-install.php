<?php

require_once 'shoppydoowp.php';

if (basename($_SERVER['SCRIPT_NAME']) == 'plugins.php' && isset($_GET['activate']) && $_GET['activate'] == 'true') {
	add_action('init','shoppydoowp_install');
}

function shoppydoowp_install()
{
	global $wpdb;
	$table_name = shoppydoowp_get_tablename();

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
	add_option('shoppydoowp_db_version',"1.0");
	//@chmod(dirname(__FILE__).'/cache',755);
	shoppydoowp_first_option_setup();
}

function shoppydoowp_get_tablename()
{
	global $wpdb;
	$table_name = $wpdb->prefix.'shoppydoowp_xml_feed';
	return $table_name;
}


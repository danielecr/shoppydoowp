<?php

/**
 * fake wpdb for testing
 * (mock)
 */

class WPDB
{
	public $prefix = '';
	function __construct()
	{
		$dbconf = parse_ini_file('dbconf.ini');
		$this->conn = new mysqli($dbconf['host'],
					     $dbconf['user'],
					     $dbconf['pass'],
					     $dbconf['dbname']
			);
	}
	function query($param)
	{
		print "PARAM::: $param\n";
		return $this->conn->query($param);
	}

	function get_var($query)
	{
		print "QUERY: :: $query\n";
		$res = $this->query($query);
		if($res && $row = $res->fetch_array()) {
			return $row[0];
		}
		return NULL;
	}

	function escape($string)
	{
		return $this->conn->escape_string($string);
	}

}



$wpdb = new WPDB();

global $wpdb;

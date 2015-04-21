<?php

require_once "bbplanetwp.php";

class bbPlanetStorer
{
	function __construct()
	{
		$this->tname = bbplanet_get_tablename(); // CHECK!!!
		$this->expireOld();
	}

	function getTagReplace($tagName)
	{
		global $wpdb;
		$tagName = $wpdb->escape($tagName);
		$repl = $wpdb->get_var("SELECT tagreplacement FROM $this->tname WHERE tagstring='$tagName'");
		if($repl) return $repl;
		return FALSE;
	}

	function expireAllVersion($version)
	{
		global $wpdb;
		$repl = $wpdb->query("DELETE FROM $this->tname WHERE templateversion=$version");
	}

	function expireOld()
	{
		global $wpdb;
		$time = time();
		$repl = $wpdb->query("DELETE FROM $this->tname WHERE updatetime+duration<$time");
	}

	function storeTagReplacement($tag,$replace)
	{
		global $wpdb;
		$version = 0;
		$duration = 24*3600;
		if(function_exists('get_option')) {
			$options = get_option('bbplanetwp_options');
			if($options) {
				$version = $options['version'];
				$duration = $options['duration'];
			} else {
				$version = 0;
				$duration = 24*3600;
			}
		}
		$tagname = $wpdb->escape($tag);
		$replace = $wpdb->escape($replace);
		$now = time();
		$wpdb->query("INSERT INTO `$this->tname`(tagstring,tagreplacement,updatetime,duration,templateversion) VALUES('$tagname', '$replace', '$now', '$duration', '$version')");
	}
}



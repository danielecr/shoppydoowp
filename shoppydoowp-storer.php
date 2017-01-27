<?php
/*
ShoppydooWP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
ShoppydooWP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with ShoppydooWP. If not, see http://www.gnu.org/licenses/gpl-2.0.html .
*/

require_once "shoppydoowp.php";

class shoppyDooWpStorer
{
	function __construct()
	{
		$this->tname = shoppydoowp_get_tablename(); // CHECK!!!
		$this->expireOld();
	}

	function getTagReplace($tagName)
	{
		global $wpdb;
		//$tagName = $wpdb->escape($tagName);
		$repl = $wpdb->get_var($wpdb->prepare("SELECT tagreplacement FROM $this->tname WHERE tagstring=%s",$tagName));
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
			$options = get_option('shoppydoowp_options');
			if($options) {
				$version = $options['tmpl_version'];
				if($options['duration']) {
					$duration = $options['duration'];
				}
			}
		}
		//$tagname = $wpdb->escape($tag);
		//$replace = $wpdb->escape($replace);
		$now = time();
		$wpdb->query($wpdb->prepare("INSERT INTO `$this->tname`(tagstring,tagreplacement,updatetime,duration,templateversion) VALUES(%s, %s, %d, %d, %s)", $tag, $replace, $now, $duration, $version));
		//$wpdb->query("INSERT INTO `$this->tname`(tagstring,tagreplacement,updatetime,duration,templateversion) VALUES('$tagname', '$replace', '$now', '$duration', '$version')");
	}
}



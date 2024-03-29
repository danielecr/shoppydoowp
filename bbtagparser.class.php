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

namespace EarnFromSD;

require_once "taginfo.class.php";

require_once "shoppydooloader.class.php";

use \EarnFromSD\TagInfo;

class bbTagParser
{
	var $tags = array();
	var $replacements = array();
	var $strict_mode = false;
	var $error = false;
	function __construct($content)
	{
		if(!preg_match_all('/\[\[([^\]]+)?\]\]/',$content,$matchTagAll,PREG_SET_ORDER)) {
			return;
		}
		foreach($matchTagAll as $matchTag) {
		$whole = $matchTag[0];
		$vardefs = preg_split('/\|/',$matchTag[1]);
		$tagElements = array();
		$unnamed_counter = 0;
		foreach($vardefs as $vardef) {
			$name_val = preg_split('/:/',$vardef);
			if(count($name_val) == 1) {
				$tagElements['unnamed_'.$unnamed_counter++]  = $name_val[0];
				continue;
			}
			if(count($name_val) != 2) {
				continue;
				$tagElements = array();
				break;  // not a good candidate
				
			}
			list($varname, $value) = $name_val;
			if(preg_match('/^[0-9a-zA-Z]+$/',$name_val[0])) {
				$tagElements[$varname]  = $value;
			}
		}
		if(count($tagElements) > 0 )  {
			$this->tags[] = new TagInfo($whole,$tagElements);
		}
		}
		return;
	}
	
	function hasError()
	{
		return $this->error;
	}

	function getError()
	{
		return print_r($this->errors,TRUE);
	}

	function calcReplacement()
	{
		$loader = new \EarnFromSD\shoppyDooLoader();
		//print_r($this->tags);
		foreach($this->tags as $taginfo) {
			$calcString = $loader->getAllMultiple($taginfo);
			$this->replacements[$taginfo->wholeTag] = $calcString;
			continue;
		}
	}
}

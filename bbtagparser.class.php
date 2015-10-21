<?php

require_once "taginfo.class.php";

require_once "shoppydooloader.class.php";

class bbTagParser
{
	/**
	 * array(bbTagInfos)
	 */
	var $tags = array();
	var $replacements = array();
	var $strict_mode = false;
	var $error = false;
	function __construct($content)
	{
		if(!preg_match('/\[\[([^\]]+)?\]\]/',$content,$matchTag)) {
			return;
		}
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
			if(preg_match('/^[0-9a-z]+$/',$name_val[0])) {
				$tagElements[$varname]  = $value;
			}
		}
		if(count($tagElements) > 0 )  {
			$this->tags[] = new TagInfo($whole,$tagElements);
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
		$loader = new shoppyDooLoader();
		//print_r($this->tags);
		foreach($this->tags as $taginfo) {
			$calcString = $loader->getAllMultiple($taginfo);
			$this->replacements[$taginfo->wholeTag] = $calcString;
			continue;
		}
	}
}

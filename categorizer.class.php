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

class categorizer
{
	var $categories = array();
	var $parent = NULL;
	var $code = NULL;
	/**
	 * given an li categorize things
	 */
	function __costruct($parent=-1)
	{
		$this->categories = array();
		$this->parent = $parent;
	}

	function addSubFromUL($UL)
	{
		foreach($UL->childNodes as $li) {
			if($li->nodeName == 'li') {
				$subcat = new categorizer($this->code?$this->code:-1);
				$subcat->addLiChild($li);
				$this->categories[] = $subcat;
			}
		}

	}

	function parseCatAndCode($text)
	{
		$this->catName = $text;
		if(preg_match('/^([^\(]+)\((.+)\)$/',$text,$matches)) {
			$this->txt = trim($matches[1]);
			$this->code = trim($matches[2]);
		} else {
			$this->nocode = "NOCODE";
		}
	}
	
	function addLiChild($LI)
	{
		foreach($LI->childNodes as $sub1) {
			
			if($sub1->nodeName == '#text') {
				$this->parseCatAndCode($sub1->textContent);
			}

			if($sub1->nodeName == 'ul') {
				$this->addSubFromUL($sub1);
			}
		}
		if(count($this->categories)>0) {
			foreach ($this->categories as $c) {
				$c->parent = $this->code;
			}
		}
		
	}
	var $basePrefix = "_";
	function OptionsOfSelect($prefix,$basearray = array())
	{
		if(isset($this->catName) && $this->catName) {
			$basearray[] = "<option value=\"$this->code\">$prefix $this->catName</option>\n";
		}
		foreach($this->categories as $cats) {
			$basearray = $cats->OptionsOfSelect($prefix.$this->basePrefix,$basearray);
		}
		return $basearray;
	}
	function getArrayWithParent($basearray=array())
	{
		if(isset($this->catName) && $this->catName) {
			if(!$this->parent) $this->parent = -1;
			$basearray[] = array('parent'=>$this->parent,'code'=>$this->code,'catname'=> $this->catName);
		}
		foreach($this->categories as $cats) {
			$basearray = $cats->getArrayWithParent($basearray);
		}
		return $basearray;
	}
}

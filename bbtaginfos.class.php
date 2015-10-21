<?php

class bbTagInfos implements Iterator
{
	var $wholeTag = '';
	var $cities = array();
	var $cats = array();
	var $catGood = array();
	var $error = false;
	var $errors = array();
	public $strict_mode = 0;

	function __construct($whole,$cities,$cats='',$strictMode = 0)
	{
		$this->wholeTag = $whole;
		$this->strict_mode = $strictMode;
		$this->parseCities($cities);
		$this->parseCats($cats);
		$this->calcIter();
	}

	function parseCities($cities)
	{
		$this->cities = explode(',',$cities);
	}

	function parseCats($cats)
	{
		$this->cats = explode(',',$cats);
		foreach($this->cats as $i => $cat) {
			$this->catGood[$i] = true;
			/*
			if(!in_array($cat, shoppyDooStru::$STRUCT_TYPES)) {
				$this->error = true;
				$this->errors[] = $cat. ' categoria non riconosciuta';
				$this->catGood[$i] = false;
			} else {
				$this->catGood[$i] = true;
			}
			*/
		}
	}
	function hasError()
	{
		return $this->error;
	}
	function getError()
	{
		return $this->errors;
	}

	private function getCouple($city,$cat)
	{
		$couple= new StdClass();
		$couple->city = $city;
		$couple->cat = $cat;
		$couple->strict_mode = $this->strict_mode;
		return $couple;
	}

	function calcIter()
	{
		$this->couples = array();
		foreach($this->cities as $city) {
			if(count($this->cats) == 0) {
				$this->couples[] = $this->getCouple($city,null);
				continue;
			}
			$added = false;
			foreach($this->cats as $i => $cat) {
				if($this->catGood[$i]) {
					$this->couples[] = $this->getCouple($city,$cat);
					$added = true;
				}
			}
			if(!$added) {
				$this->couples[] = $this->getCouple($city,null);
			}
		}
	}
	private $couples = array();
	private $cursor = 0;

	function current()
	{
		return $this->couples[$this->cursor];
	}

	function key()
	{
		return $this->cursor;
	}

	function next()
	{
		++$this->cursor;
	}

	function rewind()
	{
		$this->cursor = 0;
	}

	function valid()
	{
		if($this->cursor==0) return true;
		//return true;
		if($this->cursor < count($this->couples) &&
		   $this->cursor > 0 ) {
			return true;
		} else {
			return false;
		}
	}
}

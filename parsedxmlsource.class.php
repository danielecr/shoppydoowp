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

class parsedXmlSource
{
	static public $elements = array(
		'name',
		'description',
		'merchantName',
		'merchant',
		'price',
		'deliveryCost',
		'totalCost',
		'availability',
		'availabilityDescr',
		'url',
		'bigImage',
		'smallImage',
		'smallerImage',
		'merchantImage',
		'categoryId',
		);

	var $products = array();
	var $iC =0;

	function __construct($xml = NULL,$filter = NULL)
	{
		if($xml) {
			$this->parseXml($xml,$filter);
		}
	}
	function add($xml = NULL,$filter=NULL)
	{
		if($xml) {
			$this->parseXml($xml,$filter);
		}
	}

	function reset()
	{
		$this->iC =0;
	}

	function getOffers()
	{
		return $this->products;
	}
	
	function getStruct()
	{
		if($this->iC>count($this->products)) return false;
		return $this->products[$this->iC++];
	}

	function parseXml($xml,$filter = NULL)
	{
		foreach($xml->product as $k => $el) {
			$prod = new stdClass();
			foreach($el as $tagname => $val) {
				if(in_array($tagname,self::$elements)) {
					$prod->$tagname = (string)$val;
				}
			}
			$this->products[] = $prod;
		}
	}
}

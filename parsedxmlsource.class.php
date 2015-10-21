<?php


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

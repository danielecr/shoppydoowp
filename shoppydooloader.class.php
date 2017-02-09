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

require_once "parsedxmlsource.class.php";

class shoppyDooLoader
{


	// Old:
	//var $baseStUrl = 'http://ws.shoppydoo.com/';
	// NEW:
	var $baseStUrl = 'http://quickshop.shoppydoo.it/';
	
	var $partnerid;
	var $lang = 'IT';

	var $lastcount = 0;
	
	public function __construct($partnerid='')
	{
		$this->partnerid = $partnerid;
		if(function_exists('get_option') ) {
			$options = get_option('shoppydoowp_options');
			if(isset($options['lang'])) {
				$this->lang = $options['lang'];
			}
			if(isset($options['partnerid'])) {
				$this->partnerid = $options['partnerid'];
			}
		}
	}
	
	private function getTheXml($params)
	{
		$url = $this->buildStUrl($params);
		$xml = $this->loadXmlFromNet($url);
		return $xml;
	}

	private function loadXmlFromNet($url)
	{
		if(function_exists('wp_remote_get')) {
			$response = wp_remote_get($url);
			if(is_array($response)) {
				return simplexml_load_string($response['body']);
			}
		} else {
			return simplexml_load_file($url);
		}
	}

	private function getNStrutture($xml)
	{
		foreach($xml->attributes() as $k=>$v) {
			if($k == 'nstrutture') {
				return $v;
			}
		}
		return 0;
	}

	public function getStructs($localita, $type = NULL,$strict=false)
	{
		$basePar = array('l'=>$localita,'t'=>$type);
		$xml = $this->getTheXml($basePar);
		$nstrutture = $this->getNStrutture($xml);
		if($strict && $type != NULL) { $filter = $type; }
		else {$filter = NULL; }
		$parsed = new parsedxmlsource($xml,$filter);

		if($nstrutture>10) {
			$p = 1;
			do {
				$basePar['p'] = $p;
				$xml = $this->getTheXml($basePar);
				$parsed->add($xml,$filter);
				$p++;
			}while($p*10<$nstrutture);
		}
		return $parsed;
	}
		
	private function buildStUrl($params)
	{
		$partnerid = $this->partnerid;

		$url = $this->baseStUrl . "$partnerid/";
		$qp = array();
		if(isset($params['7pixel'])) {
			$qp['categoryId'] = $params['7pixel'];
		}
		if(isset($params['resNumCode'])) {
			$qp['resNumCode'] = $params['resNumCode'];
		}
		if(isset($params['sort'])) {
			$qp['sort'] = $params['sort'];
		}
		if(isset($params['keywords'])) {
			$url .= preg_replace('/\s+/','_',$params['keywords']);
		}

		$url .= ".aspx";

		if(count($qp)) {
			$qs = http_build_query($qp);
			$url.= "?$qs";
		}

		$this->targetUrl = $url;
		
		return $url;
		// old:
		$url = "http://ws.shoppydoo.com/$partenerid/$cat/$marcamodello.aspx";
		// new:
		$url = "http://quickshop.shoppydoo.it/bloggydooit/sony.aspx?categoryId=5,7";
	}

	function getAll($city,$cat)
	{
		$parsed = $this->getStructs($city, $cat);
		$formatter = new shoppyDooFormatter();
		$this->lastcount += count($parsed->structures);
		return $formatter->formatAll($parsed->structures);
	}

	function getAllOffers($taginfo)
	{
		$xml = $this->getTheXml($taginfo->tagElements);
		$parsedSource = new parsedXmlSource($xml);
		$this->offers = $parsedSource->getOffers();
		return $this->offers;
	}

	function getAllMultiple($taginfo)
	{

		$storer = new shoppyDooWpStorer();
		$replacement = $storer->getTagReplace($taginfo->wholeTag);
		if($replacement) return $replacement;

		$offers = $this->getAllOffers($taginfo);
		
		$formatter = new shoppyDooFormatter($offers);

		$calcString = $formatter->format($offers);
		$this->calcString = $calcString;
		$storer->storeTagReplacement($taginfo->wholeTag, $calcString);
		return $calcString;
	}
}

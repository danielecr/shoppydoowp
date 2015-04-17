<?php
/* 
   Plugin Name: BBPlanet.net Feed injector for Wordpress
   Plugin URI: http://www.smartango.com/
   Description: Inject BBPlanet hotels et all into wp articles
   Author: Daniele Cruciani
   Version: 0.1
   Author URI: http://www.smartango.com 
*/

if(function_exists('add_filter')) {
	add_filter('the_content', 'bbplanet_parse');
}


class bbTagParser
{
	var $tags = array();
	var replacements = array();
	function __construct($content)
	{
		if(preg_match('/\[\[bbplanet\:([^|]+)\|?(cat:(.*))?\]\]/',$content,$matches)) {
			$whole = $matches[0];
			$city = $matches[1];
			$cat = null;
			if(isset($matches[3])) {
				$cat = $matches[3];
			}
			$this->tags[] = new bbTagInfos($whole,$city,$cats);
		}
		
	}
	
	function calcReplacememt()
	{
		foreach($this->tags as $taginfo) {
			$stru = new bbPlanetStru();
			foreach($taginfo->cities as $city) {
				$calcString = $stru->getAll($city,$taginfo->cat[0]);
				$this->replacements[$taginfo->wholeTag] = $calcString;
			}
	}
}

class bbTagInfos
{
	var $wholeTag = '';
	var $cities = array();
	var $cats = array();
	function __construct($whole,$cities,$cats='')
	{
		$this->wholeTag = $whole;
		$this->cities  = $this->parseCities($cities);
		$this->cats  = $this->parseCats($cats);
	}

	function parseCities($cities)
	{
		$this->cities[] = $cities;
	}
	function parseCats($cats)
	{
		$this->cats[] = $cats;
	}
}

function bbplanet_tagParser($content)
{

}

function bbplanet_parse($content='')
{
	$bbparser = new bbTagParser($content);
	$bbparser->calcReplacement();
	str_replace(array_keys($bbparser->replacement),
		    array_values($bbparser->replacement),
		    $content
		);
	if(preg_match('/\[\[bbplanet\:([^|]+)\|?(cat:(.*))?\]\]/',$content,$matches)) {
		$whole = $matches[0];
		$city = $matches[1];
		$cat = null;
		if(isset($matches[3])) {
			$cat = $matches[3];
		}
		//return preg_replace("/".preg_quote($whole)."/",$city,$content);
		$stru = new bbPlanetStru();
		$calcString = $stru->getAll($city,$cat);
		return preg_replace("/".preg_quote($whole)."/",$calcString,$content);
	}
}

function bbplanet_get_structures($localita, $types = NULL)
{
}

class bbParsedStru
{
	var $elements = array(
		'IDStruttura',
		'tipologia',
		'tipologiaestesa',
		'nomestruttura',
		'stelle',
		'zona',
		'localita',
		'citta',
		'provincia',
		'regione',
		'immagine',
		'descrizione',
		'linkstruttura',
		'xmlstruttura',
		'latitudine',
		'longitudine',
		'npostiletto',
		'nfeedback',
		'datapub',
		);

	var $structures = array();
	var $iC =0;

	function __construct($xml = NULL)
	{
		if($xml) {
			$this->parseXml($xml);
		}
	}
	function add($xml = NULL)
	{
		if($xml) {
			$this->parseXml($xml);
		}
	}

	function reset()
	{
		$this->iC =0;
	}

	function getStruct()
	{
		if($this->iC>count($this->structures)) return false;
		return $this->structures[$this->iC++];
	}

	function parseXml($xml)
	{
		$ff = count($this->structures);
		foreach($this->elements as $element) {
			$delta=0;
			foreach($xml->{$element} as $k => $el) {
				if(count($this->structures) == $ff + $delta ) {
					$this->structures[$ff+$delta] = new StdClass;
				}
				$this->structures[$ff+$delta]->{$element} = (string) $el;
				$delta++;
			}
		}
	}
}

class bbPlanetStru
{
	var $stypes = array('bb' => 'BB',
			    'ho' => 'Albergo',
			    'ap' => 'Appartamento',
			    'at' => 'Agriturismo',
			    're' => 'Residence',
		);
	
	var $baseStUrl = 'http://xml.bbplanet.net/xml/strutture.xml';
	
	var $ida;
	var $lang = 'IT';
	
	public function __construct($ida='10463')
	{
		$this->ida = $ida;
	}
	
	private function getTheXml($params)
	{
		$url = $this->buildStUrl($params);
		$xml = simplexml_load_file($url);
		return $xml;
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

	public function getStructs($localita, $type = NULL)
	{
		$basePar = array('l'=>$localita,'t'=>$type);
		$xml = $this->getTheXml($basePar);
		$nstrutture = $this->getNStrutture($xml);
		$parsed = new bbParsedStru($xml);

		if($nstrutture>10) {
			$p = 1;
			do {
				$basePar['p'] = $p;
				$xml = $this->getTheXml($basePar);
				$parsed->add($xml);
				$p++;
			}while($p*10<$nstrutture);
		}
		//print_r($parsed);
		return $parsed;
	}
		
	private function buildStUrl($params)
	{
		$qp = array('ida'=>$this->ida,
			    'localita'=>$params['l'],
			    'lang' => $this->lang,
			);
		if(isset($params['t']) && $params['t'] != NULL) {
			$qs['tipologia'] = $params['t'];
		}
		if(isset($params['p']) && $params['p'] != NULL) {
			$qs['p'] = $params['p'];
		}
		$qs = http_build_query($qp);
		return $this->baseStUrl . '?' .$qs;
	}

	function getAll($city,$cat)
	{
		$parsed = $this->getStructs($city, $cat);
		$formatter = new bbPlanetFormatter();
		return $formatter->formatAll($parsed->structures);
	}
}

class bbPlanetFormatter
{
	var $begin = '<ul>';
	var $end = '</ul>';
	var $element = "<li>A [[citta]] esiste un hotel
<strong>[[nomestruttura]]<br />
<p>[[descrizione]]</p>
<p>Vacci tu a <a href=\"[[linkstruttura]]\">[[nomestruttura]]</a></p></li>";
	function __costruct()
	{
		
	}

	function formatStruct($struct)
	{
		$orig = $this->element;
		foreach($struct as $k => $v) {
			$orig = preg_replace('/\[\['.preg_quote($k).'\]\]/',$v,$orig);
		}
		return $orig;
	}

	function formatAll($structures)
	{
		$content = '';
		foreach($structures as $stru) {
			$content .= $this->formatStruct($stru);
		}
		return $this->begin . $content . $this->end;
	}
}

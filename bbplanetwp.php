<?php
/* 
   Plugin Name: BBPlanet.net Feed injector for Wordpress
   Plugin URI: http://www.smartango.com/
   Description: Inject BBPlanet hotels et all into wp articles
   Author: Daniele Cruciani
   Version: 1.0
   Author URI: http://www.smartango.com 
*/

if(function_exists('add_filter')) {
	add_filter('the_content', 'bbplanet_parse');
}


class bbTagParser
{
	/**
	 * array(bbTagInfos)
	 */
	var $tags = array();
	var $replacements = array();
	var $error = false;
	function __construct($content)
	{
		if(preg_match('/\[\[bbplanet\:([^|]+)\|?(cat:(.*))?\]\]/',$content,$matches)) {
			$whole = $matches[0];
			$city = $matches[1];
			$cat = null;
			if(isset($matches[3])) {
				$cat = $matches[3];
			}
			$ti = new bbTagInfos($whole,$city,$cat);
			$this->tags[] = $ti;
			if($ti->hasError()) {
				$this->error = true;
				$this->errors[] = $ti->getError();
			}
		}
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
		$stru = new bbPlanetStru();
		//print_r($this->tags);
		foreach($this->tags as $taginfo) {
			$calcString = $stru->getAllMultiple($taginfo);
			$this->replacements[$taginfo->wholeTag] = $calcString;
			continue;
		}
	}
}

class bbTagInfos implements Iterator
{
	var $wholeTag = '';
	var $cities = array();
	var $cats = array();
	var $catGood = array();
	var $error = false;
	var $errors = array();

	function __construct($whole,$cities,$cats='')
	{
		$this->wholeTag = $whole;
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
			if(!in_array($cat, bbPlanetStru::$STRUCT_TYPES)) {
				$this->error = true;
				$this->errors[] = $cat. ' categoria non riconosciuta';
				$this->catGood[$i] = false;
			} else {
				$this->catGood[$i] = true;
			}
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

function bbplanet_tagParser($content)
{

}

function bbplanet_parse($content='')
{
	$bbparser = new bbTagParser($content);
	$bbparser->calcReplacement();
	return str_replace(array_keys($bbparser->replacements),
		    array_values($bbparser->replacements),
		    $content
		);
}

function bbplanet_get_structures($localita, $types = NULL)
{
}

class bbParsedStru
{
	static public $elements = array(
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
		foreach(bbParsedStru::$elements as $element) {
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
	public static $STRUCT_TYPES = array(
		'BB',
		'Albergo',
		'Appartamento',
		'Agriturismo',
		'Residence',
		);
	var $stypes = array('bb' => 'BB',
			    'ho' => 'Albergo',
			    'ap' => 'Appartamento',
			    'at' => 'Agriturismo',
			    're' => 'Residence',
		);
	
	var $baseStUrl = 'http://xml.bbplanet.net/xml/strutture.xml';
	
	var $ida;
	var $lang = 'IT';

	var $lastcount = 0;
	
	public function __construct($ida='10463')
	{
		$this->ida = $ida;
		if(function_exists('get_option') ) {
			$options = get_option('bbplanetwp_options');
			$this->ida = $options['ida'];
		}
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
		$this->lastcount += count($parsed->structures);
		return $formatter->formatAll($parsed->structures);
	}

	function getAllMultiple($taginfo)
	{
		$storer = new bbPlanetStorer();
		$replacement = $storer->getTagReplace($taginfo->wholeTag);
		if($replacement) return $replacement;
		$formatter = new bbPlanetFormatter();
		$this->lastcount = 0;
		$calcString = '';
		foreach($taginfo as $i => $tp) {
			$parsed = $this->getStructs($tp->city, $tp->cat);
			$this->lastcount += count($parsed->structures);
			$calcString .= $formatter->formatAll($parsed->structures);
		}
		$this->calcString = $calcString;
		$storer->storeTagReplacement($taginfo->wholeTag, $calcString);
		return $calcString;
	}
}

class bbPlanetFormatter
{
	
	var $begin = '<ul>';
	var $end = '</ul>';
	var $element = "<li>A [[citta]] hotel
<strong>[[nomestruttura]]<strong><br />
<p>[[descrizione]]</p>
<p>Vai a <a href=\"[[linkstruttura]]\">[[nomestruttura]]</a></p></li>";
	/*
	var $begin;
	var $end;
	var $element;
	*/
	function __construct()
	{
		if(function_exists('get_option') ) {
			$options = get_option('bbplanetwp_options');
			$this->begin = stripslashes($options['head']);
			$this->end = stripslashes($options['tail']);
			$this->element = stripslashes($options['snippet']);
		}
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
		return $this->begin . $content . $this->end . "1n\n";
	}
}


require_once 'bbplanet-admin.php';
require_once 'bbplanet-install.php';
require_once 'bbplanet-storer.php';


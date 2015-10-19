<?php
/* 
   Plugin Name: Shoppydoo Feed injector for Wordpress
   Plugin URI: http://www.smartango.com/
   Description: Inject shoppydoo offer into wp articles
   Author: Daniele Cruciani
   Version: 1.0
   Author URI: http://www.smartango.com 
*/

if(function_exists('add_filter')) {
	add_filter('the_content', 'shoppydoowp_parse');
	add_action('edit_form_after_title', 'shoppydoowp_edit_form_after_title' );
}

function shoppydoowp_edit_form_after_title() {
    echo '<strong>ShoppydooWP tag example</strong>: [[shoppydoowp:Gallipoli|cat:Appartamento]]';
}

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
		if(preg_match('/\[\[shoppydoowp\:([^|]+)(\|cat:([^|]+))?(\|(strict))?\]\]/',$content,$matches)) {
			$whole = $matches[0];
			$city = $matches[1];
			$cat = null;
			if(isset($matches[3])) {
				$cat = $matches[3];
			}
			if(isset($matches[5]) && $matches[5] == 'strict') {
				$strict_mode = 1;
			} else {
				$strict_mode = 0;
			}
			$ti = new bbTagInfos($whole,$city,$cat,$strict_mode);
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
		$stru = new shoppyDooStru();
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
			if(!in_array($cat, shoppyDooStru::$STRUCT_TYPES)) {
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

function shoppydoowp_tagParser($content)
{

}

function shoppydoowp_parse($content='')
{
	$bbparser = new bbTagParser($content);
	$bbparser->calcReplacement();
	return str_replace(array_keys($bbparser->replacements),
		    array_values($bbparser->replacements),
		    $content
		);
}

function shoppydoowp_get_structures($localita, $types = NULL)
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

	function getStruct()
	{
		if($this->iC>count($this->structures)) return false;
		return $this->structures[$this->iC++];
	}

	function parseXml($xml,$filter = NULL)
	{
		$ff = count($this->structures);
		$exclusions = array();
		$delta=0;
		foreach($xml->tipologia as $k => $el) {
			if($filter != NULL && $filter != (string) $el) {
				$exclusions[] = $delta;
			} else {
				//print (string) $el;
				//print " NOT $filter\n";
				//$exclusions[] = $k;
			}
			$delta++;
		}
		//print "Exclusions\n";
		//print_r($exclusions);
		foreach(bbParsedStru::$elements as $element) {
			$delta=0;
			$index = $ff;
			foreach($xml->{$element} as $k => $el) {
				if(in_array($delta,$exclusions)) {
					$delta++;
					continue;
				}
				if(count($this->structures) == $index ) {
					$this->structures[$index] = new StdClass;
				}
				$this->structures[$index]->{$element} = (string) $el;
				$index++;
				$delta++;
			}
		}
	}
}

class shoppyDooStru
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
			$options = get_option('shoppydoowp_options');
			$this->ida = $options['ida'];
			if(isset($options['lang'])) {
				$this->lang = $options['lang'];
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
		$parsed = new bbParsedStru($xml,$filter);

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
		$qp = array('ida'=>$this->ida,
			    'localita'=>$params['l'],
			    'lang' => $this->lang,
			);
		if(isset($params['t']) && $params['t'] != NULL) {
			$qp['tipologia'] = $params['t'];
		}
		if(isset($params['p']) && $params['p'] != NULL) {
			$qp['p'] = $params['p'];
		}
		$qs = http_build_query($qp);
		return $this->baseStUrl . '?' .$qs;
	}

	function getAll($city,$cat)
	{
		$parsed = $this->getStructs($city, $cat);
		$formatter = new shoppyDooFormatter();
		$this->lastcount += count($parsed->structures);
		return $formatter->formatAll($parsed->structures);
	}

	function getAllMultiple($taginfo)
	{

		$storer = new shoppyDooStorer();
		$replacement = $storer->getTagReplace($taginfo->wholeTag);
		if($replacement) return $replacement;

		$formatter = new shoppyDooFormatter();
		$this->lastcount = 0;
		$calcString = '';
		foreach($taginfo as $i => $tp) {
			$parsed = $this->getStructs($tp->city, $tp->cat, $tp->strict_mode);
			$this->lastcount += count($parsed->structures);
			$calcString .= $formatter->formatAll($parsed->structures);
		}
		$this->calcString = $calcString;
		$storer->storeTagReplacement($taginfo->wholeTag, $calcString);
		return $calcString;
	}
}

class shoppyDooFormatter
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
			$options = get_option('shoppydoowp_options');
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
		return $this->begin . $content . $this->end . "\n";
	}
}


require_once 'shoppydoowp-admin.php';
require_once 'shoppydoowp-install.php';
require_once 'shoppydoowp-storer.php';

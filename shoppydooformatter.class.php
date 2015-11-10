<?php

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

	function format($structures)
	{
		//error_log(print_r($structures,TRUE));
		$content = '';
		foreach($structures as $stru) {
			$content .= $this->formatStruct($stru);
		}
		return $this->begin . $content . $this->end . "\n";
	}
}

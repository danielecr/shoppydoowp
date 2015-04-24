<?php


require_once "../bbplanetwp.php";

class testPlugin extends PHPUnit_Framework_TestCase
{

	function testTagParserToo()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[bbplanet:Taviano e|cat:C]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$ti = $bbparser->tags[0];
		print_r($ti);
		foreach($ti as $k => $v) {
			
			print_R($v);
		}
	}
}
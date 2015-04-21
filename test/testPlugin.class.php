<?php

require_once "../bbplanetwp.php";

class testPlugin extends PHPUnit_Framework_TestCase
{
	function testTagParser()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[bbplanet:Taviano|cat:C]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$bbparser->calcReplacement();
		
		//print_r($bbparser->replacements);
		$newtext = str_replace(array_keys($bbparser->replacements),
			    array_values($bbparser->replacements),
			    $text
			);
		$expr = '/^'.str_replace(' ','\ ',preg_quote($head)).".*".str_replace(' ','\ ',preg_quote($tail)).'$/';
		//print "expr : $expr\n";
		$expr1 = '/^'.$head.'/';
		$expr2 = '/'.$tail.'$/';
		//print "new text: $newtext\n";
		$this->assertTrue(preg_match($expr1,$newtext)==1);
		$this->assertTrue(preg_match($expr2,$newtext)==1);
		$this->assertTrue(preg_match('/'.preg_quote($thetag).'/',$newtext)==0);
		//$this->assertTrue(preg_match($expr,$newtext)==1);

		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cats[0] == 'C');

	}

	function testTagParserMultiCity()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[bbplanet:Taviano,Lecce|cat:Albergo,BB]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 2);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cities[1] == 'Lecce');
		$this->assertTrue($bbparser->tags[0]->cats[0] == 'Albergo');
		$this->assertTrue($bbparser->tags[0]->cats[1] == 'BB');
	}

	function testTagParserWrongCat()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[bbplanet:Taviano,Lecce|cat:Albergo,PP]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 2);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cities[1] == 'Lecce');
		$this->assertTrue($bbparser->tags[0]->cats[0] == 'Albergo');
		$this->assertTrue($bbparser->tags[0]->cats[1] == 'PP');
		$this->assertTrue($bbparser->hasError());
		$this->assertTrue($bbparser->getError()!='');
		print $bbparser->getError();
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}

	function testTagParserAndPrint()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[bbplanet:Taviano,Lecce]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 2);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cities[1] == 'Lecce');
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}


}

function get_option()
{
	return array(
		'ida'=>'10463',
		'head'=>'<ul>',
		'tail'=>'</ul>',
		'snippet'=>'<li>[[citta]]</li>'
		);
}
<?php
/*
ShoppydooWP is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
ShoppydooWP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with ShoppydooWP. If not, see http://www.gnu.org/licenses/gpl-2.0.html .
*/

require "./fakeWpdb.php";
$wpdb = new WPDB();
global $wpdb;
$running_test = true;
global $running_test;

require_once "../bbplanetwp.php";

class testPlugin extends PHPUnit_Framework_TestCase
{
	function testTagParser()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Taviano|cat:C]]";
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
		$thetag = "[[7pixel:Taviano,Lecce|cat:Albergo,BB]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 2);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cities[1] == 'Lecce');
		$this->assertTrue($bbparser->tags[0]->cats[0] == 'Albergo');
		$this->assertTrue($bbparser->tags[0]->cats[1] == 'BB');
	}

	function testTagParserWithSpace()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Torre San Giovanni|cat:Albergo]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 1);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Torre San Giovanni');
		$this->assertTrue($bbparser->tags[0]->cats[0] == 'Albergo');
		$this->assertTrue($bbparser->hasError());
		$this->assertTrue($bbparser->getError()!='');
		print $bbparser->getError();
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}


	function testTagParserWrongCat()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Taviano,Lecce|cat:Albergo,PP]]";
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
		$thetag = "[[7pixel:Taviano,Lecce|cat:BB]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 2);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Taviano');
		$this->assertTrue($bbparser->tags[0]->cities[1] == 'Lecce');
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}

	function testTagParserAndPrintStrict()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Pescara|cat:Albergo|strict]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 1);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Pescara');
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}

	function testTagNardoAndPrint()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Nardò|cat:Albergo]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		print_r($bbparser);
		$this->assertTrue(count($bbparser->tags) == 1);
		$this->assertTrue(count($bbparser->tags[0]->cities) == 1);
		$this->assertTrue($bbparser->tags[0]->cities[0] == 'Nardò');
		$bbparser->calcReplacement();
		print_r($bbparser->replacements);

	}

	function testTagIsidoroNardoAndPrint()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Sant'Isidoro di Nardo|cat:Albergo]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		print_r($bbparser);
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
		'snippet'=>'<li>[[citta]]</li>',
		'tmpl_version'=> 1,
		'duration' => 99999999,
		);
}
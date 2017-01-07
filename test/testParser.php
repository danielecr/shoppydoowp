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


require_once "../bbtagparser.class.php";

class testPlugin extends PHPUnit_Framework_TestCase
{

	function testTagParserToo()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:123,123|keywords:uno,e|TN]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$ti = $bbparser->tags[0];
		print_r($ti);
		foreach($ti as $k => $v) {
			
			print_R($v);
		}
	}

	function testTagParserThree()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:Taviano|cat:BB|strict]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		$ti = $bbparser->tags[0];
		print_r($ti);
		foreach($ti as $k => $v) {
			
			print_R($v);
		}
	}

}
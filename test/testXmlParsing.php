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

require_once "../shoppydooloader.class.php";

use \EarnFromSD\bbTagParser;
use \EarnFromSD\shoppyDooLoader;

class shoppyDooStorer
{
	var $lastPar = false;
	function getTagReplace($par)
	{
		$this->lastPar = $par;
	}
}

class testXmlParsing extends PHPUnit_Framework_TestCase
{

	function testParsingPlusXml()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:6,7|keywords:nexus,lg|TN]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		//print_r($bbparser);
		$loader = new shoppyDooLoader();

		
		foreach($bbparser->tags as $taginfo) {
			$offers = $loader->getAllOffers($taginfo);
			print "TARGET URL : $loader->targetUrl\n";
			print_r($offers);
			print "AAAAAA\n\n\nIIIII\n";
		}
	}

	function testParsingPlusXmlReplacement()
	{
		$head = "my litte ";
		$tail = " et all";
		$thetag = "[[7pixel:6,7|keywords:nexus,lg|TN]]";
		$text = $head.$thetag.$tail;
		$bbparser = new bbTagParser($text);
		//print_r($bbparser);
		$loader = new shoppyDooLoader();

		
		foreach($bbparser->tags as $taginfo) {
			$offers = $loader->getAllOffers($taginfo);
			print "TARGET URL : $loader->targetUrl\n";
			print_r($offers);
			print "AAAAAA\n\n\nIIIII\n";
		}
	}


}


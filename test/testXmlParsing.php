<?php


require_once "../bbtagparser.class.php";

require_once "../shoppydooloader.class.php";


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


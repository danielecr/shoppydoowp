<?php

require_once "fakeWpdb.php";

require_once "../bbplanetwp.php";

class testDbStore extends PHPUnit_Framework_TestCase
{
	function testMyretrive()
	{
		$storer = new bbPlanetStorer();
		$tag = '[[afaketag]]';
		$replacement = $storer->getTagReplace($tag);
		$this->assertTrue($replacement === false);
		
	}

	function testStore()
	{
		$storer = new bbPlanetStorer();
		$tag = '[[afaketag]]';
		$replace = 'a long string to replace it';
		$storer->storeTagReplacement($tag,$replace,$duration);
		$repl = $storer->getTagReplace($tag);
		$this->assertTrue($repl == $replace);
		$storer->expireAllVersion(0);
	}

}



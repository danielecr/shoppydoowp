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

require_once "fakeWpdb.php";

require_once "../shoppydoowp.php";

class testDbStore extends PHPUnit_Framework_TestCase
{
	function testMyretrive()
	{
		$storer = new shoppyDooStorer();
		$tag = '[[afaketag]]';
		$replacement = $storer->getTagReplace($tag);
		$this->assertTrue($replacement === false);
		
	}

	function testStore()
	{
		$storer = new shoppyDooStorer();
		$tag = '[[afaketag]]';
		$replace = 'a long string to replace it';
		$storer->storeTagReplacement($tag,$replace,$duration);
		$repl = $storer->getTagReplace($tag);
		$this->assertTrue($repl == $replace);
		$storer->expireAllVersion(0);
	}

}



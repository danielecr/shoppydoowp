<?php
/*
Earn from Shoppydoo is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Earn from Shoppydoo is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Earn from Shoppydoo. If not, see http://www.gnu.org/licenses/gpl-2.0.html .
*/

namespace EarnFromSD;

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

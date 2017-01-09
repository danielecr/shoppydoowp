<?php
/* 
   Plugin Name: Shoppydoo Feed injector for Wordpress
   Plugin URI: http://www.smartango.com/
   Description: Inject shoppydoo offer into wp articles
   Author: Daniele Cruciani
   Version: 1.0
   Author URI: http://www.smartango.com 

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

load_plugin_textdomain();

if(function_exists('add_filter')) {
	add_filter('the_content', 'shoppydoowp_parse');
	add_action('edit_form_after_title', 'shoppydoowp_edit_form_after_title' );
}

if(function_exists('add_action') ) {
	// tinymce plugin
	require_once 'tinymce_shoppydoo_tagcreator.class.php';
	add_action( 'wp_ajax_shoppydoo_product_categories_action', 'shoppydoowp_list_categories_cb' );
	add_action( 'wp_ajax_shoppydoo_tag_window_tmpl_action', 'shoppydoowp_tag_window_tmpl' );
}

function shoppydoowp_list_categories_cb($hook) {
	// get category list xml call ??????
	$categories = shoppydoowp_parse_remote_categories();
	//$categories = array('10'=>'myfirst category','2'=>'my second category','3'=>'mythird');
	header('Content-type: application/json');
	echo json_encode($categories);
	wp_die();
}

function shoppydoowp_tag_window_tmpl($hook) {
	include "tmpl/tag-creator-window.php";
}

function shoppydoowp_retrieve_catlist()
{
	$url= "https://quickshop.shoppydoo.it/categories.aspx";
	
	$content = wp_remote_get($url);
	if(! $content || !isset($content['body']) ) {
		return array();
	}
	$body = $content['body'];
	return $body;
}

function shoppydoowp_parse_remote_categories() {
	$options = get_option('shoppydoowp_catcache');
	if(!isset($options['timeout']) || $options['timeout'] > time() ) {
		$content = shoppydoowp_retrieve_catlist();
		//error_log(print_r($content,TRUE));
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		$xpath = new DOMXPath($doc);
		$doc->normalizeDocument();
		$struct = new stdClass();
		$struct->childs = array();

		$catz = new categorizer(-1);
	
		$divs = $xpath->query('/html/body/div');
		foreach( $divs as $ctx_div)  {
			$dl = $xpath->query('ul',$ctx_div);
			foreach ( $dl as $ul) {
				$catz->addSubFromUL($ul);
			}
		}
		$arrWithPar = $catz->getArrayWithParent();
		$options['timeout'] = time() + 3600 * 24 *30;
		$options['categories'] = $arrWithPar;
		update_option('shoppydoowp_catcache',$options);

		//return $arrWithPar;
	}
	return $options['categories'];
}

function shoppydoowp_edit_form_after_title() {
	include "tmpl/tag-creator.php";
	//$plug_url = plugins_url('js/shoppytag-creator.js',__FILE__);
	//wp_enqueue_script( 'shoppytag-creator', $plug_url, array('jquery') );
}

require_once "bbtagparser.class.php";
require_once "bbtaginfos.class.php";


function shoppydoowp_tagParser($content)
{

}

function shoppydoo_offerte($shop_stringa, $id=0, $cat=NULL)
{
	if($cat==NULL) $cat="20191";
	$partenerid = "cellularmagazineit";
	$options = get_option('shoppydoowp_options');
	$partenerid = $options['partnerid'];
	$marcamodello = preg_replace(array("/[:\)\(\s]+/"),array("_"),strtolower($shop_stringa));
	if($cat == NULL ) {
		$cat = '20191';
	}
	$tagstring = "[[7pixel:$cat|keywords:$marcamodello]]";

	$bbparser = new bbTagParser($tagstring);
	if(count($bbparser->tags)) {
		reset($bbparser->tags);
		$taginfo = current($bbparser->tags);
		
		$loader = new shoppyDooLoader();
		$offers = $loader->getAllOffers($taginfo);
		$a = new stdClass();
		if(count($offers)) {
			$a->product = $offers;
		}
		return $a;
	}
	return false;
}

function shoppydoowp_parse($content='')
{
	$bbparser = new bbTagParser($content);
	$bbparser->calcReplacement();
	//error_log(print_r($bbparser->replacements,TRUE));
	return str_replace(array_keys($bbparser->replacements),
		    array_values($bbparser->replacements),
		    $content
		);
}

function shoppydoowp_get_structures($localita, $types = NULL)
{
}

require_once "parsedxmlsource.class.php";

require_once "shoppydooloader.class.php";

require_once "shoppydooformatter.class.php";

require_once 'shoppydoowp-admin.php';
require_once 'shoppydoowp-install.php';
require_once 'shoppydoowp-storer.php';

require_once "categorizer.class.php";


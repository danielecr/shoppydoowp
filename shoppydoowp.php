<?php
/* 
   Plugin Name: Shoppydoo Feed injector for Wordpress
   Plugin URI: http://www.smartango.com/
   Description: Inject shoppydoo offer into wp articles
   Author: Daniele Cruciani
   Version: 1.0
   Author URI: http://www.smartango.com 
*/

if(function_exists('add_filter')) {
	add_filter('the_content', 'shoppydoowp_parse');
	add_action('edit_form_after_title', 'shoppydoowp_edit_form_after_title' );
}

function shoppydoowp_edit_form_after_title() {
    echo '<strong>ShoppydooWP tag example</strong>: [[shoppydoowp:Gallipoli|cat:Appartamento]]';
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
		return $offers;
	}
	return false;
}

function shoppydoowp_parse($content='')
{
	$bbparser = new bbTagParser($content);
	$bbparser->calcReplacement();
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


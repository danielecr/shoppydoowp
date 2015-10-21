=== shoppydoo feed ===
Contributors: danielecr
Donate link: http://www.smartango.com/
Tags: appartment, receiptive, feed, link
Stable tag: 1.0
Requires at least: 3.1
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Display a list of available shoopy offer from 7pixel (shoppydoo)

Insert the right tag in the article and with this plugin
activated it will be listed all offers available from
shoppydoo.com feed

Tag format is: [[7pixel:cat1,cat2|keywords:a,b|NN:num]]
cat1,cat2 are categories
a,b  keywords
num: number of offers to show


== Installation ==

Copy this folder in directory wp-content/plugins/ of your wordpress

Make sure the script can write the cache directory, have a look
via ftp, in doubt set it to 777

== Upgrade ==

I am experiencing some problem, deactivate and reactivate the module
should fix those. Be carefull, just make a backup of table data
(prefix_category_ext_feed), deactivate plugin, then reactivate.
Thus if data about feed is not available anymore, just replace
the table.


== Changelog ==

= 1.0 =
* a working version (port from bbplanet)


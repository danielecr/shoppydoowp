=== bbplanet feed ===
Contributors: danielecr
Donate link: http://www.smartango.com/
Tags: appartment, receiptive, feed, link
Stable tag: 1.3
Requires at least: 3.1
Tested up to: 4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Display a list of available (in bbplanet) receiptive structure
(hotel,b&b, appartments, residence, agriturism) in an article.

Insert the right tag in the article and with this plugin
activated it will be listed all structure available from
bbplanet.net feed

Tag format is: [[bbplanet:Gallipoli|cat:Albergo]]
Gallipoli is the city (more city could be specified separing
with comma ','), categories are one or more of: 'BB','Albergo',
'Appartamento','Agriturismo','Residence'.


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

= 1.3 =
* add strict mode to the tag: show only one category

= 1.2 =
* fix get remote file (use wp_remote_get)

= 1.1 =
* add language support
* reorder description

= 1.0 =
* a working version

= 0.1 =
* initial release (only readme)

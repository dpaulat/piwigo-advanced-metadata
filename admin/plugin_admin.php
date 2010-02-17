<?php
/*
 * -----------------------------------------------------------------------------
 * Plugin Name: Advanced MetaData
 * -----------------------------------------------------------------------------
 * Author     : Grum
 *   email    : grum@piwigo.org
 *   website  : http://photos.grum.fr
 *   PWG user : http://forum.piwigo.org/profile.php?id=3706
 *
 *   << May the Little SpaceFrog be with you ! >>
 *
 * -----------------------------------------------------------------------------
 *
 * See main.inc.php for release information
 *
 * -----------------------------------------------------------------------------
*/

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include(AMD_PATH."amd_aip.class.inc.php");

global $prefixeTable;

load_language('plugin.lang', AMD_PATH);

$main_plugin_object = get_plugin_data($plugin_id);

$plugin_ai = new AMD_AIP($prefixeTable, $main_plugin_object->get_filelocation());
$plugin_ai->manage();

?>

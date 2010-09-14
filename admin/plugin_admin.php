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

global $prefixeTable;

load_language('plugin.lang', AMD_PATH);

$main_plugin_object = get_plugin_data($plugin_id);

/*
 * if the plugin is newly installed, display a special configuration page
 * otherwise, display normal page
 */

$config=Array();
GPCCore::loadConfig('amd', $config);

if($config['newInstall']=='n')
{
  include(AMD_PATH."amd_aip.class.inc.php");
  $plugin_ai = new AMD_AIP($prefixeTable, $main_plugin_object->getFileLocation());}
else
{
  include(AMD_PATH."amd_aip_install.class.inc.php");
  $plugin_ai = new AMD_AIPInstall($prefixeTable, $main_plugin_object->getFileLocation());
}

$plugin_ai->manage();

?>

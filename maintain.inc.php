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
 */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', true);

include_once('amd_version.inc.php'); // => Don't forget to update this file !!


defined('AMD_DIR') || define('AMD_DIR' , basename(dirname(__FILE__)));
defined('AMD_PATH') || define('AMD_PATH' , PHPWG_PLUGINS_PATH . AMD_DIR . '/');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCore.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTables.class.inc.php');


global $gpc_installed, $gpcNeeded, $lang; //needed for plugin manager compatibility

/* -----------------------------------------------------------------------------
 * AMD needs the Grum Plugin Classe
 * -------------------------------------------------------------------------- */
$gpc_installed=false;
$gpcNeeded="3.2.0";
if(file_exists(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php'))
{
  @include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
  // need GPC release greater or equal than 3.2.0
  if(CommonPlugin::checkGPCRelease(3,2,0))
  {
    @include_once("amd_install.class.inc.php");
    $gpc_installed=true;
  }
}

function gpcMsgError(&$errors)
{
  global $gpcNeeded;
  $msg=sprintf(l10n('To install this plugin, you need to install Grum Plugin Classes %s before'), $gpcNeeded);
  if(is_array($errors))
  {
    array_push($errors, $msg);
  }
  else
  {
    $errors=Array($msg);
  }
}
// -----------------------------------------------------------------------------



load_language('plugin.lang', AMD_PATH);

function plugin_install($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable, $gpc_installed, $gpcNeeded;
  if($gpc_installed)
  {
    $amd=new AMD_install($prefixeTable, __FILE__);
    $result=$amd->install();
    GPCCore::register($amd->getPluginName(), AMD_VERSION, $gpcNeeded);
  }
  else
  {
    gpcMsgError($errors);
  }
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable, $gpcNeeded;

  $amd=new AMD_install($prefixeTable, __FILE__);
  $result=$amd->activate();
  GPCCore::register($amd->getPluginName(), AMD_VERSION, $gpcNeeded);
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable, $gpc_installed, $gpcNeeded;
  if($gpc_installed)
  {
    $amd=new AMD_install($prefixeTable, __FILE__);
    $result=$amd->uninstall();
    GPCCore::unregister($amd->getPluginName());
  }
  else
  {
    gpcMsgError($errors);
  }
}



?>

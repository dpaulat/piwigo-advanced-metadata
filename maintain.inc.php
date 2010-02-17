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

defined('AMD_DIR') || define('AMD_DIR' , basename(dirname(__FILE__)));
defined('AMD_PATH') || define('AMD_PATH' , PHPWG_PLUGINS_PATH . AMD_DIR . '/');
@include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/tables.class.inc.php');


global $gpc_installed, $lang; //needed for plugin manager compatibility

/* -----------------------------------------------------------------------------
AMD needs the Grum Plugin Classe
----------------------------------------------------------------------------- */
$gpc_installed=false;
if(file_exists(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php'))
{
  @include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/main.inc.php');
  // need GPC release greater or equal than 2.0.4

  if(checkGPCRelease(2,0,4))
  {
    @include_once("amd_install.class.inc.php");
    $gpc_installed=true;
  }
}

function gpcMsgError(&$errors)
{
  array_push($errors, sprintf(l10n('Grum Plugin Classes is not installed (release >= %s)'), "2.0.4"));
}
// -----------------------------------------------------------------------------



load_language('plugin.lang', AMD_PATH);

function plugin_install($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable, $gpc_installed;
  if($gpc_installed)
  {
    $amd=new AMD_install($prefixeTable, __FILE__);
    $result=$amd->install();
  }
  else
  {
    gpcMsgError($errors);
  }
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
  global $prefixeTable;

  $amd=new AMD_install($prefixeTable, __FILE__);
  $result=$amd->activate();
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable, $gpc_installed;
  if($gpc_installed)
  {
    $amd=new AMD_install($prefixeTable, __FILE__);
    $result=$amd->uninstall();
  }
  else
  {
    gpcMsgError($errors);
  }
}



?>

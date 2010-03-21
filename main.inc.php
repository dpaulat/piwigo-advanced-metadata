<?php
/*
Plugin Name: Advanced MetaData
Version: 0.1b
Description: An advanced metadata manager
Plugin URI: http://phpwebgallery.net/ext/extension_view.php?eid=364
Author: Piwigo team
Author URI: http://piwigo.org
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@piwigo.org
    website  : http://photos.grum.fr
    PWG user : http://forum.piwigo.org/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
--------------------------------------------------------------------------------

:: HISTORY

| release | date       |
| 0.0     | 2010/01/21 | * start coding
| 0.1b    | 2010/03/21 | * beta release
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |


:: TO DO

--------------------------------------------------------------------------------

:: NFO
  AMD_AIM : classe to manage plugin integration into plugin menu
  AMD_AIP : classe to manage plugin admin pages
  AMD_PIP : classe to manage plugin public integration

--------------------------------------------------------------------------------
*/

// pour faciliter le debug - make debug easier :o)
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('AMD_DIR' , basename(dirname(__FILE__)));
define('AMD_PATH' , PHPWG_PLUGINS_PATH . AMD_DIR . '/');

define('AMD_VERSION' , '0.1b'); //=> ne pas oublier la version dans l'entête !!

global $prefixeTable, $page;


if(defined('IN_ADMIN'))
{
  //AMD admin part loaded and active only if in admin page
  include_once("amd_aim.class.inc.php");
  $obj = new AMD_AIM($prefixeTable, __FILE__);
  $obj->init_events();
  set_plugin_data($plugin['id'], $obj);
}
else
{
  //AMD public part loaded and active only if in public page
  include_once("amd_pip.class.inc.php");
  $obj = new AMD_PIP($prefixeTable, __FILE__);
  set_plugin_data($plugin['id'], $obj);
}


?>

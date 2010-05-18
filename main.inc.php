<?php
/*
Plugin Name: Advanced MetaData
Version: 0.4
Description: An advanced metadata manager
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=364
Author: grum@piwigo.org
Author URI: http://photos.grum.fr/
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
|         |            |
| 0.1b    | 2010/03/21 | * beta release
|         |            |
| 0.2b    | 2010/03/23 | * beta release
|         |            |
| 0.3b    | 2010/04/11 | * beta release
|         |            |
| 0.4     | 2010/04/24 | * release for Piwigo 2.1
|         |            | * uses some GPC 3.1.0 functions
|         |            | * optimize ajax request to fill the metadata database
|         |            | * replace all the 'mysql_*' functions with 'pwg_db_*'
|         |            |   functions
|         |            | * update some html/css
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
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', true);

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('AMD_DIR' , basename(dirname(__FILE__)));
define('AMD_PATH' , PHPWG_PLUGINS_PATH . AMD_DIR . '/');

include_once('amd_version.inc.php'); // => Don't forget to update this file !!

global $prefixeTable, $page;


if(defined('IN_ADMIN'))
{
  //AMD admin part loaded and active only if in admin page
  include_once("amd_aim.class.inc.php");
  $obj = new AMD_AIM($prefixeTable, __FILE__);
  $obj->initEvents();
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

<?php
/*
Plugin Name: Advanced MetaData
Version: 0.5.1
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
|         |            |
| 0.1b    | 2010/03/21 | * beta release
|         |            |
|         |            |
| 0.2b    | 2010/03/23 | * beta release
|         |            |
|         |            |
| 0.3b    | 2010/04/11 | * beta release
|         |            |
|         |            |
| 0.4.0   | 2010/04/24 | * release for Piwigo 2.1
|         |            |
|         |            | * uses some GPC 3.1.0 functions
|         |            |
|         |            | * optimize ajax request to fill the metadata database
|         |            |
|         |            | * replace all the 'mysql_*' functions with 'pwg_db_*'
|         |            |   functions
|         |            |
|         |            | * update some html/css
|         |            |
|         |            |
| 0.5.0   | 2010/07/24 | * release 0.5.0 was not published, implemented features
|         |            |   are reported in the release 0.5.1
|         |            |
|         |            |
| 0.5.1   | 2010/09/12 | * update to the JpegMetadata class 1.0.1 to fix the
|         |            |   mantis bugs&features 1686, 1718 and 1719
|         |            |
|         |            | * mantis : bug 1686
|         |            |   . Picture analysis finish with an Error 500 or with a
|         |            |     problem of memory limit
|         |            |
|         |            | * mantis : feature 1719
|         |            |   . Coding a DateTime class
|         |            |
|         |            | * mantis : feature 1718
|         |            |   . Make test images lighter
|         |            |     The weight of tests images provided with the
|         |            |     JpegMetadata class was to heavy ; the file size has
|         |            |     been reduced from 9Mb to 230Kb
|         |            |
|         |            | * mantis : feature 1688
|         |            |   . Improve performance when the database is filled
|         |            |     each time a page is displayed (now using an ajax
|         |            |     call)
|         |            |
|         |            | * mantis : feature 1692
|         |            |   . Add possibility for user to build their own "magic"
|         |            |     tags
|         |            |
|         |            | * mantis : feature 1777
|         |            |   . Weight of the metadata database can becomes very
|         |            |     heavy
|         |            |
|         |            | * mantis : feature 1691
|         |            |   . Add possibility to search picture by metadata
|         |            |     properties
|         |            |
|         |            | * mantis bug:1826
|         |            |   . digiKam XMP tags are not recognized
|         |            |
|         |            | * mantis : feature 1846
|         |            |   . Read the metadata in the HD picture
|         |            |
|         |            | * mantis : feature 1857
|         |            |   . Implement a basic and an advanced interface mode
|         |            |
|         |            | * mantis : feature 1858
|         |            |   . Ability to import tags from picture to piwigo tags
|         |            |
|         |            | * mantis bug:1859
|         |            |   . JpegMetadata class can't manage multiple IPTC keywords
|         |            |     keywords
|         |            |
|         |            | * mantis bug:1861
|         |            |   . Accentued chars from ISO-8859-1 charset are not
|         |            |     recognized
|         |            |
|         |            | * ajax management entirely rewritted
|         |            |
|         |            | * user interface reviewed
|         |            |
|         |            | * add some triggers events when picture metadata are
|         |            |   loaded in the picture.php page
|         |            |   . amd_jpegMD_loaded
|         |            |   . amd_jpegMD_userDefinedValues_built
|         |            |
|         |            | * mantis : feature 1858
|         |            |   . Ability to import tags from picture to piwigo tags
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |
|         |            |


:: TO DO

--------------------------------------------------------------------------------
*
* :: NFO
* AMD_AIM : classe to manage plugin integration into plugin menu
* AMD_AIP : classe to manage plugin admin pages
* AMD_PIP : classe to manage plugin public integration
*
*
* :: Triggers & data provided
* - amd_jpegMD_loaded
*   . triggered on the picture.php page, when metadata were loaded from the
*     picture file
*   . the JpegMetadata object is provided as data to the callback function
*
* - amd_jpegMD_userDefinedValues_built
*   . triggered on the picture.php page, when metadata were loaded from the
*     picture file and user defined tags were built
*   . an array is provided as data to the callback function
*       $data['picture'] : an array of (tagId => value) with only the selected
*                          metadata
*       $data['user']    : an array of (tagId => value) with only the user
*                          defined metadata built
*
*
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

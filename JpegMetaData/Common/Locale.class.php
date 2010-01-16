<?php
/*
 * --:: JPEG MetaDatas ::-------------------------------------------------------
 *
 *  Author    : Grum
 *   email    : grum at piwigo.org
 *   website  : http://photos.grum.fr
 *
 *   << May the Little SpaceFrog be with you ! >>
 *
 *
 * +-----------------------------------------------------------------------+
 * | JpegMetaData - a PHP based Jpeg Metadata manager                      |
 * +-----------------------------------------------------------------------+
 * | Copyright(C) 2010  Grum - http://www.grum.fr                          |
 * +-----------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify  |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation                                          |
 * |                                                                       |
 * | This program is distributed in the hope that it will be useful, but   |
 * | WITHOUT ANY WARRANTY; without even the implied warranty of            |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
 * | General Public License for more details.                              |
 * |                                                                       |
 * | You should have received a copy of the GNU General Public License     |
 * | along with this program; if not, write to the Free Software           |
 * | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
 * | USA.                                                                  |
 * +-----------------------------------------------------------------------+
 *
 *
 * -----------------------------------------------------------------------------
 *
 * The Locale class is used for tag translation, reading the .mo files
 *
 * -----------------------------------------------------------------------------
 *
 *
 * -----------------------------------------------------------------------------
 */


require_once(JPEG_METADATA_DIR."External/php-gettext/gettext.inc");

$supported_locales = array('en_UK');

class Locale
{
  const JMD_TAG = "Tag";
  const JMD_TAGDESC = "TagDesc";

  static function set($language)
  {
    T_setlocale(LC_MESSAGES, $language);

    T_bindtextdomain(self::JMD_TAG, dirname(dirname(__FILE__))."/Locale");
    T_bindtextdomain(self::JMD_TAGDESC, dirname(dirname(__FILE__))."/Locale");
  }

  static function get($tagName)
  {
    return(@T_dgettext(self::JMD_TAG, $tagName));
  }

  static function getDesc($tagName)
  {
    return(@T_dgettext(self::JMD_TAGDESC, $tagName));
  }

}


?>

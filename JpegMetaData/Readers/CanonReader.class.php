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
 * The CanonReader class is the dedicated to read the specific Canon tags
 *
 * ====> See MakerNotesReader.class.php to know more about the structure <======
 *
 * -----------------------------------------------------------------------------
 *
 * .. Notes ..
 *
 *
 * The CanonReader class is derived from the MakerNotesReader class.
 *
 * =====> See MakerNotesReader.class.php to know more about common methods <====
 *
 * -----------------------------------------------------------------------------
 */



  require_once(JPEG_METADATA_DIR."TagDefinitions/CanonTags.class.php");
  require_once(JPEG_METADATA_DIR."Readers/MakerNotesReader.class.php");

  class CanonReader extends MakerNotesReader
  {
    /**
     * The constructor needs, like the ancestor, the datas to be parsed
     *
     * Some datas are offset on extra data, and this offset can be (some time)
     * absolute inside the IFD, or relative. So, the offset of the IFD structure
     * is needed
     *
     * The byte order can be different from the TIFF byte order !
     *
     * The constructor need the maker signature (see the MakerNotesSignatures
     * class for a list of known signatures)
     *
     * @param String $data
     * @param ULong $offset : offset of IFD block in the jpeg file
     * @param String $byteOrder
     * @param String $makerSignature :
     */
    function __construct($data, $offset, $byteOrder, $makerSignature)
    {
      /*
       * Canon don't have signatures in his maker note, starting directly with
       * the number of entries
       */
      $this->maker = MAKER_CANON;
      $this->header = "";
      $this->headerSize = 0;

      parent::__construct($data, $offset, $byteOrder);
    }

    function __destruct()
    {
      parent::__destruct();
    }


    /**
     * initialize the definition for Pentax exif tags
     */
    protected function initializeTagDef()
    {
      $this->tagDef = new CanonTags();
    }

    /**
     * skip the IFD header
     */
    protected function skipHeader($headerSize=0)
    {
      parent::skipHeader($headerSize);
    }

    /**
     * this function do the interpretation of specials tags
     *
     * the function return the interpreted value for the tag
     *
     * @param $tagId             : the id of the tag
     * @param $values            : 'raw' value to be interpreted
     * @param UByte $type        : if needed (for IFD structure) the type of data
     * @param ULong $valueOffset : if needed, the offset of data in the jpeg file
     * @return String or Array or DateTime or Integer or Float...
     */
    protected function processSpecialTag($tagId, $values, $type, $valuesOffset=0)
    {
      switch($tagId)
      {
        default:
          $returned="Not yet implemented;".ConvertData::toHexDump($tagId, ByteType::USHORT)." => ".ConvertData::toHexDump($values, $type);
          break;
      }
      return($returned);
    }
  }

?>

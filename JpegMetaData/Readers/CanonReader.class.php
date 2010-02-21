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
        case 0x0001: // "CanonImageType"
          $this->processSubTag0x0001($values);
          $returned=$values;
          break;
        case 0x0006: // "CanonImageType"
        case 0x0007: // "CanonFirmwareVersion"
        case 0x0009: // "OwnerName"
        case 0x0095: // "LensModel"
        case 0x0096: // "InternalSerialNumber"
          /*
           * null terminated strings
           */
          $tmp=ConvertData::toStrings($values);
          if(is_array($tmp))
          {
            $returned=$tmp[0];
          }
          else
          {
            $returned=$tmp;
          }
          break;
        case 0x000c: // "SerialNumber"
          $returned=$values;
          break;
        case 0x0010: // "CanonModelID"
          $tag=$this->tagDef->getTagById(0x0010);
          $returned=$tag['tagValues.special'][sprintf("0x%08x", $values)];
          unset($tag);
          break;
        case 0x0015: // "SerialNumberFormat"
          $tag=$this->tagDef->getTagById(0x0015);
          $returned=$tag['tagValues.special'][sprintf("0x%08x", $values)];
          unset($tag);
          break;
        default:
          $returned="Not yet implemented;".ConvertData::toHexDump($tagId, ByteType::USHORT)." => ".ConvertData::toHexDump($values, $type);
          break;
      }
      return($returned);
    }

    /**
     * this function process the subtag of the 0x0001 "CanonCameraSettings" tag
     */
    protected function processSubTag0x0001($values)
    {
      foreach($values as $key => $val)
      {
        $tagDef=$this->tagDef->getTagById("0001.$key");

        if(is_array($tagDef))
        {
          // make a fake IFDEntry
          $entry=new IfdEntryReader("\x01\x00\x00\x00\x00\x00\x00\x00\xFF\xFF\xFF".chr($key), $this->byteOrder, "", 0, null);

          $entry->getTag()->setId("0001.$key");
          $entry->getTag()->setName($tagDef['tagName']);
          $entry->getTag()->setValue($val);
          $entry->getTag()->setKnown(true);
          $entry->getTag()->setImplemented($tagDef['implemented']);
          $entry->getTag()->setTranslatable($tagDef['translatable']);

          if(array_key_exists('tagValues', $tagDef))
          {
            if(array_key_exists($val, $tagDef['tagValues']))
            {
              $entry->getTag()->setLabel($tagDef['tagValues'][$val]);
            }
            else
            {
              $entry->getTag()->setLabel("unknown (".$val.")");
            }
          }
          else
          {
            switch($key)
            {
              default:
                $entry->getTag()->setLabel("not yet implemented");
                break;
            }
          }

          $this->entries[]=$entry;

          unset($entry);
        }
        unset($tagDef);
      }
    }
  }

?>

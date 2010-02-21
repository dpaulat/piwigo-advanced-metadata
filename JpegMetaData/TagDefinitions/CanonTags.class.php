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
 * The CanonTags is the definition of the specific Canon Exif tags
 *
 * -----------------------------------------------------------------------------
 *
 * .. Notes ..
 *
 * The CanonTags class is derived from the KnownTags class.
 *
 * ======> See KnownTags.class.php to know more about the tag definitions <=====
 *
 *
 * Pentax values from
 *  - Exiftool by Phil Harvey    => http://www.sno.phy.queensu.ca/~phil/exiftool/
 *                                  http://owl.phy.queensu.ca/~phil/exiftool/TagNames
 *  - Exiv2 by Andreas Huggel    => http://www.exiv2.org/
 *
 */

  require_once(JPEG_METADATA_DIR."TagDefinitions/KnownTags.class.php");

  /**
   * Define the tags for Nikon camera
   */
  class CanonTags extends KnownTags
  {
    protected $label = "Canon specific tags";
    protected $tags = Array(
      /*
       * tags with defined values
       */

      // CanonCameraSettings, tag 0x0001
      0x0001 => Array(
        'tagName'     => "CanonCameraSettings",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonCameraSettings

      /*
       * The 'CanonCameraSettings' tags is composed of 46 sub tags
       * Each subtag name is defined bu the class by the concatenation of
       * "CanonCameraSettings" and the subtag nam
       *
       * Giving something like :
       *  "CanonCameraSettings.MacroMode" for the subtag 0x01
       *
       * This kind of data needs a particular algorythm in the CanonReader class
       *
       * >>> Begin of CanonCameraSettings subtags
       *
       */
      "0001.1" => Array(
        'tagName'     => "CanonCameraSettings.MacroMode",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            1 => "Macro",
            2 => "Normal",
          ),
      ),

      "0001.2" => Array(
        'tagName'     => "CanonCameraSettings.SelfTimer",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ),

      "0001.3" => Array(
        'tagName'     => "CanonCameraSettings.Quality",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0x01 => "Economy",
            0x02 => "Normal",
            0x03 => "Fine",
            0x04 => "RAW",
            0x05 => "Superfine",
            0x82 => "Normal Movie"
          ),
      ),

      "0001.4" => Array(
        'tagName'     => "CanonCameraSettings.CanonFlashMode",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0x00 => "Off",
            0x01 => "Auto",
            0x02 => "On",
            0x03 => "Red-eye reduction",
            0x04 => "Slow-sync",
            0x05 => "Red-eye reduction (Auto)",
            0x06 => "Red-eye reduction (On)",
            0x10 => "External flash",
          ),
      ),

      "0001.5" => Array(
        'tagName'     => "CanonCameraSettings.ContinuousDrive",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0 => "Single",
            1 => "Continuous",
            2 => "Movie",
            3 => "Continuous, Speed Priority",
            4 => "Continuous, Low",
            5 => "Continuous, High",
          ),
      ),

      "0001.7" => Array(
        'tagName'     => "CanonCameraSettings.FocusMode",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0x00 => "One-shot AF",
            0x01 => "AI Servo AF",
            0x02 => "AI Focus AF",
            0x03 => "Manual Focus (3)",
            0x04 => "Single",
            0x05 => "Continuous",
            0x06 => "Manual Focus (6)",
            0x10 => "Pan Focus",
          ),
      ),

      "0001.9" => Array(
        'tagName'     => "CanonCameraSettings.RecordMode",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0x01 => "JPEG",
            0x02 => "CRW+THM",
            0x03 => "AVI+THM",
            0x04 => "TIF",
            0x05 => "TIF+JPEG",
            0x06 => "CR2",
            0x07 => "CR2+JPEG",
          ),
      ),

      "0001.10" => Array(
        'tagName'     => "CanonCameraSettings.CanonImageSize",
        'schema'      => "Canon",
        'translatable'=> true,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues'   => Array(
            0x00 => "Large",
            0x01 => "Medium",
            0x02 => "Small",
            0x05 => "Medium 1",
            0x06 => "Medium 2",
            0x07 => "Medium 3",
            0x08 => "Postcard",
            0x09 => "Widescreen",
            0x81 => "Medium Movie",
            0x82 => "Small Movie",
          ),
      ),


      /*
       * <<< End of CanonCameraSettings subtags
       */

      // CanonFocalLength, tag 0x0000
      0x0002 => Array(
        'tagName'     => "CanonFocalLength",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonFocalLength

      // CanonFlashInfo?, tag 0x0003
      0x0003 => Array(
        'tagName'     => "CanonFlashInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonFlashInfo

      // CanonShotInfo, tag 0x0004
      0x0004 => Array(
        'tagName'     => "CanonShotInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // <

      // CanonPanorama, tag 0x0005
      0x0005 => Array(
        'tagName'     => "CanonPanorama",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonPanorama

      // CanonImageType, tag 0x0006
      0x0006 => Array(
        'tagName'     => "CanonImageType",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // <

      // CanonFirmwareVersion, tag 0x0007
      0x0007 => Array(
        'tagName'     => "CanonFirmwareVersion",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // < CanonFirmwareVersion

      // FileNumber, tag 0x0008
      0x0008 => Array(
        'tagName'     => "FileNumber",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < FileNumber

      // OwnerName, tag 0x0009
      0x0009 => Array(
        'tagName'     => "OwnerName",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // < OwnerName

      // UnknownD30, tag 0x000a
      0x000a => Array(
        'tagName'     => "UnknownD30",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < UnknownD30

      // SerialNumber, tag 0x000c
      0x000c => Array(
        'tagName'     => "SerialNumber",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // < SerialNumber

      // CanonCameraInfo, tag 0x000d
      0x000d => Array(
        'tagName'     => "CanonCameraInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonCameraInfo

      // CanonFileLength, tag 0x000e
      0x000e => Array(
        'tagName'     => "CanonFileLength",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonFileLength

      // CustomFunctions, tag 0x000f
      0x000f => Array(
        'tagName'     => "CustomFunctions",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CustomFunctions

      // CanonModelID, tag 0x0010
      0x0010 => Array(
        'tagName'     => "CanonModelID",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues.special'   => Array(
            '0x01010000' => "PowerShot A30",
            '0x01040000' => "PowerShot S300 / Digital IXUS 300 / IXY Digital 300",
            '0x01060000' => "PowerShot A20",
            '0x01080000' => "PowerShot A10",
            '0x01090000' => "PowerShot S110 / Digital IXUS v / IXY Digital 200",
            '0x01100000' => "PowerShot G2",
            '0x01110000' => "PowerShot S40",
            '0x01120000' => "PowerShot S30",
            '0x01130000' => "PowerShot A40",
            '0x01140000' => "EOS D30",
            '0x01150000' => "PowerShot A100",
            '0x01160000' => "PowerShot S200 / Digital IXUS v2 / IXY Digital 200a",
            '0x01170000' => "PowerShot A200",
            '0x01180000' => "PowerShot S330 / Digital IXUS 330 / IXY Digital 300a",
            '0x01190000' => "PowerShot G3",
            '0x01210000' => "PowerShot S45",
            '0x01230000' => "PowerShot SD100 / Digital IXUS II / IXY Digital 30",
            '0x01240000' => "PowerShot S230 / Digital IXUS v3 / IXY Digital 320",
            '0x01250000' => "PowerShot A70",
            '0x01260000' => "PowerShot A60",
            '0x01270000' => "PowerShot S400 / Digital IXUS 400 / IXY Digital 400",
            '0x01290000' => "PowerShot G5",
            '0x01300000' => "PowerShot A300",
            '0x01310000' => "PowerShot S50",
            '0x01340000' => "PowerShot A80",
            '0x01350000' => "PowerShot SD10 / Digital IXUS i / IXY Digital L",
            '0x01360000' => "PowerShot S1 IS",
            '0x01370000' => "PowerShot Pro1",
            '0x01380000' => "PowerShot S70",
            '0x01390000' => "PowerShot S60",
            '0x01400000' => "PowerShot G6",
            '0x01410000' => "PowerShot S500 / Digital IXUS 500 / IXY Digital 500",
            '0x01420000' => "PowerShot A75",
            '0x01440000' => "PowerShot SD110 / Digital IXUS IIs / IXY Digital 30a",
            '0x01450000' => "PowerShot A400",
            '0x01470000' => "PowerShot A310",
            '0x01490000' => "PowerShot A85",
            '0x01520000' => "PowerShot S410 / Digital IXUS 430 / IXY Digital 450",
            '0x01530000' => "PowerShot A95",
            '0x01540000' => "PowerShot SD300 / Digital IXUS 40 / IXY Digital 50",
            '0x01550000' => "PowerShot SD200 / Digital IXUS 30 / IXY Digital 40",
            '0x01560000' => "PowerShot A520",
            '0x01570000' => "PowerShot A510",
            '0x01590000' => "PowerShot SD20 / Digital IXUS i5 / IXY Digital L2",
            '0x01640000' => "PowerShot S2 IS",
            '0x01650000' => "PowerShot SD430 / IXUS Wireless / IXY Wireless",
            '0x01660000' => "PowerShot SD500 / Digital IXUS 700 / IXY Digital 600",
            '0x01668000' => "EOS D60",
            '0x01700000' => "PowerShot SD30 / Digital IXUS i zoom / IXY Digital L3",
            '0x01740000' => "PowerShot A430",
            '0x01750000' => "PowerShot A410",
            '0x01760000' => "PowerShot S80",
            '0x01780000' => "PowerShot A620",
            '0x01790000' => "PowerShot A610",
            '0x01800000' => "PowerShot SD630 / Digital IXUS 65 / IXY Digital 80",
            '0x01810000' => "PowerShot SD450 / Digital IXUS 55 / IXY Digital 60",
            '0x01820000' => "PowerShot TX1",
            '0x01870000' => "PowerShot SD400 / Digital IXUS 50 / IXY Digital 55",
            '0x01880000' => "PowerShot A420",
            '0x01890000' => "PowerShot SD900 / Digital IXUS 900 Ti / IXY Digital 1000",
            '0x01900000' => "PowerShot SD550 / Digital IXUS 750 / IXY Digital 700",
            '0x01920000' => "PowerShot A700",
            '0x01940000' => "PowerShot SD700 IS / Digital IXUS 800 IS / IXY Digital 800 IS",
            '0x01950000' => "PowerShot S3 IS",
            '0x01960000' => "PowerShot A540",
            '0x01970000' => "PowerShot SD600 / Digital IXUS 60 / IXY Digital 70",
            '0x01980000' => "PowerShot G7",
            '0x01990000' => "PowerShot A530",
            '0x02000000' => "PowerShot SD800 IS / Digital IXUS 850 IS / IXY Digital 900 IS",
            '0x02010000' => "PowerShot SD40 / Digital IXUS i7 / IXY Digital L4",
            '0x02020000' => "PowerShot A710 IS",
            '0x02030000' => "PowerShot A640",
            '0x02040000' => "PowerShot A630",
            '0x02090000' => "PowerShot S5 IS",
            '0x02100000' => "PowerShot A460",
            '0x02120000' => "PowerShot SD850 IS / Digital IXUS 950 IS / IXY Digital 810 IS",
            '0x02130000' => "PowerShot A570 IS",
            '0x02140000' => "PowerShot A560",
            '0x02150000' => "PowerShot SD750 / Digital IXUS 75 / IXY Digital 90",
            '0x02160000' => "PowerShot SD1000 / Digital IXUS 70 / IXY Digital 10",
            '0x02180000' => "PowerShot A550",
            '0x02190000' => "PowerShot A450",
            '0x02230000' => "PowerShot G9",
            '0x02240000' => "PowerShot A650 IS",
            '0x02260000' => "PowerShot A720 IS",
            '0x02290000' => "PowerShot SX100 IS",
            '0x02300000' => "PowerShot SD950 IS / Digital IXUS 960 IS / IXY Digital 2000 IS",
            '0x02310000' => "PowerShot SD870 IS / Digital IXUS 860 IS / IXY Digital 910 IS",
            '0x02320000' => "PowerShot SD890 IS / Digital IXUS 970 IS / IXY Digital 820 IS",
            '0x02360000' => "PowerShot SD790 IS / Digital IXUS 90 IS / IXY Digital 95 IS",
            '0x02370000' => "PowerShot SD770 IS / Digital IXUS 85 IS / IXY Digital 25 IS",
            '0x02380000' => "PowerShot A590 IS",
            '0x02390000' => "PowerShot A580",
            '0x02420000' => "PowerShot A470",
            '0x02430000' => "PowerShot SD1100 IS / Digital IXUS 80 IS / IXY Digital 20 IS",
            '0x02460000' => "PowerShot SX1 IS",
            '0x02470000' => "PowerShot SX10 IS",
            '0x02480000' => "PowerShot A1000 IS",
            '0x02490000' => "PowerShot G10",
            '0x02510000' => "PowerShot A2000 IS",
            '0x02520000' => "PowerShot SX110 IS",
            '0x02530000' => "PowerShot SD990 IS / Digital IXUS 980 IS / IXY Digital 3000 IS",
            '0x02540000' => "PowerShot SD880 IS / Digital IXUS 870 IS / IXY Digital 920 IS",
            '0x02550000' => "PowerShot E1",
            '0x02560000' => "PowerShot D10",
            '0x02570000' => "PowerShot SD960 IS / Digital IXUS 110 IS / IXY Digital 510 IS",
            '0x02580000' => "PowerShot A2100 IS",
            '0x02590000' => "PowerShot A480",
            '0x02600000' => "PowerShot SX200 IS",
            '0x02610000' => "PowerShot SD970 IS / Digital IXUS 990 IS / IXY Digital 830 IS",
            '0x02620000' => "PowerShot SD780 IS / Digital IXUS 100 IS / IXY Digital 210 IS",
            '0x02630000' => "PowerShot A1100 IS",
            '0x02640000' => "PowerShot SD1200 IS / Digital IXUS 95 IS / IXY Digital 110 IS",
            '0x02700000' => "PowerShot G11",
            '0x02710000' => "PowerShot SX120 IS",
            '0x02720000' => "PowerShot S90",
            '0x02750000' => "PowerShot SX20 IS",
            '0x02760000' => "PowerShot SD980 IS / Digital IXUS 200 IS / IXY Digital 930 IS",
            '0x02770000' => "PowerShot SD940 IS / Digital IXUS 120 IS / IXY Digital 220 IS",
            '0x02800000' => "PowerShot A495",
            '0x02810000' => "PowerShot A490",
            '0x02820000' => "PowerShot A3100 IS",
            '0x02830000' => "PowerShot A3000 IS",
            '0x02840000' => "PowerShot SD1400 IS / Digital IXUS 130 / IXY Digital 400F",
            '0x02850000' => "PowerShot SD1300 IS / Digital IXUS 105 / IXY Digital 200F",
            '0x02860000' => "PowerShot SD3500 IS / Digital IXUS 210 / IXY Digital 10S",
            '0x02870000' => "PowerShot SX210 IS",
            '0x03010000' => "PowerShot Pro90 IS",
            '0x04040000' => "PowerShot G1",
            '0x06040000' => "PowerShot S100 / Digital IXUS / IXY Digital",
            '0x4007d675' => "HV10",
            '0x4007d777' => "iVIS DC50",
            '0x4007d778' => "iVIS HV20",
            '0x4007d779' => "DC211",
            '0x4007d77b' => "iVIS HR10",
            '0x4007d87f' => "FS100",
            '0x4007d880' => "iVIS HF10",
            '0x80000001' => "EOS-1D",
            '0x80000167' => "EOS-1DS",
            '0x80000168' => "EOS 10D",
            '0x80000169' => "EOS-1D Mark III",
            '0x80000170' => "EOS Digital Rebel / 300D / Kiss Digital",
            '0x80000174' => "EOS-1D Mark II",
            '0x80000175' => "EOS 20D",
            '0x80000176' => "EOS Digital Rebel XSi / 450D / Kiss X2",
            '0x80000188' => "EOS-1Ds Mark II",
            '0x80000189' => "EOS Digital Rebel XT / 350D / Kiss Digital N",
            '0x80000190' => "EOS 40D",
            '0x80000213' => "EOS 5D",
            '0x80000215' => "EOS-1Ds Mark III",
            '0x80000218' => "EOS 5D Mark II",
            '0x80000232' => "EOS-1D Mark II N",
            '0x80000234' => "EOS 30D",
            '0x80000236' => "EOS Digital Rebel XTi / 400D / Kiss Digital X (and rare K236)",
            '0x80000250' => "EOS 7D",
            '0x80000252' => "EOS Rebel T1i / 500D / Kiss X3",
            '0x80000254' => "EOS Rebel XS / 1000D / Kiss F",
            '0x80000261' => "EOS 50D",
            '0x80000270' => "EOS Rebel T2i / 550D / Kiss X4",
            '0x80000281' => "EOS-1D Mark IV",
        ),
      ), // < CanonModelID

      // CanonAFInfo, tag 0x0012
      0x0012 => Array(
        'tagName'     => "CanonAFInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonAFInfo

      // ThumbnailImageValidArea, tag 0x0013
      0x0013 => Array(
        'tagName'     => "ThumbnailImageValidArea",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ThumbnailImageValidArea

      // SerialNumberFormat, tag 0x0015
      0x0015 => Array(
        'tagName'     => "SerialNumberFormat",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
        'tagValues.special' => Array(
            '0x90000000' => "Format 1",
            '0xa0000000' => "Format 2",
        )
      ), // < SerialNumberFormat

      // SuperMacro, tag 0x001a
      0x001a => Array(
        'tagName'     => "SuperMacro",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < SuperMacro

      // DateStampMode, tag 0x001c
      0x001c => Array(
        'tagName'     => "DateStampMode",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < DateStampMode

      // MyColors, tag 0x001d
      0x001d => Array(
        'tagName'     => "MyColors",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < MyColors

      // , tag 0x0000
      0x0000 => Array(
        'tagName'     => "",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // <

      // FirmwareRevision, tag 0x001e
      0x001e => Array(
        'tagName'     => "FirmwareRevision",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < FirmwareRevision

      // Categories, tag 0x0023
      0x0023 => Array(
        'tagName'     => "Categories",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < Categories

      // FaceDetect1, tag 0x0024
      0x0024 => Array(
        'tagName'     => "FaceDetect1",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < FaceDetect1

      // FaceDetect2, tag 0x0025
      0x0025 => Array(
        'tagName'     => "FaceDetect2",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < FaceDetect2

      // CanonAFInfo2, tag 0x0026
      0x0026 => Array(
        'tagName'     => "CanonAFInfo2",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonAFInfo2

      // ImageUniqueID, tag 0x0028
      0x0028 => Array(
        'tagName'     => "ImageUniqueID",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ImageUniqueID

      // RawDataOffset, tag 0x0081
      0x0081 => Array(
        'tagName'     => "RawDataOffset",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < RawDataOffset

      // OriginalDecisionDataOffset, tag 0x0083
      0x0083 => Array(
        'tagName'     => "OriginalDecisionDataOffset",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < OriginalDecisionDataOffset

      // CustomFunctions1D, tag 0x0090
      0x0090 => Array(
        'tagName'     => "CustomFunctions1D",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CustomFunctions1D

      // PersonalFunctions, tag 0x0091
      0x0091 => Array(
        'tagName'     => "PersonalFunctions",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < PersonalFunctions

      // PersonalFunctionValues, tag 0x0092
      0x0092 => Array(
        'tagName'     => "PersonalFunctionValues",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < PersonalFunctionValues

      // CanonFileInfo, tag 0x0093
      0x0093 => Array(
        'tagName'     => "CanonFileInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonFileInfo

      // AFPointsInFocus1D, tag 0x0094
      0x0094 => Array(
        'tagName'     => "AFPointsInFocus1D",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < AFPointsInFocus1D

      // LensModel, tag 0x0095
      0x0095 => Array(
        'tagName'     => "LensModel",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // < LensModel

      // InternalSerialNumber, tag 0x0096
      0x0096 => Array(
        'tagName'     => "InternalSerialNumber",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => true,
      ), // < InternalSerialNumber

      // DustRemovalData, tag 0x0097
      0x0097 => Array(
        'tagName'     => "DustRemovalData",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < DustRemovalData

      // CustomFunctions2, tag 0x0099
      0x0099 => Array(
        'tagName'     => "CustomFunctions2",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CustomFunctions2

      // ProcessingInfo, tag 0x00a0
      0x00a0 => Array(
        'tagName'     => "ProcessingInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ProcessingInfo

      // ToneCurveTable, tag 0x00a1
      0x00a1 => Array(
        'tagName'     => "ToneCurveTable",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ToneCurveTable

      // SharpnessTable, tag 0x00a2
      0x00a2 => Array(
        'tagName'     => "SharpnessTable",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < SharpnessTable

      // SharpnessFreqTable, tag 0x00a3
      0x00a3 => Array(
        'tagName'     => "SharpnessFreqTable",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < SharpnessFreqTable

      // WhiteBalanceTable, tag 0x00a4
      0x00a4 => Array(
        'tagName'     => "WhiteBalanceTable",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < WhiteBalanceTable

      // ColorBalance, tag 0x00a9
      0x00a9 => Array(
        'tagName'     => "ColorBalance",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ColorBalance

      // MeasuredColor, tag 0x00aa
      0x00aa => Array(
        'tagName'     => "MeasuredColor",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < MeasuredColor

      // ColorTemperature, tag 0x00ae
      0x00ae => Array(
        'tagName'     => "ColorTemperature",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ColorTemperature

      // CanonFlags, tag 0x00b0
      0x00b0 => Array(
        'tagName'     => "CanonFlags",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < CanonFlags

      // ModifiedInfo, tag 0x00b1
      0x00b1 => Array(
        'tagName'     => "ModifiedInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ModifiedInfo

      // ToneCurveMatching, tag 0x00b2
      0x00b2 => Array(
        'tagName'     => "ToneCurveMatching",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ToneCurveMatching

      // WhiteBalanceMatching, tag 0x00b3
      0x00b3 => Array(
        'tagName'     => "WhiteBalanceMatching",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < WhiteBalanceMatching

      // ColorSpace, tag 0x00b4
      0x00b4 => Array(
        'tagName'     => "ColorSpace",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ColorSpace

      // PreviewImageInfo, tag 0x00b6
      0x00b6 => Array(
        'tagName'     => "PreviewImageInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < PreviewImageInfo

      // VRDOffset, tag 0x00d0
      0x00d0 => Array(
        'tagName'     => "VRDOffset",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < VRDOffset

      // SensorInfo, tag 0x00e0
      0x00e0 => Array(
        'tagName'     => "SensorInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < SensorInfo

      // ColorData, tag 0x4001
      0x4001 => Array(
        'tagName'     => "ColorData",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ColorData

      // ColorInfo, tag 0x4003
      0x4003 => Array(
        'tagName'     => "ColorInfo",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < ColorInfo

      // AFMicroAdj, tag 0x4013
      0x4013 => Array(
        'tagName'     => "AFMicroAdj",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < AFMicroAdj

      // VignettingCorr, tag 0x4015
      0x4015 => Array(
        'tagName'     => "VignettingCorr",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < VignettingCorr

      // VignettingCorr2, tag 0x4016
      0x4016 => Array(
        'tagName'     => "VignettingCorr2",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < VignettingCorr2

      // LightingOpt, tag 0x4018
      0x4018 => Array(
        'tagName'     => "LightingOpt",
        'schema'      => "Canon",
        'translatable'=> false,
        'combiTag'    => 0,
        'implemented' => false,
      ), // < LightingOpt


    );

    function __destruct()
    {
      parent::__destruct();
    }

  } // NikonTags



?>

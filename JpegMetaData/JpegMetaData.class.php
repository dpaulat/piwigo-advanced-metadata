<?php
/**
 * --:: JPEG MetaDatas ::-------------------------------------------------------
 *
 * Version : 1.0.0
 * Date    : 2009/12/26
 *
 *  Author    : Grum
 *   email    : grum at piwigo.org
 *   website  : http://photos.grum.fr
 *
 *   << May the Little SpaceFrog be with you ! >>
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
 * +-:: HISTORY ::--------+-----------------------------------------------------
 * |         |            |
 * | Release | Date       |
 * +---------+------------+-----------------------------------------------------
 * | 0.1.0a  | 2009-12-26 |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * |         |            |
 * +---------+------------+-----------------------------------------------------
 *
 *
 * -----------------------------------------------------------------------------
 *
 * References about definition & interpretation of metadata tags :
 *  - EXIF 2.20 Specification    => http://www.exif.org/Exif2-2.PDF
 *  - TIFF 6.0 Specification     => http://partners.adobe.com/public/developer/en/tiff/TIFF6.pdf
 *  - Exiftool by Phil Harvey    => http://www.sno.phy.queensu.ca/~phil/exiftool/
 *                                  http://owl.phy.queensu.ca/~phil/exiftool/TagNames
 *  - Exiv2 by Andreas Huggel    => http://www.exiv2.org/
 *  - MetaData working group     => http://www.metadataworkinggroup.org/specs/
 *  - Adobe XMP Developer Center => http://www.adobe.com/devnet/xmp/
 *  - Gezuz                      => http://gezus.one.free.fr/?Plugin-EXIF-pour-Spip-1-9-2
 *  - JPEG format                => http://crousseau.free.fr/imgfmt_jpeg.htm
 *  - International Press Telecomunication Council specifications
 *                               => http://www.iptc.org/
 *  - IPTC headers structure     => http://www.codeproject.com/KB/graphics/iptc.aspx?msg=1014929
 *  - CPAN                       => http://search.cpan.org/dist/Image-MetaData-JPEG/lib/Image/MetaData/JPEG/Structures.pod
 *                               => http://search.cpan.org/~bettelli/Image-MetaData-JPEG/lib/Image/MetaData/JPEG/MakerNotes.pod
 *
 * -----------------------------------------------------------------------------
 * To support internationalization the JpegMetaData package uses ".po" and ".mo"
 * files, and use "php-gettext"
 * ==> See files in External/php-gettext for more information about this project
 * -----------------------------------------------------------------------------
 *
 * The JpegMetaData is the main class for reading metadata of a Jpeg file
 *
 * It provides two essentialy high level functions to read different kind of
 * metadata (EXIF, IPTC, XMP) :
 *  - (static) getTagList
 *  - load
 *  - getTags
 *
 * -----------------------------------------------------------------------------
 *
 * .. Notes ..
 *
 * About tags and translation in local lang
 * With the 'getTags()' method, the JpegMetaData returns an array of Tag objects
 * found in the jpeg file.
 *
 * A Tag object have 2 properties that can be translated into a local language :
 *  - the name, getted with 'getName()'
 *  - the valueLabel, getted with 'getLabel()'
 *
 * Theses properties ARE NOT translated automatically.
 *
 * You can translate it with the Locale class, by using the static method 'get'
 *
 * Example :
 *  Locale::get($myTag->getName()) will return the translated name of the Tag
 *  Locale::get($myTag->getLabel()) will return the translated value of the Tag
 *
 * ===========> See Tag.class.php to know more about the Tag class <============
 * ========> See Locale.class.php to know more about the Locale class <=========
 *
 *
 * -----------------------------------------------------------------------------
 */

  define("JPEG_METADATA_DIR", dirname(__FILE__)."/");

  require_once(JPEG_METADATA_DIR."Readers/JpegReader.class.php");
  require_once(JPEG_METADATA_DIR."TagDefinitions/MagicTags.class.php");

  class JpegMetaData
  {
    const TAGFILTER_KNOWN       = 0x01;
    const TAGFILTER_IMPLEMENTED = 0x02;
    const TAGFILTER_ALL         = 0x03;

    const KEY_EXIF_TIFF = "exif.tiff";
    const KEY_EXIF_EXIF = "exif.exif";
    const KEY_EXIF_GPS  = "exif.gps";
    const KEY_EXIF  = "exif";
    const KEY_IPTC  = "iptc";
    const KEY_XMP   = "xmp";
    const KEY_MAGIC = "magic";

    private $jpeg = null;
    private $tags = Array();
    private $options = Array();

    /**
     * this static function returns an array of tags definitions
     *
     * the only parameter is an array to determine filter options
     *
     * ---------------------+---------------------------------------------------
     * key                  | descriptions/values
     * ---------------------+---------------------------------------------------
     * filter               | Integer
     *                      | This options is used to filter implemented tag
     *                      |  JpegMetaData::TAGFILTER_ALL
     *                      |  => returns all the tags
     *                      |  JpegMetaData::TAGFILTER_IMPLEMENTED
     *                      |  => returns only the implemented tags
     *                      |
     * optimizeIptcDateTime | Boolean
     *                      | IPTC Date/Time are separated into 2 tags
     *                      | if this option is set to true, only dates tags are
     *                      | returned (assuming this option is used when an
     *                      | image file is loaded)
     *                      |
     * exif                 | Boolean
     * iptc                 | If set to true, the function returns all the tags
     *                      | known for the specified type tag
     * xmp                  |
     * maker                | maker => returns specifics tags from all the known
     * magic                |          makers
     *                      |
     * ---------------------+---------------------------------------------------
     *
     * returned value is an array
     * each keys is a tag name and the associated value is a 2-level array
     *  'implemented' => Boolean, allowing to know if the tags is implemented or
     *                   not
     *  'translatable'=> Boolean, allowing to know if the tag value can be
     *                   translated
     *  'name'        => String, the tag name translated in locale language
     *
     * @Param Array $options  (optional)
     * @return Array(keyName => Array('implemented' => Boolean, 'name' => String))
     */
    static public function getTagList($options=Array())
    {
      $default=Array(
        'filter' => self::TAGFILTER_ALL,
        'optimizeIptcDateTime' => false,
        'exif'  => true,
        'iptc'  => true,
        'xmp'   => true,
        'maker' => true,
        'magic' => true,
      );

      foreach($default as $key => $val)
      {
        if(array_key_exists($key, $options))
          $default[$key]=$options[$key];
      }

      $list=Array();
      $returned=Array();

      if($default['exif'])
      {
        $list[]="exif";
        $list[]="gps";
      }

      if($default['maker'])
      {
        $list[]=MAKER_PENTAX;
        $list[]=MAKER_NIKON;
        $list[]=MAKER_CANON;
      }

      if($default['iptc'])
        $list[]="iptc";

      if($default['xmp'])
        $list[]="xmp";

      if($default['magic'])
        $list[]="magic";

      foreach($list as $val)
      {
        unset($tmp);

        switch($val)
        {
          case "exif":
            $tmp=new IfdTags();
            $schema="exif";
            break;
          case "gps":
            $tmp=new GpsTags();
            $schema="exif.gps";
            break;
          case "iptc":
            $tmp=new IptcTags();
            $schema="iptc";
            break;
          case "xmp":
            $tmp=new XmpTags();
            $schema="xmp";
            break;
          case "magic":
            $tmp=new MagicTags();
            $schema="magic";
            break;
          case MAKER_PENTAX:
            include_once(JPEG_METADATA_DIR."TagDefinitions/PentaxTags.class.php");
            $tmp=new PentaxTags();
            $schema="exif.".MAKER_PENTAX;
            break;
          case MAKER_NIKON:
            include_once(JPEG_METADATA_DIR."TagDefinitions/NikonTags.class.php");
            $tmp=new NikonTags();
            $schema="exif.".MAKER_NIKON;
            break;
          case MAKER_CANON:
            include_once(JPEG_METADATA_DIR."TagDefinitions/CanonTags.class.php");
            $tmp=new CanonTags();
            $schema="exif.".MAKER_CANON;
            break;
          default:
            $tmp=null;
            $schema="?";
            break;
        }

        if(!is_null($tmp))
          foreach($tmp->getTags() as $key => $tag)
          {
            if(self::filter(true, $tag['implemented'], $default['filter']))
            {
              if(array_key_exists('tagName', $tag))
                $name=$tag['tagName'];
              else
                $name=$key;

              if(array_key_exists('schema', $tag) and $val=="exif")
                $subSchema=".".$tag['schema'];
              else
                $subSchema="";

              if($val=='xmp')
                $keyName=$schema.$subSchema.".".$key;
              else
                $keyName=$schema.$subSchema.".".$name;
              $returned[$keyName]=Array(
                'implemented' => $tag['implemented'],
                'translatable' => $tag['translatable'],
                'name' => $name
              );
            }
          }
      }

      ksort($returned);

      return($returned);
    }


    /**
     * the filter function is used by the classe to determine if a tag is
     * filtered or not
     *
     * @Param Boolean $known
     * @Param Boolean $implemented
     * @Param Integer $filter
     *
     */
    static public function filter($known, $implemented, $filter)
    {
      return(($known and (($filter & self::TAGFILTER_KNOWN) == self::TAGFILTER_KNOWN )) or
                ($implemented and (($filter & self::TAGFILTER_IMPLEMENTED) == self::TAGFILTER_IMPLEMENTED )));
    }

    /**
     * the constructor need an optional filename and options
     *
     * if no filename is given, you can use the "load" function after the object
     * is instancied
     *
     * if no options are given, the class use the default values
     *
     * ---------------------+---------------------------------------------------
     * key                  | descriptions/values
     * ---------------------+---------------------------------------------------
     * filter               | Integer
     *                      | This options is used to filter implemented tag
     *                      |  JpegMetaData::TAGFILTER_ALL
     *                      |  => returns all the tags
     *                      |  JpegMetaData::TAGFILTER_IMPLEMENTED
     *                      |  => returns only the implemented tags, not
     *                      |     implemented tag are excluded
     *                      |  JpegMetaData::TAGFILTER_KNOWN
     *                      |  => returns only the known tags (implemented or
     *                      |     not), unknown tag are excluded
     *                      |
     * optimizeIptcDateTime | Boolean
     *                      | IPTC Date/Time are separated into 2 tags
     *                      | if this option is set to true, only dates tags are
     *                      | returned (in this case, time is included is the
     *                      | date)
     *                      |
     * exif                 | Boolean
     * iptc                 | If set to true, the function returns all the tags
     * xmp                  | known for the specified type tag
     * magic                | the exif parameter include the maker tags
     *                      |
     * ---------------------+---------------------------------------------------
     *
     * @Param String $file    (optional)
     * @Param Array  $options (optional)
     *
     */
    function __construct($file = "", $options = Array())
    {
      $this->load($file, $options);
    }

    function __destruct()
    {
      $this->unsetAll();
    }

    /**
     * load a file
     *
     * options values are the same than the constructor's options
     *
     * @Param String $file
     * @Param Array  $options (optional)
     *
     */
    public function load($file, $options = Array())
    {
      $this->unsetAll();

      $this->initializeOptions($options);
      $this->tags = Array();
      $this->jpeg = new JpegReader($file);

      if($this->jpeg->isLoaded() and $this->jpeg->isValid())
      {
        foreach($this->jpeg->getAppMarkerSegments() as $key => $appMarkerSegment)
        {
          if($appMarkerSegment->dataLoaded())
          {
            $data=$appMarkerSegment->getData();

            if($data instanceof TiffReader)
            {
              /*
               * Load Exifs tags from Tiff block
               */
              if($data->getNbIFDs()>0)
              {
                $this->loadIfdTags($data->getIFD(0), self::KEY_EXIF_TIFF);
              }
            }
            elseif($data instanceof XmpReader)
            {
              /*
               * Load Xmp tags from Xmp block
               */
              $this->loadTags($data->getTags(), self::KEY_XMP);
            }
            elseif($data instanceof IptcReader)
            {
              /*
               * Load IPTC tags from IPTC block
               */
              if($this->options['optimizeIptcDateTime'])
                $data->optimizeDateTime();

              $this->loadTags($data->getTags(), self::KEY_IPTC);
            }
          }
        }

        if($this->options['magic'])
        {
          $this->processMagicTags();
        }

        ksort($this->tags);
      }
    }

    /**
     * This function returns an array of tags found in the loaded file
     *
     * It's possible to made a second selection to filter items
     *
     * ---------------------+---------------------------------------------------
     * key                  | descriptions/values
     * ---------------------+---------------------------------------------------
     * tagFilter            | Integer
     *                      | This options is used to filter implemented tag
     *                      |  JpegMetaData::TAGFILTER_ALL
     *                      |  => returns all the tags
     *                      |  JpegMetaData::TAGFILTER_IMPLEMENTED
     *                      |  => returns only the implemented tags, not
     *                      |     implemented tag are excluded
     *                      |  JpegMetaData::TAGFILTER_KNOWN
     *                      |  => returns only the known tags (implemented or
     *                      |     not), unknown tag are excluded
     *                      |
     * ---------------------+---------------------------------------------------
     *
     * Note, the filter is applied on loaded tags. If a filter was applied when
     * the file was loaded, you cannot expand the tag list, only reduce
     * example :
     *  $jpegmd = new JpegMetadata($file, Array('filter' => JpegMetaData::TAGFILTER_IMPLEMENTED));
     *     => the unknown tag are not loaded
     *  $jpegmd->getTags(JpegMetaData::TAGFILTER_ALL)
     *     => unknown tag will not be restitued because they are not loaded...
     *
     * the function returns an array of Tag.
     *
     *
     * ===========> See the Tag.class.php to know all about a tag <=============
     *
     * @Param Integer $tagFilter (optional)
     *
     */
    public function getTags($tagFilter = self::TAGFILTER_ALL)
    {
      $returned=Array();
      foreach($this->tags as $key => $val)
      {
        if(self::filter($val->isKnown(), $val->isImplemented(), $tagFilter))
        {
          $returned[$key]=$val;
        }
      }
      return($returned);
    }

    /**
     * initialize the options...
     *
     * @Param Array $options (optional)
     *
     */
    private function initializeOptions($options=Array())
    {
      $this->options = Array(
        'filter' => self::TAGFILTER_ALL,
        'optimizeIptcDateTime' => false,
        'exif'  => true,
        'iptc'  => true,
        'xmp'   => true,
        'magic' => true
      );

      foreach($this->options as $key => $val)
      {
        if(array_key_exists($key, $options))
          $this->options[$key]=$options[$key];
      }
    }

    /**
     * load tags from an IFD structure
     *
     * see Tiff.class.php and IfdReader.class.php for more informations
     *
     * @Param IfdReader $ifd
     * @Param String    $exifKey
     *
     */
    private function loadIfdTags($ifd, $exifKey)
    {
      foreach($ifd->getTags() as $key => $tag)
      {
        if((self::filter($tag->getTag()->isKnown(), $tag->getTag()->isImplemented(), $this->options['filter'])) or
           ($tag->getTag()->getName()=='Exif IFD Pointer' or
            $tag->getTag()->getName()=='MakerNote' or
            $tag->getTag()->getName()=='GPS IFD Pointer'))
        {
          /*
           * only tag responding to the filter are selected
           * note the tags 'Exif IFD Pointer', 'MakerNote' & 'GPS IFD Pointer'
           * are not declared as implemented (otherwise they are visible with
           * the static 'getTagList' function) but must be selected even if
           * filter says "implemented only"
           */
          if($tag->getTag()->getLabel() instanceof IfdReader)
          {
            switch($tag->getTag()->getName())
            {
              case 'Exif IFD Pointer':
                $exifKey2=self::KEY_EXIF_EXIF;
                break;
              case 'MakerNote':
                $exifKey2=self::KEY_EXIF.".".$tag->getTag()->getLabel()->getMaker();
                break;
              case 'GPS IFD Pointer':
                $exifKey2=self::KEY_EXIF_GPS;
                break;
              default:
                $exifKey2=$exifKey;
                break;
            }
            $this->loadIfdTags($tag->getTag()->getLabel(), $exifKey2);
          }
          else
          {
            $this->tags[$exifKey.".".$tag->getTag()->getName()]=$tag->getTag();
          }
        }
      }
    }

    /**
     * Used to load tags from an IPTc or XMP structure
     *
     * see IptcReader.class.php and XmpReader.class.php
     *
     * @Param Tag[]  $ifd
     * @Param String $tagKey
     *
     */
    private function loadTags($tags, $tagKey)
    {
      foreach($tags as $key => $tag)
      {
        if(self::filter($tag->isKnown(), $tag->isImplemented(), $this->options['filter']))
        {
          $this->tags[$tagKey.".".$tag->getName()]=$tag;
        }
      }
    }

    /**
     * MagicTags are build with this function
     */
    private function processMagicTags()
    {
      $magicTags=new MagicTags();

      foreach($magicTags->getTags() as $key => $val)
      {
        $tag=new Tag($key,0,$key);

        for($i=0; $i<count($val['tagValues']); $i++)
        {
          $found=true;
          preg_match_all('/{([a-z0-9:\.\s\/]*)(\[.*\])?}/i', $val['tagValues'][$i], $returned, PREG_PATTERN_ORDER);
          foreach($returned[1] as $testKey)
          {
            $found=$found & array_key_exists($testKey, $this->tags);
          }
          if(count($returned[1])==0) $found=false;

          if($found)
          {
            $returned=trim(preg_replace_callback(
                '/{([a-z0-9:\.\s\/\[\]]*)}/i',
                Array(&$this, "processMagicTagsCB"),
                $val['tagValues'][$i]
            ));

            $tag->setValue($returned);
            $tag->setLabel($returned);
            $tag->setKnown(true);
            $tag->setImplemented($val['implemented']);
            $tag->setTranslatable($val['translatable']);

            $i=count($val['tagValues']);
          }
        }

        if($tag->isImplemented() and $found)
        {
          $this->tags["magic.".$key]=$tag;
        }

        unset($tag);
      }
      unset($magicTags);
    }

    /**
     * this function is called by the processMagicTags to replace tagId by the
     * tag values
     *
     * @param Array $matches : array[1] = the tagId
     * @return String : the tag value
     */
    private function processMagicTagsCB($matches)
    {
      $label="";
      preg_match_all('/([a-z0-9:\.\s\/]*)\[(.*)\]/i', $matches[1], $result, PREG_PATTERN_ORDER);
      if(count($result[0])>0)
      {

        if(array_key_exists($result[1][0], $this->tags))
        {
          $tag=$this->tags[$result[1][0]]->getLabel();

          preg_match_all('/([a-z0-9:\.\s\/]*)\[(.*)\]/i', $result[2][0], $result2, PREG_PATTERN_ORDER);

          if(count($result2[0])>0)
          {
            if(array_key_exists($result2[2][0], $tag[$result2[1][0]] ))
              $label=$tag[$result2[1][0]][$result2[2][0]];
          }
          else
          {
            if(array_key_exists($result[2][0], $tag))
              $label=$tag[$result[2][0]];
          }
        }
      }
      else
      {
        if(array_key_exists($matches[1], $this->tags))
        {
          $label=$this->tags[$matches[1]]->getLabel();
        }
      }

      if($label instanceof DateTime)
        return($label->format("Y-m-d H:i:s"));

      $label=XmpTags::getAltValue($label, L10n::getLanguage());

      if(is_array($label))
        return(implode(", ", $label));

      return(trim($label));
    }



    /**
     * used by the destructor to clean variables
     */
    private function unsetAll()
    {
      unset($this->tags);
      unset($this->jpeg);
      unset($this->options);
    }


  } // class JpegMetaData

?>

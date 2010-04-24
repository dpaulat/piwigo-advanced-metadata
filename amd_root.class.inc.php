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
 * See main.inc.php for release information
 *
 * AMD_install : classe to manage plugin install
 * ---------------------------------------------------------------------------
 */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/CommonPlugin.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCCss.class.inc.php');

include_once('amd_jpegmetadata.class.inc.php');
include_once(JPEG_METADATA_DIR."Common/L10n.class.php");
include_once(JPEG_METADATA_DIR."TagDefinitions/XmpTags.class.php");

class AMD_root extends CommonPlugin
{
  protected $css;   //the css object
  protected $jpegMD;

  public function __construct($prefixeTable, $filelocation)
  {
    global $user;
    $this->setPluginName("AMetaData");
    $this->setPluginNameFiles("amd");
    parent::__construct($prefixeTable, $filelocation);

    $tableList=array('used_tags', 'images_tags', 'images', 'selected_tags', 'groups_names', 'groups');
    $this->setTablesList($tableList);

    $this->css = new GPCCss(dirname($this->getFileLocation()).'/'.$this->getPluginNameFiles().".css");
    $this->jpegMD=new AMD_JpegMetaData();

    if(isset($user['language']))
    {
      L10n::setLanguage($user['language']);
    }
  }

  public function __destruct()
  {
    unset($this->jpegMD);
    unset($this->css);
    //parent::__destruct();
  }


  /* ---------------------------------------------------------------------------
  common AIP & PIP functions
  --------------------------------------------------------------------------- */

  /* this function initialize var $config with default values */
  public function initConfig()
  {
    $this->config=array(
      'amd_NumberOfItemsPerRequest' => 25,
      'amd_GetListTags_OrderType' => "tag",
      'amd_GetListTags_FilterType' => "magic",
      'amd_GetListTags_ExcludeUnusedTag' => "y",
      'amd_GetListTags_SelectedTagOnly' => "n",
      'amd_GetListImages_OrderType' => "value",
      'amd_FillDataBaseContinuously' => "y",
      'amd_AllPicturesAreAnalyzed' => "n",
    );
  }

  public function loadConfig()
  {
    parent::loadConfig();
  }

  public function initEvents()
  {
    parent::initEvents();


    if(!isset($_REQUEST['ajaxfct']) and
       $this->config['amd_FillDataBaseContinuously']=='y' and
       $this->config['amd_AllPicturesAreAnalyzed']=='n')
    {
      /* do analyze for a random picture only if :
       *  - config is set to fill database continuously
       *  - we are not in an ajax call
       */
      add_event_handler('init', array(&$this, 'doRandomAnalyze'));
    }
  }


  /**
   * returns the number of pictures analyzed
   *
   * @return Integer
   */
  protected function getNumOfPictures()
  {
    $numOfPictures=0;
    $sql="SELECT COUNT(imageId) FROM ".$this->tables['images']."
            WHERE analyzed='y';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_row($result))
      {
        $numOfPictures=$row[0];
      }
    }
    return($numOfPictures);
  }


  /**
   * this function randomly choose a picture in the list of pictures not
   * analyzed, and analyze it
   *
   */
  public function doRandomAnalyze()
  {
    $sql="SELECT tai.imageId, ti.path FROM ".$this->tables['images']." tai
            LEFT JOIN ".IMAGES_TABLE." ti ON tai.imageId = ti.id
          WHERE tai.analyzed = 'n'
          ORDER BY RAND() LIMIT 1;";
    $result=pwg_query($sql);
    if($result)
    {
      // $path = path of piwigo's on the server filesystem
      $path=dirname(dirname(dirname(__FILE__)));

      while($row=pwg_db_fetch_assoc($result))
      {
        $this->analyzeImageFile($path."/".$row['path'], $row['imageId']);
      }

      $this->makeStatsConsolidation();
    }
  }


  /**
   * this function analyze tags from a picture, and insert the result into the
   * database
   *
   * NOTE : only implemented tags are analyzed and stored
   *
   * @param String $fileName : filename of picture to analyze
   * @param Integer $imageId : id of image in piwigo's database
   * @param Boolean $loaded  : default = false
   *                            WARNING
   *                            if $loaded is set to TRUE, the function assume
   *                            that the metadata have been alreay loaded
   *                            do not use the TRUE value if you are not sure
   *                            of the consequences
   */
  protected function analyzeImageFile($fileName, $imageId, $loaded=false)
  {
    /*
     * the JpegMetaData object is instancied in the constructor
     */
    if(!$loaded)
    {
      $this->jpegMD->load(
        $fileName,
        Array(
          'filter' => AMD_JpegMetaData::TAGFILTER_IMPLEMENTED,
          'optimizeIptcDateTime' => true,
          'exif' => true,
          'iptc' => true,
          'xmp' => true
        )
      );
    }

    $sqlInsert="";
    $massInsert=array();
    $nbTags=0;
    foreach($this->jpegMD->getTags() as $key => $val)
    {
      $value=$val->getLabel();

      if($val->isTranslatable())
        $translatable="y";
      else
        $translatable="n";

      if($value instanceof DateTime)
      {
        $value=$value->format("Y-m-d H:i:s");
      }
      elseif(is_array($value))
      {
        /*
         * array values are stored in a serialized string
         */
        $value=serialize($value);
      }

      $sql="SELECT numId FROM ".$this->tables['used_tags']." WHERE tagId = '$key'";

      $result=pwg_query($sql);
      if($result)
      {
        $numId=-1;
        while($row=pwg_db_fetch_assoc($result))
        {
          $numId=$row['numId'];
        }

        if($numId>0)
        {
          $nbTags++;
          if($sqlInsert!="") $sqlInsert.=", ";
          $sqlInsert.="($imageId, '$numId', '".addslashes($value)."')";
          $massInsert[]="('$imageId', '$numId', '".addslashes($value)."') ";
        }
      }
    }

    if(count($massInsert)>0)
    {
      $sql="REPLACE INTO ".$this->tables['images_tags']." (imageId, numId, value) VALUES ".implode(", ", $massInsert).";";
      pwg_query($sql);
    }
    //mass_inserts($this->tables['images_tags'], array('imageId', 'numId', 'value'), $massInsert);

    $sql="UPDATE ".$this->tables['images']."
            SET analyzed = 'y', nbTags=".$nbTags."
            WHERE imageId=$imageId;";
    pwg_query($sql);


    return("$imageId=$nbTags;");
  }


  /**
   * do some consolidations on database to optimize other requests
   *
   */
  protected function makeStatsConsolidation()
  {
    $sql="UPDATE ".$this->tables['used_tags']." ut,
            (SELECT COUNT(imageId) AS nb, numId
              FROM ".$this->tables['images_tags']."
              GROUP BY numId) nb
          SET ut.numOfImg = nb.nb
          WHERE ut.numId = nb.numId;";
    pwg_query($sql);


    $sql="SELECT COUNT(imageId) AS nb
          FROM ".$this->tables['images']."
          WHERE analyzed = 'n';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $this->config['amd_AllPicturesAreAnalyzed']=($row['nb']==0)?'y':'n';
      }

    }
    $this->saveConfig();
  }


  /**
   * This function :
   *  - convert arrays (stored as a serialized string) into human readable string
   *  - translate value in user language (if value is translatable)
   *
   * @param String $value         : value to prepare
   * @param Boolean $translatable : set to tru if the value can be translated in
   *                                the user language
   * @param String $separator     : separator for arrays items
   * @return String               : the value prepared
   */
  protected function prepareValueForDisplay($value, $translatable=true, $separator=", ")
  {
    global $user;

    if(preg_match('/^a:\d+:\{.*\}$/is', $value))
    {
      // $value is a serialized array
      $tmp=unserialize($value);

      if(count($tmp)==0)
      {
        return(L10n::get("Unknown"));
      }

      if(array_key_exists("computed", $tmp) and array_key_exists("detail", $tmp))
      {
        /* keys 'computed' and 'detail' are present
         *
         * assume this is the 'exif.exif.Flash' metadata and return the computed
         * value only
         */
        return(L10n::get($tmp['computed']));
      }
      elseif(array_key_exists("type", $tmp) and array_key_exists("values", $tmp))
      {
        /* keys 'computed' and 'detail' are present
         *
         * assume this is an Xmp 'ALT', 'BAG' or 'SEQ' metadata and return the
         * values only
         */
        if($tmp['type']=='alt')
        {
          /* 'ALT' structure
           *
           * ==> assuming the structure is used only for multi language values
           *
           * Array(
           *    'type'   => 'ALT'
           *    'values' =>
           *        Array(
           *            Array(
           *                'type'  => Array(
           *                            'name'  =>'xml:lang',
           *                            'value' => ''           // language code
           *                           )
           *               'value' => ''         //value in the defined language
           *            ),
           *
           *            Array(
           *                // data2
           *            ),
           *
           *        )
           * )
           */
          $tmp=XmpTags::getAltValue($tmp, $user['language']);
          if(trim($tmp)=="") $tmp="(".L10n::get("not defined").")";

          return($tmp);
        }
        else
        {
          /* 'SEQ' or 'BAG' structure
           *
           *  Array(
           *    'type'   => 'XXX',
           *    'values' => Array(val1, val2, .., valN)
           *  )
           */
          $tmp=$tmp['values'];

          if(trim(implode("", $tmp))=="")
          {
            return("(".L10n::get("not defined").")");
          }
        }
      }


      foreach($tmp as $key=>$val)
      {
        if(is_array($val))
        {
          if($translatable)
          {
            foreach($val as $key2=>$val2)
            {
              $tmp[$key][$key2]=L10n::get($val2);
            }
            if(count($val)>0)
            {
              $tmp[$key]="[".implode($separator, $val)."]";
            }
            else
            {
              unset($tmp[$key]);
            }
          }
        }
        else
        {
          if($translatable)
          {
            $tmp[$key]=L10n::get($val);
          }
        }
      }
      return(implode($separator, $tmp));
    }
    elseif(preg_match('/\d{1,3}°\s\d{1,2}\'\s(\d{1,2}\.{0,1}\d{0,2}){0,1}.,\s(north|south|east|west)$/i', $value))
    {
      /* \d{1,3}°\s\d{1,2}\'\s(\d{1,2}\.{0,1}\d{0,2}){0,1}.
       *
       * keys 'coord' and 'card' are present
       *
       * assume this is a GPS coordinate
       */
        return(preg_replace(
          Array('/, north$/i', '/, south$/i', '/, east$/i', '/, west$/i'),
          Array(" ".L10n::get("North"), " ".L10n::get("South"), " ".L10n::get("East"), " ".L10n::get("West")),
          $value)
        );
    }
    else
    {
      if(trim($value)=="")
      {
        return("(".L10n::get("not defined").")");
      }

      if(strpos($value, "|")>0)
      {
        $value=explode("|", $value);
        if($translatable)
        {
          foreach($value as $key=>$val)
          {
            $value[$key]=L10n::get($val);
          }
        }
        return(implode("", $value));
      }

      if($translatable)
      {
        return(L10n::get($value));
      }
      return($value);
    }
  }

} // amd_root  class



?>

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

include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/css.class.inc.php');

include_once('JpegMetaData/JpegMetaData.class.php');
include_once(JPEG_METADATA_DIR."Common/L10n.class.php");

class AMD_root extends common_plugin
{
  protected $css;   //the css object
  protected $jpegMD;

  public function __construct($prefixeTable, $filelocation)
  {
    $this->plugin_name="AMetaData";
    $this->plugin_name_files="amd";
    parent::__construct($prefixeTable, $filelocation);

    $tableList=array('used_tags', 'images_tags', 'images', 'selected_tags', 'groups_names', 'groups');
    $this->set_tables_list($tableList);

    $this->css = new css(dirname($this->filelocation).'/'.$this->plugin_name_files.".css");
    $this->jpegMD=new JpegMetaData();
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

  /* this function initialize var $my_config with default values */
  public function init_config()
  {
    $this->my_config=array(
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

  public function load_config()
  {
    parent::load_config();
  }

  public function init_events()
  {
    parent::init_events();


    if(!isset($_REQUEST['ajaxfct']) and
       $this->my_config['amd_FillDataBaseContinuously']=='y' and
       $this->my_config['amd_AllPicturesAreAnalyzed']=='n')
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
      while($row=mysql_fetch_row($result))
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

      while($row=mysql_fetch_assoc($result))
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
          'filter' => JpegMetaData::TAGFILTER_IMPLEMENTED,
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
        while($row=mysql_fetch_assoc($result))
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
      while($row=mysql_fetch_assoc($result))
      {
        $this->my_config['amd_AllPicturesAreAnalyzed']=($row['nb']==0)?'y':'n';
      }

    }
    $this->save_config();
  }


} // amd_root  class



?>

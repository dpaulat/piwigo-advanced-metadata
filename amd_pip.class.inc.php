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
 * PIP classe => manage integration in public interface
 * -----------------------------------------------------------------------------
*/

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once('amd_root.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCAjax.class.inc.php');

class AMD_PIP extends AMD_root
{
  function AMD_PIP($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);

    $this->loadConfig();
    $this->initEvents();
  }


  /* ---------------------------------------------------------------------------
  Public classe functions
  --------------------------------------------------------------------------- */


  /*
    initialize events call for the plugin
  */
  public function initEvents()
  {
    parent::initEvents();
    add_event_handler('loc_begin_picture', array(&$this, 'loadMetadata'));
  }

  /**
   * override piwigo's metadata with picture metadata
   *
   */
  public function loadMetadata()
  {
    global $conf, $template, $page, $user;

    L10n::setLanguage($user['language']);

    $path=dirname(dirname(dirname(__FILE__)));
    $filename="";
    $analyzed='n';

    $sql="SELECT ti.path, tai.analyzed FROM ".IMAGES_TABLE." ti
            LEFT JOIN ".$this->tables['images']." tai ON tai.imageId = ti.id
          WHERE ti.id=".$page['image_id'].";";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $filename=$row['path'];
        $analyzed=$row['analyzed'];
      }
      $filename=$path."/".$filename;
    }

    $this->jpegMD->load(
      $filename,
      Array(
        'filter' => AMD_JpegMetaData::TAGFILTER_IMPLEMENTED,
        'optimizeIptcDateTime' => true,
        'exif' => true,
        'iptc' => true,
        'xmp' => true,
        'magic' => true,
      )
    );

    $conf['show_exif']=false;
    $conf['show_iptc']=false;

    $tagsList=Array();
    $sql="SELECT st.tagId, gn.name as gName
          FROM (".$this->tables['selected_tags']." st
            LEFT JOIN ".$this->tables['groups']." gr
              ON gr.groupId = st.groupId)
            LEFT JOIN ".$this->tables['groups_names']." gn
              ON st.groupId = gn.groupId
          WHERE gn.lang='".$user['language']."'
            AND st.groupId <> -1
          ORDER BY gr.order, st.order;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $tagsList[$row['tagId']]=$row['gName'];
      }
    }

    $metadata=Array();
    $md=null;
    $group=null;

    $picturesTags=$this->jpegMD->getTags();

    foreach($tagsList as $key => $val)
    {
      if(array_key_exists($key, $picturesTags))
      {
        $value=$picturesTags[$key]->getLabel();

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

        if($group!=$val)
        {
          $group=$val;
          if(!is_null($md))
          {
            $metadata[]=$md;
            unset($md);
          }
          $md=Array(
            'TITLE' => $val,
            'lines' => Array()
          );
        }
        $md['lines'][L10n::get($picturesTags[$key]->getName())]=$this->prepareValueForDisplay($value, $picturesTags[$key]->isTranslatable());
      }
    }

    if(!is_null($md))
    {
      $metadata[]=$md;
    }


    if($analyzed=='n' and
       $this->config['amd_FillDataBaseContinuously']=='y' and
       $this->config['amd_AllPicturesAreAnalyzed']=='n')
    {
      /* if picture is not analyzed, do analyze
       *
       * note : the $loaded parameter is set to true, in this case the function
       *        analyzeImageFile uses data from the $this->jpegMD object which
       *        have data already loaded => the picture is not analyzed twice,
       *        the function only do the database update
       */
      $this->analyzeImageFile($filename, $page['image_id'], true);
      $this->makeStatsConsolidation();
    }

    $template->assign('metadata', $metadata);
  }

} // AMD_PIP class


?>

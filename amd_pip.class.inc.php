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

class AMD_PIP extends AMD_root
{
  private $pictureProperties=array(
    'id' => 0,
    'analyzed' => 'n'
  );
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
    //parent::initEvents();
    add_event_handler('loc_begin_picture', array(&$this, 'loadMetadata'));
    add_event_handler('loc_end_page_tail', array(&$this, 'applyJS'));
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
    $this->pictureProperties['id']=$page['image_id'];

    $sql="SELECT ti.path, tai.analyzed FROM ".IMAGES_TABLE." ti
            LEFT JOIN ".$this->tables['images']." tai ON tai.imageId = ti.id
          WHERE ti.id=".$page['image_id'].";";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $filename=$row['path'];
        $this->pictureProperties['analyzed']=$row['analyzed'];
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

    trigger_notify('amd_jpegMD_loaded', $this->jpegMD);

    $conf['show_exif']=false;
    $conf['show_iptc']=false;

    $picturesTags=$this->jpegMD->getTags();
    $tagsList=Array();
    $userDefinedList=array(
      'list' => array(),
      'values' => array(),
    );
    $sql="(SELECT st.tagId, gn.name as gName, ut.numId, ut.name, 'y' AS displayStatus, st.order AS tOrder, gr.order as gOrder, gr.groupId
          FROM ((".$this->tables['selected_tags']." st
            LEFT JOIN ".$this->tables['groups']." gr
              ON gr.groupId = st.groupId)
            LEFT JOIN ".$this->tables['groups_names']." gn
              ON st.groupId = gn.groupId)
            LEFT JOIN ".$this->tables['used_tags']." ut
              ON ut.tagId = st.tagId
          WHERE gn.lang='".$user['language']."'
            AND st.groupId <> -1)

          UNION

          (SELECT DISTINCT ut3.tagId, '', pautd.value, '', 'n', -1, -1, -1
          FROM ((".$this->tables['selected_tags']." st2
            LEFT JOIN ".$this->tables['used_tags']." ut2 ON ut2.tagId = st2.tagId)
            LEFT JOIN ".$this->tables['user_tags_def']." pautd ON pautd.numId=ut2.numId)
            LEFT JOIN ".$this->tables['used_tags']." ut3 ON ut3.numId = pautd.value
          WHERE st2.tagId LIKE 'userDefined.%'
          AND pautd.type = 'M')

          ORDER BY gOrder, tOrder;";

    $result=pwg_query($sql);
    if($result)
    {
      $numMD=0;

      while($row=pwg_db_fetch_assoc($result))
      {
        if(trim($row['gName'])=='')
        {
          $sql2="SELECT gn0.name
                 FROM ".$this->tables['groups_names']." gn0
                 WHERE gn0.lang='en_UK' AND gn0.name <> '' AND groupId=".$row['groupId']."

                 UNION

                 SELECT gn0.name
                 FROM ".$this->tables['groups_names']." gn0
                 WHERE gn0.lang<>'".$user['language']."' AND gn0.name <> ''  AND groupId=".$row['groupId']."

                 LIMIT 0,1;";
          $result2=pwg_query($sql2);
          if($result2)
          {
            $row2=pwg_db_fetch_assoc($result2);
            if($row2['name']!='') $row['gName']=$row2['name'];
          }
        }

        if($row['gName']=='')
        {
          $row['gName']='Metadatas #'.$numMD;
          $numMD++;
        }

        $tagsList[$row['tagId']]=$row;
        if(preg_match('/^userDefined\./i', $row['tagId']))
        {
          $userDefinedList['list'][]=$row['numId'];
        }
        else
        {
          if(array_key_exists($row['tagId'], $picturesTags))
          {
            $value=$picturesTags[$row['tagId']]->getLabel();

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
            $userDefinedList['values'][$row['numId']]=AMD_root::prepareValueForDisplay($value, $picturesTags[$row['tagId']]->isTranslatable());;
          }
        }
      }
    }

    $metadata=$template->get_template_vars('metadata');
    $md=null;
    $group=null;

    $userDefinedValues=$this->pictureGetUserDefinedTags($userDefinedList['list'], $userDefinedList['values']);

    trigger_notify('amd_jpegMD_userDefinedValues_built',
      array(
        'picture' => $userDefinedList['values'],
        'user'    => $userDefinedValues,
      )
    );

    foreach($tagsList as $key => $tagProperties)
    {
      $keyExist=array_key_exists($key, $picturesTags) & ($tagProperties['displayStatus']=='y');
      $userDefined=preg_match('/^userDefined\./i', $key);

      if(($group!='AMD'.$tagProperties['groupId']) and
         ( $keyExist or $userDefined) )
      {
        $group='AMD'.$tagProperties['groupId'];
        if(!isset($metadata[$group]))
        {
          $metadata[$group]=Array(
                              'TITLE' => $tagProperties['gName'],
                              'lines' => Array()
                            );
        }
      }

      if($keyExist)
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

        if($value!="")
        {
          $metadata[$group]['lines'][L10n::get($picturesTags[$key]->getName())]=AMD_root::prepareValueForDisplay($value, $picturesTags[$key]->isTranslatable());
        }
      }
      elseif($userDefined and isset($userDefinedValues[$tagProperties['numId']]) and $userDefinedValues[$tagProperties['numId']]!='')
      {
        $metadata[$group]['lines'][$tagProperties['name']]=$userDefinedValues[$tagProperties['numId']];
      }
    }

    $template->assign('metadata', $metadata);
  }

  /**
   * used by the 'loc_end_page_tail' event
   *
   * on each public page viewed, add a script to do an ajax call to the "public.makeStats.doPictureAnalyze" function
   */
  public function applyJS()
  {
    global $template;

    if($this->config['amd_FillDataBaseContinuously']=='y' and
       $this->config['amd_AllPicturesAreAnalyzed']=='n')
    {
      $template->set_filename('applyJS',
                    dirname($this->getFileLocation()).'/templates/doAnalyze.tpl');

      $datas=array(
        'urlRequest' => $this->getAdminLink('ajax'),
        'id' => ($this->pictureProperties['analyzed']=='n')?$this->pictureProperties['id']:'0'
      );

      GPCCore::setTemplateToken();
      $template->assign('datas', $datas);
      $template->append('footer_elements', $template->parse('applyJS', true));
    }
  }

} // AMD_PIP class


?>

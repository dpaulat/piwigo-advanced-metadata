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
 * AIP classe => manage integration in administration interface
 *
 * -----------------------------------------------------------------------------
 */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once('amd_root.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCTabSheet.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/GPCAjax.class.inc.php');
include_once(PHPWG_PLUGINS_PATH.'GrumPluginClasses/classes/genericjs.class.inc.php');



class AMD_AIP extends AMD_root
{
  protected $tabsheet;

  /**
   *
   * constructor needs the prefix of piwigo's tables and the location of plugin
   *
   * @param String $prefixeTable
   * @param String $filelocation
   */
  public function __construct($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);

    $this->loadConfig();
    $this->initEvents();

    $this->tabsheet = new tabsheet();
    $this->tabsheet->add('metadata',
                          l10n('g003_metadata'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=metadata');
    $this->tabsheet->add('help',
                          l10n('g003_help'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=help');
  }

  public function __destruct()
  {
    unset($this->tabsheet);
    unset($this->ajax);
    parent::__destruct();
  }


  /*
   * ---------------------------------------------------------------------------
   * Public classe functions
   * ---------------------------------------------------------------------------
   */


  /**
   * manage the plugin integration into piwigo's admin interface
   */
  public function manage()
  {
    global $template, $page;

    $template->set_filename('plugin_admin_content', dirname(__FILE__)."/admin/amd_admin.tpl");

    $this->initRequest();

    $this->returnAjaxContent();

    $this->tabsheet->select($_REQUEST['fAMD_tabsheet']);
    $this->tabsheet->assign();
    $selected_tab=$this->tabsheet->get_selected();
    $template->assign($this->tabsheet->get_titlename(), "[".$selected_tab['caption']."]");

    $template_plugin["AMD_VERSION"] = "<i>".$this->getPluginName()."</i> ".l10n('g003_version').AMD_VERSION;
    $template_plugin["AMD_PAGE"] = $_REQUEST['fAMD_tabsheet'];
    $template_plugin["PATH"] = AMD_PATH;

    $template->assign('plugin', $template_plugin);

    if($_REQUEST['fAMD_tabsheet']=='help')
    {
      $this->displayHelp($_REQUEST['fAMD_page']);
    }
    elseif($_REQUEST['fAMD_tabsheet']=='metadata')
    {
      $this->displayMetaData($_REQUEST['fAMD_page']);
    }

    $template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');
  }

  /**
   * initialize events call for the plugin
   *
   * don't inherits from its parent => it's normal
   */
  public function initEvents()
  {
    add_event_handler('loc_end_page_header', array(&$this->css, 'applyCSS'));
    GPCCss::applyGpcCss();
  }

  /**
   * ---------------------------------------------------------------------------
   * Private & protected functions
   * ---------------------------------------------------------------------------
   */

  /**
   * manage the ajax requests
   * this function function determine if there is an ajax call, manage the
   * request and returns the content of the request
   *
   * no params are given, the function works with the "$_REQUEST" var
   *
   * @return String
   */
  protected function returnAjaxContent()
  {
    global $ajax, $template;

    if(isset($_REQUEST['ajaxfct']))
    {
      //$this->debug("AJAXFCT:".$_REQUEST['ajaxfct']);
      $result="<p class='errors'>".l10n('g002_error_invalid_ajax_call')."</p>";
      switch($_REQUEST['ajaxfct'])
      {
        case 'makeStatsGetList':
          $result=$this->ajax_amd_makeStatsGetList($_REQUEST['selectMode'], $_REQUEST['numOfItems']);
          break;
        case 'makeStatsDoAnalyze':
          $result=$this->ajax_amd_makeStatsDoAnalyze($_REQUEST['imagesList']);
          break;
        case 'makeStatsConsolidation':
          $result=$this->ajax_amd_makeStatsConsolidation();
          break;
        case 'makeStatsGetStatus':
          $result=$this->ajax_amd_makeStatsGetStatus();
          break;
        case 'showStatsGetListTags':
          $result=$this->ajax_amd_showStatsGetListTags($_REQUEST['orderType'], $_REQUEST['filterType'], $_REQUEST['excludeUnusedTag'], $_REQUEST['selectedTagOnly']);
          break;
        case 'showStatsGetListImages':
          $result=$this->ajax_amd_showStatsGetListImages($_REQUEST['tagId'], $_REQUEST['orderType']);
          break;
        case 'updateTagSelect':
          $result=$this->ajax_amd_updateTagSelect($_REQUEST['numId'], $_REQUEST['tagSelected']);
          break;
        case 'groupGetList':
          $result=$this->ajax_amd_groupGetList();
          break;
        case 'groupDelete':
          $result=$this->ajax_amd_groupDelete($_REQUEST['id']);
          break;
        case 'groupGetNames':
          $result=$this->ajax_amd_groupGetNames($_REQUEST['id']);
          break;
        case 'groupSetNames':
          $result=$this->ajax_amd_groupSetNames($_REQUEST['id'], $_REQUEST['listNames']);
          break;
        case 'groupSetOrder':
          $result=$this->ajax_amd_groupSetOrder($_REQUEST['listGroup']);
          break;
        case 'groupGetTagList':
          $result=$this->ajax_amd_groupGetTagList($_REQUEST['id']);
          break;
        case 'groupSetTagList':
          $result=$this->ajax_amd_groupSetTagList($_REQUEST['id'], $_REQUEST['listTag']);
          break;
        case 'groupGetOrderedTagList':
          $result=$this->ajax_amd_groupGetOrderedTagList($_REQUEST['id']);
          break;
        case 'groupSetOrderedTagList':
          $result=$this->ajax_amd_groupSetOrderedTagList($_REQUEST['id'], $_REQUEST['listTag']);
          break;
      }
      GPCAjax::returnResult($result);
    }
  }


  /**
   * if empty, initialize the $_REQUEST var
   *
   * if not empty, check validity for the request values
   *
   */
  private function initRequest()
  {
    //initialise $REQUEST values if not defined
    if($this->getNumOfPictures()==0)
    {
      $defautTabsheet="database";
    }
    else
    {
      $defautTabsheet="select";
    }

    if(!isset($_REQUEST['fAMD_tabsheet']))
    {
      $_REQUEST['fAMD_tabsheet']=$defautTabsheet;
    }

    if($_REQUEST['fAMD_tabsheet']!="metadata" and
       $_REQUEST['fAMD_tabsheet']!="help")
    {
      $_REQUEST['fAMD_tabsheet']="metadata";
    }

    if($_REQUEST['fAMD_tabsheet']=="metadata" and !isset($_REQUEST['fAMD_page']))
    {
      $_REQUEST['fAMD_page']=$defautTabsheet;
    }

    if($_REQUEST['fAMD_tabsheet']=="metadata" and
       !($_REQUEST['fAMD_page']=="select" or
         $_REQUEST['fAMD_page']=="database" or
         $_REQUEST['fAMD_page']=="display"))
    {
      $_REQUEST['fAMD_page']=$defautTabsheet;
    }


    if($_REQUEST['fAMD_tabsheet']=="help" and !isset($_REQUEST['fAMD_page']))
    {
      $_REQUEST['fAMD_page']="exif";
    }

    if($_REQUEST['fAMD_tabsheet']=="help" and
       !($_REQUEST['fAMD_page']=="exif" or
         $_REQUEST['fAMD_page']=="iptc" or
         $_REQUEST['fAMD_page']=="xmp" or
         $_REQUEST['fAMD_page']=="magic"))
    {
      $_REQUEST['fAMD_page']="exif";
    }


    /*
     * check ajax
     */
    if(isset($_REQUEST['ajaxfct']))
    {
      /*
       * check makeStatsGetList values
       */
      if($_REQUEST['ajaxfct']=="makeStatsGetList" and !isset($_REQUEST['selectMode']))
      {
        $_REQUEST['selectMode']="caddieAdd";
      }

      if($_REQUEST['ajaxfct']=="makeStatsGetList" and
         !($_REQUEST['selectMode']=="notAnalyzed" or
           $_REQUEST['selectMode']=="caddieAdd" or
           $_REQUEST['selectMode']=="caddieReplace" or
           $_REQUEST['selectMode']=="all"))
      {
        $_REQUEST['selectMode']="caddieAdd";
      }

      if($_REQUEST['ajaxfct']=="makeStatsGetList" and !isset($_REQUEST['numOfItems']))
      {
        $_REQUEST['numOfItems']=25;
      }

      /*
       * check makeStatsDoAnalyze values
       */
      if($_REQUEST['ajaxfct']=="makeStatsDoAnalyze" and !isset($_REQUEST['imagesList']))
      {
        $_REQUEST['imagesList']="";
      }

      /*
       * check makeStatsConsolidate values
       */
      if($_REQUEST['ajaxfct']=="makeStatsConsolidate" and !isset($_REQUEST['step']))
      {
        $_REQUEST['step']="*";
      }

      /*
       * check showStatsGetListTags values
       */
      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and !isset($_REQUEST['orderType']))
      {
        $_REQUEST['orderType']="tag";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and
         !($_REQUEST['orderType']=="tag" or
           $_REQUEST['orderType']=="label" or
           $_REQUEST['orderType']=="num"))
      {
        $_REQUEST['orderType']="tag";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and !isset($_REQUEST['filterType']))
      {
        $_REQUEST['filterType']="";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and
         !($_REQUEST['filterType']=="" or
           $_REQUEST['filterType']=="magic" or
           $_REQUEST['filterType']=="exif" or
           $_REQUEST['filterType']=="exif.Canon" or
           $_REQUEST['filterType']=="exif.Nikon" or
           $_REQUEST['filterType']=="exif.Pentax" or
           $_REQUEST['filterType']=="xmp" or
           $_REQUEST['filterType']=="iptc"))
      {
        $_REQUEST['filterType']="";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and !isset($_REQUEST['excludeUnusedTag']))
      {
        $_REQUEST['excludeUnusedTag']="n";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and
         !($_REQUEST['excludeUnusedTag']=="y" or
           $_REQUEST['excludeUnusedTag']=="n" ))
      {
        $_REQUEST['excludeUnusedTag']="n";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and !isset($_REQUEST['selectedTagOnly']))
      {
        $_REQUEST['selectedTagOnly']="n";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListTags" and
         !($_REQUEST['selectedTagOnly']=="y" or
           $_REQUEST['selectedTagOnly']=="n" ))
      {
        $_REQUEST['selectedTagOnly']="n";
      }


      /*
       * check showStatsGetListImagess values
       */
      if($_REQUEST['ajaxfct']=="showStatsGetListImages" and !isset($_REQUEST['orderType']))
      {
        $_REQUEST['orderType']="num";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListImages" and
         !($_REQUEST['orderType']=="value" or
           $_REQUEST['orderType']=="num"))
      {
        $_REQUEST['orderType']="num";
      }

      if($_REQUEST['ajaxfct']=="showStatsGetListImages" and !isset($_REQUEST['tagId']))
      {
        $_REQUEST['tagId']="*";
      }


      /*
       * check showStatsGetListImagess values
       */
      if($_REQUEST['ajaxfct']=="updateTagSelect" and !isset($_REQUEST['numId']))
      {
        $_REQUEST['numId']="";
      }

      if($_REQUEST['ajaxfct']=="updateTagSelect" and !isset($_REQUEST['tagSelected']))
      {
        $_REQUEST['tagSelected']="";
      }




      /*
       * check groupDelete values
       */
      if($_REQUEST['ajaxfct']=="groupDelete" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }



      /*
       * check groupSetOrder values
       */
      if($_REQUEST['ajaxfct']=="groupSetOrder" and !isset($_REQUEST['listGroup']))
      {
        $_REQUEST['listGroup']="";
      }

      /*
       * check groupGetNames values
       */
      if($_REQUEST['ajaxfct']=="groupGetNames" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }

      /*
       * check groupSetNames values
       */
      if($_REQUEST['ajaxfct']=="groupSetNames" and !isset($_REQUEST['listNames']))
      {
        $_REQUEST['listNames']="";
      }

      if($_REQUEST['ajaxfct']=="groupSetNames" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }


      /*
       * check groupGetTagList values
       */
      if($_REQUEST['ajaxfct']=="groupGetTagList" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }

      /*
       * check groupSetTagList values
       */
      if($_REQUEST['ajaxfct']=="groupSetTagList" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }

      if($_REQUEST['ajaxfct']=="groupSetTagList" and !isset($_REQUEST['listTag']))
      {
        $_REQUEST['listTag']="";
      }


      /*
       * check groupGetOrderedTagList values
       */
      if($_REQUEST['ajaxfct']=="groupGetOrderedTagList" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }

      /*
       * check groupSetOrderedTagList values
       */
      if($_REQUEST['ajaxfct']=="groupSetOrderedTagList" and !isset($_REQUEST['id']))
      {
        $_REQUEST['id']="";
      }

      if($_REQUEST['ajaxfct']=="groupSetOrderedTagList" and !isset($_REQUEST['listTag']))
      {
        $_REQUEST['listTag']="";
      }

    }
  } //init_request


  /**
   * manage adviser profile
   *
   * @return Boolean : true if user is adviser, otherwise false (and push a
   *                   message in the error list)
   */
  protected function adviser_abort()
  {
    if(is_adviser())
    {
      $this->display_result(l10n("g003_adviser_not_allowed"), false);
      return(true);
    }
    return(false);
  }


  /**
   * display and manage the metadata page
   * the page have three tabsheet :
   *  - select tag management, to manage tags to be selected on the galerie
   *  - display tag management, to choose how the tags are displayed
   *  - manage database
   *
   * @param String $tab : the selected tab on the stat page
   */
  protected function displayMetaData($tab)
  {
    global $template, $user;
    $template->set_filename('body_page', dirname(__FILE__).'/admin/amd_metadata.tpl');

    $statTabsheet = new GPCTabSheet('statTabsheet', $this->tabsheet->get_titlename(), 'tabsheet2 gcBorder', 'itab2');
    $statTabsheet->select($tab);
    $statTabsheet->add('database',
                          l10n('g003_database'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=metadata&amp;fAMD_page=database');
    $statTabsheet->add('select',
                          l10n('g003_select'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=metadata&amp;fAMD_page=select');
    $statTabsheet->add('display',
                          l10n('g003_display'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=metadata&amp;fAMD_page=display');
    $statTabsheet->assign();



    if($tab=="select")
    {
      $template->assign('sheetContent', $this->displayMetaDataSelect());
    }
    elseif($tab=="display")
    {
      $template->assign('sheetContent', $this->displayMetaDataDisplay());
    }
    else
    {
      $template->assign('sheetContent', $this->displayMetaDataDatabase());
    }

    $template->assign_var_from_handle('AMD_BODY_PAGE', 'body_page');
  }

  /**
   * display and manage the metadata page allowing to make tags selection
   *
   * @return String : the content of the page
   */
  protected function displayMetaDataSelect()
  {
    global $template, $theme, $themes, $themeconf;
    /*echo "A".print_r($theme, true)."<br>";
    echo "B".print_r($themes, true)."<br>";
    echo "C".print_r($themeconf, true)."<br>";
    echo "D".print_r($template->smarty->[], true)."<br>";*/

    $template->set_filename('sheet_page',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_select.tpl');

    $datas=array(
      'urlRequest' => $this->getAdminLink(),
      'config_GetListTags_OrderType' => $this->config['amd_GetListTags_OrderType'],
      'config_GetListTags_FilterType' => $this->config['amd_GetListTags_FilterType'],
      'config_GetListTags_ExcludeUnusedTag' => $this->config['amd_GetListTags_ExcludeUnusedTag'],
      'config_GetListTags_SelectedTagOnly' => $this->config['amd_GetListTags_SelectedTagOnly'],
      'config_GetListImages_OrderType' => $this->config['amd_GetListImages_OrderType']
    );


    $template->assign('datas', $datas);

    return($template->parse('sheet_page', true));
  }


  /**
   * display and manage the metadata page allowing to choose tags order
   *
   * @return String : the content of the page
   */
  protected function displayMetaDataDisplay()
  {
    global $user, $template;

    //$local_tpl = new Template(AMD_PATH."admin/", "");
    $template->set_filename('sheet_page',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_display.tpl');


    $datas=array(
      'urlRequest' => $this->getAdminLink(),
      'selectedTags' => Array(),
      'groups' => Array(),
      'tagByGroup' => Array(),
    );

    $sql="SELECT st.tagId, st.order, st.groupId, ut.numId
          FROM ".$this->tables['selected_tags']." st
            LEFT JOIN ".$this->tables['used_tags']." ut
              ON ut.tagId = st.tagId
          ORDER BY st.groupId ASC, st.order ASC, st.tagId ASC";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        if($row['groupId']==-1)
        {
          $datas['selectedTags'][]=Array(
            'numId' => $row['numId'],
            'tagId' => $row['tagId']
          );
        }
        else
        {
          $datas['tagByGroup'][]=Array(
            'numId' => $row['numId'],
            'tagId' => $row['tagId'],
            'group' => $row['groupId'],
            'order' => $row['order']
          );
        }
      }
    }

    $sql="SELECT g.groupId, gn.name
          FROM ".$this->tables['groups']." g
            LEFT JOIN ".$this->tables['groups_names']." gn
              ON g.groupId = gn.groupId
          WHERE gn.lang = '".$user['language']."'
          ORDER BY g.order;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $datas['groups'][]=Array(
          'id' => $row['groupId'],
          'name' => $row['name']
        );
      }
    }

    $template->assign('datas', $datas);
    return($template->parse('sheet_page', true));
  }


  /**
   * display and manage the database page
   *
   * the function automatically update the AMD tables :
   *  - add new pictures in the AMD image table (assuming image is not analyzed
   *    yet)
   *  - remove deleted pictures in the AMD image & image_tags table
   *
   * @return String : the content of the page
   */
  private function displayMetaDataDatabase()
  {
    global $template, $page;

    /*
     * insert new image (from piwigo images table) in the AMD images table, with
     * statut 'not analyzed'
     */
    $sql="INSERT INTO ".$this->tables['images']."
            SELECT id, 'n', 0
              FROM ".IMAGES_TABLE."
              WHERE id NOT IN (SELECT imageId FROM ".$this->tables['images'].")";
    pwg_query($sql);


    /*
     * delete image who are in the AMD images table and not in the piwigo image
     * table
     */
    $sql="DELETE FROM ".$this->tables['images']."
            WHERE imageId NOT IN (SELECT id FROM ".IMAGES_TABLE.")";
    pwg_query($sql);


    /*
     * delete metdata for images that are not in the AMD image table
     */
    $sql="DELETE FROM ".$this->tables['images_tags']."
            WHERE imageId NOT IN (SELECT imageId FROM ".$this->tables['images'].")";
    pwg_query($sql);


    $template->set_filename('sheet_page', dirname(__FILE__).'/admin/amd_metadata_database.tpl');

    $datas=array(
      'urlRequest' => $this->getAdminLink(),
      'NumberOfItemsPerRequest' => $this->config['amd_NumberOfItemsPerRequest'],
    );

    $template->assign("datas", $datas);

    return($template->parse('sheet_page', true));
  } // displayDatabase





  /**
   * display and manage the help page
   *
   * @param String $tab : the selected tab on the help page
   */
  protected function displayHelp($tab)
  {
    global $template, $user, $lang;
    $template->set_filename('body_page', dirname(__FILE__).'/admin/amd_help.tpl');

    $statTabsheet = new GPCTabSheet('statTabsheet', $this->tabsheet->get_titlename(), 'tabsheet2 gcBorder', 'itab2');
    $statTabsheet->select($tab);
    $statTabsheet->add('exif',
                          l10n('g003_help_tab_exif'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=help&amp;fAMD_page=exif');
    $statTabsheet->add('iptc',
                          l10n('g003_help_tab_iptc'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=help&amp;fAMD_page=iptc');
    $statTabsheet->add('xmp',
                          l10n('g003_help_tab_xmp'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=help&amp;fAMD_page=xmp');
    $statTabsheet->add('magic',
                          l10n('g003_help_tab_magic'),
                          $this->getAdminLink().'&amp;fAMD_tabsheet=help&amp;fAMD_page=magic');
    $statTabsheet->assign();

    $data=Array(
      'sheetContent' => GPCCore::BBtoHTML($lang['g003_help_'.$tab]),
      'title' => l10n('g003_help_tab_'.$tab),
    );

    $template->assign('data', $data);

    $template->assign_var_from_handle('AMD_BODY_PAGE', 'body_page');
  }


  /*
   * ---------------------------------------------------------------------------
   * ajax functions
   * ---------------------------------------------------------------------------
   */

  /**
   * return a list of picture Id
   *
   * picture id are separated with a space " "
   * picture id are grouped in blocks of 'amd_NumberOfItemsPerRequest' items and
   * are separated with a semi-colon ";"
   *
   * client side just have to split blocks, and transmit it to the server
   *
   * There is two mode to determine the pictures being analyzed :
   *  - "all"         : analyze all the images
   *  - "notAnalyzed" : analyze only the images not yet analyzed
   *
   * @param String $mode
   * @param Integer $nbOfItems : number of items per request
   * @return String : list of image id to be analyzed, separated with a space
   *                      "23 78 4523 5670"
   */
  private function ajax_amd_makeStatsGetList($mode, $nbOfItems)
  {
    global $user;

    $returned="";
    $this->config['amd_NumberOfItemsPerRequest']=$nbOfItems;
    $this->saveConfig();

    $sql="SELECT ait.imageId FROM ".$this->tables['images']." ait";
    if($mode=="notAnalyzed")
    {
      $sql.=" WHERE ait.analyzed='n'";
    }
    elseif($mode=="caddieAdd" or $mode=="caddieReplace")
    {

      $sql.=" LEFT JOIN ".CADDIE_TABLE." ct ON ait.imageId = ct.element_id
            WHERE ct.user_id = ".$user['id']." ";

      if($mode=="caddieAdd") $sql.=" AND ait.analyzed='n'";
    }

    if($mode=="all" or $mode=="caddieReplace")
    {
      pwg_query("UPDATE ".$this->tables['images']." SET analyzed='n', nbTags=0");
      pwg_query("UPDATE ".$this->tables['used_tags']." SET numOfImg=0");
      pwg_query("DELETE FROM ".$this->tables['images_tags']);
    }

    $result=pwg_query($sql);
    if($result)
    {
      $i=0;
      while($row=pwg_db_fetch_row($result))
      {
        $returned.=$row[0];
        $i++;
        if($i>=$nbOfItems)
        {
          $returned.=";";
          $i=0;
        }
        else
        {
          $returned.=" ";
        }
      }
    }
    return(trim($returned).";");
  }


  /**
   * extract metadata from images
   *
   * @param String $imageList : list of image id to be analyzed, separated with
   *                            a space
   *                                "23 78 4523 5670"
   * @return String : list of the analyzed pictures, with number of tags found
   *                  for each picture
   *                    "23=0;78=66;4523=33;5670=91;"
   */
  private function ajax_amd_makeStatsDoAnalyze($imagesList)
  {
    $list=explode(" ", trim($imagesList));

    $returned="";

    if(count($list)>0 and trim($imagesList)!='')
    {
      // $path = path of piwigo's on the server filesystem
      $path=dirname(dirname(dirname(__FILE__)));

      $sql="SELECT id, path FROM ".IMAGES_TABLE." WHERE id IN (".implode(", ", $list).")";
      $result=pwg_query($sql);
      if($result)
      {
        while($row=pwg_db_fetch_assoc($result))
        {
          /*
           * in some case (in a combination of some pictures), when there is too
           * much pictures to analyze in the same request, a fatal error occurs
           * with the message : "Allowed memory size of XXXXX bytes exhausted"
           *
           *
           * tracking memory leak is not easy... :-(
           *
           */
          //echo "analyzing:".$row['id']."\n";
          //$mem1=memory_get_usage();
          //echo "memory before analyze:".$mem1."\n";
          $returned.=$this->analyzeImageFile($path."/".$row['path'], $row['id']);
          //echo $returned."\n";
          //$mem2=memory_get_usage();
          //echo "memory after analyze:".$mem2." (".($mem2-$mem1).")\n";
        }
      }
    }
    return($returned);
  }

  /**
   * do some consolidation on database to optimize other requests
   *
   */
  private function ajax_amd_makeStatsConsolidation()
  {
    $this->makeStatsConsolidation();
  }

  /**
   * returns a list of formated string, separated with a semi-colon :
   *  - number of current analyzed pictures + number of current analyzed tags
   *    for the analyzed pictures
   *  - number of pictures not analyzed
   *  - number of pictures without tag
   *
   * @return String
   */
  private function ajax_amd_makeStatsGetStatus()
  {
    $numOfMetaData=0;
    $numOfPictures=0;
    $numOfPicturesNotAnalyzed=0;

    $sql="SELECT COUNT(imageId), SUM(nbTags) FROM ".$this->tables['images']."
            WHERE analyzed='y';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_row($result))
      {
        $numOfPictures=$row[0];
        $numOfMetaData=$row[1];
      }
    }


    $sql="SELECT COUNT(imageId) FROM ".$this->tables['images']."
            WHERE analyzed='n';";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_row($result))
      {
        $numOfPicturesNotAnalyzed=$row[0];
      }
    }

    $sql="SELECT COUNT(imageId) FROM ".$this->tables['images']."
            WHERE nbTags=0;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_row($result))
      {
        $numOfPicturesWithoutTags=$row[0];
      }
    }

    return(sprintf(l10n("g003_numberOfAnalyzedPictures"), $numOfPictures, $numOfMetaData).";".
              sprintf(l10n("g003_numberOfNotAnalyzedPictures"), $numOfPicturesNotAnalyzed).";".
              sprintf(l10n("g003_numberOfPicturesWithoutTags"), $numOfPicturesWithoutTags));
  }


  /**
   * return a formatted <table> (using the template "amd_stat_show_iListTags")
   * of used tag with, for each tag, the number and the percentage of pictures
   * where the tag was found
   *
   * @param String $orderType : order for the list (by tag 'tag' or by number of
   *                            pictures 'num')
   * @param String $filterType : filter for the list ('exif', 'xmp', 'iptc' or '')
   * @return String
   */
  private function ajax_amd_showStatsGetListTags($orderType, $filterType, $excludeUnusedTag, $selectedTagOnly)
  {
    global $template;

    $this->config['amd_GetListTags_OrderType'] = $orderType;
    $this->config['amd_GetListTags_FilterType'] = $filterType;
    $this->config['amd_GetListTags_ExcludeUnusedTag'] = $excludeUnusedTag;
    $this->config['amd_GetListTags_SelectedTagOnly'] = $selectedTagOnly;
    $this->saveConfig();

    $local_tpl = new Template(AMD_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_select_iListTags.tpl');

    $numOfPictures=$this->getNumOfPictures();

    $datas=array();
    $sql="SELECT ut.numId, ut.tagId, ut.translatable, ut.name, ut.numOfImg, if(st.tagId IS NULL, 'n', 'y') as checked, ut.translatedName
            FROM ".$this->tables['used_tags']." ut
              LEFT JOIN ".$this->tables['selected_tags']." st
                ON st.tagId = ut.tagId ";
    $where="";

    if($filterType!='')
    {
      if($filterType=='exif')
      {
        $where.=" WHERE ut.tagId LIKE 'exif.tiff.%'
                    OR ut.tagId LIKE 'exif.exif.%'
                    OR ut.tagId LIKE 'exif.gps.%'  ";
      }
      else
      {
        $where.=" WHERE ut.tagId LIKE '".$filterType.".%' ";
      }
    }

    if($excludeUnusedTag=='y')
    {
      ($where=="")?$where=" WHERE ":$where.=" AND ";
      $where.=" ut.numOfImg > 0 ";
    }

    if($selectedTagOnly=='y')
    {
      ($where=="")?$where=" WHERE ":$where.=" AND ";
      $where.=" st.tagId IS NOT NULL ";
    }

    $sql.=$where;

    switch($orderType)
    {
      case 'tag':
        $sql.=" ORDER BY tagId ASC";
        break;
      case 'num':
        $sql.=" ORDER BY numOfImg DESC, tagId ASC";
        break;
      case 'label':
        $sql.=" ORDER BY translatedName ASC, tagId ASC";
        break;
    }

    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $datas[]=array(
          "numId" => $row['numId'],
          "tagId" => $row['tagId'],
          "label" => L10n::get($row['name']),
          "nb"    => $row['numOfImg'],
          "pct"   => ($numOfPictures!=0)?sprintf("%.2f", 100*$row['numOfImg']/$numOfPictures):"0",
          "tagChecked" => ($row['checked']=='y')?"checked":""
        );
      }
    }

    $local_tpl->assign('themeconf', Array('name' => $template->get_themeconf('name')));
    $local_tpl->assign('datas', $datas);

    return($local_tpl->parse('body_page', true));
  }


  /*
   *
   *
   */
  private function ajax_amd_showStatsGetListImages($tagId, $orderType)
  {
    global $template;

    $this->config['amd_GetListImages_OrderType'] = $orderType;
    $this->saveConfig();

    $local_tpl = new Template(AMD_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_select_iListImages.tpl');



    $datas=array();
    $sql="SELECT ut.translatable, ut.numOfImg, COUNT(it.imageId) AS Nb, it.value
            FROM ".$this->tables['used_tags']." ut
              LEFT JOIN ".$this->tables['images_tags']." it
              ON ut.numId = it.numId
            WHERE ut.tagId = '".$tagId."'
              AND it.value IS NOT NULL
            GROUP BY it.value
            ORDER BY ";
    switch($orderType)
    {
      case 'value':
        $sql.="it.value ASC";
        break;
      case 'num':
        $sql.="Nb DESC";
        break;
    }

    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $datas[]=array(
          "value" => $this->prepareValueForDisplay($row['value'], ($row['translatable']=='y'), ", "),
          "nb"    => $row['Nb'],
          "pct"   => ($row['numOfImg']!=0)?sprintf("%.2f", 100*$row['Nb']/$row['numOfImg']):"-"
        );
      }
    }

    if(count($datas)>0)
    {
      $local_tpl->assign('themeconf', Array('name' => $template->get_themeconf('name')));
      $local_tpl->assign('datas', $datas);
      return($local_tpl->parse('body_page', true));
    }
    else
    {
      return("<div style='width:100%;text-align:center;padding-top:20px;'>".l10n('g003_selected_tag_isnot_linked_with_any_picture')."</div>");
    }
  }


  /*
   *
   *
   */
  private function ajax_amd_updateTagSelect($numId, $selected)
  {
    if($selected=='y')
    {
      $sql="SELECT ut.tagId FROM ".$this->tables['selected_tags']." st
              LEFT JOIN ".$this->tables['used_tags']." ut
                ON ut.tagID = st.tagId
              WHERE ut.numId = $numId;";
      $result=pwg_query($sql);
      if($result)
      {
        if(pwg_db_num_rows($result)==0)
        {
          $sql="INSERT INTO ".$this->tables['selected_tags']."
                  SELECT ut.tagId, 0, -1
                  FROM ".$this->tables['used_tags']." ut
                    LEFT JOIN ".$this->tables['selected_tags']." st
                      ON ut.tagID = st.tagId
                  WHERE ut.numId = $numId;";
          pwg_query($sql);
        }
      }
    }
    elseif($selected=='n')
    {
      $sql="DELETE FROM ".$this->tables['selected_tags']." st
              USING ".$this->tables['used_tags']." ut
                LEFT JOIN ".$this->tables['selected_tags']." st
                  ON ut.tagID = st.tagId
              WHERE ut.numId = $numId;";
      pwg_query($sql);
    }

  }


  /**
   * this function return the list of tags :
   *  - associated with the group
   *  - not associated with a group
   * the list is used to make tags selection
   *
   * @param String $id      : Id of the current group
   * @return String : an HTML formatted list with checkbox
   */
  private function ajax_amd_groupGetTagList($id)
  {
    global $template;

    if($id!="")
    {
      $sql="SELECT st.tagId, st.groupId, ut.name, ut.numId
            FROM ".$this->tables['selected_tags']." st
              LEFT JOIN ".$this->tables['used_tags']." ut
                ON st.tagId = ut.tagId
            ORDER BY tagId";
      $result=pwg_query($sql);
      if($result)
      {
        $datas=Array();
        while($row=pwg_db_fetch_assoc($result))
        {
          if($row['groupId']==$id)
          {
            $state="checked";
          }
          elseif($row['groupId']==-1)
          {
            $state="";
          }
          else
          {
            $state="n/a";
          }

          if($state!="n/a")
            $datas[]=Array(
              'tagId' => $row['tagId'],
              'name'  => L10n::get($row['name']),
              'state' => $state,
              'numId' => $row['numId']
            );
        }

        if(count($datas)>0)
        {
          $local_tpl = new Template(AMD_PATH."admin/", "");
          $local_tpl->set_filename('body_page',
                        dirname($this->getFileLocation()).'/admin/amd_metadata_display_groupListTagSelect.tpl');
          $local_tpl->assign('themeconf', Array('name' => $template->get_themeconf('name')));
          $local_tpl->assign('datas', $datas);
          return($local_tpl->parse('body_page', true));
        }
        else
        {
          return(l10n("g003_no_tag_can_be_selected"));
        }
      }
    }
    else
    {
      return(l10n("g003_invalid_group_id"));
    }
  }


  /**
   * this function associate tags to a group
   *
   * @param String $id      : Id of group
   * @param String $listTag : list of selected tags, items are separated by a
   *                          semi-colon ";" char
   */
  private function ajax_amd_groupSetTagList($id, $listTag)
  {
    if($id!="")
    {
      $sql="UPDATE ".$this->tables['selected_tags']."
            SET groupId = -1
            WHERE groupId = $id;";
      pwg_query($sql);

      if($listTag!="")
      {
        $sql="UPDATE ".$this->tables['selected_tags']." st, ".$this->tables['used_tags']." ut
              SET st.groupId = $id
              WHERE st.tagId = ut.tagId
                AND ut.numId IN ($listTag);";
        pwg_query($sql);
      }
    }
    else
    {
      return("KO");
    }
  }


  /**
   * this function returns an ordered list of tags associated with a group
   *
   * @param String $id        : the group Id
   * @return String : an HTML formatted list
   */
  private function ajax_amd_groupGetOrderedTagList($id)
  {
    global $template;
    if($id!="")
    {
      $numOfPictures=$this->getNumOfPictures();

      $sql="SELECT st.tagId, ut.name, ut.numId, ut.numOfImg
            FROM ".$this->tables['selected_tags']." st
              LEFT JOIN ".$this->tables['used_tags']." ut
                ON st.tagId = ut.tagId
            WHERE st.groupId = $id
            ORDER BY st.order ASC, st.tagId ASC";
      $result=pwg_query($sql);
      if($result)
      {
        $datas=Array();
        while($row=pwg_db_fetch_assoc($result))
        {
          $datas[]=Array(
            'tagId' => $row['tagId'],
            'name'  => L10n::get($row['name']),
            'numId' => $row['numId'],
            'nbItems' => $row['numOfImg'],
            'pct'   => ($numOfPictures==0)?"0":sprintf("%.2f", 100*$row['numOfImg']/$numOfPictures)
          );
        }

        if(count($datas)>0)
        {
          $template->set_filename('list_page',
                        dirname($this->getFileLocation()).'/admin/amd_metadata_display_groupListTagOrder.tpl');
          $template->assign('datas', $datas);
          $template->assign('group', $id);
          return($template->parse('list_page', true));
        }
        else
        {
          return(l10n("g003_no_tag_can_be_selected"));
        }
      }
    }
    else
    {
      return(l10n("g003_invalid_group_id"));
    }
  }


  /**
   * this function update the tags order inside a group
   *
   * @param String $id        : the group Id
   * @param String $listGroup : the ordered list of tags, items are separated
   *                            by a semi-colon ";" char
   */
  private function ajax_amd_groupSetOrderedTagList($id, $listTag)
  {
    $tags=explode(';', $listTag);
    if($id!="" and count($tags)>0)
    {
      /*
       * by default, all items are set with order equals -1 (if list is not
       * complete, forgotten items are sorted in head)
       */
      pwg_query("UPDATE ".$this->tables['selected_tags']." st
                  SET st.order = -1
                  WHERE st.groupId = $id;");

      foreach($tags as $key=>$val)
      {
        $sql="UPDATE ".$this->tables['selected_tags']." st, ".$this->tables['used_tags']." ut
              SET st.order = $key
              WHERE st.groupId = $id
                AND st.tagId = ut.tagId
                AND ut.numId = $val;";
        $result=pwg_query($sql);
      }
    }
  }



  /**
   * this function update the groups order
   *
   * @param String $listGroup : the ordered list of groups, items are separated
   *                            by a semi-colon ";" char
   */
  private function ajax_amd_groupSetOrder($listGroup)
  {
    $groups=explode(";",$listGroup);
    if(count($groups)>0)
    {
      /*
       * by default, all items are set with order equals -1 (if list is not
       * complete, forgotten items are sorted in head)
       */
      pwg_query("UPDATE ".$this->tables['groups']." g SET g.order = -1;");

      foreach($groups as $key=>$val)
      {
        $sql="UPDATE ".$this->tables['groups']." g
              SET g.order = $key
              WHERE g.groupId = $val;";
        $result=pwg_query($sql);
      }
    }
  }

  /**
   * this function is used to create a new group ($groupId = "") or update the
   * group name (names are given in all langs in a list)
   *
   * @param String $groupId : the groupId to update, or "" to create a new groupId
   * @param String $listNames : name of the group, in all language given as a
   *                            list ; each lang is separated by a carraige
   *                            return "\n" char, each items is defined as
   *                            lang=value
   *                              en_UK=the name group
   *                              fr_FR=le nom du groupe
   */
  private function ajax_amd_groupSetNames($groupId, $listNames)
  {
    $names=explode("\n", $listNames);
    if($groupId=="" and count($names)>0)
    {
      $sql="INSERT INTO ".$this->tables['groups']." VALUES('', 9999)";
      $result=pwg_query($sql);
      $groupId=pwg_db_insert_id();
    }

    if(is_numeric($groupId) and count($names)>0)
    {
      $sql="DELETE FROM ".$this->tables['groups_names']."
            WHERE groupId = $groupId;";
      pwg_query($sql);


      $sql="";
      foreach($names as $val)
      {
        $tmp=explode("=", $val);
        if($sql!="") $sql.=", ";
        $sql.=" ($groupId, '".$tmp[0]."', '".$tmp[1]."')";
      }
      $sql="INSERT INTO ".$this->tables['groups_names']." VALUES ".$sql;
      pwg_query($sql);
    }
  }

  /**
   * this function returns an html form, allowing to manage the group
   *
   * @param String $groupId : the groupId to manage, or "" to return a creation
   *                          form
   * @return String : the form
   */
  private function ajax_amd_groupGetNames($groupId)
  {
    global $user;

    $local_tpl = new Template(AMD_PATH."admin/", "");
    $local_tpl->set_filename('body_page',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_display_groupEdit.tpl');

    $datasLang=array(
      'language_list' => Array(),
      'lang_selected' => $user['language'],
      'fromlang' => substr($user['language'],0,2),
      'default' => ''
    );

    $langs=get_languages();
    foreach($langs as $key => $val)
    {
      $datasLang['language_list'][$key] = Array(
        'langName' => str_replace("\n", "", $val),
        'name' => ""
      );
    }

    if($groupId!="")
    {
      $sql="SELECT lang, name FROM ".$this->tables['groups_names']."
            WHERE groupId = $groupId;";
      $result=pwg_query($sql);
      if($result)
      {
        while($row=pwg_db_fetch_assoc($result))
        {
          if(array_key_exists($row['lang'], $datasLang['language_list']))
          {
            $datasLang['language_list'][$row['lang']]['name']=htmlentities($row['name'], ENT_QUOTES, 'UTF-8');
            if($user['language']==$row['lang'])
            {
              $datasLang['default']=$datasLang['language_list'][$row['lang']]['name'];
            }
          }
        }
      }
    }

    $local_tpl->assign('datasLang', $datasLang);

    return($local_tpl->parse('body_page', true));
  }


  /**
   * this function returns an html form, allowing to manage the group
   *
   * @param String $groupId : the groupId to manage, or "" to return a creation
   *                          form
   * @return String : the form
   */
  private function ajax_amd_groupGetList()
  {
    global $user, $template;

    //$local_tpl = new Template(AMD_PATH."admin/", "");
    $template->set_filename('group_list',
                  dirname($this->getFileLocation()).'/admin/amd_metadata_display_groupList.tpl');


    $datas=array(
      'groups' => Array(),
    );

    $sql="SELECT g.groupId, gn.name
          FROM ".$this->tables['groups']." g
            LEFT JOIN ".$this->tables['groups_names']." gn
              ON g.groupId = gn.groupId
          WHERE gn.lang = '".$user['language']."'
          ORDER BY g.order;";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=pwg_db_fetch_assoc($result))
      {
        $datas['groups'][]=Array(
          'id' => $row['groupId'],
          'name' => htmlentities($row['name'], ENT_QUOTES, "UTF-8")
        );
      }
    }

    $template->assign('datas', $datas);
    return($template->parse('group_list', true));
  }


  /**
   * delete the group
   * associated tag returns in the available tag list
   *
   * @param String $groupId : the groupId to delete
   */
  private function ajax_amd_groupDelete($groupId)
  {
    if($groupId!="")
    {
      $sql="DELETE FROM ".$this->tables['groups']."
            WHERE groupId = $groupId;";
      pwg_query($sql);

      $sql="DELETE FROM ".$this->tables['groups_names']."
            WHERE groupId = $groupId;";
      pwg_query($sql);

      $sql="UPDATE ".$this->tables['selected_tags']."
            SET groupId = -1
            WHERE groupId = $groupId;";
      pwg_query($sql);
    }
  }

} // AMD_AIP class


?>

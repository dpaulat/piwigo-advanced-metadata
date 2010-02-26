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

  function AMD_root($prefixeTable, $filelocation)
  {
    $this->plugin_name="AMetaData";
    $this->plugin_name_files="amd";
    parent::__construct($prefixeTable, $filelocation);

    $tableList=array('used_tags', 'images_tags', 'images', 'selected_tags', 'groups_names', 'groups');
    $this->set_tables_list($tableList);

    $this->css = new css(dirname($this->filelocation).'/'.$this->plugin_name_files.".css");
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
      'amd_GetListTags_FilterType' => "",
      'amd_GetListTags_ExcludeUnusedTag' => "n",
      'amd_GetListTags_SelectedTagOnly' => "n",
      'amd_GetListImages_OrderType' => "value"
    );
  }

  public function load_config()
  {
    parent::load_config();
    if(!$this->css->css_file_exists())
    {
      $this->css->make_CSS($this->generate_CSS());
    }
  }

  public function init_events()
  {
    parent::init_events();
  }

  /*
   * generate the css code
   */
  function generate_CSS()
  {
    $text = "
      .formtable, .formtable P { text-align:left; display:block; }
      .formtable tr { vertical-align:top; }
      .littlefont { font-size:90%; }
      .littlefont td { padding:1px; }
      table.littlefont th { padding:3px; text-align:left;}
      table.littlefont td { padding:1px 3px; }
      #iprogressbar_contener { border:1px solid #606060; margin:0px; padding:0px; display:block; height:20px; }
      #iprogressbar_bg { background:#606060; display:block; z-index:100; position:relative; height:20px; }
      #iprogressbar_fg { color:#FF3363; width:100%; text-align:center; display: block; z-index:200; position:relative; top:-18px;  }
      #iHeaderListTags { width:100%; border:1px solid; border-collapse: collapse; }
      #iListTags, #iListImages { width:100%; border:1px solid; margin-bottom:20px; height:120px; border-top:0px; overflow:auto;}
      #iListTags table, #iListImages table, table.listTags { width:100%; text-align:left; border-collapse: collapse; }
      #iListTags table tr:hover { cursor:pointer; background:#303030; }
      #iListImages table tr:hover, table.listTags tr:hover { background:#303030; cursor:default; }
      #iHeaderListImages { width:100%; border:1px solid; }
      .warning { color:#dd0000; border:1px solid #dd0000; margin-bottom:8px; margin-top:8px; padding:8px; }
      .warning p { margin-top:0.5em; margin-bottom:0em; }
      .warning ul { margin-top:0em; margin-bottom:0.5em; }
      .pctBar { height:6px; background:#FF7700; }
      li.groupItems { border:1px solid #666666; margin-bottom:5px; padding:0 5px; width:90%; cursor:move; padding:4px; }
      div.addGroup { padding-left:40px; text-align:left; }
      #iGroups { list-style: none; }
      .ui-dialog { background: #222222; border:2px solid #FF3363; }
      .ui-dialog-buttonpane { padding:4px; }
      .ui-dialog-buttonpane button { margin-right:8px; }
      .ui-dialog-titlebar { background:#111111; font-weight:bold; }
      .ui-dialog-title-dialog { text-align: left; }
      .ui-dialog-titlebar-close { float: right; }
      .ui-dialog-content { overflow:auto; }
      .ui-dialog-container { }
      .ui-dialog-titlebar-close { display:none; }
      .tagListOrder { list-style: none; padding:0px; margin-right:8px; margin-left:35px; }
      .tagListOrder li { border:none; background:#333333; padding:1px; margin-bottom:2px; width:100%; }
      .groupTags { padding-top:8px; }
      .editGroupListButton { margin-left:8px; position:absolute; z-index:1000; }
      table.tagListOrderItem { width:100%; border-collapse:collapse; }
      .dialogForm { text-align:left; margin:8px; }
      #ianalyzestatus { background: #333333; margin:8px; padding:8px; }
      #ianalyzestatus ul { margin:0px; padding:0 0 0 20px; }
      #iamd_nb_item_per_request_display { display:inline-block; width:70px; }
      #iamd_nb_item_per_request_slider { display:inline-block; width:350px; }
      #iDialogProgress { margin:16px 8px 8px; }
    ";

    return($text);
  }

} // amd_root  class



?>
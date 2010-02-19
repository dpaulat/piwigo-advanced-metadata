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

  @include_once('amd_root.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/tables.class.inc.php');


  class AMD_install extends AMD_root
  {
    private $tablef;

    public function AMD_install($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablef= new manage_tables($this->tables);
    }

    /*
     * function for installation process
     * return true if install process is ok, otherwise false
     */
    public function install()
    {
      $tables_def=array(
"CREATE TABLE `".$this->tables['used_tags']."` (
  `numId` int(10) unsigned NOT NULL auto_increment,
  `tagId` varchar(80) NOT NULL default '',
  `translatable` char(1) NOT NULL default 'n',
  `name` varchar(200) NOT NULL default '',
  `numOfImg` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numId`),
  KEY `by_tag` (`tagId`)
);",
"CREATE TABLE `".$this->tables['images_tags']."` (
  `imageId` mediumint(8) unsigned NOT NULL default '0',
  `numId` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) default NULL,
  PRIMARY KEY  USING BTREE (`imageId`,`numId`)
);",
"CREATE TABLE `".$this->tables['images']."` (
  `imageId` MEDIUMINT(8) UNSIGNED NOT NULL,
  `analyzed` CHAR(1)  NOT NULL DEFAULT 'n',
  `nbTags` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`imageId`)
);",
"CREATE TABLE `".$this->tables['selected_tags']."` (
  `tagId` VARCHAR(80)  NOT NULL,
  `order` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `groupId` INTEGER  NOT NULL DEFAULT -1,
  PRIMARY KEY (`tagId`)
);",
"CREATE TABLE `".$this->tables['groups_names']."` (
  `groupId` INTEGER  NOT NULL,
  `lang` CHAR(5)  NOT NULL,
  `name` VARCHAR(80)  NOT NULL,
  PRIMARY KEY (`groupId`, `lang`)
);",
"CREATE TABLE `".$this->tables['groups']."` (
  `groupId` INTEGER  NOT NULL AUTO_INCREMENT,
  `order` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`groupId`)
);"
);
      //$table_def array
      $tables_def = create_table_add_character_set($tables_def);
      $result=$this->tablef->create_tables($tables_def);

      /*
       * fill the 'used_tags' table with default values
       */
      foreach(JpegMetaData::getTagList(Array('filter' => JpegMetaData::TAGFILTER_IMPLEMENTED, 'xmp' => true, 'maker' => true, 'iptc' => true)) as $key => $val)
      {
        $sql="INSERT INTO ".$this->tables['used_tags']." VALUES('', '".$key."', '".(($val['translatable'])?'y':'n')."', '".$val['name']."', 0);";
        pwg_query($sql);
      }

      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $this->delete_config();
      $this->tablef->drop_tables();
    }

    public function activate()
    {
      global $template;

      $this->init_config();
      $this->load_config();
      $this->save_config();
    }


    public function deactivate()
    {
    }

  } //class

?>

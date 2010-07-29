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

    public function __construct($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablef= new GPCTables($this->tables);
    }

    public function __destruct()
    {
      unset($this->tablef);
      parent::__destruct();
    }

    /*
     * function for installation process
     * return true if install process is ok, otherwise false
     */
    public function install()
    {
      global $user, $lang;

      $this->initConfig();
      $this->loadConfig();
      $this->config['installed']=AMD_VERSION2;
      $this->saveConfig();

      $tables_def=array(
"CREATE TABLE `".$this->tables['used_tags']."` (
  `numId` int(10) unsigned NOT NULL auto_increment,
  `tagId` varchar(80) NOT NULL default '',
  `translatable` char(1) NOT NULL default 'n',
  `name` varchar(200) NOT NULL default '',
  `numOfImg` int(10) unsigned NOT NULL default '0',
  `translatedName` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`numId`),
  KEY `by_tag` (`tagId`)
);",
"CREATE TABLE `".$this->tables['images_tags']."` (
  `imageId` mediumint(8) unsigned NOT NULL default '0',
  `numId` int(10) unsigned NOT NULL default '0',
  `value` text default NULL,
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
);",
"CREATE TABLE `".$this->tables['user_tags_label']."` (
  `numId` INTEGER UNSIGNED NOT NULL,
  `lang` CHAR(5)  NOT NULL,
  `label` VARCHAR(200)  NOT NULL,
  PRIMARY KEY (`numId`, `lang`)
);",
"CREATE TABLE `".$this->tables['user_tags_def']."` (
  `numId` int(10) unsigned NOT NULL COMMENT 'Id of the tag',
  `defId` int(10) unsigned NOT NULL default '0' COMMENT 'also used for ordering',
  `parentId` int(10) unsigned NOT NULL default '0' COMMENT 'Id of the parent',
  `order` int(10) unsigned NOT NULL,
  `type` char(1) NOT NULL default 'T' COMMENT 'T = static text ; M = metadata value ; C = condition',
  `value` varchar(200) NOT NULL,
  `conditionType` char(2) NOT NULL default 'E',
  `conditionValue` varchar(200) NOT NULL,
  PRIMARY KEY  (`numId`,`defId`),
  KEY `byTagParentId` USING BTREE (`numId`,`parentId`,`order`),
  KEY `byTagOrder` (`numId`,`order`)
);",
      );


      $tables_def = create_table_add_character_set($tables_def);
      $result=$this->tablef->create($tables_def);
      unset($tables_def);

      $tables_insert=array(
"INSERT INTO `".$this->tables['groups']."` VALUES(1, 0)",
"INSERT INTO `".$this->tables['groups_names']."` VALUES(1, '".$user['language']."', '".$lang['g003_default_group_name']."')",
"INSERT INTO `".$this->tables['selected_tags']."` VALUES
    ('magic.Camera.Make', 0, 1),
    ('magic.Camera.Model', 1, 1),
    ('magic.ShotInfo.Lens', 2, 1),
    ('magic.ShotInfo.Aperture', 3, 1),
    ('magic.ShotInfo.Exposure', 4, 1),
    ('magic.ShotInfo.ISO', 5, 1),
    ('magic.ShotInfo.FocalLength', 6, 1),
    ('magic.ShotInfo.FocalLengthIn35mm', 7, 1),
    ('magic.ShotInfo.Flash.Fired', 8, 1)"
      );
      foreach($tables_insert as $sql)
      {
        pwg_query($sql);
      }

      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $this->deleteConfig();
      $this->tablef->drop();
    }

    public function activate()
    {
      global $template, $user;

      $this->initConfig();
      $this->loadConfig();
      $this->loadConfigFromFile(dirname($this->getFileLocation()).'/activatePlugin.conf.php');

      /*
       * if there is no version information available, assume the previous
       *  installed release of the plugin is 0.4.0
       */
      if(!isset($this->config['installed'])) $this->config['installed']='00.04.00';

      switch($this->config['installed'])
      {
        case '00.04.00':
          $this->updateFrom_000400();
          break;
        default:
          /*
           * default is applied for fresh install, and consist to fill the
           * database with default values
           */
          $this->initializeDatabase();
          break;
      }

      $this->config['amd_FillDataBaseExcludeTags']=array();
      $this->config['installed']=AMD_VERSION2; //update the installed release number
      $this->saveConfig();
    }


    public function deactivate()
    {
    }

    /**
     * update the database from the release 0.4.0
     */
    private function updateFrom_000400()
    {
      /*
       * create new tables
       */
      $tableDef=array(
"CREATE TABLE `".$this->tables['user_tags_label']."` (
  `numId` INTEGER UNSIGNED NOT NULL,
  `lang` CHAR(5)  NOT NULL,
  `label` VARCHAR(200)  NOT NULL,
  PRIMARY KEY (`numId`, `lang`)
);",
"CREATE TABLE `".$this->tables['user_tags_def']."` (
  `numId` int(10) unsigned NOT NULL COMMENT 'Id of the tag',
  `defId` int(10) unsigned NOT NULL default '0' COMMENT 'also used for ordering',
  `parentId` int(10) unsigned NOT NULL default '0' COMMENT 'Id of the parent',
  `order` int(10) unsigned NOT NULL,
  `type` char(1) NOT NULL default 'T' COMMENT 'T = static text ; M = metadata value ; C = condition',
  `value` varchar(200) NOT NULL,
  `conditionType` char(2) NOT NULL default 'E',
  `conditionValue` varchar(200) NOT NULL,
  PRIMARY KEY  (`numId`,`defId`),
  KEY `byTagParentId` USING BTREE (`numId`,`parentId`,`order`),
  KEY `byTagOrder` (`numId`,`order`)
);"
      );
      $tablesDef = create_table_add_character_set($tablesDef);
      $result=$this->tablef->create($tablesDef);
      unset($tablesDef);

      /*
       * update old tables
       */

      // no tables to update
    }




    /**
     * fill the database with some default value
     */
    private function initializeDatabase()
    {
      global $user;

      L10n::setLanguage('en_UK');

      pwg_query("DELETE FROM ".$this->tables['used_tags']);
      pwg_query("DELETE FROM ".$this->tables['images_tags']);
      pwg_query("UPDATE ".$this->tables['images']." SET analyzed='n', nbTags=0;");
      pwg_query("INSERT INTO ".$this->tables['images']."
                  SELECT id, 'n', 0
                    FROM ".IMAGES_TABLE."
                    WHERE id NOT IN (SELECT imageId FROM ".$this->tables['images'].")");
      /*
       * fill the 'used_tags' table with default values
       */
      foreach(AMD_JpegMetaData::getTagList(Array('filter' => AMD_JpegMetaData::TAGFILTER_IMPLEMENTED, 'xmp' => true, 'maker' => true, 'iptc' => true)) as $key => $val)
      {
        $sql="INSERT INTO ".$this->tables['used_tags']." VALUES('', '".$key."', '".(($val['translatable'])?'y':'n')."', '".$val['name']."', 0, '".addslashes(L10n::get($val['name']))."');";
        pwg_query($sql);
      }

      /*
       * exclude unauthorized tag with the 'amd_FillDataBaseExcludeTags' option
       */
      if(count($this->config['amd_FillDataBaseExcludeTags']))
      {
        $sql="";
        foreach($this->config['amd_FillDataBaseExcludeTags'] as $key => $tag)
        {
          if($sql!="") $sql.=" OR ";
          $sql.=" tagId LIKE '$tag' ";
        }
        $sql="DELETE FROM ".$this->tables['used_tags']."
              WHERE ".$sql;
        pwg_query($sql);
      }

      $listToAnalyze=Array(Array(), Array());
      /*
       * select 25 pictures into the caddie
       */
      $sql="SELECT ti.id, ti.path
            FROM ".CADDIE_TABLE." tc
              LEFT JOIN ".IMAGES_TABLE." ti ON ti.id = tc.element_id
            WHERE tc.user_id = ".$user['id']."
              AND ti.id IS NOT NULL
            ORDER BY RAND() LIMIT 25;";
      $result=pwg_query($sql);
      if($result)
      {
        while($row=pwg_db_fetch_assoc($result))
        {
          $listToAnalyze[0][]=$row;
          $listToAnalyze[1][]=$row['id'];
        }
      }
      /*
       * if caddie is empty, of is have less than 25 pictures, select other
       * pictures from the gallery
       */
      if(count($listToAnalyze[0])<25)
      {
        if(count($listToAnalyze[0])>0)
        {
          $excludeList="WHERE ti.id NOT IN(".implode(",", $listToAnalyze[1]).") ";
        }
        else
        {
          $excludeList="";
        }
        $sql="SELECT ti.id, ti.path
              FROM ".IMAGES_TABLE." ti ".$excludeList."
              ORDER BY RAND() LIMIT ".(25-count($listToAnalyze[0])).";";
        $result=pwg_query($sql);
        if($result)
        {
          while($row=pwg_db_fetch_assoc($result))
          {
            $listToAnalyze[0][]=$row;
          }
        }
      }

      /*
       * analyze the 25 selected pictures
       */
      if(count($listToAnalyze[0])>0)
      {
        // $path = path of piwigo's on the server filesystem
        $path=dirname(dirname(dirname(__FILE__)));

        foreach($listToAnalyze[0] as $val)
        {
          $this->analyzeImageFile($path."/".$val['path'], $val['id']);
        }

        $this->makeStatsConsolidation();
      }
    }

  } //class

?>

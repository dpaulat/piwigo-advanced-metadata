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
 * -----------------------------------------------------------------------------
 *
 * -----------------------------------------------------------------------------
 *
 * -----------------------------------------------------------------------------
 */

  define("KNOWN_TAGS_IMPLEMENTED",      0x01);
  define("KNOWN_TAGS_NOT_IMPLEMENTED",  0x02);
  define("KNOWN_TAGS_ALL", KNOWN_TAGS_NOT_IMPLEMENTED | KNOWN_TAGS_IMPLEMENTED);

  class KnownTags
  {
    protected $label = "";
    protected $tags = Array();

    function __construct()
    {

    }

    function __destruct()
    {
      unset($this->tags);
    }

    public function getTags($filter = Array('implemented' => KNOWN_TAGS_ALL, 'schema' => NULL))
    {
      if(!array_key_exists('implemented', $filter))
      {
        $filter['implemented'] = KNOWN_TAGS_ALL;
      }

      if(!array_key_exists('schema', $filter))
      {
        $filter['schema'] = NULL;
      }


      $returned=Array();
      foreach($this->tags as $key => $val)
      {
        if(( ($val['implemented'] and ($filter['implemented'] & KNOWN_TAGS_IMPLEMENTED)) or
             (!$val['implemented'] and ($filter['implemented'] & KNOWN_TAGS_NOT_IMPLEMENTED)) ) and

             (is_null($filter['schema']) or
             (!is_null($filter['schema']) and $val['schema']==$filter['schema']) )
          )
        {
          $returned[$key]=$val;
        }
      }
      return($returned);
    }

    public function tagIdExists($id)
    {
      return(array_key_exists($id, $this->tags));
    }


    public function getTagById($id)
    {
      if(array_key_exists($id, $this->tags))
      {
        return($this->tags[$id]);
      }
      return(false);
    }

    public function getTagIdByName($name)
    {
      foreach($this->tags as $key => $val)
      {
        if($val['tagName']==$name)
         return($key);
      }
      return(false);
    }

    public function getTagByName($name)
    {
      $index=$this->getTagIdByName($name);
      if(index!==false)
        return($this->tags[$index]);
      return(false);
    }

    public function getLabel()
    {
      return($this->label);
    }

  }

?>

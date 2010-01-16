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
 *
 * -----------------------------------------------------------------------------
 *
 * -----------------------------------------------------------------------------
 */

  class SegmentReader
  {
    protected $data = null;
    protected $isValid = false;
    protected $isLoaded = false;

    function __construct(Data $data)
    {
      $this->data = $data;
      $this->data->seek();
    }

    public function getIsValid()
    {
      return($this->isValid);
    }

    public function getIsLoaded()
    {
      return($this->isLoaded);
    }

    public function toString()
    {
      $returned="";
      return($returned);
    }

  }


?>

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

  require_once(JPEG_METADATA_DIR."Common/ConvertData.class.php");
  require_once(JPEG_METADATA_DIR."Common/Data.class.php");
  require_once(JPEG_METADATA_DIR."Readers/SegmentReader.class.php");
  require_once(JPEG_METADATA_DIR."Readers/IfdReader.class.php");

  class TiffReader extends SegmentReader
  {
    private $IFDs = Array();
    private $offsetData = 0;
    private $byteOrder = BYTE_ORDER_LITTLE_ENDIAN;
    private $firstIFDOffset = 0;

    function __construct(Data $data, $offsetData=0)
    {
      parent::__construct($data);

      $this->offsetData = $offsetData;
      $header=$this->data->readASCII(2);

      /*
       * TIFF Header begins wih "II" or "MM" (indicate the byte order)
       * next value is an USHORT, must equals 0x2a
       *
       * all data have to be read with the byte order defined in header
       */
      if($header=="II" or $header="MM")
      {
        $this->byteOrder=$header;
        $this->data->setByteOrder($this->byteOrder);

        $header=$this->data->readUShort();
        if($header==0x2a)
        {
          $this->isValid=true;
          $this->firstIFDOffset=$this->data->readULong();
          $this->readData();
        }
      }
    }

    function __destruct()
    {
      unset($this->IFDs);
    }

    private function readData()
    {
      $nextIFD = $this->firstIFDOffset;
      while($nextIFD!=0)
      {
        $this->data->seek($nextIFD);
        $IFD = new IfdReader($this->data->readASCII(), $nextIFD, $this->byteOrder);
        $this->IFDs[]=$IFD;
        $nextIFD = $IFD->getNextIFDOffset();
      }
    }

    public function getNbIFDs()
    {
      return(count($this->IFDs));
    }

    public function getIFDs()
    {
      return($this->IFDs);
    }

    public function getIFD($num)
    {
      if($num>=0 and $num<count($this->IFDs))
        return($this->IFDs[$num]);
      else
        return(null);
    }


    public function toString()
    {
      $returned="TIFF block offset: ".sprintf("%08x", $this->offsetData).
                " ; byteOrder: ".$this->byteOrder.
                " ; isValid: ".($this->isValid?"Y":"N").
                " ; isLoaded: ".($this->isValid?"Y":"N").
                " ; IFDs: ".count($this->IFDs).
                " ; first IFD Offset: ".sprintf("0x%04x", $this->firstIFDOffset);
      return($returned);
    }

  }


?>

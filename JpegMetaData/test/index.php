<?php
 ini_set('error_reporting', E_ALL | E_STRICT);
 ini_set('display_errors', true);

  require_once("./../JpegMetaData.class.php");
  require_once(JPEG_METADATA_DIR."Readers/JpegReader.class.php");
  require_once(JPEG_METADATA_DIR."Common/XmlData.class.php");
  require_once(JPEG_METADATA_DIR."Common/Locale.class.php");

  require_once(JPEG_METADATA_DIR."TagDefinitions/IfdTags.class.php");
  require_once(JPEG_METADATA_DIR."TagDefinitions/PentaxTags.class.php");
  require_once(JPEG_METADATA_DIR."TagDefinitions/GpsTags.class.php");
  require_once(JPEG_METADATA_DIR."TagDefinitions/XmpTags.class.php");

echo "
<html>
<header>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
</header>
<body>
-- Tests --<br>
";

echo "<hr>-- Images --<br>";

$d = scandir(dirname(__FILE__));

foreach($d as $key => $file)
{
  if(preg_match("/.*\.(jpg|jpeg)/i",$file))
    echo "[<a href='?file=".$file."'>$file</a>]&nbsp; ";
}


echo "<hr>-- Resultat --<br>";


function dump_xml($xml)
{
  $color=Array(
   0 => "000000",
   1 => "ff0000",
   2 => "0000ff",
   3 => "008000",
   4 => "800000",
   5 => "000080",
   6 => "008080",
   7 => "808000",
   8 => "800080",
   9 => "808080");

  $parser = xml_parser_create();
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
  xml_parse_into_struct($parser, $xml, $values, $tags);
  xml_parser_free($parser);

  foreach($values as $key => $val)
  {
    switch($val['type'])
    {
      case "open":
        echo "<span style='color:#".$color[$val['level']]."'>(".$val['level'].")".str_repeat("&nbsp;", 3*$val['level'])."".$val['tag']."</span>";
        if(array_key_exists("attributes", $val))
          foreach($val['attributes'] as $key2 => $val2)
          {
            echo "<br><span style='color:#".$color[$val['level']]."'>".str_repeat("&nbsp;", 5+3*$val['level'])."<i>[".$key2."] ".$val2."</i></span>";
          }
        break;
      case "close":
        echo "<span style='color:#".$color[$val['level']]."'>(".$val['level'].")".str_repeat("&nbsp;", 3*$val['level'])."/".$val['tag']."</span>";
        if(array_key_exists("attributes", $val))
          foreach($val['attributes'] as $key2 => $val2)
          {
            echo "<br><span style='color:#".$color[$val['level']]."'>".str_repeat("&nbsp;", 5+3*$val['level'])."<i>[".$key2."] ".$val2."</i></span>";
          }
        break;
      case "complete":
        echo "<span style='color:#".$color[$val['level']]."'>(".$val['level'].")".str_repeat("&nbsp;", 3*$val['level'])."/".$val['tag']."</span>";
        if(array_key_exists("attributes", $val))
          foreach($val['attributes'] as $key2 => $val2)
          {
            echo "<br><span style='color:#".$color[$val['level']]."'>".str_repeat("&nbsp;", 5+3*$val['level'])."<i>[".$key2."] ".$val2."</i></span>";
          }
        break;
    }
    if(array_key_exists('value', $val))
     echo " <span style='color:#ff00ff;'>".$val['value']."</span>";
    echo "<br>";

  }

  $tmp=new XmlData($xml);

  //echo "has node:".($tmp->hasNodes()?"Y":"N")."<br>";

  dump_node($tmp->getFirstNode());


}

function dump_node($node)
{
  if($node==NULL)
    return(false);
  //echo "name:".$node->getName()." / level:".$node->getLevel()." / attributes: ".count($node->getAttributes())." / has child:".($node->hasChilds()?"Y":"N")."<br>";

  if($node->hasChilds())
  {
    dump_node($node->getFirstChild());
  }

  dump_node($node->getNextNode());
}

function dump_ifd($key2, $val2)
{
  echo sprintf("IFD %02d: ", $key2).$val2->toString()."<br>";

  foreach($val2->getTags() as $key3 => $val3)
  {
    dump_tag($key3, $val3->getTag(), "<span style='color:#804080;'>".sprintf("[%02d] ", $key3).$val3->toString()."</span><br>");

    /*
    if($val3->isOffset())
    {
      echo "<div style='color:#ff0000;margin-left:40px;'>";
      echo ConvertData::toHexDump($val3->getValue(), $val3->getType(),15)." => ".substr($val3->getValue(),0,254)."<br>";
      echo "</div>";
    }
    */

    if($val3->getTag()->getLabel() instanceof IfdReader)
    {
      echo "<div style='padding:1px;margin-bottom:2px;margin-right:4px;margin-left:25px;border:1px dotted #6060FF;'>";
      dump_ifd($key3, $val3->getTag()->getLabel());
      echo "</div>";
    }
  }
}

function dump_xmp($key2, $val2)
{
  if(is_string($val2->getValue()))
    $extra=$val2->getValue();
  elseif(is_array($val2->getValue()))
    $extra=print_r($val2->getValue(), true);
  else
    $extra=ConvertData::toHexDump($val2->getValue(), ByteType::ASCII);

  $extra="<br><span style='color:#000000;'>".$extra."</span><br>";

  echo "<div style='color:#000000;margin-left:12px;border-bottom:1px solid #808080;";
  if(!$val2->getIsKnown())
  {
    echo "background:#ffd0d0;'>";
  }
  elseif(!$val2->getIsImplemented())
  {
    echo "background:#ffffd0;'>";
  }
  else
  {
    echo "background:#d0ffd0;'>";
    $extra="";
  }

  echo "<span style='color:#804080;'>".$val2->toString().$extra;
 /* if($val2->getName()!=$val2->getId())
    echo " ==> Id: ".$val2->getId();*/
  echo "</span>";

  echo "</div>";
}

function dump_tag($key3, $val3, $extra)
{
    echo "<div style='color:#000000;margin-left:12px;border-bottom:1px solid #808080;";
    if(!$val3->getIsKnown())
    {
      echo "background:#ffd0d0;'>".$extra;
    }
    elseif(!$val3->getIsImplemented())
    {
      echo "background:#ffffd0;'>".$extra;
    }
    else
    {
      echo "background:#d0ffd0;'>";
    }

    echo str_replace(" ", "&nbsp;", "     ").$val3->toString("small")."<br>";

    echo "</div>";
}

/*
function cmp($a, $b)
{
    if ($a['value'] == $b['value']) {
        return 0;
    }
    return ($a['value'] < $b['value']) ? -1 : 1;
}

function list_for_po()
{

  $tmpTagName=Array();
  $tmpValues=Array();

  $tagList=Array(
    new IfdTags(),
    new XmpTags(),
    new IptcTags(),
    new GpsTags(),
    new PentaxTags(),
  );


  foreach($tagList as $key => $tag)
  {

    foreach($tag->getTags() as $key => $val)
    {
      if(array_key_exists('tagName', $val))
        $name=$val['tagName'];
      else
        $name="";

      if(is_string($key))
        $tKey=$key;
      else
        $tKey=sprintf("0x%04x", $key);

      if($name!="")
        $tKey.=" ($name)";

      if($name!="")
      {
        $tmpTagName[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $name);
      }
      else
      {
        $tmpTagName[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $key);
      }

      if(array_key_exists('tagValues', $val) and $val['translatable'])
      {
        foreach($val['tagValues'] as $key2 => $val2)
        {
          $tmpValues[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $val2);
        }
      }

      if(array_key_exists('tagValues.special', $val) and $val['translatable'])
      {
        foreach($val['tagValues.special'] as $key2 => $val2)
        {
          $tmpValues[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $val2);
        }
      }

      if(array_key_exists('tagValues.specialNames', $val) and $val['translatable'])
      {
        foreach($val['tagValues.specialNames'] as $key2 => $val2)
        {
          $tmpValues[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $val2);
        }
      }

      if(array_key_exists('tagValues.specialValues', $val) and $val['translatable'])
      {
        foreach($val['tagValues.specialValues'] as $key2 => $val2)
        {
          $tmpValues[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $val2);
        }
      }

      if(array_key_exists('tagValues.computed', $val) and $val['translatable'])
      {
        foreach($val['tagValues.computed'] as $key2 => $val2)
        {
          $tmpValues[]=Array('group' => $tag->getLabel()." / ".$tKey, 'value' => $val2);
        }
      }


    }

  }

  $tmp=array_merge($tmpTagName, $tmpValues);
  usort($tmp, "cmp");

  foreach($tmp as $key => $val)
  {
    echo "#. ".$val['group']."<br>";
    echo "msgid \"".$val['value']."\"<br>";
    echo "msgstr \"".$val['value']."\"<br><br>";
  }

}
*/

  if(isset($_GET["file"]))
  {
    $file=$_GET["file"];
  }
  else
  {
    die("no filename ?<br/></body></html>");
  }

  $memory=memory_get_usage();
  echo "memory : ".$memory."<br>";

  $jpeg = new JpegReader($file);
  echo "<span style='font-family:monospace;'>JpegReader<br>";
  echo "fileName=".$jpeg->getFileName()."<br>";
  echo "isValid=".($jpeg->isValid()?"Y":"N")."<br>";
  echo "isLoaded=".($jpeg->isLoaded()?"Y":"N")."<br>";
  echo "NbMarkers=".$jpeg->countAppMarkerSegments()."<br>";
  foreach($jpeg->getAppMarkerSegments() as $key => $val)
  {
    echo "<div style='border:1px solid #000000;padding:4px;margin:1px;'>";
    echo sprintf("[%02d] ", $key).$val->toString()."<br>";
    if($val->dataLoaded())
    {
      echo "<div style='color:#0000ff;font-weight:bold;margin-left:20px;'>";
      $data=$val->getData();
      if($data instanceof TiffReader)
      {
        echo $data->toString()."<br>";

        foreach($data->getIFDs() as $key2 => $val2)
        {
          echo "<div style='color:#0000ff;font-weight:normal;margin-left:12px;'>";

          dump_ifd($key2, $val2);

          echo "</div>";
        }

      }
      elseif($data instanceof XmpReader)
      {
        echo htmlentities($data->toString())."<br>";
        echo dump_xml($data->toString())."<br>";

        foreach($data->getTags() as $key2 => $val2)
        {
          echo "<div style='color:#0000ff;font-weight:normal;margin-left:12px;'>";

          dump_xmp($key2, $val2);

          echo "</div>";
        }


      }
      elseif($data instanceof IptcReader)
      {
        $data->optimizeDateTime();
        foreach($data->getTags() as $key2 => $val2)
        {
          echo "<div style='color:#0000ff;font-weight:normal;margin-left:12px;'>";

          dump_tag($key2, $val2, "");

          echo "</div>";
        }


      }
      else
      {
       echo htmlentities($val->getData())."<br>";
      }
      echo "</div>";
    }
    echo "</div>";

  }
  echo "</span><hr>";


  Locale::set("en_UK");

  echo "<span style='font-family:monospace;'>JpegMetaData - tag from test file<br>";
  echo "<table style='border:1px solid #000000;'>";
  echo "<tr style='border-bottom:1x solid #000000;'><th>Key</th><th>Name</th><th>Value</th><th>Computed Value</th></tr>";
  $jpegmd = new JpegMetaData($file, Array(
    'filter' => JpegMetaData::TAGFILTER_IMPLEMENTED,
    'optimizeIptcDateTime' => true)
  );

  $i=0;
  foreach($jpegmd->getTags() as $key => $val)
  {
    $txt=$val->getLabel();
    $value=$val->getValue();

    if($val->getIsTranslatable())
      $style="color:#0000ff";
    else
      $style="color:#000000";

    if(is_string($txt) and $val->getIsTranslatable())
      $txt=Locale::get($txt);
    if($txt instanceof DateTime)
      $txt=$txt->format("Y-m-d H:i:s");
    if(is_array($txt))
      $txt=print_r($txt, true);
    if(is_array($value))
      $value=print_r($value, true);
    echo "<tr><td>".$key."</td><td>".Locale::get($val->getName())."</td><td>".$value."</td><td style='$style'>".$txt."</td></tr>";
    $i++;
  }
  echo "</table>Total tags: $i</span><hr>";

  $i=0;
  $j=0;
  echo "<span style='font-family:monospace;'>JpegMetaData - known tags<br>";
  echo "<table style='border:1px solid #000000;'>";
  echo "<tr style='border-bottom:1x solid #000000;'><th>Key</th><th>Name</th><th>Implemented</th></tr>";
  foreach($jpegmd->getTagList(Array('filter' => JpegMetaData::TAGFILTER_ALL, 'xmp' => true, 'maker' => true, 'iptc' => true)) as $key => $val)
  {
    $val['implemented']?$i++:$j++;
    echo "<tr><td>".$key."</td><td>".Locale::get($val['name'])."</td><td>".($val['implemented']?"yes":"no")."</td></tr>";
  }
  echo "</table>Total tags ; implemented: $i - not implemented: $j</span><hr>";

  unset($jpegmd);
  unset($jpeg);
  $memory2=memory_get_usage();
  echo "memory : ".$memory2." (memory leak ? = ".($memory2-$memory).")<br>";
  echo "<br/></body></html>";
?>

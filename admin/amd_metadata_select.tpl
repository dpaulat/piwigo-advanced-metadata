{literal}
<script type="text/javascript">

  var globalTagId;

  function init()
  {
    computedWidth=$("#content").get(0).clientWidth;
    computedHeight=$("#content").get(0).clientHeight;
    $("#dialogViewDetail")
    .dialog(
      {
        autoOpen: false,
        resizable: false,
        width:computedWidth,
        height:computedHeight,
        modal: true,
        draggable:false,
        title: '{/literal}{"g003_metadata_detail"|@translate}{literal}',
        overlay:
        {
          backgroundColor: '#000',
          opacity: 0.5
        },
        open: function(event, ui)
        {
          bH=$("div.ui-dialog-buttonpane").get(0).clientHeight;
          $("#dialogViewDetail").css('height', (this.clientHeight-bH)+"px");
          $("#iListImages").css('height', (this.clientHeight-bH-$("#iListImagesNb").get(0).clientHeight-$("#iHeaderListImages").get(0).clientHeight)+"px");
        },
        buttons:
        {
          '{/literal}{"g003_ok"|@translate}{literal}':
            function()
            {
              $(this).dialog('close');
            }
        }
      }
    );
  }

  function loadTagList()
  {
    $("body").css("cursor", "wait");
    order=$('#iSelectOrderTagList').val();
    filter=$("#iSelectFilterTagList").val();
    unusedTag=($("#iExcludeUnusedTagList").get(0).checked)?"y":"n";
    selectedOnly=($("#iSelectedTagOnly").get(0).checked)?"y":"n";

    displayTagListOrder();

    $("#iListTags").html(
      $.ajax({
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"showStatsGetListTags", orderType:order, filterType:filter, excludeUnusedTag:unusedTag, selectedTagOnly:selectedOnly }
       }).responseText
    );
    $("#iListTagsNb").html(
      "{/literal}{'g003_number_of_filtered_metadata'|@translate}{literal} "+$("#iListTags table tr").length
    );

    //onclick="updateTagSelect('iNumId{$data.numId}', '')"
    $("input.cbiListTags")
      .bind('click',
        function(event)
        {
          event.stopPropagation();
          updateTagSelect($(this).get(0).id, '');
        }
      );

    $("a.cbiListTags")
      .bind('click',
        function(event)
        {
          event.stopPropagation();
          loadTagDetail($(this).get(0).id.substr(7));
        }
      );




    $("body").css("cursor", "default");
  }

  function loadTagDetail(tag)
  {
    $("#dialogViewDetail").dialog('open');

    globalTagId=tag;
    order=$('#iSelectOrderImageList').val();
    $("#iListImages").html("<br>{/literal}{'g003_loading'|@translate}{literal}");
    $("#iHeaderListImagesTagName").html("["+tag+"]");

    $.ajax(
      {
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: true,
        data: { ajaxfct:"showStatsGetListImages", orderType:order, tagId:tag,  },
        success:
          function(msg)
          {
            $("#iListImages").html(msg);
            $("#iListImagesNb").html(
              "{/literal}{'g003_number_of_distinct_values'|@translate}{literal} "+$("#iListImages table tr").length
            );
          }
      }
    );
  }

  function updateTagSelect(numId, mode)
  {
    $("body").css("cursor", "wait");

    if(mode=='switch')
    {
      $("#"+numId).get(0).checked=!$("#"+numId).get(0).checked;
    }

    selected=($("#"+numId).get(0).checked)?"y":"n";

    $("#iListImages").html(
      $.ajax({
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"updateTagSelect", tagSelected:selected, numId:numId.substr(6) }
       }).responseText
    );
    $("body").css("cursor", "default");
  }

  function sortTagList(by)
  {
    $("#iSelectOrderTagList").val(by);
    displayTagListOrder();
    loadTagList();
  }

  function sortTagDetail(by, tag)
  {
    $("#iSelectOrderImageList").val(by);
    displayTagDetailOrder();
    loadTagDetail(tag);
  }

  function displayTagListOrder()
  {
    if($("#iSelectOrderTagList").val()=="tag")
    {
      $("#iHLTOrderTag").html("&#8593;");
      $("#iHLTOrderLabel").html("");
      $("#iHLTOrderNum").html("");
    }
    else if($("#iSelectOrderTagList").val()=="num")
    {
      $("#iHLTOrderTag").html("");
      $("#iHLTOrderLabel").html("");
      $("#iHLTOrderNum").html("&#8593;");
    }
    else
    {
      // by label
      $("#iHLTOrderTag").html("");
      $("#iHLTOrderLabel").html("&#8593;");
      $("#iHLTOrderNum").html("");
    }
  }

  function displayTagDetailOrder()
  {
    if($("#iSelectOrderImageList").val()=="value")
    {
      $("#iHLIOrderValue").html("&#8593;");
      $("#iHLIOrderNum").html("");
    }
    else
    {
      $("#iHLIOrderValue").html("");
      $("#iHLIOrderNum").html("&#8593;");
    }
  }


</script>
{/literal}


<h3>{'g003_select_metadata'|@translate}</h3>

<form>
  <input type="hidden" id="iSelectOrderTagList" value="{$datas.config_GetListTags_OrderType}"/>

  <label>{'g003_filter'|@translate}
    <select id="iSelectFilterTagList" onchange="loadTagList();">
      <option value="" {if $datas.config_GetListTags_FilterType==""}selected{/if}>{'g003_no_filter'|@translate}</option>
      <option value="magic" {if $datas.config_GetListTags_FilterType=="magic"}selected{/if}>{'g003_magic_filter'|@translate}</option>
      <option value="exif" {if $datas.config_GetListTags_FilterType=="exif"}selected{/if}>Exif</option>
      <option value="exif.Canon" {if $datas.config_GetListTags_FilterType=="exif.Canon"}selected{/if}>Exif [Canon]</option>
      <option value="exif.Nikon" {if $datas.config_GetListTags_FilterType=="exif.Nikon"}selected{/if}>Exif [Nikon]</option>
      <option value="exif.Pentax" {if $datas.config_GetListTags_FilterType=="exif.Pentax"}selected{/if}>Exif [Pentax]</option>
      <option value="xmp" {if $datas.config_GetListTags_FilterType=="xmp"}selected{/if}>Xmp</option>
      <option value="iptc" {if $datas.config_GetListTags_FilterType=="iptc"}selected{/if}>Iptc</option>
    </select>
  </label>

  <label>
    <input type="checkbox" id="iExcludeUnusedTagList" onchange="loadTagList();"  {if $datas.config_GetListTags_ExcludeUnusedTag=="y"}checked{/if}>&nbsp;{'g003_exclude_unused_tags'|@translate}
  </label>

  <label>
    <input type="checkbox" id="iSelectedTagOnly" onchange="loadTagList();" {if $datas.config_GetListTags_SelectedTagOnly=="y"}checked{/if}>&nbsp;{'g003_selected_tags_only'|@translate}
  </label>

</form>

<table id='iHeaderListTags' class="littlefont">
  <tr>
    <th style="width:35%;min-width:340px;"><span id="iHLTOrderTag"></span><a onclick="sortTagList('tag');">{'g003_TagId'|@translate}</a></th>
    <th><span id="iHLTOrderLabel"></span><a onclick="sortTagList('label');">{'g003_TagLabel'|@translate}</a></th>
    <th width="80px"><span id="iHLTOrderNum"></span><a onclick="sortTagList('num');">{'g003_NumOfImage'|@translate}</a></th>
    <th width="40px">{'g003_Pct'|@translate}</th>
    <th width="110px">&nbsp;</th>
  </tr>
</table>
<div id='iListTags'>
</div>
<div id="iListTagsNb"></div>


<div id="dialogViewDetail">
  <form>
    <input type="hidden" id="iSelectOrderImageList" value="{$datas.config_GetListImages_OrderType}"/>
  </form>

  <table id='iHeaderListImages' class="littlefont">
    <tr>
      <th><span id="iHLIOrderValue"></span><a onclick="sortTagDetail('value', globalTagId);">{'g003_Value'|@translate}</a>&nbsp;<span id="iHeaderListImagesTagName"></span></th>
      <th width="80px"><span id="iHLIOrderNum"></span><a onclick="sortTagDetail('num', globalTagId);">{'g003_NumOfImage'|@translate}</a></th>
      <th width="40px">{'g003_Pct'|@translate}</th>
      <th width="110px">&nbsp;</th>
    </tr>
  </table>

  <div id='iListImages'>
    <div style="width:100%;text-align:center;padding-top:20px;">{'g003_no_items_selected'|@translate}</div>
  </div>
  <div id="iListImagesNb"></div>
</div>


<script type="text/javascript">
  init();
  loadTagList();
  displayTagDetailOrder();
</script>

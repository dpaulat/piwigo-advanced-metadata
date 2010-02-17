{literal}
<script type="text/javascript">

  var globalTagId;

  function loadTagList()
  {
    $("body").css("cursor", "wait");
    order=$('#iSelectOrderTagList').val();
    filter=$("#iSelectFilterTagList").val();
    unusedTag=($("#iExcludeUnusedTagList").get(0).checked)?"y":"n";
    selectedOnly=($("#iSelectedTagOnly").get(0).checked)?"y":"n";

    $("#iListTags").html(
      $.ajax({
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"showStatsGetListTags", orderType:order, filterType:filter, excludeUnusedTag:unusedTag, selectedTagOnly:selectedOnly }
       }).responseText
    );
    $("body").css("cursor", "default");
  }

  function loadTagDetail(tag)
  {
    globalTagId=tag;
    order=$('#iSelectOrderImageList').val();
    $("#iListImages").html("<br>{/literal}{'g003_loading'|@translate}{literal}");

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
          }
      }
    );
  }

  function updateTagSelect(numId)
  {
    $("body").css("cursor", "wait");
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


</script>
{/literal}


<h3>{'g003_select_metadata'|@translate}</h3>

<form>
  <label>{'g003_order'|@translate}
    <select id="iSelectOrderTagList" onchange="loadTagList();">
      <option value="tag" {if $datas.config_GetListTags_OrderType=="tag"}selected{/if}>{'g003_tagOrder'|@translate}</option>
      <option value="num" {if $datas.config_GetListTags_OrderType=="num"}selected{/if}>{'g003_numOrder'|@translate}</option>
    </select>
  </label>

  <label>{'g003_filter'|@translate}
    <select id="iSelectFilterTagList" onchange="loadTagList();">
      <option value="" {if $datas.config_GetListTags_FilterType==""}selected{/if}>{'g003_no_filter'|@translate}</option>
      <option value="exif" {if $datas.config_GetListTags_FilterType=="exif"}selected{/if}>Exif</option>
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
    <th style="width:35%;min-width:340px;">{'g003_TagId'|@translate}</th>
    <th>{'g003_TagLabel'|@translate}</th>
    <th width="80px">{'g003_NumOfImage'|@translate}</th>
    <th width="40px">{'g003_Pct'|@translate}</th>
    <th width="110px">&nbsp;</th>
  </tr>
</table>
<div id='iListTags'>
</div>


<form>
  <label>{'g003_order'|@translate}
    <select id="iSelectOrderImageList" onchange="loadTagDetail(globalTagId);">
      <option value="value"  {if $datas.config_GetListImages_OrderType=="value"}selected{/if}>{'g003_valueOrder'|@translate}</option>
      <option value="num"  {if $datas.config_GetListImages_OrderType=="num"}selected{/if}>{'g003_numOrder'|@translate}</option>
    </select>
  </label>
</form>

<table id='iHeaderListImages' class="littlefont">
  <tr>
    <th>{'g003_Value'|@translate}</th>
    <th width="80px">{'g003_NumOfImage'|@translate}</th>
    <th width="40px">{'g003_Pct'|@translate}</th>
    <th width="110px">&nbsp;</th>
  </tr>
</table>

<div id='iListImages'>
  <div style="width:100%;text-align:center;padding-top:20px;">{'g003_no_items_selected'|@translate}</div>
</div>


<script type="text/javascript">
  loadTagList();
</script>

{known_script id="jquery.ui" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.core.packed.js"}
{known_script id="jquery.ui.slider" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.slider.packed.js"}
{known_script id="jquery.ui.dialog" src=$ROOT_URL|@cat:"template-common/lib/ui/ui.dialog.packed.js"}

{literal}
<style>
 .ui-slider {
    width:350px;
    height:10px;
    border:1px solid;
    margin-left:10px;
  }
 .ui-slider-handle {
    width:12px;
    height:12px;
    position:relative;
    top:-2px;
    border:1px solid;
    background:#cccccc;
  }
</style>

<script type="text/javascript">

  function init()
  {
    formatNbItemPerRequest({/literal}{$datas.NumberOfItemsPerRequest}{literal});
    $("#iamd_nb_item_per_request_slider").slider(
      {
        min:5,
        max:150,
        steps:29,
        startValue:{/literal}{$datas.NumberOfItemsPerRequest}{literal},
        slide: function(event, ui) { formatNbItemPerRequest(ui.value); }
      }
    );
    getStatus();
  }

  function formatNbItemPerRequest(nbItems)
  {
    $("#iamd_NumberOfItemsPerRequest").val(nbItems);
    $("#iamd_nb_item_per_request_display").html(nbItems);
  }

  function getStatus()
  {
    data=$.ajax(
      {
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"makeStatsGetStatus" }
      }
    ).responseText;

    list=data.split(";");
    $("#ianalyzestatus").html("<ul><li>"+list[0]+"</li><li>"+list[1]+"</li><li>"+list[2]+"</li></ul>");
  }

  function doAnalyze()
  {
    $("body").css("cursor", "wait");

    mode="all";
    modeLabel="";

    if($("#ianalyze_action0").get(0).checked)
    {
      mode="notAnalyzed";
      modeLabel="{/literal}{'g003_analyze_not_analyzed_pictures'|@translate}{literal}";
    }
    else if($("#ianalyze_action1").get(0).checked)
    {
      mode="all";
      modeLabel="{/literal}{'g003_analyze_all_pictures'|@translate}{literal}";
    }
    else if($("#ianalyze_action2").get(0).checked)
    {
      mode="caddieAdd";
      modeLabel="{/literal}{'g003_analyze_caddie_add_pictures'|@translate}{literal}";
    }
    else if($("#ianalyze_action3").get(0).checked)
    {
      mode="caddieReplace";
      modeLabel="{/literal}{'g003_analyze_caddie_replace_pictures'|@translate}{literal}";
    }


    doAnalyze="<br><form id='iDialogProgress' class='formtable'>"+
      "<div id='iprogressbar_contener'>"+
      "<span id='iprogressbar_bg' style='width:0%;'>&nbsp;</span>"+
      "<span id='iprogressbar_fg'>0%</span>"+
      "</div>{/literal}{'g003_analyze_in_progress'|@translate}{literal}</form>";

    $("#dialog")
    .html("")
    .dialog(
      {
        resizable: false,
        width:480,
        height:120,
        modal: true,
        draggable:false,
        title: '{/literal}{"g003_updating_metadata"|@translate}{literal}&nbsp;('+modeLabel+')',
        overlay:
        {
          backgroundColor: '#000',
          opacity: 0.5,
        }
      }
    ).html(doAnalyze);

    NumberOfItemsPerRequest=$("#iamd_NumberOfItemsPerRequest").val();

    $.ajax(
      {
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"makeStatsGetList", selectMode:mode, numOfItems:NumberOfItemsPerRequest },
        success: function(msg)
          {
            doStep_getList(msg);
          },
        error: function()
          {
            alert('error');
          }
      }
    );
  }


  function displayTime(eTime)
  {
    seconds=(eTime%60).toFixed(2);
    minutes=((eTime-seconds)/60).toFixed(0);
    returned=seconds+"s";
    if(minutes>0) returned=minutes+"m"+returned;
    return(returned);
  }

  function doStep_getList(data)
  {
    timeStart = new Date();
    list=data.split(";");
    for(i=0;i<list.length-1;i++)
    {
      tmp = $.ajax({
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"makeStatsDoAnalyze", imagesList:list[i] }
       }).responseText;

      pct=100*(i+1)/list.length;
      $("#iprogressbar_bg").css("width", pct+"%");
      $("#iprogressbar_fg").html(Math.round(pct)+"%");
    }

    tmp = $.ajax({
      type: "POST",
      url: "{/literal}{$datas.urlRequest}{literal}",
      async: false,
      data: { ajaxfct:"makeStatsConsolidation" }
     }).responseText;


    timeEnd = new Date();
    timeElapsed=timeEnd.getTime()-timeStart.getTime();


    $("#dialog")
    .dialog("destroy")
    .html("")
    .get(0).removeAttribute('style');

    $("#dialog")
    .dialog(
      {
        resizable: false,
        width:480,
        height:120,
        modal: true,
        draggable:false,
        title: '{/literal}{"g003_updating_metadata"|@translate}{literal}',
        overlay:
        {
          backgroundColor: '#000',
          opacity: 0.5
        },
        open: function(event, ui)
        {
          bH=$("div.ui-dialog-buttonpane").get(0).clientHeight;
          $("#dialog").css('height', (this.clientHeight-bH)+"px");
        },
        buttons:
        {
          '{/literal}{"g003_ok"|@translate}{literal}':
            function()
            {
              $(this).dialog('destroy').html("").get(0).removeAttribute('style');
            }
        }
      }
    ).html("<br>{/literal}{'g003_analyze_is_finished'|@translate}{literal}&nbsp;("+displayTime(timeElapsed/1000)+")");

    getStatus();
    $("body").css("cursor", "default");
  }



</script>
{/literal}

<h3>{'g003_status_of_database'|@translate}</h3>

<div id="dialog"></div>

<div id="ianalyzestatus">
  <ul>
    <li>{'g003_loading'|@translate}</li>
    <li>{'g003_loading'|@translate}</li>
    <li>{'g003_loading'|@translate}</li>
  </ul>
</div>

<div id='ianalyzearea'>
  <fieldset>
    <legend>{'g003_update_metadata'|@translate}</legend>
      <form class="formtable">
        <div class="warning">
          <p style="font-weight:bold; font-size:+1;">{'g003_warning_on_analyze_0'|@translate}</p>
          <p>{'g003_warning_on_analyze_1'|@translate}</p>
          <p  style="font-weight:bold;">{'g003_warning_on_analyze_2'|@translate}</p>
          <p>{'g003_warning_on_analyze_3'|@translate}</p>
        </div>

        <label>
          <input type="radio" value="caddieAdd" name="fAMD_analyze_action" id="ianalyze_action2" checked>&nbsp;
          {'g003_analyze_caddie_add_pictures'|@translate}
        </label><br>

        <label>
          <input type="radio" value="caddieReplace" name="fAMD_analyze_action" id="ianalyze_action3">&nbsp;
          {'g003_analyze_caddie_replace_pictures'|@translate}
        </label><br>


        <label>
          <input type="radio" value="notAnalayzed" name="fAMD_analyze_action" id="ianalyze_action0">&nbsp;
          {'g003_analyze_not_analyzed_pictures'|@translate}
        </label><br>

        <label>
          <input type="radio" value="all" name="fAMD_analyze_action" id="ianalyze_action1">&nbsp;
          {'g003_analyze_all_pictures'|@translate}
        </label><br>

        <br>
        {'g003_setting_nb_items_per_request'|@translate}&nbsp;
        <input type="hidden" id="iamd_NumberOfItemsPerRequest" value="{$datas.NumberOfItemsPerRequest}">
        <div id="iamd_nb_item_per_request_slider"></div>
        <div id="iamd_nb_item_per_request_display"></div>
        <br><br>

        <input type="button" value="{'g003_analyze'|@translate}" onclick="doAnalyze();">

      </form>
  </fieldset>
</div>




<script type="text/javascript">
  init();
</script>

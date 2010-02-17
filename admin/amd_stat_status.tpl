{literal}
<script type="text/javascript">

  function getStatus()
  {
    $.get("{/literal}{$datas.urlRequest}{literal}", { ajaxfct:"makeStatsGetStatus" },
      function (data)
      {
        list=data.split(";");
        $("#ianalyzestatus").html("<ul><li>"+list[0]+"</li><li>"+list[1]+"</li><li>"+list[2]+"</li></ul>");
      }
    );
  }

  function doAnalyze()
  {
    $("body").css("cursor", "wait");

    mode="all";
    modeLabel="{/literal}{'g003_analyze_all_pictures'|@translate}{literal}";
    if($("#ianalyze_action0").get(0).checked)
    {
      mode="notAnalyzed";
      modeLabel="{/literal}{'g003_analyze_not_analyzed_pictures'|@translate}{literal}";
    }

    doAnalyze="<fieldset><legend>{/literal}{'g003_updating_metadata'|@translate}{literal}&nbsp;("+modeLabel+")</legend>"+
      "<form class='formtable'>"+
      "<div id='iprogressbar_contener'>"+
      "<span id='iprogressbar_bg' style='width:0%;'>&nbsp;</span>"+
      "<span id='iprogressbar_fg'>0%</span>"+
      "</div><div id='iprogress'>{/literal}{'g003_analyze_in_progress'|@translate}{literal}</div></form></fieldset>";

    $("#ianalyzearea").html(doAnalyze);

    $.get("{/literal}{$datas.urlRequest}{literal}", { ajaxfct:"makeStatsGetList", selectMode:mode }, doStep_getList);
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

      pct=100*(i+1)/(list.length+2);
      $("#iprogressbar_bg").css("width", pct+"%");
      $("#iprogressbar_fg").html(Math.round(pct)+"%");
    }

    for(j=0;j<3;j++)
    {
      tmp = $.ajax({
        type: "POST",
        url: "{/literal}{$datas.urlRequest}{literal}",
        async: false,
        data: { ajaxfct:"makeStatsConsolidate", step:j }
       }).responseText;

      pct=100*(i+j+1)/(list.length+2);
      $("#iprogressbar_bg").css("width", pct+"%");
      $("#iprogressbar_fg").html(Math.round(pct)+"%");
    }

    timeEnd = new Date();
    timeElapsed=timeEnd.getTime()-timeStart.getTime();
    $("#iprogress").html("{/literal}{'g003_analyze_is_finished'|@translate}{literal}&nbsp;("+displayTime(timeElapsed/1000)+")");
    getStatus();
    $("body").css("cursor", "default");
  }


</script>
{/literal}


<h3>{'g003_status_of_database'|@translate}</h3>

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
        <label>
          <input type="radio" value="notAnalayzed" name="fAMD_analyze_action" id="ianalyze_action0" checked>&nbsp;
          {'g003_analyze_not_analyzed_pictures'|@translate}
        </label><br>
        <label>
          <input type="radio" value="all" name="fAMD_analyze_action" id="ianalyze_action1">&nbsp;
          {'g003_analyze_all_pictures'|@translate}
        </label><br>
        <div class="warning">
          <p style="font-weight:bold; font-size:+1;">{'g003_warning_on_analyze_0'|@translate}</p>
          <p>{'g003_warning_on_analyze_1'|@translate}</p>
          <ul>
            <li>{'g003_warning_on_analyze_2'|@translate}</li>
            <li>{'g003_warning_on_analyze_3'|@translate}</li>
          </ul>
          <p  style="font-weight:bold;">{'g003_warning_on_analyze_4'|@translate}</p>
          <p>{'g003_warning_on_analyze_5'|@translate}</p>
        </div>
        <input type="button" value="{'g003_analyze'|@translate}" onclick="doAnalyze();">
      </form>
  </fieldset>
</div>

<script type="text/javascript">
  getStatus();
</script>

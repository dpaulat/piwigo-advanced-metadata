{literal}
<script type='text/javascript'>
  $.ajax(
    {
      type: "POST",
      url: "{/literal}{$datas.urlRequest}{literal}",
      async: true,
      data: { ajaxfct:"public.makeStats.doPictureAnalyze", token:'{/literal}{$token}{literal}', id:{/literal}{$datas.id}{literal} }
    }
  );
</script>
{/literal}

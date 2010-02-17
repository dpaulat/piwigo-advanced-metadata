<table class="littlefont">
  {foreach from=$datas key=name item=data}
  <tr onclick="loadTagDetail('{$data.tagId}');">
    <td style="width:35%;min-width:340px;"><input type="checkbox" id="iNumId{$data.numId}" onclick="updateTagSelect('iNumId{$data.numId}')" {$data.tagChecked}>&nbsp;{$data.tagId}</td>
    <td>{$data.label}</td>
    <td width="80px">{$data.nb}</td>
    <td width="40px">{$data.pct}</td>
    <td width="110px">
      <div class="pctBar" style="width:{$data.pct}px;"></div>
    </td>
  </tr>
  {/foreach}
</table>

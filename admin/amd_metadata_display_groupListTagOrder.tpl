{foreach from=$datas key=name item=data}
<li id="g{$group}t{$data.numId}" class="gcBgTabSheet">
  <table class="tagListOrderItem">
    <tr>
      <td style="width:20px;">
        <span class='listMove' title="{'Drag to re-order'|@translate}"></span>
      </td>
      <td style="width:30%;">{$data.tagId}</td>
      <td>{$data.name}</td>
      {if $data.pct!=''}
        <td style="width:35px;text-align:right;">{$data.nbItems}</td>
        <td style="width:50px;text-align:right;">{$data.pct}%</td>
        <td style="width:104px;"><span class="pctBar{$themeconf.name}" style="display:inline-block;width:{$data.pct}px;"></td>
      {/if}
    </tr>
  </table>
</li>
{/foreach}

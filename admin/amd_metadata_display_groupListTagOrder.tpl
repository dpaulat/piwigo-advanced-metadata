{foreach from=$datas key=name item=data}
<li id="g{$group}t{$data.numId}">
  <table class="tagListOrderItem">
    <tr>
      <td style="width:20px;"><img src="{$themeconf.admin_icon_dir}/cat_move.png" class="button drag_button" alt="{'Drag to re-order'|@translate}" title="{'Drag to re-order'|@translate}"/></td>
      <td style="width:30%;">{$data.tagId}</td>
      <td>{$data.name}</td>
      <td style="width:35px;text-align:right;">{$data.nbItems}</td>
      <td style="width:50px;text-align:right;">{$data.pct}%</td>
      <td style="width:104px;"><span class="pctBar" style="display:inline-block;width:{$data.pct}px;"></td>
    </tr>
  </table>
</li>
{/foreach}
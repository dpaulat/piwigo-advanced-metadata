<table class="littlefont">
  {foreach from=$datas item=data}
  <tr>
    <td style="width:35%;min-width:340px;">{$data.tagId}</td>
    <td>{$data.label}</td>
    <th style="width:15%;">{$data.numOfRules}</th>
    <td width="40px">
      <span class="buttonEdit"
            title="{'g003_edit'|@translate}"
            onclick='udm.editMetadata({$data.numId});'></span>
      <span class="buttonDelete"
            title="{'g003_delete'|@translate}"
            onclick='udm.deleteMetadata({$data.numId});'></span>
    </td>
  </tr>
  {/foreach}
</table>

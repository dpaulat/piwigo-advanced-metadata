{foreach from=$datas.groups key=name item=data}
  <li id="iGroupId{$data.id}" class="groupItems gcBgPage">
    <span class='listMove' title="{'Drag to re-order'|@translate}"></span>
    <input type="hidden" id="iGroupName{$data.id}" value="{$data.name}">
    {$data.name}

    <a onclick="deleteGroup('{$data.id}');">
      <span class='buttonDelete button' title="{'g003_click_to_delete_group'|@translate}"></span>
    </a>

    <a onclick="editGroup('{$data.id}');">
      <span class='buttonEdit button' title="{'g003_click_to_edit_group'|@translate}"></span>
    </a>

    <a onclick="manageGroup('{$data.id}', '');">
      <span class='buttonPreferences button' title="{'g003_click_to_manage_group'|@translate}"></span>
    </a>


    <div name="fGroupId{$data.id}_content" id="iGroupId{$data.id}_content" style="visibility:hidden;height:0px;" class="groupTags">
      <a onclick="editGroupList('{$data.id}');" class="button editGroupListButton">
        <span class='buttonEdit' title="{'g003_click_to_manage_list'|@translate}"></span>
      </a>
      <ul id="iGroupId{$data.id}_tags" class="tagListOrder g{$data.id}_connectedSortableTags">
      </ul>
    </div>
  </li>
{/foreach}

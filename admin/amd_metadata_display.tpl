

{literal}
<script type="text/javascript">

  function init()
  {
    $("#iGroups")
    .html(
      $.ajax(
        {
          type: "POST",
          url: "{/literal}{$datas.urlRequest}{literal}",
          async: false,
          data: { ajaxfct:"groupGetList" }
        }
      ).responseText
    )
    .sortable(
      {
        connectWith: '.connectedSortable',
        cursor: 'move',
        opacity:0.6,
        items: 'li.groupItems',
        axis:'y',
        tolerance:'intersect',
        start: function (event, ui)
          {
            manageGroup(event.originalTarget.id.substr(8), 'n');
          },
        update: function (event, ui)
          {
            list="";
            $("#iGroups li").each(
                function ()
                {
                  if(list!="") list+=";";
                  list+=this.id.substr(8);
                }
              );
            list=list.substr(0, list.lastIndexOf(';'));
            $.ajax(
              {
                type: "POST",
                url: "{/literal}{$datas.urlRequest}{literal}",
                async: false,
                data: { ajaxfct:"groupSetOrder", listGroup:list }
              }
            ).responseText;
          }
      }
    );
  }

  function initSubList(groupId)
  {
    $("#iGroupId"+groupId+"_tags").sortable(
      {
        connectWith: '.g'+groupId+'_connectedSortableTags',
        cursor: 'move',
        opacity:0.6,
        items: 'li',
        axis:'y',
        tolerance:'pointer',
        containment: 'parent',
        update: function (event, ui)
          {
            list="";
            $("#iGroupId"+groupId+"_tags li").each(
                function ()
                {
                  if(list!="") list+=";";
                  list+=this.id.substr(this.id.indexOf('t')+1);
                }
              );
            list=list.substr(0, list.lastIndexOf(';'));
            $.ajax(
              {
                type: "POST",
                url: "{/literal}{$datas.urlRequest}{literal}",
                async: false,
                data: { ajaxfct:"groupSetOrderedTagList", id:groupId, listTag:list }
              }
            ).responseText;
          }

      }
    );
  }

  function loadGroupTags(groupId)
  {
    $("#iGroupId"+groupId+"_tags").html(
      $.ajax(
        {
          type: "POST",
          url: "{/literal}{$datas.urlRequest}{literal}",
          async: false,
          data: { ajaxfct:"groupGetOrderedTagList", id:groupId }
        }
      ).responseText
    );
  }

  function manageGroup(groupId, force)
  {
    if(force=='y')
    {
      currentVisibility='hidden';
    }
    else if(force=='n')
    {
      currentVisibility='visible';
    }
    else
    {
      currentVisibility=$("#iGroupId"+groupId+"_content").css('visibility');
    }


    if(currentVisibility=='visible')
    {
      $("div[name=fGroupId"+groupId+"_content]").css(
       {
         visibility:"hidden",
         height:"0px"
       }
      );
    }
    else
    {
      $("div[name=fGroupId"+groupId+"_content]").css(
       {
         visibility:"visible",
         height:"auto"
       }
      );
      loadGroupTags(groupId);
    }
  }

  function deleteGroup(groupId)
  {
    if(groupId!="")
    {
      groupName=$("#iGroupName"+groupId).val();
      $("#dialog")
      .html("")
      .dialog(
        {
          resizable: true,
          width:480,
          height:120,
          modal: true,
          draggable:true,
          title: "{/literal}{'g003_deleting_a_group'|@translate}{literal}",
          overlay:
          {
            backgroundColor: '#000',
            opacity: 0.5
          },
          buttons:
          {
            '{/literal}{"g003_yes"|@translate}{literal}':
              function()
              {
                $.ajax(
                  {
                    type: "POST",
                    url: "{/literal}{$datas.urlRequest}{literal}",
                    async: false,
                    data: { ajaxfct:"groupDelete", id:groupId }
                  }
                ).responseText;
                $("#iGroups").html(
                  $.ajax(
                    {
                      type: "POST",
                      url: "{/literal}{$datas.urlRequest}{literal}",
                      async: false,
                      data: { ajaxfct:"groupGetList" }
                    }
                  ).responseText
                );
                $(this).dialog('destroy').html("").get(0).removeAttribute('style');
              },
            '{/literal}{"g003_no"|@translate}{literal}':
              function()
              {
                $(this).dialog('destroy').html("").get(0).removeAttribute('style');
              }
          }
        }
      )
      .html("<div class='dialogForm'>{/literal}{'g003_confirm_group_delete'|@translate}{literal}</div>".replace("%s", "<i>"+groupName+"</i>", "gi"));
    }
  }

  function editGroup(groupId)
  {
    if(groupId=="")
    {
      dialogTitle="{/literal}{'g003_adding_a_group'|@translate}{literal}";
    }
    else
    {
      dialogTitle="{/literal}{'g003_editing_a_group'|@translate}{literal}";
    }

    $("#dialog")
    .html("")
    .dialog(
      {
        resizable: true,
        width:480,
        height:120,
        modal: true,
        draggable:true,
        title: dialogTitle,
        overlay:
        {
          backgroundColor: '#000',
          opacity: 0.5
        },
        buttons:
        {
          '{/literal}{"g003_ok"|@translate}{literal}':
            function()
            {
              list="";
              $("form.dialogForm input[type='hidden']").each(
                function ()
                {
                  if(list!="") list+="\n";
                  list+=this.id.substr(6)+"="+this.value;
                }
              )

              $.ajax(
                {
                  type: "POST",
                  url: "{/literal}{$datas.urlRequest}{literal}",
                  async: false,
                  data: { ajaxfct:"groupSetNames", id:groupId, listNames:list }
                }
              ).responseText;
              $("#iGroups").html(
                $.ajax(
                  {
                    type: "POST",
                    url: "{/literal}{$datas.urlRequest}{literal}",
                    async: false,
                    data: { ajaxfct:"groupGetList" }
                  }
                ).responseText
              );
              $(this).dialog('destroy').html("").get(0).removeAttribute('style');
            },
          '{/literal}{"g003_cancel"|@translate}{literal}':
            function()
            {
              $(this).dialog('destroy').html("").get(0).removeAttribute('style');
            }
        }
      }
    )
    .html(
      $.ajax(
        {
          type: "POST",
          url: "{/literal}{$datas.urlRequest}{literal}",
          async: false,
          data: { ajaxfct:"groupGetNames", id:groupId }
        }
      ).responseText
    );
  }


  function editGroupList(groupId)
  {
    $("#dialog")
    .html("")
    .dialog(
      {
        resizable: false,
        width:480,
        height:320,
        modal: true,
        draggable:true,
        title: '{/literal}{"g003_add_delete_tags"|@translate}{literal}',
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
              list="";
              $("#dialog table input").each(
                function ()
                {
                  if(this.checked)
                  {
                    if(list!="") list+=",";
                    list+=this.id.substr(6);
                  }
                }
              )

              $.ajax(
                {
                  type: "POST",
                  url: "{/literal}{$datas.urlRequest}{literal}",
                  async: false,
                  data: { ajaxfct:"groupSetTagList", id:groupId, listTag:list }
                }
              ).responseText;
              $(this).dialog('destroy').html("").get(0).removeAttribute('style');
              loadGroupTags(groupId);
            },
          '{/literal}{"g003_cancel"|@translate}{literal}':
            function()
            {
              $(this).dialog('destroy').html("").get(0).removeAttribute('style');
            }
        }
      }
    )
    .html(
      $.ajax(
        {
          type: "POST",
          url: "{/literal}{$datas.urlRequest}{literal}",
          async: false,
          data: { ajaxfct:"groupGetTagList", id:groupId }
        }
      ).responseText
    );
  }

</script>
{/literal}


<h3>{'g003_display_management'|@translate}</h3>

<div class="addGroup">
  <a onclick="editGroup('');">{'g003_add_a_group'|@translate}</a>
</div>


<div id="dialog"></div>


<div id="iGroupContainer">
  <ul class="connectedSortable" id="iGroups">
  </ul>
</div>


<script type="text/javascript">
  init();
  {foreach from=$datas.groups key=name item=data}
  initSubList({$data.id});
  {/foreach}
</script>
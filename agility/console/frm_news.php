<!-- 
frm_news.php

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<?php
require_once(__DIR__ . "/../server/tools.php");
require_once(__DIR__ . "/../server/auth/Config.php");
$config =Config::getInstance();
?>
<div id="dlg_news" class="easyui-window">
<div id="dlg_news_layout" class="easyui-layout" data-options="fit:true">
    <div data-options="region:'north',border:false" style="width:100%;height:15%;display:inline-block">
        <span style="display:inline-block;float:left;padding-left:50px;font-size:20px"><strong><br/>AgilityContest News</strong></span>
        <span style="display:inline-block;float:right;padding:5px">
            <img src="../images/AgilityContest.png" height="60" alt="AgilityContest Logo"/>
        </span>
    </div>
    <div data-options="region:'center',border:false" >
        <div id="news_panel" style="width:100%;padding:5px;background:#eeee"></div>
    </div>
    <div data-options="region:'south',border:false" style="width:100%;height:10%;text-align:center;">
        <br/>
        <a id="news-okButton" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-ok'"
           onclick="$('#dlg_news').window('close');"><?php _e('Accept'); ?></a>
        <br/>
    </div>
</div>
</div>

<script type="text/javascript">
        $('#news_panel').panel({
            href:"/agility/ajax/serverRequest.php",
            fit:true,
            width:'auto',
            noheader:true,
            border:false,
            closable:false,
            collapsible:false,
            collapsed:false,
            resizable:false,
            callback: null,
            onBeforeLoad:function(params) {
                // let timestamp in format 'Y-m-d_H:i:s'
                var a=ac_authInfo.LastLogin.split(" "); // ' YYYY-mm-dd HH:ii:ss on XXXX'
                params.Operation='getNews';
                params.timestamp=a[1]+"_"+a[2];
                params.Serial=ac_regInfo.Serial;
                params.Revision=ac_config.version_date;
                return true;
            },
            onLoad: function() {
                var content=$('#news_panel').panel('body')[0].innerHTML;
                if (content==="<p>No news</p>")  { // on no news just close window
                    setTimeout(function() { $('#dlg_news').window('close')},500);
                } else {
                    $('#dlg_news_layout').layout('panel','center').panel(resize,{height:'auto'});
                }
            }
        });

        $('#dlg_news').window({
            title: "<?php _e('AgilityContest News');?>",
            width:700,
            height:550,
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:false,
            modal:true,
            iconCls: 'icon-web',
            onOpen: function() { $('#last_login').html(ac_authInfo.LastLogin);  },
            onClose: function() {loadContents('../console/frm_main.php','');
            }
        });

        $('#dlg_news_layout').layout();

        addTooltip($('#news-okButton').linkbutton(),'<?php _e("Close Window"); ?>');

</script>
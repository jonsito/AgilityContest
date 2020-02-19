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

<div id="dlg_about" class="easyui-window" style="width:750px;padding:10px">
	<img src="../images/AgilityContest.png"
		width="150" height="100" alt="AgilityContest Logo" 
		style="border:1px solid #000000;margin:10px;float:right;padding:5px">
	<hr />
    <div id="news_panel"></div>
    <p>
        last login: <span id="last_login"> </span>
    </p>
</div>
<script type="text/javascript">
        $('#news_panel').panel({
            href:"/agility/ajax/serverRequest.php"+
                "?Operation=getNews"+
                "&timestamp="+ac_authInfo.LastLogin+
                "&Serial="+ac_regInfo.Serial,
            fit:true,
            noheader:true,
            border:false,
            closable:false,
            collapsible:false,
            collapsed:false,
            resizable:false,
            callback: null,
            onLoad: function() {
                var content=$('#news_panel').panel('body')[0].innerHTML;
                if (content==="<p>No news</p>")  { // on no news just close window
                    setTimeout(function() { $('#dlg_about').window('close')},500);
                }
            }
        });

        $('#dlg_about').window({
            title: "<?php _e('AgilityContest News');?>",
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:true,
            modal:true,
            iconCls: 'icon-web',
            onOpen: function() { $('#last_login').html(ac_authInfo.LastLogin);  },
            onClose: function() {loadContents('../console/frm_main.php','');
            }
        })
</script>
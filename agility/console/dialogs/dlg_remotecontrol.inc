<!-- 
dlg_remotecontrol.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>
    <!-- ventana de control remoto para los cronos/videomarcadores/livestream definidos en una o varias sesiones -->
    <div id="remote-dialog" style="width:720px;height:480px;padding:5px;">
        <div id="remote-accordion">
            <div title="<?php _e('Videowall Screens');?>">
                <p>List of currently active Videowall screens</p>
                <table id="remote-videowall-datagrid" style="width:60%"></table>
            </div>
            <div title="<?php _e('Live Streaming windows');?>">
                <p>List of currently active livestream windows</p>
                <table id="remote-livestream-datagrid" style="width:60%"></table>
            </div>
            <div title="<?php _e('Chronometer displays');?>">
                <p>List of currently active Video-chronometer displays</p>
                <table id="remote-chronometer-datagrid" style="width:60%"></table>
            </div>
        </div>
    </div>

    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="remote-dlg-buttons" style="width:100%;display:inline-block">
        <span style="float:left;">
        <a id="remote-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-reload'" onclick="reloadRemoteClientList();"><?php _e('Refresh'); ?></a>
        </span>
        <span style="float:right;">
        <a id="remote-okBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-ok'" onclick=" $('#remote-dialog').dialog('close');"><?php _e('Done'); ?></a>
        </span>
    </div>

	<script type="text/javascript">
		// declaraciones
		$('#remote-dialog').dialog({
            title: '<?php _e("Video control dialog");?>',
            closed:true,
            buttons:'#remote-dlg-buttons',
            iconCls:'icon-video-tv',
            modal:true
		});
        // on ESC key close session
        $('#remote-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#remote-dialog').dialog('close'); 	}
        });
        $('#remote-accordion').accordion({
            animate:false,
            fit:true
        });

        // tablas de displays activos

        // datos de la tabla de video marcadores
        $('#remote-videowall-datagrid').datagrid({
            // datos del panel padre asociado
            fit: false,
            border: true,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            // datos de la conexion ajax
            url: '/agility/server/database/sessionFunctions.php',
            queryParams: { Operation:'getClients',SessionType:'videowall' },
            loadMsg: '<?php _e('Updating videowall session list'); ?>',
            method: 'get',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: false,
            multiSort: false,
            remoteSort: false,
            idField:'SessionName',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:40, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:10, sortable:true,title:'<?php _e('Ring'); ?>' },// ring name
                { field:'View',		    width:50, sortable:true,title:'<?php _e('View mode'); ?>' },
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler
        });

        // datos de la tabla de video marcadores
        $('#remote-livestream-datagrid').datagrid({
            // datos del panel padre asociado
            fit: false,
            border: true,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            // datos de la conexion ajax
            url: '/agility/server/database/sessionFunctions.php',
            queryParams: { Operation:'getClients',SessionType:'livestream' },
            loadMsg: '<?php _e('Updating videowall session list'); ?>',
            method: 'get',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: false,
            multiSort: false,
            remoteSort: false,
            idField:'SessionName',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:40, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:10, sortable:true,title:'<?php _e('Ring'); ?>' },// ring name
                { field:'View',		    width:50, sortable:true,title:'<?php _e('View mode'); ?>' },
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler
        });

        // datos de la tabla de video marcadores
        $('#remote-chronometer-datagrid').datagrid({
            // datos del panel padre asociado
            fit: false,
            border: true,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            // datos de la conexion ajax
            url: '/agility/server/database/sessionFunctions.php',
            queryParams: { Operation:'getClients',SessionType:'chronometer' },
            loadMsg: '<?php _e('Updating videowall session list'); ?>',
            method: 'get',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: false,
            multiSort: false,
            remoteSort: false,
            idField:'SessionName',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:40, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:10, sortable:true,title:'<?php _e('Ring'); ?>' },// ring name
                { field:'View',		    width:50, sortable:true,title:'<?php _e('View mode'); ?>' },
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler
        });

        // tooltips de los botones
        addTooltip($('#remote-reloadBtn').linkbutton(),'<?php _e("Reload and update active client lists"); ?>');
        addTooltip($('#remote-okBtn').linkbutton(),'<?php _e("End video display remote control session. Close window"); ?>');
    </script>
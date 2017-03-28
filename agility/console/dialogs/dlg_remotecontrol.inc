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
require_once(__DIR__ . "/../../server/auth/AuthManager.php");
$config =Config::getInstance();
$am=new AuthManager("Chrono");
?>
    <!-- ventana de control remoto para los cronos/videomarcadores/livestream definidos en una o varias sesiones -->
    <div id="remote-dialog" style="width:750px;height:400px;padding:5px;">
        <div id="remote-accordion">
            <div title="<?php _e('Videowall Screens');?>" data-options="iconCls:'icon-video-tv'">
<?php if( $am->allowed(ENABLE_VIDEOWALL) ) { ?>
                <span style="float:left;display:inline-block;width:55%;padding:5px">
                    <p>
                        <?php _e('List of currently active Videowall displays');?><br/><br/>
                        <?php _e('Choose display(s) to work with');?><br/>
                        <?php _e('and invoke desired operation');?>
                    </p>
                    <table id="remote-videowall-datagrid" style="width:100%"></table>
                </span>
                <span style="float:right;display:inline-block;width:40%;padding:5px">
                    <strong><?php _e('Parameters for display change');?></strong>
                    <form id="remote-videowall-form" name="remote-videowall-form">
                        <?php _e('Select');?>
                        <input type="checkbox" id="remote-videowall-all"/>
                        <label for="remote-videowall-all"><?php _e('All');?></label>
                        <input type="checkbox" id="remote-videowall-none"/>
                        <label for="remote-videowall-none"><?php _e('None');?></label><br/>
                        <label for="remote-videowall-ring"><?php _e('New ring');?>:</label>
       		            <select id="remote-videowall-ring" name="Ring" style="width:200px"></select><br/>
                        <label for="remote-videowall-view"><?php _e('New view');?>:</label>
                        <select id="remote-videowall-view" style="width:200px"> <!-- combobox -->
                            <optgroup label="<?php _e('Video Wall');?> ">
					            <option value="1"><?php _e('Training session'); ?></option>
                                <option value="0"><?php _e('Starting order'); ?></option>
					            <option value="2"><?php _e('Partial scores'); ?></option>
					            <option value="4"><?php _e('Final scores'); ?></option>
                            </optgroup>
				            <optgroup label="<?php _e('Combo view');?> ">
					            <option value="7"><?php _e('Call to ring '); ?> / <?php _e('Partial scores'); ?></option>
					            <option value="8"><?php _e('Call to ring '); ?> / <?php _e('Final scores'); ?></option>
				            </optgroup>
				            <optgroup label="<?php _e('Simplified');?> ">
					            <option value="6"><?php _e('Training session'); ?> (<?php _e('simplified'); ?>)</option>
					            <option value="3"><?php _e('Partial Scores'); ?> (<?php _e('simplified'); ?>)</option>
					            <option value="9"><?php _e('Final Scores'); ?> (<?php _e('simplified'); ?>)</option>
				            </optgroup>
                            <optgroup label="<?php _e('Extra info'); ?> ">
                                <option value="5"><?php _e('Advertising videos'); ?></option>
                            </optgroup>
                        </select><br/>
                        <span style="display:inline-block;width:200px">&nbsp;</span>
                        <a id="remote-videowall-sendBtn" href="#" class="easyui-linkbutton"
                             data-options="iconCls:'icon-on'" onclick="alert('send');"><?php _e('Send'); ?></a>
                        <br>
                        <strong><?php _e('Simplified videowall controls');?></strong>
                        <span style="display:inline-block;width:200px"><?php _e("Font family");?>:</span>
                        <a id="remote-videowall-ffprevBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-prev'" onclick="alert('ff-prev');"></a>
                        <a id="remote-videowall-ffnextBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-next'" onclick="alert('ff-next');"></a>
                        <br/>
                        <span style="display:inline-block;width:200px"><?php _e("Font size");?>:</span>
                        <a id="remote-videowall-fsprevBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-prev'" onclick="alert('fs-prev');"></a>
                        <a id="remote-videowall-fsnextBtn" href="#" class="easyui-linkbutton" data-options="iconCls:'icon-next'" onclick="alert('fs-next');"></a>
                    </form>
                </span>
<?php } else { ?>
                <p><?php _e('Current license permissions does not allow videowall operations');?></p>
                <img src="/agility/images/sad_dog.png" alt="triste"/>
                <table id="remote-videowall-datagrid" style="display:none"></table>
                <select id="remote-videowall-ring" name="Ring" style="display:none"></select>
<?php } ?>
            </div>
            <div title="<?php _e('Live Streaming windows');?>" data-options="iconCls:'icon-live'">
<?php if( $am->allowed(ENABLE_LIVESTREAM) ) { ?>
                <p><?php _e('List of currently active LiveStream screens');?></p>
                <table id="remote-livestream-datagrid" style="width:60%"></table>
<?php } else { ?>
                <p><?php _e('Current license permissions does not allow live stream operations');?></p>
                <img src="/agility/images/sad_dog.png" alt="triste"/>
                <table id="remote-livestreaml-datagrid" style="display:none"></table>
<?php } ?>
            </div>
            <div title="<?php _e('Chronometer displays');?>" data-options="iconCls:'icon-clock'">
<?php if( $am->allowed(ENABLE_CHRONO) ) { ?>
                <p><?php _e('List of currently active video-chronometers scoreboards');?></p>
                <table id="remote-chronometer-datagrid" style="width:60%"></table>
<?php } else { ?>
                <p><?php _e('Current license permissions does not allow chronometer operations');?></p>
                <img src="/agility/images/sad_dog.png" alt="triste"/>
                <table id="remote-chronometer-datagrid" style="display:none"></table>
<?php } ?>
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
            modal:true,
            onBeforeOpen: function(param) {
                reloadRemoteClientList();
                return true;
            }
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
            idField:'Name',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:40, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:20, sortable:true,title:'<?php _e('Ring'); ?>', formatter: formatRingName },// ring name
                { field:'View',		    width:40, sortable:true,title:'<?php _e('View mode'); ?>', formatter: formatVideowallView },
                { field:'Mode',		    hidden:true }, // no sense in videowall
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler
        });

        $('#remote-videowall-view').combobox({
            valueField:'value',
            panelHeight:'auto',
            editable:false
        });

        $('#remote-videowall-ring').combogrid({
            panelWidth: 400,
            panelHeight: 'auto',
            idField: 'ID',
            textField: 'Nombre',
            url: '/agility/server/database/sessionFunctions.php',
            queryParams: { Operation:'selectring', Hidden:0},
            method: 'get',
            mode: 'remote',
            required: true,
            rownumber: true,
            multiple: false,
            fitColumns: true,
            singleSelect: true,
            editable: false, // avoid keyboard deploy
            columns: [[
                { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
                { field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: '<?php _e('Name'); ?>' },
                { field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: '<?php _e('Description'); ?>' },
                { field:'Prueba',	    hidden:true },
                { field:'Jornada',	    hidden:true },
                { field:'Background',	hidden:true },
                { field:'LiveStream2',	hidden:true },
                { field:'LiveStream3',	hidden:true }
            ]]
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
            idField:'Name',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:40, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:20, sortable:true,title:'<?php _e('Ring'); ?>', formatter:formatRingName },// ring name
                { field:'View',		    width:40, sortable:true,title:'<?php _e('View mode'); ?>',formatter: formatLiveStreamView },
                { field:'Mode',		    hidden:true }, // do not show in livestream, but need to retrieve
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
            queryParams: { Operation:'getClients',SessionType:'chrono' },
            loadMsg: '<?php _e('Updating videowall session list'); ?>',
            method: 'get',
            pagination: false,
            rownumbers: false,
            fitColumns: true,
            singleSelect: false,
            multiSort: false,
            remoteSort: false,
            idField:'Name',
            columns: [[
                { field:'Source',		hidden:true },
                { field:'Name',	        width:60, sortable:true,title:'<?php _e('Display name'); ?>' },
                { field:'Session',	    width:40, sortable:true,title:'<?php _e('Ring'); ?>', formatter:formatRingName  },// ring name
                { field:'View',		    hidden:true }, // no sense in chrono
                { field:'Mode',		    hidden:true }, // no sense in chrono
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler
        });

        // tooltips de los botones
        $('#remote-videowall-ffnextBtn').linkbutton();
        $('#remote-videowall-ffprevBtn').linkbutton();
        $('#remote-videowall-fsnextBtn').linkbutton();
        $('#remote-videowall-fsprevBtn').linkbutton();
        addTooltip($('#remote-videowall-sendBtn').linkbutton(),'<?php _e("Switch selected displays to choosen ring/view"); ?>');
        addTooltip($('#remote-reloadBtn').linkbutton(),'<?php _e("Reload and update active client lists"); ?>');
        addTooltip($('#remote-okBtn').linkbutton(),'<?php _e("End video display remote control session. Close window"); ?>');
    </script>
<!-- 
dlg_remotecontrol.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
$am=AuthManager::getInstance("Chrono");
?>
    <!-- ventana de control remoto para los cronos/videomarcadores/livestream definidos en una o varias sesiones -->
    <div id="remote-dialog" style="width:750px;height:400px;padding:5px;">
        <div id="remote-accordion">
            <div title="<?php _e('Videowall Screens');?>" data-options="iconCls:'icon-video-tv'">
                <div style="display:<?php echo $am->allowed(ENABLE_VIDEOWALL)?'inherit':'none';?>">
                    <span style="float:left;display:inline-block;width:55%;padding:5px">
                        <p>
                            <?php _e('List of currently active Videowall displays');?><br/><br/>
                            <?php _e('Choose display(s) to work with');?><br/>
                            <?php _e('and invoke desired operation');?>
                        </p>
                        <table id="remote-videowall-datagrid" style="width:100%"></table>
                    </span>
                    <span style="float:right;display:inline-block;width:40%;padding:5px">
                        <br/>
                        <strong><?php _e('Parameters for display change');?></strong>
                        <form id="remote-videowall-form" name="remote-videowall-form">
                            <?php _e('Select');?>
                            <input type="checkbox" id="remote-videowall-all" onclick="remoteVideowallAllorNone(1);"/>
                            <label for="remote-videowall-all"><?php _e('All');?></label>
                            <input type="checkbox" id="remote-videowall-none" onclick="remoteVideowallAllorNone(0);"/>
                            <label for="remote-videowall-none"><?php _e('None');?></label><br/>

                            <label for="remote-videowall-ring" style="display:inline-block;width:100px"><?php _e('New ring');?>:</label>
       	    	            <select id="remote-videowall-ring" name="Ring" style="width:175px"></select><br/>

                            <label for="remote-videowall-view" style="display:inline-block;width:100px"><?php _e('New view');?>:</label>
                            <select id="remote-videowall-view" style="width:175px"> <!-- combobox -->
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
                                    <option value="10"><?php _e('Standby screen'); ?></option>
                                </optgroup>
                            </select><br/>
                            <input type="hidden" id="remote-videowall-mode" name="remote-videowall-mode" value="0"/>
                            <span style="display:inline-block;width:175px">&nbsp;</span>
                            <a id="remote-videowall-sendBtn" href="#" class="easyui-linkbutton"
                                data-options="iconCls:'icon-reload'" onclick="remoteSendChangeEvent('#remote-videowall')"><?php _e('Switch'); ?></a>
                            <br/>

                            <strong><?php _e('Video selection');?></strong><br/>
                            <label for="remote-videowall-playlist" style="display:inline-block;width:100px"><?php _e('Video');?>:</label>
       	    	            <select id="remote-videowall-playlist" name="PlayList" style="width:175px"></select>
                            <br/>

                            <br/>
                            <strong><?php _e('Simplified videowall controls');?></strong>
                            <span style="display:inline-block;width:200px"><?php _e("Font family");?>:</span>
                            <a id="remote-videowall-ffprevBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-prev'" onclick="remoteSendKeyValueEvent('#remote-videowall',EVTCMD_SETFONTFAMILY,-1);"></a>
                            <a id="remote-videowall-ffnextBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-next'" onclick="remoteSendKeyValueEvent('#remote-videowall',EVTCMD_SETFONTFAMILY,1);"></a>
                            <br/>
                            <span style="display:inline-block;width:200px"><?php _e("Font size");?>:</span>
                            <a id="remote-videowall-fsprevBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-prev'" onclick="remoteSendKeyValueEvent('#remote-videowall',EVTCMD_SETFONTSIZE,-0.1);"></a>
                            <a id="remote-videowall-fsnextBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-next'" onclick="remoteSendKeyValueEvent('#remote-videowall',EVTCMD_SETFONTSIZE,0.1);"></a>
                            <br/>

                            <br/>
                            <strong><?php _e('Videowall message handling');?></strong><br/>
                            <label for="remote-videowall-msg" style="display:inline-block;width:100px"><?php _e('Message');?>:</label>
                            <input id="remote-videowall-msg" name="remote-videowall-msg" value="" style="width:175px">
                            <br/><br/>
                            <div style="width:100%;display:inline-block;">
                                <span style="float:left;width:50%"><?php _e("Duration (secs)");?>:</span>
                                <span style="float:right;width:40%;padding-right:10px;padding-left:5px;">
                                    <input id="remote-videowall-msgtimeout" name="remote-videowall-msgtimeout"/>
                                </span>
                            </div>
                            <span style="display:inline-block;width:175px">&nbsp;</span>
                            <a id="remote-videowall-msgBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-alert'" onclick="remoteSendMessageEvent('#remote-videowall')"><?php _e('Show'); ?></a>
                        </form>
                    </span>
                </div>
                <div style="display:<?php echo $am->allowed(ENABLE_VIDEOWALL)?'none':'inherit';?>">
                    <p><?php _e('Current license permissions does not allow videowall operations');?></p>
                    <img src="../../images/sad_dog.png" alt="triste"/>
                </div>
            </div>
            <div title="<?php _e('Live Streaming windows');?>" data-options="iconCls:'icon-live'">
                <div style="display:<?php echo $am->allowed(ENABLE_LIVESTREAM)?'inherit':'none';?>">
                    <span style="float:left;display:inline-block;width:55%;padding:5px">
                        <p>
                            <?php _e('List of currently active LiveStream windows');?><br/><br/>
                            <?php _e('Choose window(s) to work with');?><br/>
                            <?php _e('and invoke desired operation');?>
                        </p>
                        <table id="remote-livestream-datagrid" style="width:100%"></table>
                    </span>

                    <span style="float:right;display:inline-block;width:40%;padding:5px">
                        <br/>
                        <strong><?php _e('Parameters for window change');?></strong>
                        <form id="remote-livestream-form" name="remote-livestream-form">
                            <?php _e('Select');?>
                            <input type="checkbox" id="remote-livestream-all" onclick="remoteLivestreamAllorNone(1);"/>
                            <label for="remote-livestream-all"><?php _e('All');?></label>
                            <input type="checkbox" id="remote-livestream-none" onclick="remoteLivestreamAllorNone(0);"/>
                            <label for="remote-livestream-none"><?php _e('None');?></label><br/>

                            <label for="remote-livestream-ring"><?php _e('New ring');?>:</label>
           		            <select id="remote-livestream-ring" name="Ring" style="width:200px"></select><br/>

                            <label for="remote-livestream-view"><?php _e('New view');?>:</label>
                            <select id="remote-livestream-view" style="width:200px"> <!-- combobox -->
				                <option value="4"><?php _e('Training session'); ?></option>
				                <option value="0"><?php _e('Starting order'); ?></option>
				                <option value="1"><?php _e('Live Stream'); ?></option>
				                <option value="2"><?php _e('Partial scores'); ?></option>
    				            <option value="3"><?php _e('Final scores'); ?></option>
                            </select><br/>
                            <input type="hidden" id="remote-livestream-mode" name="remote-livestream-mode" value="0"/>
                            <span style="display:inline-block;width:200px">&nbsp;</span>
                            <a id="remote-livestream-sendBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-reload'" onclick="remoteSendChangeEvent('#remote-livestream');"><?php _e('Switch'); ?></a>
                            <br/> <br/>
                            <strong><?php _e('Live Stream controls');?></strong>
                            <span style="display:inline-block;width:200px"><?php _e("On Screen Display");?>:</span>
                            <a id="remote-livestream-onBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-on'" onclick="remoteSendKeyValueEvent('#remote-livestream',EVTCMD_ENABLEOSD,1);"></a>
                            <a id="remote-livestream-offBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-off'" onclick="remoteSendKeyValueEvent('#remote-livestream',EVTCMD_ENABLEOSD,0);"></a>
                            <br/>&nbsp;<br/>
                            <div style="width:100%;display:inline-block;">
                                <span style="float:left;width:50%"><?php _e("OSD/video sync delay");?>:</span>
                                <span style="float:right;width:40%;padding-right:5px;padding-left:5px;">
                                    <input id="remote-livestream-delay" name="remote-livestream-delay"/>
                                </span>
                            </div>
                            <br/>&nbsp;<br/>
                            <div style="width:100%;display:inline-block;">
                                <span style="float:left;width:50%"><?php _e("OSD Transparency level");?>:</span>
                                <span style="float:right;width:40%;padding-right:5px;padding-left:5px;">
                                    <input id="remote-livestream-alpha" name="remote-livestream-alpha"/>
                            </div>
                        </form>
                    </span>
                </div>

                <div style="display:<?php echo $am->allowed(ENABLE_LIVESTREAM)?'none':'inherit';?>">
                    <p><?php _e('Current license permissions does not allow live stream operations');?></p>
                    <img src="../../images/sad_dog.png" alt="triste"/>
                </div>
            </div>
            <div title="<?php _e('Chronometer displays');?>" data-options="iconCls:'icon-clock'">
                <div style="display:<?php echo $am->allowed(ENABLE_CHRONO)?'inherit':'none';?>">
                    <span style="float:left;display:inline-block;width:55%;padding:5px">
                        <p><?php _e('List of currently active video-chronometers scoreboards');?></p>
                      <table id="remote-chronometer-datagrid" style="width:100%"></table>
                    </span>
                        <span style="float:right;display:inline-block;width:40%;padding:5px">
                        <br/>
                        <strong><?php _e('Parameters for chrono display change');?></strong>
                        <form id="remote-chronometer-form" name="remote-chronometer-form">
                            <?php _e('Select');?>
                            <input type="checkbox" id="remote-chronometer-all" onclick="remoteChronometerAllorNone(1);"/>
                            <label for="remote-chronometer-all"><?php _e('All');?></label>
                            <input type="checkbox" id="remote-chronometer-none" onclick="remoteChronometerAllorNone(0);"/>
                            <label for="remote-chronometer-none"><?php _e('None');?></label><br/>

                            <label for="remote-chronometer-ring"><?php _e('New ring');?>:</label>
           		            <select id="remote-chronometer-ring" name="Ring" style="width:200px"></select>
                            <span style="display:none"><select id="remote-chronometer-view" name="View"></select></span>
                            <br/>
                            <input type="hidden" id="remote-chronometer-mode" name="remote-chronometer-mode" value="0"/>
                            <span style="display:inline-block;width:200px">&nbsp;</span>
                            <a id="remote-chronometer-sendBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-reload'" onclick="remoteSendChangeEvent('#remote-chronometer');"><?php _e('Switch'); ?></a>
                        </form>
                    </span>
                </div>
                <div style="display:<?php echo $am->allowed(ENABLE_CHRONO)?'none':'inherit';?>">
                    <p><?php _e('Current license permissions does not allow chronometer operations');?></p>
                    <img src="../../images/sad_dog.png" alt="triste"/>
                </div>
            </div>

            <div title="<?php _e('Internet access');?>" data-options="iconCls:'icon-web'">
                <div style="display:<?php echo $am->allowed(ENABLE_PUBLIC)?'inherit':'none';?>">
                    <span style="float:left;display:inline-block;width:55%;padding:5px">
                        <p><?php _e('Control panel for remote public access');?></p>

        		        <div class="fitem">
					        <label for="remote-internet-prueba"><?php _e('Select contest');?>:</label>
            		        <select id="remote-internet-prueba" name="remote-internet-prueba" style="width:200px"></select>
        		        </div>
                    </span>
                    <span style="float:right;display:inline-block;width:40%;padding:5px">
                        <form id="remote-internet-form" name="remote-internet-form">
                            <br/>
                            <strong><?php _e('Internet messaging board');?></strong>
                            <br/><br/>
                            <label for="remote-internet-msg" style="display:inline-block;width:100px"><?php _e('Message');?>:</label>
                            <input id="remote-internet-msg" name="remote-internet-msg" value="" style="width:175px">
                            <br/><br/>
                            <label for="remote-internet-dorsals" style="display:inline-block;width:100px"><?php _e('Dorsals');?>:</label>
                            <input id="remote-internet-dorsals" name="remote-internet-dorsals" value="" style="width:175px">
                            <br/><br/>
                            <div style="width:100%;display:inline-block;">
                                <span style="float:left;width:50%"><?php _e("Duration (secs)");?>:</span>
                                <span style="float:right;width:40%;padding-right:10px;padding-left:5px;">
                                    <input id="remote-internet-msgtimeout" name="remote-internet-msgtimeout"/>
                                </span>
                            </div>
                            <span style="display:inline-block;width:175px">&nbsp;</span>
                            <a id="remote-internet-msgBtn" href="#" class="easyui-linkbutton"
                               data-options="iconCls:'icon-mail'" onclick="remoteSendInternetNotification('#remote-internet')"><?php _e('Send'); ?></a>
                        </form>
                    </span>
                </div>
                <div style="display:<?php echo $am->allowed(ENABLE_PUBLIC)?'none':'inherit';?>">
                    <p><?php _e('Current license permissions does not allow internet public access');?></p>
                    <img src="../../images/sad_dog.png" alt="triste"/>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES DE REFRESH/DONE DEL CUADRO DE DIALOGO -->
    <div id="remote-dlg-buttons" style="width:100%;display:inline-block">
        <span style="float:left;">
        <a id="remote-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-reload'" onclick="reloadRemoteClientList();"><?php _e('Refresh'); ?></a>
        </span>
        <span style="float:right;">
        <a id="remote-okBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-ok'" onclick=" $('#remote-dialog').dialog('close');"><?php _e('Done.'); ?></a>
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
                $('#remote-videowall-playlist').combogrid('grid').datagrid('reload');
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
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'getClients', SessionType: 'videowall' },
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
                { field:'Session',	    width:20, sortable:true,title:'<?php _e('Ring'); ?>', formatter: formatRingName },// sess id
                { field:'View',		    width:40, sortable:true,title:'<?php _e('View mode'); ?>', formatter: formatVideowallView },
                { field:'Mode',		    hidden:true }, // no sense in videowall
                { field:'LastCall',		hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            onUnselect: function(index,row) {
                $('#remote-videowall-all').prop('checked',false);
                remoteSetOnSingleSelection("#remote-videowall");
            },
            onSelect: function(index,row) {
                $('#remote-videowall-none').prop('checked',false);
                remoteSetOnSingleSelection("#remote-videowall");
            }
        });

        $('#remote-videowall-playlist').combogrid({
            panelWidth: 400,
            panelHeight: 'auto',
            idField: 'ID',
            textField: 'Name',
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'playlist' },
            method: 'get',
            mode: 'remote',
            required: true,
            rownumber: false,
            multiple: false,
            fitColumns: true,
            singleSelect: true,
            editable: false, // avoid keyboard deploy
            columns: [[
                { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
                { field:'Name',		width:'75%', sortable:false,   align:'left',  title: '<?php _e('Name'); ?>' },
                { field:'Type',	width:'0%', sortable:false,   align:'right',  title: '<?php _e('Type'); ?>' }
            ]]
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
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'selectring', Hidden: 0},
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

        $('#remote-videowall-msg').textbox({
            validType:'lenght[0,25]'
        });
        $('#remote-videowall-dorsals').textbox({
            validType:'lenght[0,25]'
        });
        $('#remote-videowall-msgtimeout').slider({ mode:'h', showTip:true, min:1, max:30, value:5,   step:1 });

        $('#remote-internet-prueba').combogrid({
            panelWidth: 400,
            panelHeight: 125,
            idField: 'ID',
            textField: 'Nombre',
            url: '../ajax/database/pruebaFunctions.php',
            queryParams: { Operation: 'enumerate' },
            method: 'get',
            mode: 'remote',
            required: true,
            editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
            columns: [[
                {field:'ID',hidden:true},
                {field:'Nombre',        title:'<?php _e('Name');?>', width:50,align:'right'},
                {field:'Club',hidden:true},
                {field:'NombreClub',    title:'<?php _e('Club');?>',   width:30,align:'right'},
                {field:'RSCE',			title:'<?php _e('Fed');?>.',	width:10,	align:'center', formatter:formatFederation},
                {field:'Observaciones', hidden:true }
            ]],
            multiple: false,
            fitColumns: true,
            singleSelect: true,
            selectOnNavigation: false
        });

        $('#remote-internet-msg').textbox({
            required: true,
            validType:'lenght[0,127]'
        });
        $('#remote-internet-dorsals').textbox({
            validType:'lenght[0,256]'
        });
        $('#remote-internet-msgtimeout').slider({ mode:'h', showTip:true, min:1, max:15, value:5,   step:1 });

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
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'getClients', SessionType: 'livestream' },
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
            rowStyler:myRowStyler,
            onUnselect: function(index,row) {
                $('#remote-livestream-all').prop('checked',false);
                remoteSetOnSingleSelection("#remote-livestream");
            },
            onSelect: function(index,row) {
                $('#remote-livestream-none').prop('checked',false);
                remoteSetOnSingleSelection("#remote-livestream");
            }
        });

        $('#remote-livestream-view').combobox({
            valueField:'value',
            panelHeight:'auto',
            editable:false
        });

        $('#remote-livestream-delay').slider({
            mode:'h',
            showTip:true,
            min: 0,
            max: 50,
            value: 10,
            step: 1,
            // rule: [ 0,'|',1,'|',2,'|',3,'|',4,'|',5 ],
            tipFormatter: function(val) { return toFixedT(val/10.0,1); },
            onChange: function(nval,oval) {
                remoteSendKeyValueEvent('#remote-livestream',EVTCMD_OSDSETDELAY,nval/10.0);
            }
        });

        $('#remote-livestream-alpha').slider({
            mode:'h',
            showTip:true,
            min: 0,
            max: 100,
            value: 50,
            step: 1,
            // rule: [ 0,'|',1,'|',2,'|',3,'|',4,'|',5 ],
            tipFormatter: function(val) { return toFixedT(val/100.0,2); },
            onChange: function(nval,oval) {
                remoteSendKeyValueEvent('#remote-livestream',EVTCMD_OSDSETALPHA,nval/100.0);
            }
        });

        $('#remote-livestream-ring').combogrid({
            panelWidth: 400,
            panelHeight: 'auto',
            idField: 'ID',
            textField: 'Nombre',
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'selectring', Hidden: 0},
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
        $('#remote-chronometer-datagrid').datagrid({
            // datos del panel padre asociado
            fit: false,
            border: true,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            // datos de la conexion ajax
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'getClients', SessionType: 'chrono' },
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
            rowStyler:myRowStyler,
            onUnselect: function(index,row) {
                $('#remote-chronometer-all').prop('checked',false);
                remoteSetOnSingleSelection("#remote-chronometer");
            },
            onSelect: function(index,row) {
                $('#remote-chronometer-none').prop('checked',false);
                remoteSetOnSingleSelection("#remote-chronometer");
            }
        });

        // need for compatibility, but invisible and not used in chronometer
        $('#remote-chronometer-view').combobox({ panelHeight:'auto', valueField:'value', editable:false });

        $('#remote-chronometer-ring').combogrid({
            panelWidth: 400,
            panelHeight: 'auto',
            idField: 'ID',
            textField: 'Nombre',
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'selectring', Hidden: 0},
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

        // tooltips de los botones
        $('#remote-videowall-ffnextBtn').linkbutton();
        $('#remote-videowall-ffprevBtn').linkbutton();
        $('#remote-videowall-fsnextBtn').linkbutton();
        $('#remote-videowall-fsprevBtn').linkbutton();
        addTooltip($('#remote-chronometer-sendBtn').linkbutton(),'<?php _e("Switch selected video-chronometers to choosen ring"); ?>');
        addTooltip($('#remote-livestream-sendBtn').linkbutton(),'<?php _e("Switch selected livestreamn windows to choosen ring/view"); ?>');
        addTooltip($('#remote-livestream-offBtn').linkbutton(),'<?php _e("Turn On-Screen-Display overlay OFF"); ?>');
        addTooltip($('#remote-livestream-onBtn').linkbutton(),'<?php _e("Turn On-Screen-Display overlay ON"); ?>');
        addTooltip($('#remote-videowall-msgBtn').linkbutton(),'<?php _e("Show text message on selected screen with specified duration"); ?>');
        addTooltip($('#remote-videowall-sendBtn').linkbutton(),'<?php _e("Switch selected scoreboard displays to choosen ring/view"); ?>');
        addTooltip($('#remote-internet-msgBtn').linkbutton(),'<?php _e("Send text message to connected internet clients<br/>listening to selected contest"); ?>');
        addTooltip($('#remote-internet-dorsals').textbox('textbox'),'<?php _e("Comma separated list of dorsals to send message to (or empty)"); ?>');
        addTooltip($('#remote-reloadBtn').linkbutton(),'<?php _e("Reload and update active client lists"); ?>');
        addTooltip($('#remote-okBtn').linkbutton(),'<?php _e("End video display remote control session. Close window"); ?>');

    </script>
<!-- 
dlg_sesiones.inc

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
$config =Config::getInstance();
?>
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Sesiones -->
    <div id="sesiones-dialog" style="width:575px;height:auto;padding:10px 20px">
        <div class="ftitle"><?php _e('Session information'); ?></div>
        <form id="sesiones-form" method="get">
            <div class="fitem">
                <label for="sesiones-Nombre"><?php _e('Name'); ?>:</label>
                <input id="sesiones-Nombre" name="Nombre" type="text" style="width:250px" />
                <input id="sesiones-ID" name="ID" type="hidden" /> 
                <input id="sesiones-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <input id="sesiones-Operador" name="Operador" type="hidden" /> 
                <label for="sesiones-Login"><?php _e('User'); ?></label>
                <input id="sesiones-Login" type="text" name="Login" style="width:250px" />
                <a id="sesiones-logoutBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-exit'" onclick="session_logout()"><?php _e('Disconnect'); ?></a>
            </div>
            <div class="fitem">
                <label for="sesiones-Comentario"><?php _e('Comments'); ?></label>
                <textarea id="sesiones-Comentario" name="Comentario" style="height:40px;width:375px"></textarea>
            </div>
            <div class="fitem">
                <label for="sesiones-Background"><?php _e('Background image/mjpeg'); ?></label>
                <input id="sesiones-Background" name="Background" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-Livestream"><?php _e('Stream MPEG-4'); ?></label>
                <input id="sesiones-Livestream" name="LiveStream" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-LiveStream2"><?php _e('Stream OGV'); ?></label>
                <input id="sesiones-LiveStream2" name="LiveStream2" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-LiveStream3"><?php _e('Stream Web-M'); ?></label>
                <input id="sesiones-LiveStream3" name="LiveStream3" type="text" style="width:375px" />
            </div>

        </form>
    </div>


<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO de edicion de sesiones -->
<div id="sesiones-dlg-buttons">
    <a id="sesiones-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls:'icon-ok'" onclick="saveSession()"><?php _e('Save'); ?></a>
    <a id="sesiones-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls:'icon-cancel'" onclick="$('#sesiones-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<!-- TABLA DE jquery-easyui para listar y editar la BBDD DE SESIONES -->
<div  style="width:100%;height:500px">
    <!-- DECLARACION DE LA TABLA DE SESIONES -->
    <table id="sesiones-datagrid">  </table>
</div>

<!-- BARRA DE TAREAS DE LA TABLA DE SESIONES -->
<div id="sesiones-toolbar" style="width:100%;display:inline-block">
 			<span style="float:left;padding:5px">
   				<a id="sesiones-newBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-order'"
                   onclick="newSession('#sesiones-datagrid',$('#sesiones-datagrid-search').val())"><?php _e('New session'); ?></a>
   				<a id="sesiones-editBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-edit'"
                   onclick="editSession('#sesiones-datagrid')"><?php _e('Edit session'); ?></a>
   				<a id="sesiones-delBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-trash'"
                   onclick="deleteSession('#sesiones-datagrid')"><?php _e('Delete session'); ?></a>
   				<a id="sesiones-resetBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-redo'"
                   onclick="resetSession('#sesiones-datagrid')"><?php _e('Reset session'); ?></a>
   				<input id="sesiones-datagrid-search" type="text" value="<?php _e('-- Search --'); ?>" class="search_textfield"
                       onfocus="handleSearchBox(this,true);" onblur="handleSearchBox(this,false);"/>
   			</span>
    <span style="float:right;padding:5px">
				<a id="sesiones-seqBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-video-tv'" onclick="$('#remote-dialog').dialog('open');"><?php _e('Display Control'); ?></a>
   				<a id="sesiones-reloadBtn" href="#" class="easyui-linkbutton"
                   data-options="iconCls:'icon-brush'"
                   onclick="
                           // clear selection and reload table
                           $('#sesiones-datagrid-search').val('<?php _e('-- Search --'); ?>');
                           $('#sesiones-datagrid').datagrid('load',{ where: '' });"
                ><?php _e('Clear'); ?></a>
   			</span>
</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#sesiones-dialog').dialog({ closed:true, buttons:'#sesiones-dlg-buttons', iconCls:'icon-order', modal:true });
		$('#sesiones-form').form({novalidate:true});
        // - botones
		addTooltip($('#sesiones-logoutBtn').linkbutton(),'<?php _e("Disconnect current user from session"); ?>');
		addTooltip($('#sesiones-okBtn').linkbutton(),'<?php _e("Register session information. Upate database"); ?>');
		addTooltip($('#sesiones-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
        // on ESC key close session
        $('#sesiones-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#sesiones-dialog').dialog('close');
        	}
        });
        $('#sesiones-Nombre').textbox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#sesiones-Login').textbox({editable:false,readonly:true,disabled:true});
        $('#sesiones-Background').textbox();
        $('#sesiones-Livestream').textbox();
        $('#sesiones-LiveStream2').textbox();
        $('#sesiones-LiveStream3').textbox();

        // datos de la tabla de sesiones
        $('#sesiones-datagrid').datagrid({
            // datos del panel padre asociado
            fit: true,
            border: false,
            closable: false,
            collapsible: false,
            expansible: false,
            collapsed: false,
            title: '<?php _e('Session (ring) data management'); ?>',
            // datos de la conexion ajax
            url: '../ajax/database/sessionFunctions.php',
            queryParams: { Operation: 'select' },
            loadMsg: '<?php _e('Updating session list'); ?>',
            method: 'get',
            toolbar: '#sesiones-toolbar',
            pagination: false,
            rownumbers: true,
            fitColumns: true,
            singleSelect: true,
            view: scrollview,
            pageSize: 50,
            multiSort: true,
            remoteSort: true,
            columns: [[
                { field:'ID',		hidden:true },
                { field:'Nombre',		width:25, sortable:true,title:'<?php _e('Name'); ?>' },
                { field:'Comentario',	width:55, sortable:true,title:'<?php _e('Description'); ?>' },
                { field:'Operador',		hidden:true },
                { field:'Login',		width:25, sortable:true,title:'<?php _e('User'); ?>' },
                { field:'Background',	width:30,				title:'Stream MJPEG' },
                { field:'LiveStream',	width:30,				title:'Stream h264' },
                { field:'LiveStream2',	width:30,   			title:'Stream Ogg' },
                { field:'LiveStream3',	width:30,				title:'Stream WebM' }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            // on double click fireup editor dialog
            onDblClickRow:function() {
                editSession('#sesiones-datagrid');
            }
        });

        // key handler ( notify "null" dialog cause we dont want to close tabs on escape :-)
        addKeyHandler('#sesiones-datagrid',null,newSession,editSession,deleteSession);

        // tooltips de la tabla de sesiones
        addTooltip($('#sesiones-newBtn').linkbutton(),'<?php _e("Add a new session (ring)<br/>into data base"); ?>');
        addTooltip($('#sesiones-editBtn').linkbutton(),'<?php _e("Modify data on selected session (ring)"); ?>');
        addTooltip($('#sesiones-delBtn').linkbutton(),'<?php _e("Remove selected session from database"); ?>');
        addTooltip($('#sesiones-resetBtn').linkbutton(),'<?php _e("Reset/Clear event log from selected session"); ?>');
        addTooltip($('#sesiones-reloadBtn').linkbutton(),'<?php _e("Clear search box. Update data"); ?>');
        addTooltip($('#sesiones-datagrid-search'),'<?php _e("Look for sessions matching search criteria"); ?>');
        addTooltip($('#sesiones-seqBtn').linkbutton(),'<?php _e("View/control active displays"); ?>'+" <br />"+'<?php _e("on currently defined sessions"); ?>');
    </script>
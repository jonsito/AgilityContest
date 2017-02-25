<!-- 
dlg_sesiones.inc

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
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Sesiones -->
    <div id="sesiones-dialog" style="width:550px;height:auto;padding:10px 20px">
        <div class="ftitle"><?php _e('Session information'); ?></div>
        <form id="sesiones-form" method="get">
            <div class="fitem">
                <label for="sesiones-Nombre"><?php _e('Name'); ?>:</label>
                <input id="sesiones-Nombre" name="Nombre" type="text" class="easyui-validatebox" style="width:250px" />
                <input id="sesiones-ID" name="ID" type="hidden" /> 
                <input id="sesiones-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <input id="sesiones-Operador" name="Operador" type="hidden" /> 
                <label for="sesiones-Login"><?php _e('User'); ?></label>
                <input id="sesiones-Login" type="text" class="easyui-validatebox" name="Login" readonly="readonly" style="width:250px" />
                <a id="sesiones-logoutBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-exit'" onclick="session_logout()"><?php _e('Disconnect'); ?></a>
            </div>
            <div class="fitem">
                <label for="sesiones-Comentario"><?php _e('Comments'); ?></label>
                <textarea id="sesiones-Comentario" name="Comentario" style="height:40px;width:375px"></textarea>
            </div>
            <div class="fitem">
                <label for="sesiones-Background"><?php _e('Background image/mjpeg'); ?></label>
                <input id="sesiones-Background" name="Background" type="text" class="easyui-textbox" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-Livestream"><?php _e('Stream MPEG-4'); ?></label>
                <input id="sesiones-Livestream" name="LiveStream" type="text" class="easyui-textbox" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-LiveStream2"><?php _e('Stream OGV'); ?></label>
                <input id="sesiones-LiveStream2" name="LiveStream2" type="text" class="easyui-textbox" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="sesiones-LiveStream3"><?php _e('Stream Web-M'); ?></label>
                <input id="sesiones-LiveStream3" name="LiveStream3" type="text" class="easyui-textbox" style="width:375px" />
            </div>
            <div class="fitem" style="display:inline-block;float:right; padding-right:40px">
				<a id="sesiones-seqBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-video-tv'" onclick="session_sequences()"><?php _e('Sequences'); ?></a>
            </div>
            
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="sesiones-dlg-buttons">
   	    <a id="sesiones-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveSession()"><?php _e('Save'); ?></a>
   	    <a id="sesiones-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#sesiones-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
   	</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#sesiones-dialog').dialog({ closed:true, buttons:'#sesiones-dlg-buttons', iconCls:'icon-order', modal:true });
		$('#sesiones-form').form({novalidate:true});
        // - botones
		addTooltip($('#sesiones-logoutBtn').linkbutton(),'<?php _e("Disconnect current user from session"); ?>');
		addTooltip($('#sesiones-seqBtn').linkbutton(),'<?php _e("View/Edit sequences for"); ?>'+" <br />"+'<?php _e("videowall attached to this session"); ?>');
		addTooltip($('#sesiones-okBtn').linkbutton(),'<?php _e("Register session information. Upate database"); ?>');
		addTooltip($('#sesiones-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
        // on ESC key close session
        $('#sesiones-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#sesiones-dialog').dialog('close');
        	}
        });
        $('#sesiones-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
    </script>
<!-- 
dlg_sesiones.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Sesiones -->
    <div id="sesiones-dialog" style="width:550px;height:auto;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n de la sesi&oacute;n</div>
        <form id="sesiones-form" method="get">
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="sesiones-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:250px" />
                <input id="sesiones-ID" name="ID" type="hidden" /> 
                <input id="sesiones-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <input id="sesiones-Operador" name="Operador" type="hidden" /> 
                <label for="Login">Usuario</label>
                <input id="sesiones-Login" class="easyui-validatebox" name="Login" readonly="readonly" type="text" style="width:250px" />
                <a id="sesiones-logoutBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-exit'" onclick="session_logout()">Desconectar</a>
            </div>
            <div class="fitem">
                <label for="Comentario">Comentarios</label>
                <textarea id="sesiones-Comentario" name="Comentario" style="height:40px;width:375px"></textarea>
            </div>
            <div class="fitem">
                <label for="Background">Imagen de fondo</label>
                <input id="sesiones-Background" name="Background" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="LiveStream">Stream MPEG-4</label>
                <input id="sesiones-Livestream" name="LiveStream" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="LiveStream2">Stream OGV</label>
                <input id="sesiones-LiveStream2" name="LiveStream2" type="text" style="width:375px" />
            </div>
            <div class="fitem">
                <label for="LiveStream3">Stream Web-M</label>
                <input id="sesiones-LiveStream3" name="LiveStream3" type="text" style="width:375px" />
            </div>
            <div class="fitem" style="position:inline-block;float:right; padding-right:40px">
				<a id="sesiones-seqBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-video-tv'" onclick="session_sequences()">Secuencias</a>
            </div>
            
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="sesiones-dlg-buttons">
   	    <a id="sesiones-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveSession()">Guardar</a>
   	    <a id="sesiones-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#sesiones-dialog').dialog('close')">Cancelar</a>
   	</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#sesiones-dialog').dialog({ closed:true, buttons:'#sesiones-dlg-buttons', iconCls:'icon-order', modal:true });
		$('#sesiones-form').form({novalidate:true});
        // - botones
		addTooltip($('#sesiones-logoutBtn').linkbutton(),"Desconectar este usuario de la sesi&oacute;n");
		addTooltip($('#sesiones-seqBtn').linkbutton(),"Ver/editar las secuencias para <br />los videomarcadores de esta sesi&oacute;n");
		addTooltip($('#sesiones-okBtn').linkbutton(),"Registrar los datos de la sesi&oacute;n y actualizar la BBDD");
		addTooltip($('#sesiones-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
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
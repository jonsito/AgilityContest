<!-- 
dlg_usuarios.inc

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

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Usuarios -->
    <div id="usuarios-dialog" style="width:550px;height:300px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del usuario</div>
        <form id="usuarios-form" method="get">
            <div class="fitem">
                <label for="Login">Nombre</label>
                <input id="usuarios-Login" class="easyui-validatebox" name="Login" type="text" style="width:250px" />
                <a id="usuarios-passwdBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-key'" onclick="setPassword">Contrase&ntilde;a</a>
                <input id="usuarios-ID" name="ID" type="hidden" /> 
                <input id="usuarios-Operation" name="Operation" type="hidden" /> <!-- insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="Gecos">Informaci&oacute;n</label>
                <input id="usuarios-Gecos" name="Gecos" type="text" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="Phone">Tel&eacute;fono</label>
                <input id="usuarios-Phone" name="Phone" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Email">E-Mail</label>
                <input id="usuarios-Email" class="easyui-validatebox" name="Email" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="Perms">Categor&iacute;a</label>
                <select name="Perms">
                <option value="1">Administrador</option>
                <option value="2">Operador</option>
                <option value="3">Asistente</option>
                <option value="4">Invitado</option>
                </select>
            </div>
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="usuarios-dlg-buttons">
   	    <a id="usuarios-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveUser()">Guardar</a>
   	    <a id="usuarios-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#usuarios-dialog').dialog('close')">Cancelar</a>
   	</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#usuarios-dialog').dialog({ closed:true, buttons:'#usuarios-dlg-buttons', iconCls:'icon-users', modal:true });
		$('#usuarios-form').form({novalidate:true});
        // - botones
		addTooltip($('#usuarios-passwdBtn').linkbutton(),"Cambiar la contrase&ntilde;a del usuario");
		addTooltip($('#usuarios-okBtn').linkbutton(),"Guardar datos del usuario y actualizar la BBDD");
		addTooltip($('#usuarios-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
        // on ESC key close session
        $('#usuarios-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#usuarios-dialog').dialog('close');
        	}
        });
        $('#usuarios-Login').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#usuarios-Email').validatebox({
            required: false,
            validType: 'email'
        });
        </script>
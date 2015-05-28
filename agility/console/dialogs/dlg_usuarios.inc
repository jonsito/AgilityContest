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
	<div id="password-dialog" style="width:550px;height:300px;padding:10px 20px">
		<div class="ftitle">Cambio de contrase&ntilde;a</div>
		<form id="password-form" method="get">
			<div id="password-SameUser" class="fitem">
				<input id="password-UserID" type="hidden" name="UserID" value="" />
                <label for="password-CurrentPassword">Contrase&ntilde;a actual</label>
                <input id="password-CurrentPassword" name="CurrentPassword" type="password" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="password-NewPassword">Nueva contrase&ntilde;a</label>
                <input id="password-NewPassword" name="NewPassword" type="password" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="password-NewPassword2">Nueva contrase&ntilde;a (otra vez)</label>
                <input id="password-NewPassword2" name="NewPassword2" type="password" style="height:25px;width:375px"/>
            </div>
		</form>
	</div>
	
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE CAMBIO DE CONTRASENYA -->
   	<div id="password-dlg-buttons">
   	    <a id="password-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="savePassword()">Guardar</a>
   	    <a id="password-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#password-dialog').dialog('close')">Cancelar</a>
   	</div>
   	
    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE Usuarios -->
    <div id="usuarios-dialog" style="width:550px;height:300px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del usuario</div>
        <form id="usuarios-form" method="get">
            <div class="fitem">
                <label for="usuarios-Login">Nombre</label>
                <input id="usuarios-Login" class="easyui-validatebox" name="Login" type="text" style="width:250px" />
                <a id="usuarios-passwdBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-key'" onclick="setPassword('#usuarios-datagrid');">Contrase&ntilde;a</a>
                <input id="usuarios-ID" name="ID" type="hidden" /> 
                <input id="usuarios-Operation" name="Operation" type="hidden" /> <!-- insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="usuarios-Gecos">Informaci&oacute;n</label>
                <input id="usuarios-Gecos" name="Gecos" type="text" style="height:25px;width:375px"/>
            </div>
            <div class="fitem">
                <label for="usuarios-Phone">Tel&eacute;fono</label>
                <input id="usuarios-Phone" name="Phone" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="usuarios-Email">E-Mail</label>
                <input id="usuarios-Email" class="easyui-validatebox" name="Email" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="usuarios-Perms">Categor&iacute;a</label>
                <select id="usuarios-Perms" name="Perms">
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
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#usuarios-dialog').dialog('close')">Cancelar</a>
   	</div>
   	
	<script type="text/javascript">
		// declaraciones
		$('#usuarios-dialog').dialog({ closed:true, buttons:'#usuarios-dlg-buttons', iconCls:'icon-users', modal:true });
		$('#usuarios-form').form({novalidate:true});
		$('#password-dialog').dialog({ closed:true, buttons:'#password-dlg-buttons', iconCls:'icon-users', modal:true });
		$('#password-form').form({novalidate:true});
		
        // - botones
		addTooltip($('#usuarios-passwdBtn').linkbutton(),"Cambiar la contrase&ntilde;a del usuario");
		addTooltip($('#usuarios-okBtn').linkbutton(),"Guardar datos del usuario y actualizar la BBDD");
		addTooltip($('#usuarios-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");

		addTooltip($('#password-okBtn').linkbutton(),"Cambiar la contrase&ntilde;a");
		addTooltip($('#password-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
		
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

     	// bind enter key on password dialog
        $('#password-CurrentPassword').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword').focus();
            return false;
        });
        $('#password-NewPassword').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-NewPassword2').focus();
            return false;
        });
        //on Enter key on passwd field click on accept
        $('#password-NewPassword2').bind('keypress', function (evt) {
            if (evt.keyCode != 13) return true;
            $('#password-okBtn').click();
            return false;
        });
	</script>
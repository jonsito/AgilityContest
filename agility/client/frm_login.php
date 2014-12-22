<!-- 
frm_login.php

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
<!-- FORMULARIO DE introduccion de usuario y contrasenya -->
<div id="login-dialog" style="width:500px;height:250px;padding:10px" class="easyui-dialog"
	data-options="title: 'Autenticaci&oacute;n',iconCls: 'icon-users',buttons: '#login-Buttons',collapsible: false,	minimizable: false,
		maximizable: false,	closable: true,	closed: false,shadow: true, modal: true">
		<div style="padding:5px;">
			<p>
			Algunas operaciones requieren la autenticaci&oacute;n del usuario.<br /> 
			Si &eacute;ste no se especifica, se asumir&aacute; acceso como "invitado"
			</p>
		</div> <!-- explicacion -->
		<div id="login-Content" style="padding:5px;">
			<form id="login-Selection">
        		<div class="fitem">
            		<label for="Username">Nombre de usuario:</label>
            		<input id="login-Username" name="Username" style="width:200px" type="text"
            			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
        		</div>        		
        		<div class="fitem">
            		<label for="Password">Contrase&ntilde;a:</label>
            		<input id="login-Password" name="Password" style="width:200px" type="password"
            			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
        		</div>
			</form>
		</div> <!-- contenido -->
</div> <!-- Window -->

<div id="login-Buttons" style="text-align:right;padding:5px;">
    <a id="login-okBtn" href="#" class="easyui-linkbutton" 
  	    	data-options="iconCls: 'icon-ok'" onclick="acceptLogin()">Aceptar</a>
    <a id="login-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()">Cancelar</a>
</div>	<!-- botones -->

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />
<script type="text/javascript">

	$('#login-form').form();

	addTooltip($('#login-okBtn').linkbutton(),"Iniciar sesi&oacute;n con el usuario seleccionado");
	addTooltip($('#login-cancelBtn').linkbutton(),"Terminar la sesi&oacute;n. Cerrar ventana");

	function acceptLogin() {
		$.ajax({
  			type: 'POST',
    		url: '/agility/server/database/userFunctions.php',
    		dataType: 'json',
    		data: {
    			Operation: 'login',
    			Username: $('#login-Username').val(),
    			Password: $('#login-Password').val(),
    		},
    		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
    		success: function(data) {
        		if (data.errorMsg) { // error
        			$.messager.alert("Error",data.errorMsg,"error");
        		} else {// success: 
            		// alert(JSON.stringify(data));
            		initAuthInfo(data);
        		} 
        	},
    		error: function() { alert("error");	},
   		});
		$('#login-dialog').dialog('close');	
	}
	
	function cancelLogin() {
		// TODO: call logout on server
		$('#login-Usuario').val('');
		$('#login-Password').val('');
		// close window
		$('#login-dialog').dialog('close');
	}
</script>
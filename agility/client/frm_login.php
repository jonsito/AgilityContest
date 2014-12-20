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
<div id="login-window" style="position:relative,width:500px;height:175px;padding:10px 10px">
	<div id="login-Layout" class="easyui-layout" data-options="fit:'true'">
		<form id="login-Selection">
			<div id="login-Content" data-options="region:'north',border:'true'">
        		<div class="fitem">
            		<label for="Username">Nombre de usuario:</label>
            		<input id="login-Username" name="Username" style="width:200px" type="text"/>
        		</div>        		
        		<div class="fitem">
            		<label for="Password">Contrase&ntilde;a</label>
            		<input id="login-Password" name="Password" style="width:200px" type="password"/>
        		</div>
			</div> <!-- contenido -->
		</form>
		<div data-options="region:'center'">&nbsp;</div>
		<div id="login-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="login-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-ok'" onclick="acceptLogin()">Aceptar</a>
    	    <a id="login-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()">Cancelar</a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle"/>
<script type="text/javascript">
	$('#login-window').window({
		title: 'Autenticaci&oacute;n',
		collapsible: false,
		minimizable: false,
		maximizable: false,
		closable: true,
		closed: false,
		shadow: true,
		modal: true
	});
	$('#login-form').form();
	$('#login-layout').layout();

	addTooltip($('#login-okBtn').linkbutton(),"Iniciar sesi&oacute;n con el usuario seleccionado");
	addTooltip($('#login-cancelBtn').linkbutton(),"Terminar la sesi&oacute;n. Cerrar ventana");

	function acceptLogin() {
		// TODO: call login on server
		$('#login-window').window('close');	
	}
	function cancelLogin() {
		// TODO: call logout on server
		$('#login-Usuario').combogrid('setValue','');
		$('#login-Password').combogrid('setValue','');
		// close window
		$('#login-window').window('close');
	}
</script>
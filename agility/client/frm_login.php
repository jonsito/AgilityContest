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

<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
?>

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />

<!-- FORMULARIO DE introduccion de usuario y contrasenya -->
<div id="login-window" class="easyui-window" style="position:relative;width:500px;height:auto;padding:20px 20px">
<!-- panel de login -->
	<div id="login-Layout" class="easyui-layout" data-options="fit:true'">
	<div style="padding:5px;" data-options="region:'north',border:'false'">
		<p>
		Algunas operaciones requieren la autenticaci&oacute;n del usuario.<br /> 
		Si &eacute;ste no se especifica, se asumir&aacute; acceso como "invitado"
		</p>
	</div> 
	<!-- formulario de datos de login -->
	<div id="login-Form" style="padding:5px;" data-options="region:'center',border:'true'">
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
       		<div class="fitem">
       	   		<label for="Federation">Federaci&oacute;n:</label>
       	   		<select id="login-Federation" name="Federation" onChange="setFederation(this.value);">
       	   			<option value="0" selected="selected">Real Sociedad Canina de Espa&ntilde;a (RSCE)</option>
       	   			<option value="1">Real Federaci&oacute;n Espa&ntilde;ola de Caza (RFEC)</option>
       	   			<option value="2">Uni&oacute;n de Clubes de Agility (UCA)</option>	
       	   		</select>
       		</div>
		</form>
	</div>
	
	<!-- botones del menu de login-->
	<div id="login-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
		<a id="login-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-ok'" onclick="acceptLogin()">Aceptar</a>
		<a id="login-cancelBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()">Cancelar</a>
	</div>
	</div>
</div> <!-- Dialog -->
<script type="text/javascript">

$('#login-window').window({
	title:'Iniciar Sesi&oacute;n',
	iconCls:'icon-users',
	collapsible:false,
	minimizable:false,
	maximizable:false,
	closable:true,
	closed:false,
	shadow:true,
	modal:true,
	onBeforeOpen: function () {
		$('#login-Usuario').val('');
		$('#login-Password').val('');
		return true;
	}
});

addTooltip($('#login-okBtn').linkbutton(),"Iniciar sesi&oacute;n con el usuario seleccionado");
addTooltip($('#login-cancelBtn').linkbutton(),"Cancelar apertura de sesion. Cerrar ventana");

// on Enter key on login field focus on password
$('#login-Username').bind('keypress', function (evt) {
    if (evt.keyCode != 13) return true;
    $('#login-Password').focus();
    return false;
});
//on Enter key on passwd field click on accept
$('#login-Password').bind('keypress', function (evt) {
    if (evt.keyCode != 13) return true;
    $('#login-okBtn').click();
    return false;
});

</script>
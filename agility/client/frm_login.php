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
<div id="login-dialog" style="padding:10px" >

<!-- panel de login -->
	<div id="login-Content">
		<!-- explicacion del login-->
		<div style="padding:5px;">
			<p>
			Algunas operaciones requieren la autenticaci&oacute;n del usuario.<br /> 
			Si &eacute;ste no se especifica, se asumir&aacute; acceso como "invitado"
			</p>
		</div> 
		<!-- formulario de datos de login -->
		<div id="login-Form" style="padding:5px;">
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
		</div>
		
		<!-- botones del menu de login-->
		<div id="login-Buttons" style="text-align:right;padding:5px;">
    		<a id="login-okBtn" href="#" class="easyui-linkbutton" 
  	    		data-options="iconCls: 'icon-ok'" onclick="acceptLogin()">Aceptar</a>
    		<a id="login-cancelBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()">Cancelar</a>
		</div>
	</div>
	
	<!-- panel de logout -->
	<div id="logout-Content">
		<!--  texto del panel de logout -->
		<div>
		&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
		Confirme que desea cerrar la sesion del usuario: <span id="logout-UserName">Usuario</span>
		&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />
		</div>
		<!-- botones del menu de logout-->
		<div id="logout-Buttons" style="text-align:right;padding:5px;">
    		<a id="logout-okBtn" href="#" class="easyui-linkbutton" 
  	    		data-options="iconCls: 'icon-ok'" onclick="acceptLogout()">Aceptar</a>
    		<a id="logout-cancelBtn" href="#" class="easyui-linkbutton" 
   	    		data-options="iconCls: 'icon-cancel'" onclick="cancelLogin()">Cancelar</a>
		</div>
	</div>
	
</div> <!-- Dialog -->

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />
<script type="text/javascript">

$('#login-dialog').dialog({
	width:500,
	height:250,
	title:'Autenticaci&oacute;n',
	iconCls:'icon-users',
	collapsible:false,
	minimizable:false,
	maximizable:false,
	closable:true,
	closed:false,
	shadow:true,
	modal:true,
	onBeforeOpen: function() {
		if(authInfo.SessionKey==null){ 
			$('#login-dialog').dialog('options').height=250;
			$('#login-Content').css('display','inherit');
			$('#logout-Content').css('display','none');
		} else { 
			$('#login-dialog').dialog('options').height=100;
			$('#logout-UserName').text(authInfo.Login);
			$('#login-Content').css('display','none');
			$('#logout-Content').css('display','inherit');
		}
	}
});
		
addTooltip($('#login-okBtn').linkbutton(),"Iniciar sesi&oacute;n con el usuario seleccionado");
addTooltip($('#login-cancelBtn').linkbutton(),"Cancelar apertura de sesion. Cerrar ventana");
addTooltip($('#logout-okBtn').linkbutton(),"Cerrar la sesion del usuario actual");
addTooltip($('#logout-cancelBtn').linkbutton(),"Cancelar cierre de sesion. Cerrar ventana");

</script>
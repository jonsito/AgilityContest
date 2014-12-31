<!-- 
frm_myAdmin.php

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

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle" />

<!-- FORMULARIO DE introduccion de usuario y contrasenya -->
<div id="myAdmin-window" class="easyui-window" style="position:relative;width:640px;height:375px;padding:20px 20px">
<!-- panel de myAdmin -->
	<div id="myAdmin-Layout" class="easyui-layout" data-options="fit:true'">
	<div style="padding:5px;" data-options="region:'north',border:'false'">
		<h2>AVISO:</h2>
		<p>
		<strong>
		El acceso directo a la base de datos es una operaci&oacute;n muy delicada <br />
		que debe ser realizada por alguien con conocimiento _real_ de lo que est&aacute; haciendo
		</strong>
		</p>
		<p>
		La modificaci&oacute;n incorrecta de la Base de Datos puede dejar &eacute;sta inconsistente<br />
		Y quedar la aplicaci&oacute;n inutilizable
		</p>
		<p>
		Es preciso introducir nombre y contrase&ntilde;a de un usuario con <em>permisos de administrador</em> para continuar
		</p>
	</div> 
	<!-- formulario de datos de myAdmin -->
	<div id="myAdmin-Form" style="padding:5px;" data-options="region:'center',border:'true'">
		<form id="myAdmin-Selection">
    		<div class="fitem">
    	   		<label for="Username">Usuario administrador:</label>
       	   		<input id="myAdmin-Username" name="Username" style="width:200px" type="text"
        			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
       		</div>        		
       		<div class="fitem">
       	   		<label for="Password">Contrase&ntilde;a:</label>
       	   		<input id="myAdmin-Password" name="Password" style="width:200px" type="password"
       	   			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
       		</div>
		</form>
	</div>
	
	<!-- botones del menu de myAdmin-->
	<div id="myAdmin-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
		<a id="myAdmin-okBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-ok'" onclick="acceptMyAdmin()">Aceptar</a>
		<a id="myAdmin-cancelBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls: 'icon-cancel'" onclick="cancelMyAdmin()">Cancelar</a>
	</div>
	</div>
</div> <!-- Dialog -->
<script type="text/javascript">

$('#myAdmin-window').window({
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
		$('#myAdmin-Usuario').val('');
		$('#myAdmin-Password').val('');
		return true;
	}
});
		
addTooltip($('#myAdmin-okBtn').linkbutton(),"Validar usuario. Abrir panel de acceso a Base de Datos");
addTooltip($('#myAdmin-cancelBtn').linkbutton(),"Cancelar operacion. Cerrar ventana");

</script>
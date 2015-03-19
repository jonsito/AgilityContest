<!-- 
frm_about.php

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
<div id="dlg_register" class="easyui-window" style="width:640px;padding:10px">
	<img src="/agility/images/AgilityContest.png" 
		width="150" height="100" alt="AgilityContest Logo" 
		style="border:1px solid #000000;margin:10px;float:right;padding:5px">
	<dl>
		<dt>
			<strong>Version: </strong><span id="reg_version">version</span> - <span id="reg_date">date</span> 
		</dt>
		<dt>
			<strong>AgilityContest</strong> es Copyright &copy; 2013-2015 de <em>Juan Antonio Martínez &lt;juansgaviota@gmail.com&gt;</em>
		</dt>
		<dd>
		El código fuente está disponible en <a href="https://github.com/jonsito/AgilityContest">https://github.com/jonsito/AgilityContest</a><br />
		Se permite su uso, copia, modificación y redistribución bajo los t&eacute;rminos de la 
		<a target="license" href="/agility/License">Licencia General P&uacute;blica de GNU</a>
		</dd>
	</dl>
	<p>
	Inscrito en el Registro Territorial de la Propiedad Intelectual de Madrid. <em>Expediente: 09-RTPI-09439.4/2014</em> 
	</p>
	<hr />&nbsp;<br />
	<strong>Informaci&oacute;n de registro:</strong>
	<form id="registration_data">
		<div class="fitem">
			<label for="User">Nombre:</label>
			<input type="text" readonly="readonly" name="User" /><br/>
		</div>
		<div class="fitem">
			<label for="Email">Contacto:</label>
			<input type="text" readonly="readonly" name="Email" /><br/>
		</div>
		<div class="fitem">
			<label for="Club">Club:</label>
			<input type="text" readonly="readonly" name="Club" /><br/>
		</div>
		<div class="fitem">
			<label for="Serial">Num. serie:</label>
			<input type="text" readonly="readonly" name="Serial" /><br/>
		</div>
	</form>
	<hr />&nbsp;<br />
	<form id="register_file">
	<div>
		<span style="float:left">	
		<a id="registration-okButton" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-key'"
   			onclick="send_regFile()">Registro</a>
		<input type="file" name="fichero" required="required" onchange="read_regFile(this)"/><br/>
		<input id="registrationData" type="hidden" name="Data" value="">
		</span> 
		<span style="float:right">
			<a id="registration-cancelButton" href="#" class="easyui-linkbutton"
   			data-options="iconCls:'icon-cancel'"
   			onclick="$('#dlg_register').window('close');">Cerrar</a>
		</span>
	</div>
	</form>
</div>

<script type="text/javascript">
        $('#dlg_register').window({
            title: "Informaci&oacute;n de registro",
            collapsible:false,
            minimizable:false,
            maximizable:false,
            resizable:false,
            closable:true,
            modal:true,
            iconCls: 'icon-dog',
            onOpen: function() { 
                $('#reg_version').html(ac_config.version_name);
                $('#reg_date').html(ac_config.version_date);
                $('#registration_data').form('load','/agility/server/adminFunctions.php?Operation=reginfo');
            },  
            onClose: function() {loadContents('/agility/client/frm_main.php','');
            }
        })
        
		addTooltip($('#registration-okButton').linkbutton(),"Incluir fichero de informacion de registro en la aplicaci&oacute;n");
		addTooltip($('#registration-cancelButton').linkbutton(),"Cancelar operaci&oacute;n. Cerrar ventana"); 
</script>
<!-- 
dlg_configuracion.php

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
 
 <div id="config-panel" class="easyui-panel" style="width:550px;height:300px;padding:10px 20px"
	data-options="fit:true,footer:'#config-dlg-buttons',onOpen:loadConfig">
	<form id="config-form" name="configuracion" class="easyui-form">
		<input id="config-Operation" type="hidden" name="Operation" value="saveConfig"/>
		<!-- TODO: Los parametros de la base de datos solo se deben presentar en el primer arranque -->
		<h3>General</h3>
		<label for="lang">Idioma</label>
		<select id="config_lang" name="lang">
			<option value="es" selected="selected">Espa&ntilde;ol</option>
			<option value="en">Ingl&eacute;s</option>
		</select>
		<h3>Escritorio</h3>
		<label for="easyui_theme">Tema del escritorio</label>
		<select id="config_theme" name="easyui_theme">
			<option value="default" selected="selected">Tema por defecto</option>
			<option value="metro">Metro</option>
			<option value="gray">Gris</option>
			<option value="black">Negro</option>
		</select><br />
		<label for="easyui_bgcolor">Color del fondo del escritorio</label>
		<input name="easyui_bgcolor" value="#0000ff" type="color"/><br />
		<label for="easyui_hdrcolor">Color del T&iacute;tulo de cabecera</label>
		<input name="easyui_hdrcolor" value="#00ff00" type="color"/><br />
		<label for="easyui_opcolor">Color del T&iacute;tulo del men&uacute; activo</label>
		<input name="easyui_opcolor" value="opcolor" type="color"/><br />
		<label for="easyui_rowcolor1">Color de filas pares en las tablas</label>
		<input name="easyui_rowcolor1" value="#e0e0e0" type="color"/><br />
		<label for="easyui_rowcolor2">Color de filas impares en las tablas</label>
		<input name="easyui_rowcolor2" value="#c0c0c0" type="color"/><br />
		<h3>Video-marcadores</h3>
		<label for="vw_polltime">Frecuencia de refresco (seg.)</label>
		<input name="vw_polltime" type="text" size="2" value="5"/><br />
		<label for="vw_alpha">Nivel de transparencia en OnScreenDisplay </label>
		<input name="vw_alpha" type="text" size="3" value="0.5"/><br />
		<label for="vw_hdrbg1">Color de fondo de la cabecera principal</label>
		<input name="vw_hdrbg1" type="color" value="#0000ff"/><br />
		<label for="vw_hdrfg1">Color del texto de la cabecera principal</label>
		<input name="vw_hdrfg1" type="color" value="#ffffff"/><br />
		<label for="vw_hdrbg2">Color de fondo de la cabecera secundaria</label>
		<input name="vw_hdrbg2" type="color" value="#c0c0c0"/><br />
		<label for="vw_hdrfg2">Color del texto de la cabecera secundaria</label>
		<input name="vw_hdrfg2" type="color" value="#ffffff"/><br />
		<label for="vw_rowcolor1">Color de fondo de las filas pares</label>
		<input name="vw_rowcolor1" type="color" value="#e0e0e0"/><br />
		<label for="vw_rowcolor2">Color de fondo las filas impares</label>
		<input name="vw_rowcolor2" type="color" value="#c0c0c0"/><br />
		<h3>Generaci&oacute;n de Informes</h3>
		<label for="pdf_hdrbg1">Color de fondo de la cabecera principal</label>
		<input name="pdf_hdrbg1" type="color" value="#ff8020"/><br />
		<label for="pdf_hdrfg1">Color del texto de la cabecera principal</label>
		<input name="pdf_hdrfg1" type="color" value="#000000"/><br />
		<label for="pdf_hdrbg2">Color de fondo de la cabecera secundaria</label>
		<input name="pdf_hdrbg2" type="color" value="#c0c0c0"/><br />
		<label for="pdf_hdrfg2">Color del texto de la cabecera secundaria</label>
		<input name="pdf_hdrfg2" type="color" value="#ffffff"/><br />
		<label for="pdf_rowcolor1">Color de fondo de las filas pares</label>
		<input name="pdf_rowcolor1" type="color" value="#ffffff"/><br />
		<label for="pdf_rowcolor2">Color de fondo las filas impares</label>
		<input name="pdf_rowcolor2" type="color" value="#e0ebff"/><br />
		<label for="pdf_linecolor">Color de las lineas de separaci&oacute;n</label>
		<input name="pdf_linecolor" type="color" value="#808080"/><br />
	</form>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE CAMBIO DE CONTRASENYA -->
<div id="config-dlg-buttons">
	<span  style="float:left;padding:5px">
    <a id="config-resetBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-reload'" onclick="loadConfig()">Restaurar</a>
    <a id="config-defaultBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-setup'" onclick="defaultConfig()">Default</a>
    </span>
	<span  style="float:right;padding:5px">
    <a id="config-okBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-ok'" onclick="saveConfig()">Guardar</a>
    <a id="config-cancelBtn" href="#" class="easyui-linkbutton" 
    	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#config-dialog').dialog('close')">Cancelar</a>
    </span>
</div>

<script type="text/javascript">
	addTooltip($('#config-resetBtn').linkbutton(),"Anula modificaciones. Recupera datos desde el servidor.");
	addTooltip($('#config-defaultBtn').linkbutton(),"Recuperar valores de fabrica de la aplicaci&oacute;n");
	addTooltip($('#config-okBtn').linkbutton(),"Aceptar datos. Guardar nueva configuraci&oacute;n");
	addTooltip($('#config-cancelBtn').linkbutton(),"Cancelar la operaci&oacute;n. Cerrar ventana");
	
	function saveConfig() {
		$('#config-Operation').val('saveConfig');
	    var frm = $('#config-form');
	    $.ajax({
	        type: 'GET',
	        url: '/agility/server/database/sessionFunctions.php',
	        data: frm.serialize(),
	        dataType: 'json',
	        success: function (result) {
	            if (result.errorMsg){
	                $.messager.show({ width:300, height:200, title: 'Error', msg: result.errorMsg });
	            } else {
		            $.messager.alert("Save",
				            "Configuraci&oacute;n guardada correctamente. <br />"+
				            "Deber&aacute; cerrar la sesi&oacute;n y recargar la p&aacute;gina"+
				            " para que los cambios tengan efecto",
				            "info");
	            }
	        }
	    });
	}	

	function defaultConfig() {
		$.messager.confirm("Confirmar","Recuperar valores de f&aacute;brica.¿Seguro?",function(r){
			if (r) {
				$('#config-form').form('load','/agility/server/database/sessionFunctions.php?Operation=defaultConfig');
			}
		});
	}
	
	function loadConfig() {
		$('#config-form').form('load','/agility/server/database/sessionFunctions.php?Operation=loadConfig');
	}
</script>
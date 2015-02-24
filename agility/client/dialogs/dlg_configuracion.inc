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
	<div id="config-tabs" class="easyui-tabs" data-options="tabPosition:'left'" style="width:100%;height:auto">
		<input id="config-Operation" type="hidden" name="Operation" value="saveConfig"/>
		<!-- TODO: Los parametros de la base de datos solo se deben presentar en el primer arranque -->

		<div title="Escritorio" style="padding:10px">		
			<h3>Apariencia del escritorio</h3>
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
		</div>
	
		<div title="Tablet" style="padding:10px">
			<h3>Funcionalidad del tablet del asistente de pista</h3>
			<label for="tablet_beep">Se&ntilde;al sonora al pulsar el tablet</label> 
			<input type="checkbox" name="tablet_beep" value="1"/><br />
			<label for="tablet_dnd">Orden de salida ajustable desde el tablet</label> 
			<input type="checkbox" name="tablet_dnd" value="1"/><br />
			<label for="tablet_chrono">Mostrar datos de cron&oacute;metro en tablet</label> 
			<input type="checkbox" name="tablet_chrono" value="1"/><br />
		</div>
		
		<div title="VideoMarcadores" style="padding:10px">
			<h3>Par&aacute;metros de los Video-marcadores</h3>
			<label for="vw_polltime">Frecuencia de refresco (seg.)</label>
			<input name="vw_polltime" type="text" size="2" value="5"/><br />
			<label for="vw_alpha">Transparencia del OnScreenDisplay (0.0-1.0)</label>
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
		</div>
		
		<div title="Informes" style="padding:10px">		
			<h3>Generaci&oacute;n de Informes y Formularios PDF</h3>
			<label for="pdf_topmargin">Margen superior impr. etiquetas (mmts)</label>
			<input name="pdf_topmargin" type="text" size="4" value="10.0"/><br />
			<label for="pdf_leftmargin">Margen izquierdo impr. etiquetas (mmts)</label>
			<input name="pdf_leftmargin" type="text" size="4" value="10.0"/><br />
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
		</div>
		
		<div title="Internet" style="padding:10px">
		<h3>Acceso p&uacute;blico v&iacute;a web</h3>
		</div>
				
		<div title="Sistema" style="padding:10px">
			<h3>Configuraci&oacute;n general</h3>
			<label for="lang">Idioma</label>
			<select id="config_lang" name="lang">
				<!-- TODO: retrieve list of available langs -->
				<option value="es_ES.UTF-8" selected="selected">Espa&ntilde;ol</option>
				<option value="en_US.UTF-8">Ingl&eacute;s</option>
			</select><br />
			<label for="debug_level">Nivel de depuracion</label>
			<select id="debug_level" name="debug_level">
				<!-- "PANIC","ALERT","ERROR","WARN","NOTICE","INFO","DEBUG","TRACE","ALL" -->
				<option value="0" >Ninguno.</option>
				<option value="1" >Alertas de sistema inutilizable</option>
				<option value="2" selected="selected">Errores no recuperables</option>
				<option value="3" >Errores recuperables</option>
				<option value="4" >Avisos a tener en cuenta</option>
				<option value="5" >Mensajes informativos</option>
				<option value="6">Informaci&oacute;n de depuraci&oacute;n</option>
				<option value="7">Registrar trazas de ejecuci&oacute;n</option>
				<option value="8">Registrar absolutamente todo</option>
			</select><br />
			<label for="proximity_alert">Alerta de proximidad en orden de salida</label>
			<input type="number" min="1" max="20" "size="2" value="5" name="proximity_alert"/> Perros<br />
		</div>
	</div>
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
    	data-options="iconCls:'icon-save'" onclick="saveConfig()">Guardar</a>
    </span>
</div>

<script type="text/javascript">
	addTooltip($('#config-resetBtn').linkbutton(),"Anula modificaciones. Recupera datos desde el servidor.");
	addTooltip($('#config-defaultBtn').linkbutton(),"Recuperar valores de fabrica de la aplicaci&oacute;n");
	addTooltip($('#config-okBtn').linkbutton(),"Aceptar datos. Guardar nueva configuraci&oacute;n");
	
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
<!-- 
dlg_equipos.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/tools.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA LOS DATOS DE EQUIPOS DE UNA PRUEBA -->
<div id="team_edit_dialog" title="Nuevo/Editar equipo" style="width:500px;padding:10px">
   	<div class="ftitle"><?php _e('Team information'); ?>:</div>
   	<form id="team_edit_dialog-form" class="easyui-form">
   		<input id="team_edit_dialog-ID" type="hidden" name="ID" value="0"/>
   		<input id="team_edit_dialog-Prueba" type="hidden" name="Prueba" value="0"/>
		<input id="team_edit_dialog-Jornada" type="hidden" name="Jornada" value="0"/>
   		<input id="team_edit_dialog-Operation" type="hidden" name="Operation" value="insert"/>
   	 	<div class="fitem">
			<label for="team_edit_dialog-Nombre" style="width:100px"><?php _e('Name'); ?>:</label>
			<input id="team_edit_dialog-Nombre" name="Nombre" style="width:250px;"/>
		</div>
		<div class="fitem">
			<label for="team_edit_dialog-Categorias" style="width:100px"><?php _e('Categories');?>:</label>
			<input id="team_edit_dialog-Categorias" type="hidden" name="Categorias" value=""/>
            <span id="team_edit_dialog-xlarge">
                <input type="checkbox" id="team_edit_dialog-X" name="X" value="X"/>
                <label id="team_edit_dialog_lbl-X" for="team_edit_dialog-X" style="width:40px">Xlarge</label>
            </span>
            <input type="checkbox" id="team_edit_dialog-L" name="L" value="L">
            <label id="team_edit_dialog_lbl-L" for="team_edit_dialog-L" style="width:40px">Large</label>
			<input type="checkbox" id="team_edit_dialog-M" name="M" value="M">
            <label id="team_edit_dialog_lbl-M" for="team_edit_dialog-M" style="width:40px">Medium</label>
			<input type="checkbox" id="team_edit_dialog-S" name="S" value="S">
            <label id="team_edit_dialog_lbl-S" for="team_edit_dialog-S" style="width:40px">Small</label>
			<span id="team_edit_dialog-tiny">
                <input type="checkbox" id="team_edit_dialog-T" name="T" value="T">
                <label id="team_edit_dialog_lbl-T" for="team_edit_dialog-T" style="width:40px">Tiny</label>
            </span>
		</div>
		<div class="fitem">
			<label for="team_edit_dialog-Observaciones" style="width:100px"><?php _e('Comments'); ?>:</label>
			<input id="team_edit_dialog-Observaciones" name="Observaciones" style="width:300px"/>
		</div>
   	</form>
	
</div>
   
<div id="team_dialog-buttons">
   	<a id="team_edit_dialog-okBtn" href="#" class="easyui-linkbutton" 
   			data-options="iconCls:'icon-save'" onclick="saveTeam()"><?php _e('Save'); ?></a>
	<a id="team_edit_dialog-cancelBtn" href="#" class="easyui-linkbutton" 
			data-options="iconCls:'icon-cancel'" onclick="$('#team_edit_dialog').dialog('close')"><?php _e('Close'); ?></a>
</div>

<script type="text/javascript">

// declaracion del dialogo
$('#team_edit_dialog').dialog({
	iconCls:'icon-huella',
	modal:true,
	closed:true,
	closable:true,
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:false,
	buttons:'#team_dialog-buttons',
	onBeforeOpen:function(){
		// hide tiny and xlarge button on affected federations
		var heights=howManyHeights();
        $('#team_edit_dialog-tiny').css('display',(parseInt(heights)!==3)?'inline':'none');
        $('#team_edit_dialog-xlarge').css('display',(parseInt(heights)===5)?'inline':'none');
	}
});

$('#team_edit_dialog-Nombre').textbox({
    required: true,
    validType: 'length[1,100]'
});

$.each(['X','L','M','S','T'],function(index,cat){
    var str=workingData.datosFederation.ListaCategoriasShort[cat];
    $('#team_edit_dialog_lbl-'+cat).html(str);
});

addTooltip($('#team_edit_dialog-okBtn').linkbutton(),'<?php _e("Register team data.<br />Update database"); ?>');
addTooltip($('#team_edit_dialog-cancelBtn').linkbutton(),'<?php _e("Terminate operations. Close window"); ?>');
</script>
<!-- 
import_ordensalida.inc

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
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE IMPORTACION DE UN RESULTADO -->
    <div id="importOrdenSalida-dialog" class="easyui-dialog" style="width:550px;height:auto;padding:10px 20px;"
        data-options="modal:true,closable: false,closed: true,buttons: '#importOrdenSalida-dlg-buttons',iconCls: 'icon-flag'">
        <div id="importOrdenSalida-title" class="ftitle"><?php _e('Results import'); ?></div>
        <p><span id="importOrdenSalida-Text"></span></p>
        <form id="importOrdenSalida-header">
        	<div class="fitem">
                <label for="importOrdenSalida-Search"><?php _e('Search'); ?>: </label>
                <select id="importOrdenSalida-Search" name="Search" style="width:250px"></select>&nbsp;
                <a id="importOrdenSalida-clearBtn" href="#" class="easyui-linkbutton"
                	data-options="iconCls: 'icon-undo'"><?php _e('Clear'); ?></a>
                <input type="hidden" id="importOrdenSalida-ResultID" value="0"/>
                <input type="hidden" name="importOrdenSalida-UseExcelNames" value="0"/>
        	</div>
        </form>
    </div>
    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO
    <div id="importOrdenSalida-dlg-buttons" style="display:inline-block;">
    -->
    <div id="importOrdenSalida-dlg-buttons" style="display:inline-block;">
        <!--
        No se pueden crear al vuelo inscripciones:hay que usar una ventana separada
        Por eso en este dialogo no hay boton de "create"
        <span style="float:left;">&nbsp;</span>
        <span style="float:right">
        -->
        	<a id="importOrdenSalida-okBtn" href="#" class="easyui-linkbutton"
                onclick="importAction('OrdenSalida','update',$('#importOrdenSalida-ResultID').val(),$('#importOrdenSalida-Search').combogrid('getValue'))"
        		data-options="iconCls:'icon-ok'"><?php _e('Select'); ?></a>
        	<a id="importOrdenSalida-cancelBtn" href="#" class="easyui-linkbutton"
                onclick="importAction('OrdenSalida','ignore',$('#importOrdenSalida-ResultID').val(),$('#importOrdenSalida-Search').combogrid('getValue'))"
        		data-options="iconCls:'icon-cancel'"><?php _e('Ignore'); ?></a>
        <!-- </span> -->
    </div>
    
    <script type="text/javascript">

    // datos del formulario de select/ignore
    // - declaracion del formulario
    $('#importOrdenSalida-form').form();

    // - botones
    addTooltip($('#importOrdenSalida-okBtn').linkbutton(),'<?php _e("Use selected dog for requested Excel import data"); ?>');
    addTooltip($('#importOrdenSalida-cancelBtn').linkbutton(),'<?php _e("Ignore data. Do not add entry to evaluated starting order"); ?>');
    addTooltip($('#importOrdenSalida-clearBtn').linkbutton(),'<?php _e("Clear selection"); ?>');
    $('#importOrdenSalida-clearBtn').bind('click',function() {
        $('#importOrdenSalida-header').form('reset'); // restore to initial values
    });

    // combo de busqueda/seleccion de perro inscrito
    $('#importOrdenSalida-Search').combogrid({
		panelWidth: 400,
		panelHeight: 150,
		idField: 'Perro',
        delay: 500,
		textField: 'Nombre',
		url: '../ajax/database/resultadosFunctions.php',
		queryParams: { Operation: 'enumerate' },
		method: 'get',
		mode: 'remote',
		columns: [[
			{field:'Perro',hidden:'true'},
            {field:'Licencia',title:'<?php _e('Lic'); ?>',width:10,align:'right'},
            {field:'Nombre',title:'<?php _e('Dog'); ?>',width:15,align:'right'},
			{field:'Categoria',title:'<?php _e('Cat'); ?>.',width:5,align:'center',formatter:formatCategoria},
			{field:'Grado',title:'<?php _e('Grade'); ?>',width:5,align:'center',formatter:formatGrado},
			{field:'NombreGuia',title:'<?php _e('Handler'); ?>',width:30,align:'right'},
			{field:'NombreClub',title:'<?php _e('Club'); ?>',width:20,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		singleSelect: true,
        onBeforeLoad: function(params) { // don't invoke if no manga declared (ie: at startup )
            if (typeof(workingData.manga)==="undefined") return false;
            if (parseInt(workingData.manga)===0) return false;
            params.Operation='enumerate';
            params.Federation=workingData.federation;
            params.Manga=workingData.manga;
            params.Mode=ac_import.mode;
            return true;
        }
	});
    </script>
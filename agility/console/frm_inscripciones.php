<!-- 
frm_inscripciones.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

<!-- background image -->
<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle"/>

<!-- FORMULARIO DE SELECCION DE PRUEBAS ABIERTAS-->
<div id="selprueba-window" style="position:relative;width:400px;height:auto;padding:20px 20px">
	<div id="selprueba-Layout" class="easyui-layout" data-options="fit:true'">
		<div id="selprueba-Content" data-options="region:'north',border:'true'">
			<form id="selprueba-Prueba">
        		<div class="fitem">
					<label for="Search"><?php _e('Select contest');?>:</label>
            		<select id="selprueba-Search" name="Search" style="width:200px"></select>
        		</div>
			</form>
		</div> <!-- contenido -->
		<div data-options="region:'center'"></div>
		<div id="selprueba-Buttons" data-options="region:'south',border:false" style="text-align:right;padding:5px 0 0;">
    	    <a id="selprueba-okBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-ok'" onclick="acceptSelectPrueba()"><?php _e('Accept'); ?></a>
    	    <a id="selprueba-cancelBtn" href="#" class="easyui-linkbutton" 
    	    	data-options="iconCls:'icon-cancel'" onclick="cancelSelectPrueba()"><?php _e('Cancel'); ?></a>
		</div>	<!-- botones -->
	</div> <!-- Layout -->
</div> <!-- Window -->

<script type="text/javascript">

$('#selprueba-window').window({
	title: "<?php _e('Select active contest');?>",
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: true,
	shadow: true,
	modal: true
}).window('open');

addTooltip($('#selprueba-okBtn').linkbutton(),'<?php _e("Continue working with selected contest");?>');
addTooltip($('#selprueba-cancelBtn').linkbutton(),'<?php _e("Cancel selection. close window");?>');

$('#selprueba-Search').combogrid({
	panelWidth: 450,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	editable: isMobileDevice()?false:true, //disable keyboard deploy on mobile devices
	columns: [[
	    {field:'ID',hidden:true},
		{field:'Nombre',        title:'<?php _e('Name');?>', width:60,align:'right'},
		{field:'Club',hidden:true},
		{field:'NombreClub',    title:'<?php _e('Club');?>',   width:30,align:'right'},
        {field:'RSCE',			title:'<?php _e('Fed');?>.',	width:10,	align:'center', formatter:formatFederation},
        {field:'OpeningReg',    hidden:true},
        {field:'ClosingReg',    hidden:true},
		{field:'Observaciones', hidden:true }
	]],
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	selectOnNavigation: false,
	onLoadSuccess: function(data) {
		if (workingData.prueba!=0) $('#selprueba-Search').combogrid('setValue',workingData.prueba);
	}
});

function acceptSelectPrueba() {
	// si no hay ninguna prueba valida seleccionada aborta
	var title="";
	var page="/agility/console/frm_main.php";
	var p=$('#selprueba-Search').combogrid('grid').datagrid('getSelected');
	if (p==null) {
		// indica error
		$.messager.alert("Error",'<?php _e("You should select a valid contest");?>',"error");
		return;
	} else {
	    // comprobamos el periodo de inscripcion
        // si fuera de plazo avisamos, pero dejamos continuar
        var current=Date.now();
        var rfrom=Date.parse(p.OpeningReg); // abre a las 00:00 del primer dia
        var rto=86400000+Date.parse(p.ClosingReg); // cierra a las 23:59 del ultimo dia
        if ( (rfrom>current) || (rto<current)) {
            $.messager.alert({
                title: '<?php _e("Warning");?>',
                msg: '<?php _e("Out inscription period for this contest");?>',
                icon: 'warning',
                width: 350
            });
        }
        console.log("current:"+current+" from:"+rfrom+" to:"+rto);
		setPrueba(p);
		setFederation(p.RSCE);
		page="/agility/console/frm_inscripciones2.php";
		title='<?php _e("Inscriptions - Registering form");?>';
	}
	$('#selprueba-window').window('close');
	var extradlgs={
	    'inscripciones':'#new_inscripcion-dialog',
        'equipos':'#team_datagrid-dialog',
        'import':'#inscripciones-excel-dialog',
	    'newdog':'#perros-dialog'
	};
	check_softLevel(access_level.PERMS_OPERATOR,function() {loadContents(page,title,extradlgs);});
}

function cancelSelectPrueba() {
	var title="";
	var page="/agility/console/frm_main.php";
	$('#selprueba-window').window('close');
	loadContents(page,title);
}


</script>

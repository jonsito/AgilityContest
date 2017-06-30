<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
/*
 index.php

 Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

 This program is free software; you can redistribute it and/or modify it under the terms
 of the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once(__DIR__ . "/server/tools.php");
require_once(__DIR__ . "/server/auth/Config.php");
require_once(__DIR__ . "/server/auth/AuthManager.php");
if(!isset($config)) $config =Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
	die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}
$am=new AuthManager("Public");
if (!$am->allowed(ENABLE_PUBLIC)) {
	die("Current license has no permissions to handle public (web) access related functions");
}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="application-name" content="Agility Contest" />
<meta name="copyright" content="© 2013-2015 Juan Antonio Martinez" />
<meta name="author" lang="en" content="Juan Antonio Martinez" />
<meta name="description"
        content="A web client-server (xampp) app to organize, register and show results for FCI Dog Agility Contests" />
<meta name="distribution" 
	content="This program is free software; you can redistribute it and/or modify it under the terms of the 
		GNU General Public License as published by the Free Software Foundation; either version 2 of the License, 
		or (at your option) any later version." />
<!-- try to disable zoom in tablet on double click -->
<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=0.5, user-scalable=yes"/>
<title>AgilityContest (Public)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
<link rel="stylesheet" type="text/css" href="/agility/css/public_css.php" />
<script src="/agility/lib/jquery-1.12.3.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/results_and_scores.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/public/public.js.php" type="text/javascript" charset="utf-8" > </script>
<script type="text/javascript" charset="utf-8">

/* make sure configuration is loaded from server before onLoad() event */

var pb_config = {
    'Timeout': null,
    'LastEvent': 0,
    'ConsoleMessages': '',
    'SelectedDorsal': 0
};
loadConfiguration();
getLicenseInfo();
getFederationInfo();

/* not really needed for public access, but stay here for compatibility */
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!==null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',ac_authInfo.SessionKey);
		}
	    return true;
	  }
	});
}

function myRowStyler(idx,row) { return pbRowStyler(idx,row); }
function myRowStyler2(idx,row) { return pbRowStyler2(idx,row); }
	
</script>

<style>
    body {
        /* default background from environment */
        font-size: 100%;
        background: <?php echo $config->getEnv('easyui_bgcolor'); ?>;
    }

    /* remove underlines around footer imagelinks */
    a,
    a img {
        text-decoration: none;
        outline: none;
        border: 0px none transparent;
    }

	/* tip for fix data size in smartphones ----------- */
	@media only screen and (max-width: 760px) {

		.datagrid-cell {
			font-size:0.75em;
		}

	}

</style>

</head>

<body style="margin:0;padding:0" onload="initialize();">

<div id="public-contenido" style="width:100%;height:100%;margin:0;padding:0"></div>

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="public-dialog" style="width:400px;height:200px;padding:10px" class="easyui-dialog"
	data-options="title: '<?php _e("Select contest, journey and view");?>',iconCls: 'icon-list',buttons: '#public-Buttons',collapsible:false, minimizable:false,
		maximizable:false, closable:true, closed:false, shadow:true, modal:true">
	<form id="public-form">       		
    	<div class="fitem">
       		<label for="public-Prueba"><?php _e('Contest'); ?>:</label>
       		<select id="public-Prueba" name="Prueba" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="public-Jornada"><?php _e('Journey'); ?>:</label>
       		<select id="public-Jornada" name="Jornada" style="width:200px"></select>
    	</div>    	
    	<div class="fitem">
       		<label for="public-Operation"><?php _e('Select View'); ?>:</label>
       		<select id="public-Operation" name="Operation" style="width:200px">
				<option value="inscritos"><?php _e('Inscription list'); ?></option>
				<option value="entrenamientos"><?php _e('Training session'); ?></option>
       		<option value="ordensalida"><?php _e('Starting order'); ?></option>
       		<option value="parciales"><?php _e('Partial scores'); ?></option>
       		<option value="clasificaciones"><?php _e('Final scores'); ?></option>
            <option value="programa"><?php _e('Journey activities timetable'); ?></option>
       		</select>
    	</div>
	</form>
</div> <!-- Dialog -->

<div id="public-Buttons" style="text-align:right;padding:5px;">
   	<a id="public-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls:'icon-ok'" onclick="public_acceptSelection()">Aceptar</a>
</div>	<!-- botones -->

<script type="text/javascript">

$('#public-form').form();
$('#public-Operation').combobox({valueField:'value',panelHeight:'auto'});

$('#public-Prueba').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/pruebaFunctions.php?Operation=enumerate',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	selectOnNavigation: true, // let use cursor keys to interactive select
	columns: [[
	   	    {field:'ID',hidden:true},
			{field:'Nombre',        title:'<?php _e('Name'); ?>',width:'50%',align:'right'},
            {field:'Club',hidden:true},
			{field:'NombreClub',    title:'<?php _e('Club'); ?>',width:'30%',align:'right'},
            {field:'RSCE',			title:'<?php _e('Fed'); ?>.',	width:'10%',	align:'center', formatter:formatFederation},
			{field:'Observaciones',hidden:true }
	]],
	onChange:function(value){
        var p=$('#public-Prueba').combogrid('grid').datagrid('getSelected');
        if (p===null) return; // no selection
        setPrueba(p); // ajusta los datos de la prueba
		setFederation(p.RSCE);
		var g = $('#public-Jornada').combogrid('grid');
		g.datagrid('load',{Prueba:p.ID});
	}
});

$('#public-Jornada').combogrid({
	panelWidth: 550,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/jornadaFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false, // to disable tablet keyboard popup
	columns: [[
	    { field:'ID',			hidden:true }, // ID de la jornada
	    { field:'Prueba',		hidden:true }, // ID de la prueba
	    { field:'Numero',		width:4, sortable:false,	align:'center', title: '#'},
		{ field:'Nombre',		width:30, sortable:false,   align:'right',  title: '<?php _e('Name');?>/<?php _e('Comments');?>' },
		{ field:'Fecha',		hidden:true},
		{ field:'Hora',			hidden:true},
		{ field:'Grado1',		width:8, sortable:false,	align:'center', title: 'G-I    ' },
		{ field:'Grado2',		width:8, sortable:false,	align:'center', title: 'G-II   ' },
		{ field:'Grado3',		width:8, sortable:false,	align:'center', title: 'G-III  ' },
		{ field:'Open',		    width:8, sortable:false,	align:'center', title: 'Open   ' },
		{ field:'Equipos3',		width:8, sortable:false,	align:'center', title: 'Eq.Best' },
		{ field:'Equipos4',		width:8, sortable:false,	align:'center', title: 'Eq.Comb' },
		{ field:'PreAgility',	width:8, sortable:false,	align:'center', title: 'PreAg. ' },
        { field:'PreAgility2',	hidden:true }, /* not used since 3.4 */
		{ field:'Junior',	    width:8, sortable:false,	align:'center', title: 'Junior ' },
        { field:'Senior',   	hidden:true }, // not used yet
		{ field:'KO',			width:8, sortable:false,	align:'center', title: 'K.O.   ' },
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate';
		param.Prueba=workingData.prueba;
		param.AllowClosed=1;
		param.HideUnassigned=1;
		return true;
	}
});

function public_acceptSelection() {
	var o=$('#public-Operation').combobox('getValue');
	// si datos invalidos cancelamos operacion
	var p=$('#public-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#public-Jornada').combogrid('grid').datagrid('getSelected');
	if ( (p==null) || (j==null) || (o==null)) {
		// indica error
		$.messager.alert("Error",'<?php _e("You should select contest, journey and view to continue"); ?>',"error");
		return;
	}
    setPrueba(p);
    setJornada(j);
	setFederation(p.RSCE);
	workingData.manga=0;
	workingData.tanda=0;
	workingData.mode=-1;
    var page='/agility/console/frm_notavailable.php';
	switch (o){
	case 'inscritos':
        if (isJornadaEquipos(null) ) page="/agility/public/pb_inscripciones_equipos.php";
        else page="/agility/public/pb_inscripciones.php";
		break;
	case 'ordensalida':
		page="/agility/public/pb_ordensalida.php";
		break;
	case 'entrenamientos':
			page="/agility/public/pb_entrenamientos.php";
			break;
	case 'parciales':
        if (isJornadaEquipos(null) ) page="/agility/public/pb_parciales_equipos.php";
        else page="/agility/public/pb_parciales.php";
		break;
	case 'clasificaciones':
        if (isJornadaEquipos(null) ) page="/agility/public/pb_finales_equipos.php";
        else page="/agility/public/pb_finales.php";
        break;
    case 'programa':
        page="/agility/public/pb_programa.php";
        break;
    }
	$('#public-dialog').dialog('close').remove();
	$('#public-contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#public_contenido').load('/agility/console/frm_notavailable.php');
			}
		);
}

</script>
</body>
</html> 

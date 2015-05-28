<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
require_once(__DIR__ . "/server/auth/Config.php");
require_once(__DIR__ . "/server/tools.php");
$config =Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
	die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
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
<meta content='width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=no;' name='viewport' />
<title>AgilityContest (Public)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
<link rel="stylesheet" type="text/css" href="/agility/css/public_css.php" />
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-groupview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/public/public.js" type="text/javascript" charset="utf-8" > </script>
<script type="text/javascript" charset="utf-8">

/* not really needed for public access, but stay here for compatibility */
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(authInfo.SessionKey)!=='undefined' && authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',authInfo.SessionKey);
		}
	    return true;
	  }
	});
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @param {int} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('vw_rowcolor1'); ?>';
	var c2='<?php echo $config->getEnv('vw_rowcolor2'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

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

    /* ajuste de las cabeceras de los datagrid groupview */
    .datagrid-body .datagrid-group {
        background-color: #ccc;
        height:40px;
        line-height: 40px;
    }
    .datagrid-body .datagrid-group .datagrid-group-title {
        height:40px;
        line-height: 40px;
        font-weight: bold;
    }
    .datagrid-body .datagrid-group .datagrid-group-expander {
        margin-top:7px;
    }

</style>

</head>

<body style="margin:0;padding:0" onload="initialize();">

<div id="public-contenido" style="width:100%;height:100%;margin:0;padding:0"></div>

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="public-dialog" style="width:350px;height:200px;padding:10px" class="easyui-dialog"
	data-options="title: 'Indicar Prueba, Jornada y Vista',iconCls: 'icon-list',buttons: '#public-Buttons',collapsible:false, minimizable:false,
		maximizable:false, closable:true, closed:false, shadow:true, modal:true">
	<form id="public-form">       		
    	<div class="fitem">
       		<label for="public-Prueba">Prueba:</label>
       		<select id="public-Prueba" name="Prueba" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="public-Jornada">Jornada:</label>
       		<select id="public-Jornada" name="Jornada" style="width:200px"></select>
    	</div>    	
    	<div class="fitem">
       		<label for="public-Operation">Vista:</label>
       		<select id="public-Operation" name="Operation" style="width:200px">
       		<option value="inscritos">Listado de Inscritos</option>
       		<option value="ordensalida">Orden de Salida</option>
       		<option value="parciales">Resultados Provisionales</option>
       		<option value="clasificaciones">Clasificaciones</option>
            <option value="programa">Programa de la jornada</option>
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

$('#public-Prueba').combogrid({
	panelWidth: 400,
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
			{field:'Nombre',title:'Nombre',width:50,align:'right'},
            {field:'Club',hidden:true},
			{field:'NombreClub',title:'Club',width:20,align:'right'},
            {field:'RSCE',			title:'Fed.',			width:15,	align:'center', formatter:formatRSCE},
			{field:'Observaciones',title:'Observaciones.',width:30,align:'right'}
	]],
	onChange:function(value){
        var p=$('#public-Prueba').combogrid('grid').datagrid('getSelected');
        if (p===null) return; // no selection
        setPrueba(p); // ajusta los datos de la prueba
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
		{ field:'Nombre',		width:30, sortable:false,   align:'right',  title: 'Nombre/Comentario' },
		{ field:'Fecha',		hidden:true},
		{ field:'Hora',			hidden:true},
		{ field:'Grado1',		width:8, sortable:false,	align:'center', title: 'G-I    ' },
		{ field:'Grado2',		width:8, sortable:false,	align:'center', title: 'G-II   ' },
		{ field:'Grado3',		width:8, sortable:false,	align:'center', title: 'G-III  ' },
		{ field:'Open',		    width:8, sortable:false,	align:'center', title: 'Open   ' },
		{ field:'Equipos3',		width:8, sortable:false,	align:'center', title: 'Eq.3x4 ' },
		{ field:'Equipos4',		width:8, sortable:false,	align:'center', title: 'Eq.Conj' },
		{ field:'PreAgility',	width:8, sortable:false,	align:'center', title: 'Pre. 1 ' },
		{ field:'PreAgility2',	width:8, sortable:false,	align:'center', title: 'Pre. 2 ' },
		{ field:'KO',			width:8, sortable:false,	align:'center', title: 'K.O.   ' },
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate';
		param.Prueba=workingData.prueba;
		param.AllowClosed=0;
		param.HideUnassigned=1;
		return true;
	}
});

function public_acceptSelection() {
	var o=$('#public-Operation').val();
	// si datos invalidos cancelamos operacion
	var p=$('#public-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#public-Jornada').combogrid('grid').datagrid('getSelected');
	var page="'/agility/console/frm_notavailable.php";
	if ( (p==null) || (j==null) || (o==null)) {
		// indica error
		$.messager.alert("Error","Debe indicar los datos de prueba, jornada y vista seleccionada","error");
		return;
	}
    setPrueba(p);
    setJornada(j);
	workingData.manga=0;
	workingData.tanda=0;
	workingData.mode=-1;
    page='/agility/console/frm_notavailable.php';
	switch (o){
	case 'inscritos':
        if (isJornadaEq3() ) page="/agility/public/pb_inscripciones_equipos.php";
        else if (isJornadaEq4() ) page="/agility/public/pb_inscripciones_equipos.php";
        else page="/agility/public/pb_inscripciones.php";
		break;
	case 'ordensalida':
		page="/agility/public/pb_ordensalida.php";
		break;
	case 'parciales':
        if (isJornadaEq3() ) page="/agility/public/pb_parciales_eq3.php";
        else if (isJornadaEq4() ) page="/agility/public/pb_parciales_eq4.php";
        else page="/agility/public/pb_parciales.php";
		break;
	case 'clasificaciones':
        if (isJornadaEq3() ) page="/agility/public/pb_finales_eq3.php";
        else if (isJornadaEq4() ) page="/agility/public/pb_finales_eq4.php";
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

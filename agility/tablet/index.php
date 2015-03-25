<?php
header("Access-Control-Allow-Origin: https//{$_SERVER['SERVER_ADDR']}/agility",false);
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}/agility",false);
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
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
<title>AgilityContest (Tablet)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.1/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/tablet.css" />
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.1/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<?php if (toBoolean($config->getEnv('tablet_chrono'))) { ?>
<script src="/agility/lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<?php } ?>
<script src="/agility/lib/jquery-easyui-1.4.1/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/tablet/tablet.js" type="text/javascript" charset="utf-8" > </script>
<script type="text/javascript" charset="utf-8">
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(authInfo.SessionKey)!=undefined && authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',authInfo.SessionKey);
		}
	    return true;
	  }
	});
}

/**
 * Common rowStyler function for AgilityContest datagrids
 * @paramm {integer} idx Row index
 * @param {Object} row Row data
 * @return {string} proper row style for given idx
 */
function myRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
	var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}
 
var ac_config= {
	// version, logging y depuracion
	'debug_level'		: '<?php echo $config->getEnv('debug_level'); ?>',
	'version_name'		: '<?php echo $config->getEnv('version_name'); ?>',
	'version_date'		: '<?php echo $config->getEnv('version_date'); ?>',
	// Internacionalizacion. Idiomas
	'lang'				: '<?php echo $config->getEnv('lang'); ?>',
	// variables del sistema
	'proximity_alert'	: <?php echo $config->getEnv('proximity_alert'); ?>,

	// entorno grafico
	'easyui_theme' 		: '<?php echo $config->getEnv('easyui_theme'); ?>',
	'easyui_bgcolor'	: '<?php echo $config->getEnv('easyui_bgcolor'); ?>',
	'easyui_hdrcolor'	: '<?php echo $config->getEnv('easyui_hdrcolor'); ?>',
	'easyui_opcolor'	: '<?php echo $config->getEnv('easyui_opcolor'); ?>',
	'easyui_rowcolor1'	: '<?php echo $config->getEnv('easyui_rowcolor1'); ?>',
	'easyui_rowcolor2'	: '<?php echo $config->getEnv('easyui_rowcolor2'); ?>',
	'easyui_rowcolor3'	: '<?php echo $config->getEnv('easyui_rowcolor3'); ?>',
	// configuracion del videowall
	'vw_polltime'		: <?php echo $config->getEnv('vw_polltime'); ?>,
	'vw_alpha'			: <?php echo $config->getEnv('vw_alpha'); ?>,
	'vw_hdrfg1'			: '<?php echo $config->getEnv('vw_hdrfg1'); ?>',
	'vw_hdrbg1'			: '<?php echo $config->getEnv('vw_hdrbg1'); ?>',
	'vw_hdrfg2'			: '<?php echo $config->getEnv('vw_hdrfg2'); ?>',
	'vw_hdrbg2'			: '<?php echo $config->getEnv('vw_hdrbg2'); ?>',
	'vw_rowcolor1'		: '<?php echo $config->getEnv('vw_rowcolor1'); ?>',
	'vw_rowcolor2'		: '<?php echo $config->getEnv('vw_rowcolor2'); ?>',
	// generacion de PDF's
	'pdf_topmargin'		: '<?php echo $config->getEnv('pdf_topmargin'); ?>',
	'pdf_leftmargin'	: '<?php echo $config->getEnv('pdf_leftmargin'); ?>',
	'pdf_hdrfg1'		: '<?php echo $config->getEnv('pdf_hdrfg1'); ?>',
	'pdf_hdrbg1'		: '<?php echo $config->getEnv('pdf_hdrbg1'); ?>',
	'pdf_hdrfg2'		: '<?php echo $config->getEnv('pdf_hdrfg2'); ?>',
	'pdf_hdrbg2'		: '<?php echo $config->getEnv('pdf_hdrbg2'); ?>',
	'pdf_rowcolor1'		: '<?php echo $config->getEnv('pdf_rowcolor1'); ?>',
	'pdf_rowcolor2'		: '<?php echo $config->getEnv('pdf_rowcolor2'); ?>',
	'pdf_linecolor'		: '<?php echo $config->getEnv('pdf_linecolor'); ?>',
	// personalizacion del tablet
	'tablet_beep'		: <?php echo toBoolean($config->getEnv('tablet_beep'))?'true':'false'; ?>,
	'tablet_dnd'		: <?php echo toBoolean($config->getEnv('tablet_dnd'))?'true':'false'; ?>,
	'tablet_chrono'		: <?php echo toBoolean($config->getEnv('tablet_chrono'))?'true':'false'; ?>,
	'tablet_next'		: <?php echo toBoolean($config->getEnv('tablet_next'))?'true':'false'; ?>
}
</script>

<style>
body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }
</style>

</head>

<body style="margin:0;padding:0" onload="initialize();">

<div id="tablet_contenido" style="width:100%;height:100%;margin:0;padding:0"></div>

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="seltablet-dialog" style="width:350px;height:275px;padding:10px" class="easyui-dialog"
	data-options="title: 'Datos de Usuario, Prueba y Jornada',iconCls: 'icon-list',buttons: '#seltablet-Buttons',collapsible:false, minimizable:false,
		maximizable:false, closable:true, closed:false, shadow:true, modal:true">
	<form id="seltablet-form">
       	<div class="fitem">
       		<label for="Username">Usuario:</label>
       		<input id="seltablet-Username" name="Username" style="width:200px" type="text"
       			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
       	</div>        		
       	<div class="fitem">
       		<label for="Password">Contrase&ntilde;a:</label>
       		<input id="seltablet-Password" name="Password" style="width:200px" type="password"
       			class="easyui-validatebox" data-options="required:true,validType:'length[1,255]'"/>
       	</div>
       	<div>&nbsp;</div>
    	<div class="fitem">
       		<label for="Sesion">Sesi&oacute;n:</label>
       		<select id="seltablet-Sesion" name="Sesion" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="Prueba">Prueba:</label>
       		<select id="seltablet-Prueba" name="Prueba" style="width:200px"></select>
    	</div>        		
    	<div class="fitem">
       		<label for="Jornada">Jornada:</label>
       		<select id="seltablet-Jornada" name="Jornada" style="width:200px"></select>
    	</div>
	</form>
</div> <!-- Dialog -->

<div id="seltablet-Buttons" style="text-align:right;padding:5px;">
   	<a id="seltablet-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls:'icon-ok'" onclick="tablet_acceptSelectJornada()">Aceptar</a>
</div>	<!-- botones -->

<script type="text/javascript">

$('#seltablet-form').form();

$('#seltablet-Sesion').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/sessionFunctions.php',
	method: 'get',
	mode: 'remote',
	required: true,
	rownumber: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false,  // to disable tablet keyboard popup
	columns: [[
	   	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
			{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: 'Nombre' },
			{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: 'Observaciones' }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='select';
		param.Hidden=0;
		return true;
	}
});

$('#seltablet-Prueba').combogrid({
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
		{field:'ID',			hidden:true},
		{field:'Nombre',		title:'Nombre',			width:50,	align:'right'},
		{field:'Club',			hidden:true},
		{field:'NombreClub',	title:'Club',			width:20,	align:'right'},
		{field:'RSCE',			title:'Fed.',			width:15,	align:'center', formatter:formatRSCE},
		{field:'Observaciones',	title:'Observaciones.',	width:30,	align:'right'}
	]],
	onChange:function(value){
		var p=$('#seltablet-Prueba').combogrid('grid').datagrid('getSelected');
		if (p===null) return; // no selection
		setPrueba(p); // retorna jornada, o 0 si la prueba ha cambiado
		$('#seltablet-Jornada').combogrid('clear');
		$('#seltablet-Jornada').combogrid('grid').datagrid('load',{Prueba:p.ID});
	}
});

$('#seltablet-Jornada').combogrid({
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
		{ field:'Especial',		width:8, sortable:false,	align:'center', title: 'Show   ' },
	]],
	onBeforeLoad: function(param) { 
		param.Operation='enumerate', 
		param.Prueba=workingData.prueba;
		param.AllowClosed=0;
		param.HideUnassigned=1;
		return true;
	}
});

function tablet_acceptSelectJornada() {
	// si prueba invalida cancelamos operacion
	var s=$('#seltablet-Sesion').combogrid('grid').datagrid('getSelected');
	var p=$('#seltablet-Prueba').combogrid('grid').datagrid('getSelected');
	var j=$('#seltablet-Jornada').combogrid('grid').datagrid('getSelected');
	var user=$('#seltablet-Username').val();
	var pass=$('#seltablet-Password').val();
	if ( (p==null) || (j==null) ) {
		// indica error
		$.messager.alert("Error","Debe<br />- Indicar la sesión para los videomarcadores<br />- Seleccionar prueba/jornada para manejo de datos","error");
		return;
	}
	if (!user || !user.length) {
		$.messager.alert("Invalid data","No ha indicado ningún usuario","error");
		return;
	};

	var parameters={ 
		'Operation':'login',
		'Username': user,
		'Password': pass,
		'Session' : s.ID,
		'Nombre'  : s.Nombre,
		'Source'  : 'tablet_'+s.ID,
		'Prueba'  : p.ID,
		'Jornada' : j.ID,
		'Manga'   : 0,
		'Tanda'   : 0,
		'Perro'   : 0
	};

	// call login
	// updateSessionInfo(s.ID,{Nombre: s.Nombre,Prueba:p.ID, Jornada:j.ID});
	
	$.ajax({
		type: 'POST',
  		url: 'https://'+window.location.hostname+'/agility/server/database/userFunctions.php',
   		dataType: 'jsonp',
   		data: parameters,
   		contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
   		success: function(data) {
    		if (data.errorMsg) { 
        		$.messager.alert("Error",data.errorMsg,"error"); 
        		initAuthInfo(); // initialize to null
        	} else {
        		$.messager.alert(
        	    	"Usuario: "+data.Login,
        	    	"Sesi&oacute;n iniciada correctamente",
        	    	"info",
        	    	function() {
        	    	   	initAuthInfo(data);
        	    	   	initWorkingData(s.ID);
        	    	   	// los demas valores se actualizan en la linea anterior
        	    		workingData.nombreSesion=s.Nombre;
        	    		workingData.nombrePrueba=p.Nombre;
        	    		workingData.nombreJornada=j.Nombre;	
        	    		var page="/agility/tablet/tablet_competicion.php";
        	    		if (workingData.datosJornada.Equipos3==1) {
        	    			page="/agility/tablet/tablet_competicion_eq3.php";
        	    		}
        	    		if (workingData.datosJornada.Equipos4==1) {
        	    			page="/agility/tablet/tablet_competicion_eq4.php";
        	    		}
        	    		if (workingData.datosJornada.Open==1) {
        	    			page="/agility/tablet/tablet_competicion_open.php";
        	    		}
        	    		if (workingData.datosJornada.KO==1) {
        	    			page="/agility/tablet/tablet_competicion_ko.php";
        	    		}
        	    		$('#seltablet-dialog').dialog('close');
        	    		$('#tablet_contenido').load(	
        	    				page,
        	    				function(response,status,xhr){
        	    					if (status=='error') $('#tablet_contenido').load('/agility/frm_notavailable.php');
        	    				}
        	    			); // load
        	    	} // close dialog; open main window
        	    ); // alert calback
        	} // if no ajax error
    	}, // success function
   		error: function() { alert("error");	},
	}); // ajax call
}

//on Enter key on login field fo	cus on password
$('#seltablet-Username').bind('keypress', function (evt) {
    if (evt.keyCode != 13) return true;
    $('#seltablet-Password').focus();
    return false;
});
//on Enter key on login field fo	cus on password
$('#seltablet-Password').bind('keypress', function (evt) {
    if (evt.keyCode != 13) return true;
    $('#seltablet-Sesion').next().find('input').focus();
    return false;
});
</script>
</body>
</html> 

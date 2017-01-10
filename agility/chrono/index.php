<?php
/*
 chrono/index.php

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
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
if ( intval($config->getEnv('restricted'))!=0) {
    die("Access other than public directory is not allowed");
}
$am=new AuthManager("Chrono");
if (!$am->allowed(ENABLE_CHRONO)) {
	die("Current license has no permissions to handle chronometer related functions");
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
<title>AgilityContest (Chrono)</title>
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/chrono_css.php" />
<script src="/agility/lib/HackTimer/HackTimer.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fittext-1.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/chrono/chrono.js.php" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript" charset="utf-8">
function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!=null) {
			jqXHR.setRequestHeader('X-AC-SessionKey',ac_authInfo.SessionKey);
		}
	    return true;
	  }
	});
	loadConfiguration( function(config){c_reconocimiento.reset(60*parseInt(config.crono_rectime)); } );
	getLicenseInfo();
	getFederationInfo();
}

</script>
<style>
body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }
</style>
</head>

<body style="margin:0;padding:0;background-color:blue;font-size:100%" onload="initialize();">
<div id="chrono-contenido">

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="chrono-dialog" class="easyui-dialog" style="position:relative;width:350px;height:auto;padding:10px 10px">
	<form id="chrono-Selection">
    	<div class="fitem">
       		<label for="chrono-Session"><?php _e('Ring');?>:</label>
       		<select id="chrono-Session" name="Session" style="width:200px"></select>
    	</div>
	</form>
</div> <!-- Window -->

<div id="chrono-Buttons" style="text-align:right">
   	<a id="chrono-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls: 'icon-ok'" onclick="chrono_accept()"><?php _e('Accept');?></a>
</div>	<!-- botones -->

</div> <!-- contenido -->

<script type="text/javascript">
$('#chrono-dialog').dialog({
	title: "<?php _e('Select ring');?>",
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: true,
	modal: true,
	buttons: '#chrono-Buttons' 
});

$('#chrono-form').form();

addTooltip($('#chrono-okBtn').linkbutton(),"<?php _e('Work with selected ring/session');?>");

$('#chrono-Session').combogrid({
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
	editable: false, // avoid keyboard deploy
	columns: [[
	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
		{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: "<?php _e('Name');?>" },
		{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: "<?php _e('Comments');?>" },
		{ field:'Prueba', hidden:true },
		{ field:'Jornada', hidden:true }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='selectring';
		param.Hidden=0;
		return true;
	},
	onLoadSuccess: function(data) {
		var cs=$('#chrono-Session');
		var def= cs.combogrid('grid').datagrid('getRows')[0].ID; // get first ID
		cs.combogrid('setValue',def);
	},
	onSelect: function(index,row) { setupWorkingData(row.Prueba,row.Jornada,(row.manga>0)?row.manga:1); }
});

function chrono_accept() {
	// si sesion invalida cancelamos operacion
	var sid=$('#chrono-Session').combogrid('getValue');
	if ( sid===null ) {
		// indica error
		$.messager.alert("Error","<?php _e('You should select a valid session/ring');?>","error");
		return;
	}
	// store selected data into global structure
	initWorkingData(sid,chrono_eventManager);
	var page='/agility/chrono/chrono.inc.php';
	$('#chrono-dialog').dialog('close');
	$('#chrono-contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#chrono-contenido').load('/agility/console/frm_notavailable.php');
			}
		);
}

</script>
</body>
</html> 

<?php
/*
 chrono/index.php

 Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
define("SYSTEM_INI",__DIR__ . "/../../config/system.ini");
if (!file_exists(SYSTEM_INI)) {
    die("Missing system configuration file: ".SYSTEM_INI." . Please properly configure and install application");
}
$config =Config::getInstance();
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
if ( intval($config->getEnv('running_mode')) === AC_RUNMODE_SLAVE ) {
    die("Access other than public directory is not allowed");
}
$am=AuthManager::getInstance("Chrono");
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
<link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="../css/chrono_css.php" />
<script src="../lib/HackTimer/HackTimer.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/jquery.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-fittext-1.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="../chrono/chrono.js.php" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript" charset="utf-8">

var ac_clientOpts = {
    BaseName:   'chrono',
    Ring:       2, // defaults to session id:2 --> ring 1
    View:       0,
    Mode:       0, // no view nor mode in chrono, but needed
    SensorDate: 0,
    Timeout:    0,
    Name:       '',
    SessionName: '' // to be filled later
};

function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(ac_authInfo.SessionKey)!=='undefined' && ac_authInfo.SessionKey!==null) {
			jqXHR.setRequestHeader('X-Ac-Sessionkey',ac_authInfo.SessionKey);
		}
	    return true;
	  }
	});
	loadConfiguration( function(config){
	    c_reconocimiento.reset(60*parseInt(config.crono_rectime));
        config.pending_events={'llamada':null,'aceptar':null}; // to store events that need to be parsed
	} );
	getLicenseInfo();
	getFederationInfo();
    ac_clientOpts.Ring=<?php echo http_request("Ring","i",1); ?>; // defaults to ring 1
    ac_clientOpts.Timeout=<?php echo http_request("Timeout","i",0); ?>; // auto start displaying after x seconds. 0 disable
    // session name. defaults to random string(8)@client.ip.address
    ac_clientOpts.Name='<?php echo http_request("Name","s", getDefaultClientName("chrono")); ?>';
    ac_clientOpts.SessionName=composeClientSessionName(ac_clientOpts);
    if (parseInt(ac_clientOpts.Timeout)!==0) setTimeout(function() { chrono_accept();},1000*ac_clientOpts.Timeout); // if requested fire autostart
}

</script>
<style>
body { font-size: 100%;	background: <?php echo $config->getEnv('easyui_bgcolor'); ?>; }
</style>
</head>

<body style="margin:0;padding:0;background-color:blue;font-size:100%" onload="initialize();">
<div id="chrono-contenido">

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="chrono-dialog" class="easyui-dialog" style="position:relative;width:400px;height:auto;padding:10px 10px">
	<form id="chrono-Selection">
        <p><?php _e('Select ring and display name (optional)');?></p>
    	<div class="fitem">
            <label for="chrono-SessionName"><?php _e('Display name');?>:</label>
            <input type="text" id="chrono-SessionName" name="SessionName" value=""/>
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
	title: "<?php _e('Chronometer');?>: "+ac_clientOpts.Name+" ",
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: false,
	modal: true,
	buttons: '#chrono-Buttons'
});

$('#chrono-form').form();

addTooltip($('#chrono-okBtn').linkbutton(),"<?php _e('Work with selected ring/session');?>");

$('#chrono-SessionName').textbox({
    value:  ac_clientOpts.Name,
    required: false,
    validType: 'length[1,255]',
    onChange: function(value) {
        ac_clientOpts.Name=value.replace(/:/g,'');
        ac_clientOpts.SessionName=composeClientSessionName(ac_clientOpts);
    }
});

$('#chrono-Session').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '../ajax/database/sessionFunctions.php',
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
	    /*
	    // retrieve first available session id
		var cs=$('#chrono-Session');
		var def= cs.combogrid('grid').datagrid('getRows')[0].ID; // get first ID
		cs.combogrid('setValue',def);
		*/
	    // set ring according with default parameters
        setTimeout(function() {
            $('#chrono-Session').combogrid('setValue', (ac_clientOpts.Ring+1).toString())
        },0); // also fires onSelect()
	},
	onSelect: function(index,row) {
        ac_clientOpts.Ring=row.ID;
	    setupWorkingData(row.Prueba,row.Jornada,(row.manga>0)?row.manga:1);
	}
});

function chrono_accept() {
	// si sesion invalida cancelamos operacion
	var sid=$('#chrono-Session').combogrid('getValue');
	if ( sid===null ) {
		// indica error
		$.messager.alert("Error","<?php _e('You should select a valid session/ring');?>","error");
		return;
	}
    // disable ok to avoid pressing twice
	$('#chrono-okBtn').linkbutton('disable');
	// store selected data into global structure
	initWorkingData(sid,chrono_eventManager);
	var page='../chrono/chrono.inc.php';
	$('#chrono-dialog').dialog('close');
	$('#chrono-contenido').load(	
			page,
			function(response,status,xhr){
				if (status=='error') $('#chrono-contenido').load('../console/frm_notavailable.php');
			}
		);
}

</script>
</body>
</html> 

<?php
/*
 qrcode/index.php

 Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

 This program is free software; you can redistribute it and/or modify it under the terms
 of the GNU General Public License as published by the Free Software Foundation;
 either version 2 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along with this program;
 if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
// let the browser make https ajax calls from http
header("Access-Control-Allow-Origin: https://{$_SERVER['SERVER_NAME']}",false);
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");

define("SYSTEM_INI",__DIR__ . "/../../config/system.ini");
if (!file_exists(SYSTEM_INI)) {
    die("Missing system configuration file: ".SYSTEM_INI." . Please properly configure and install application");
}
$config =Config::getInstance();

/* check for properly installed xampp */
if( ! function_exists('openssl_get_publickey')) {
	die("Invalid configuration: please uncomment line 'module=php_openssl.dll' in file '\\xampp\\php\\php.ini'");
}

/* Check for https protocol. Previous versions allowed http in linux. This is no longer true*/
if (!is_https()) {
    die("You MUST use https protocol to access this application");
}

/* check for properly installed xampp */
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
$runmode=intval($config->getEnv('running_mode'));
if ( $runmode === AC_RUNMODE_SLAVE ) { // in slave mode restrict access to public directory
    die("Access other than public directory is not allowed");
}
?>
<!DOCTYPE html>
<html lang="es">
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
<!-- try to disable zoom in qrcode on double click -->
<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no' name='viewport' />
<title>AgilityContest (qrcode)</title>
<link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="../lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/datagrid.css" />
<script src="../lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" ></script>
<script src="../lib/jquery-easyui-1.4.2/locale/easyui-lang-<?php echo substr($config->getEnv('lang'),0,2);?>.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-dnd/datagrid-dnd.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-scrollview.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="../lib/html5-qrcode.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/admin.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="../scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="../qrcode/qrcode.js.php" type="text/javascript" charset="utf-8" > </script>

<script type="text/javascript" charset="utf-8">
/* make sure configuration is loaded from server before onLoad() event */
loadConfiguration();
getLicenseInfo();
getFederationInfo();

var ac_clientOpts = {
    Source:       'qrcode',
    Destination:    '',
	Ring:           0,
    View:           0,
    Mode:           0,
	StartStopMode:  0, // 0:stop, 1:start, -1:auto
	DataEntryEnabled: 0, // 0: roundSelection enabled 1:dataEntry enabled
	CourseWalk:     0, // 0 reconocimiento de pista parado else time
    // nombre del cliente utilizado para control de expire-time
    // after login becomes "qrcode_random@ring"
    Name:           '<?php echo http_request("Name","s",getDefaultClientName('qrcode')); ?>',
    SessionName:    ""
};
ac_clientOpts.SessionName=composeClientSessionName(ac_clientOpts);

function initialize() {
	// make sure that every ajax call provides sessionKey
	$.ajaxSetup({
	  beforeSend: function(jqXHR,settings) {
		if ( typeof(ac_authInfo.SessionKey)!=="undefined" && ac_authInfo.SessionKey!==null) {
			jqXHR.setRequestHeader('X-Ac-Sessionkey',ac_authInfo.SessionKey);
		}
	    return true;
	  }
	});
}

</script>

<style type="text/css">
body {
    font-size: 100%;
    background: <?php echo $config->getEnv('easyui_bgcolor'); ?>;
    }

#scanned label {
     padding-left: 50px;
     width: 60px;
     display: inline-block;
}
</style>

</head>

<body style="margin:0;padding:0" onload="initialize();">

<div id="qrcode_contenido" style="width:100%;height:100%;margin:0;padding:0"></div>

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="selqrcode-dialog" style="width:300px;height:auto;padding:10px" class="easyui-dialog"
	data-options="title: '<?php _e('QRCode reader sesion init:'); ?>',iconCls: 'icon-list',buttons: '#selqrcode-Buttons',collapsible:false, minimizable:false,
		maximizable:false, closable:true, closed:false, shadow:false, modal:true">
	<form id="selqrcode-form">
       	<div class="fitem">
       		<label for="selqrcode-Username"><?php _e('User'); ?>:</label>
       		<input id="selqrcode-Username" name="Username" style="width:200px" type="text"
       			class="easyui-textbox" data-options="iconCls:'icon-man',required:true,validType:'length[1,255]'"/>
       	</div>        		
       	<div class="fitem">
       		<label for="selqrcode-Password"><?php _e('Password'); ?>:</label>
       		<input id="selqrcode-Password" name="Password" style="width:200px" type="password"
       			class="easyui-textbox" data-options="iconCls:'icon-lock',required:true,validType:'length[1,255]'"/>
       	</div>
       	<div>&nbsp;</div>
    	<div class="fitem">
       		<label for="selqrcode-Sesion"><?php _e('Session'); ?>:</label>
       		<select id="selqrcode-Sesion" name="Sesion" style="width:200px"></select>
    	</div>
	</form>
</div> <!-- Dialog -->

<div id="selqrcode-Buttons" style="text-align:right;padding:5px;">
   	<a id="selqrcode-okBtn" href="#" class="easyui-linkbutton"
   	   	data-options="iconCls:'icon-ok'" onclick="qrcode_loginSession()"><?php _e('Accept'); ?></a>
</div>	<!-- botones -->

<script type="text/javascript">

workingData.qrcodeData= {
	'Parent':		"",
	'Prueba':		0,
	'Jornada':		0,
	'Manga':		0,
	'Tanda':		"",
	'ID':			0,
	'Perro':		0,
	'Licencia':		"",
	'Pendiente':	0,
	'Equipo':		0,
	'NombreEquipo': "",  // to be set by qrcode reader
	'Dorsal':		0, // to be set by qrcode reader
	'Nombre':		"<?php _e('Test dog'); ?>",  // to be set by qrcode reader
	'NombreLargo':	"",
	'Celo':			0,
	'NombreGuia':	"",  // to be set by qrcode reader
	'NombreClub':	"",  // to be set by qrcode reader
	'Categoria':	"-", // to be set by qrcode reader
	'Grado':		"-", // to be set by qrcode reader
	'Faltas':		0,
	'Rehuses':		0,
	'Tocados':		0,
	'Tiempo':		0.0,
	'TIntermedio':	0.0,
	'Eliminado':	0,
	'NoPresentado':	0,
	'Observaciones': ""
};

$('#selqrcode-form').form();

$('#selqrcode-Sesion').combogrid({
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
	editable: false,  // to disable qrcode keyboard popup
	columns: [[
	   	{ field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
		{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: '<?php _e('Name');?>' },
		{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: '<?php _e('Comments');?>' },
		{ field:'Prueba', hidden:true },
		{ field:'Jornada', hidden:true }
	]],
	onBeforeLoad: function(param) { 
		param.Operation='selectring';
		param.Hidden=0;
		return true;
	},
	onLoadSuccess: function(data) {
		var ts=$('#selqrcode-Sesion');
		var def= ts.combogrid('grid').datagrid('getRows')[0].ID; // get first ID
		ts.combogrid('setValue',def);
	},
    onSelect: function(index,row) {
        // update session name and ring information
	    var ri=parseInt(row.ID)-1;
        ac_clientOpts.Ring=row.ID;
        ac_clientOpts.Name= ac_clientOpts.Name.replace(/@.*/g,"@"+ri);
        ac_clientOpts.SessionName= composeClientSessionName(ac_clientOpts);
    }
});

function qrcode_loginSession() {
	// si prueba invalida cancelamos operacion
	var s=$('#selqrcode-Sesion').combogrid('grid').datagrid('getSelected');
	var user=$('#selqrcode-Username').val();
	var pass=$('#selqrcode-Password').val();
	if (!user || !user.length) {
		$.messager.alert("Invalid data",'<?php _e("No user has been selected");?>',"error");
		return;
    }
    var parameters={
		'Operation':'pwcheck',
		'Username': user,
		'Password': pass,
		'Session' : s.ID,
		'Nombre'  : s.Nombre,
		'Source'  : 'qrcode_'+s.ID,
		'Prueba'  : 0,
		'Jornada' : 0,
		'Manga'   : 0,
		'Tanda'   : 0,
		'Perro'   : 0
	};
	// de-activate accept button during ajax call
    $('#selqrcode-okBtn').linkbutton('disable');
	$.ajax({
		type: 'POST',
        url: '../ajax/database/userFunctions.php',
   		dataType: 'json',
   		data: parameters,
   		success: function(data) {
    		if (data.errorMsg) { 
        		$.messager.alert("Error",data.errorMsg,"error"); 
        		initAuthInfo(); // initialize to null
        	} else {
    		    var ll="";
    		    if (data.LastLogin!=="") {
    		        ll = "<?php _e('Last login');?>:<br/>"+data.LastLogin;
                }
                // store selected data into global structure
                workingData.session=s.ID;
                workingData.nombreSesion=s.Nombre;
                initWorkingData(s.ID,qrcode_eventManager);
                // close dialog
                $('#selqrcode-dialog').dialog('close');
        		$.messager.alert(
        	    	"<?php _e('User');?>: "+data.Login,
        	    	'<?php _e("Session successfully started");?><br/>'+ll,
        	    	"info",
        	    	function() {
        	    	    data.SessionKey=null; // unset var as no longer needed and will collide with tablet
        	    	   	initAuthInfo(data);
        	    		var page="../qrcode/qrcode_main.php";
                        // load requested page
        	    		$('#qrcode_contenido').load(
        	    				page,
        	    				function(response,status,xhr){
        	    					if (status==='error') $('#qrcode_contenido').load('../frm_notavailable.php');
        	    					else startEventMgr();
        	    				}
        	    			); // load
        	    	} // close dialog; open main window
        	    ); // alert calback
        	} // if no ajax error
    	}, // success function
        error: function(XMLHttpRequest,textStatus,errorThrown) {
            // connection error: show an slide message error at bottom of the screen
            $.messager.show({
                title:"<?php _e('Error');?>",
                msg: "<?php _e('Error');?>: qrCodeLogin(): "+XMLHttpRequest.status+" - "+XMLHttpRequest.responseText+" - "+textStatus + " "+ errorThrown,
                timeout: 5000,
                showType: 'slide',
                height:200
            });
        },
        // after ajax call re-enable "Accept" button
        complete: function(data) {
            $('#selqrcode-okBtn').linkbutton('enable');
        }
	}); // ajax call
}

//on Enter key on login field fo	cus on password
$('#selqrcode-Username').bind('keypress', function (evt) {
    if (evt.keyCode !== 13) return true;
    $('#selqrcode-Password').focus();
    return false;
});

//on Enter password focus on "accept"
$('#selqrcode-Password').bind('keypress', function (evt) {
    if (evt.keyCode !== 13) return true;
    $('#selqrcode-okBtn').linkbutton('select');
    return false;
});

// on qrcode reader we only use open,close, and llamada
var eventHandler= {
    null:       null, // null event: no action taken
    init:       null, // open session
    open:       null, // operator select tanda
    close:      null, // no more dogs in tanda
    datos:      null, // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
    llamada:    null, // llamada a pista
    salida:     null, // orden de salida
    start:      null, // start crono manual
    stop:       null, // stop crono manual
    crono_start:    null, // arranque crono automatico
    crono_restart:  null,// paso de tiempo intermedio a manual
    crono_int:  	null, // tiempo intermedio crono electronico
    crono_stop:     null, // parada crono electronico
    crono_reset:    null, // puesta a cero del crono electronico
    crono_error:    null, // fallo en los sensores de paso
    crono_dat:      null, // datos desde crono electronico
    crono_ready:    null, // chrono ready and listening
    user:       null, // user defined event
    aceptar:	null, // operador pulsa aceptar
    cancelar:   null, // operador pulsa cancelar
    camera:	    null, // change video source
    command:    function(event){ // videowall remote control
        handleCommandEvent
        (
            event,
            [
                /* EVTCMD_NULL:         */ function(e) {console.log("Received null command"); },
                /* EVTCMD_SWITCH_SCREEN:*/ null,
                /* EVTCMD_SETFONTFAMILY:*/ null,
                /* EVTCMD_NOTUSED3:     */ null,
                /* EVTCMD_SETFONTSIZE:  */ null,
                /* EVTCMD_OSDSETALPHA:  */ null,
                /* EVTCMD_OSDSETDELAY:  */ null,
                /* EVTCMD_NOTUSED7:     */ null,
                /* EVTCMD_MESSAGE:      */ function(e) {console_showMessage(e); },
                /* EVTCMD_ENABLEOSD:    */ null
            ]
        )
    },
    reconfig:	function(event) { loadConfiguration(); }, // reload configuration from server
    info:	    null // click on user defined tandas
};
</script>
</body>
</html> 

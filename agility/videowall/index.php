<?php
/*
 videowall/index.php

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
/* check for properly installed xampp */
if( ! function_exists('password_verify')) {
    die("Invalid environment: You should have php-5.5.X or higher version installed");
}
if ( intval($config->getEnv('restricted'))!=0) {
    die("Access other than public directory is not allowed");
}
$am=new AuthManager("VideoWall");
if (!$am->allowed(ENABLE_VIDEOWALL)) {
	die("Current license has no permissions to handle videowall related functions");
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
<title>AgilityContest (VideoWall)</title>
<link rel="stylesheet" type="text/css" href="/agility/fonts/fonts.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/<?php echo $config->getEnv('easyui_theme'); ?>/easyui.css" />
<link rel="stylesheet" type="text/css" href="/agility/lib/jquery-easyui-1.4.2/themes/icon.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/style.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/datagrid.css" />
<link rel="stylesheet" type="text/css" href="/agility/css/videowall_css.php" />
<link rel="stylesheet" type="text/css" href="/agility/css/public_css.php" />
<script src="/agility/lib/HackTimer/HackTimer.js" type="text/javascript" charset="utf-8" ></script>
<script src="/agility/lib/jquery-2.2.4.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/jquery.easyui.min.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-easyui-1.4.2/extensions/datagrid-view/datagrid-detailview.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fileDownload-1.4.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/easyui-patches.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/datagrid_formatters.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-chronometer.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/jquery-fittext-1.2.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/lib/sprintf.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/common.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/auth.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/competicion.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/results_and_scores.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/scripts/events.js" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/videowall/videowall.js.php" type="text/javascript" charset="utf-8" > </script>
<script src="/agility/videowall/vws.js.php" type="text/javascript" charset="utf-8" > </script>

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
		.datagrid-body .datagrid-group {
			background-color: <?php echo $config->getEnv("vw_hdrbg3"); ?>;
			color: <?php echo $config->getEnv("vw_hdrfg3"); ?>;
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

<script type="text/javascript" charset="utf-8">

var ac_clientOpts = {
    'BaseName':'videowall',
    'Ring':2, // sessid:2 --> ring 1
    'View':3,
    'Mode':0,
    'Timeout':0,
    'SessionName':''
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
	loadConfiguration();
	getLicenseInfo();
	getFederationInfo();
	ac_clientOpts.Ring=<?php _e(http_request("Ring","i",2)); ?>; // defaults to sessid:2 ring 1
	ac_clientOpts.View=<?php _e(http_request("View","i",3)); ?>; // defaults to OSD chroma key
	ac_clientOpts.Timeout=<?php _e(http_request("Timeout","i",0)); ?>; // auto start displaying after x seconds. 0 disable
    // session name. defaults to random string(8)@client.ip.address
    ac_clientOpts.SessionName='<?php echo http_request("SessionName","s",getDefaultSessionName()); ?>';
	if (parseInt(ac_clientOpts.Timeout)!==0) setTimeout(function() { vw_accept();},1000*ac_clientOpts.Timeout); // if requested fire autostart
}

/**
 * rowStyler function for videowall datagrids
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function vwRowStyler(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('vw_rowcolor1'); ?>';
	var c2='<?php echo $config->getEnv('vw_rowcolor2'); ?>';
	if ( (idx&0x01)===0) { return res+c1+";"; } else { return res+c2+";"; }
}
/**
 * rowStyler function for videowall secondary datagrids
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function vwRowStyler2(idx,row) {
	var res="background-color:";
	var c1='<?php echo $config->getEnv('vw_rowcolor3'); ?>';
	var c2='<?php echo $config->getEnv('vw_rowcolor4'); ?>';
	if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

/* clases para la sesion de entrenamiento */

function vws_rowStylerOdd() {
    return "background-color:<?php echo $config->getEnv('vws_rowcolor1'); ?>";
}

function vws_rowStylerEven() {
    return "background-color:<?php echo $config->getEnv('vws_rowcolor2'); ?>";
}

function vws_rowStylerDown() {
    return "height: 10vw;"+
        "line-height:10vw;"+
        "font-size: 5vw;"+
        "background-color:<?php echo $config->getEnv('vws_rowcolor5'); ?>";
}


/**
 * rowStyler function for videowall simplified training session
 * @param {int} idx Row index
 * @param {object} row Row data
 * @return {string} proper row style for given idx
 */
function vws_rowStyler(idx,row) {
	if (idx==9)  return  vws_rowStylerDown();
	if ((idx&0x01)==0)  return vws_rowStylerEven();
	else  return vws_rowStylerOdd();
}

function myRowStyler(idx,row) { return vwRowStyler(idx,row); }
function myRowStyler2(idx,row) { return vwRowStyler2(idx,row); }

/* same as above, but tracks tanda and team information */
function myLlamadaRowStyler(idx,row) {
	var height=(ac_config.vwc_simplified==0)?40:50;
	var res="height:"+height+"px;line-height:"+height+"px;background-color:";
    var c1='<?php echo $config->getEnv('easyui_rowcolor1'); ?>';
    var c2='<?php echo $config->getEnv('easyui_rowcolor2'); ?>';
    var tnd='<?php echo $config->getEnv('vw_hdrbg2'); ?>';
    var eqp='<?php echo $config->getEnv('vw_hdrbg3'); ?>';
    if (parseInt(row.Orden)==-1) return res+tnd+";";
    if (parseInt(row.Orden)==0) return res+eqp+";";
    if ( (idx&0x01)==0) { return res+c1+";"; } else { return res+c2+";"; }
}

</script>
</head>

<body style="margin:0;padding:0;background-color:blue;font-size:100%" onload="initialize();">
<div id="vw_contenido" style="width:inherit;height:inherit;margin:0;padding:0">

<!--  CUERPO PRINCIPAL DE LA PAGINA (se modifica con el menu) -->

<div id="selvw-dialog" class="easyui-dialog" style="position:relative;width:500px;height:auto;padding:20px 20px">
	<form id="selvw-Selection">
        <div class="fitem">
            <p><?php _e('Select ring, display name (optional) and view to deploy');?></p>
            <label for="selvw-SessionName"><?php _e('Display name');?>:</label>
            <input type="text" id="selvw-SessionName" name="SessioName" value=""/><br/>
        </div>
    	<div class="fitem">
       		<label for="selvw-Session"><?php _e('Select Session/Ring'); ?>:</label>
       		<select id="selvw-Session" name="Session" style="width:200px"></select>
    	</div>
    	<div class="fitem">
       		<label for="selvw-Vista"><?php _e('Select View'); ?>:</label>
       		<select id="selvw-Vista" name="Vista" style="width:200px">
                <optgroup label="<?php _e('Video Wall');?> ">
                    <!-- videowall -->
					<option value="1"><?php _e('Training session'); ?></option>
                    <option value="0"><?php _e('Starting order'); ?></option>
					<option value="2"><?php _e('Partial scores'); ?></option>
					<option value="4"><?php _e('Final scores'); ?></option>
                </optgroup>
				<optgroup label="<?php _e('Combo view');?> ">
					<option value="7"><?php _e('Call to ring '); ?> / <?php _e('Partial scores'); ?></option>
					<option value="8"><?php _e('Call to ring '); ?> / <?php _e('Final scores'); ?></option>
				</optgroup>
				<optgroup label="<?php _e('Simplified');?> ">
					<option value="6"><?php _e('Training session'); ?> (<?php _e('simplified'); ?>)</option>
					<option value="3"><?php _e('Partial Scores'); ?> (<?php _e('simplified'); ?>)</option>
					<option value="9"><?php _e('Final Scores'); ?> (<?php _e('simplified'); ?>)</option>
				</optgroup>
                <optgroup label="<?php _e('Extra info'); ?> ">
                    <option value="5"><?php _e('Advertising videos'); ?></option>
                    <option value="10"><?php _e('Standby screen'); ?></option>
                </optgroup>
       		</select>
    	</div>
    	
	</form>
</div> <!-- Window -->

<div id="selvw-Buttons" style="text-align:right">
   	<a id="selvw-okBtn" href="#" class="easyui-linkbutton" 
   	   	data-options="iconCls: 'icon-ok'" onclick="vw_accept()"><?php _e('Accept'); ?></a>
</div>	<!-- botones -->

</div> <!-- contenido -->

<script type="text/javascript">

//add 'callback' property to store interval references
$.extend($.fn.window.defaults,{callback:null});

$('#selvw-SessionName').textbox({
    value: ac_clientOpts.SessionName,
    required:false,
    validType:'length[1,255]',
    onChange: function(value) {ac_clientOpts.SessionName=value.replace(/:/g,'');}
});

$('#selvw-Vista').combobox({
    valueField:'value',
    panelHeight:'auto',
    editable:false,
    onSelect:function(data){
        ac_clientOpts.View=$('#selvw-Vista').combobox('getValue');
    }
});

$('#selvw-dialog').dialog({
	title: '<?php _e('Videowall'); ?>',
	collapsible: false,
	minimizable: false,
	maximizable: false,
	closable: true,
	closed: false,
	shadow: true,
	modal: true,
	buttons: '#selvw-Buttons' 
});

$('#selvw-form').form();

addTooltip($('#selvw-okBtn').linkbutton(),'<?php _e("Use selected as working session"); ?>');

$('#selvw-Session').combogrid({
	panelWidth: 500,
	panelHeight: 150,
	idField: 'ID',
	textField: 'Nombre',
	url: '/agility/server/database/sessionFunctions.php',
	method: 'get',
    queryParams: {'Operation':'selectring','Hidden':0 },
	mode: 'remote',
	required: true,
	rownumber: true,
	multiple: false,
	fitColumns: true,
	singleSelect: true,
	editable: false, // avoid keyboard deploy
	columns: [[
	    { field:'ID',			width:'5%', sortable:false, align:'center', title:'ID' }, // Session ID
		{ field:'Nombre',		width:'25%', sortable:false,   align:'center',  title: '<?php _e('Name'); ?>' },
		{ field:'Comentario',	width:'60%', sortable:false,   align:'left',  title: '<?php _e('Comments'); ?>' },
        { field:'Prueba',	    hidden:true },
        { field:'Jornada',	    hidden:true },
		{ field:'Background',	hidden:true },
		{ field:'LiveStream2',	hidden:true },
		{ field:'LiveStream3',	hidden:true }
	]],
	onLoadSuccess: function(data) {
		setTimeout(function() {
			$('#selvw-Vista').combobox('setValue',ac_clientOpts.View.toString());
			$('#selvw-Session').combogrid('setValue', (ac_clientOpts.Ring).toString());
		},0); // also fires onSelect()
	},
    onBeforeSelect: function(index,row) {
	    if (parseInt(row.Prueba)!==0) return true;
	    $.messager.alert("<?php _e('Error');?>","<?php _e('No active contest being played in this ring');?>","error");
	    return false;
    },
    onSelect: function(index,row) {
        ac_clientOpts.Ring=row.ID;
	    setupWorkingData(row.Prueba,row.Jornada,(row.manga>0)?row.manga:1);
	}
});

function vw_accept() {
	// si prueba invalida cancelamos operacion
	var s=$('#selvw-Session').combogrid('grid').datagrid('getSelected');
	if ( s===null ) {
		// indica error
		$.messager.alert("Error",'<?php _e("You should select a valid session"); ?>',"error");
		return;
	}
    requestFullScreen(document.body);
	// store selected data into global structure
	workingData.sesion=s.ID;
	workingData.nombreSesion=s.Nombre;
	initWorkingData(s.ID,videowall_eventManager);
	ac_config.vwc_simplified=0;
	ac_config.vw_combined=0;
	var page="/agility/console/frm_notavailable.php";
	var n=parseInt($('#selvw-Vista').combobox('getValue'));
	switch (n){
	case 0: // Ordenes de Salida
		page="/agility/videowall/vw_ordensalida.php";
        ac_config.vw_combined=0;
        ac_config.vwc_simplified=0;
		break;
	case 1: // sesion de entrenamientos
		check_permissions(access_perms.ENABLE_TRAINING,function(res) {
			if (res.errorMsg) {
				$.messager.alert('License error','<?php _e("Current license has no permission to handle training sessions"); ?>',"error");
				page=null;
				return;
			}
			page="/agility/videowall/vw_entrenamientos.php";
			ac_config.vw_combined=0;
			ac_config.vwc_simplified=0;
			$('#selvw-dialog').dialog('close');
			$('#vw_contenido').load(
				page,
				function(response,status,xhr){
					if (status==='error') {
					    $('#vw_contenido').load('/agility/console/frm_notavailable.php');
					    return false;
                    }
				}
			);
		});
		return; // use return instead of break to avoid executin load twice
	case 2: // Resultados Parciales
		page="/agility/videowall/vw_parciales.php";
        ac_config.vw_combined=0;
        ac_config.vwc_simplified=0;
		break;
	case 3: // Combinada parcial simplificada
		page="vws_parcial.php";
		ac_config.vw_combined=1;
		ac_config.vwc_simplified=1;
		break;
	case 4: // Clasificacion final
		page="/agility/videowall/vw_finales.php";
        ac_config.vw_combined=0;
        ac_config.vwc_simplified=0;
		break;
    case 5: // videos promocionales
        page="/agility/videowall/vw_anuncios.php";
        ac_config.vw_combined=1; // allow mix video background and foreground data
        ac_config.vwc_simplified=0;
        break;
    case 6: // entrenamientos simplificado
		check_permissions(access_perms.ENABLE_TRAINING,function(res) {
			if (res.errorMsg) {
				$.messager.alert('License error','<?php _e("Current license has no permission to handle training sessions"); ?>',"error");
				page=null;
				return false;
			}
			page="/agility/videowall/vws_entrenamientos.php";
			ac_config.vw_combined=0;
			ac_config.vwc_simplified=0;
			$('#selvw-dialog').dialog('close');
			$('#vw_contenido').load(
				page,
				function(response,status,xhr){
					if (status=='error') {
					    $('#vw_contenido').load('/agility/console/frm_notavailable.php');
					    return false;
                    }
				}
			);
		});
		return; // use return instead of break to avoid executin load twice
	case 7: // pantalla combinada ( Resultados parciales )
		page="/agility/videowall/vwc_parciales.php";
        ac_config.vw_combined=1;
        ac_config.vwc_simplified=0;
		break;
	case 8: // pantalla comobinada ( Clasificacion final )
		page="/agility/videowall/vwc_finales.php";
		ac_config.vw_combined=1;
        ac_config.vwc_simplified=0;
		break;
	case 9: // Combinada Final simplificada
        page="vws_final.php";
		ac_config.vw_combined=1;
		ac_config.vwc_simplified=1;
		break;
    case 10: // standby mode
        page="vw_standby.php";
        ac_config.vw_combined=0;
        ac_config.vwc_simplified=0;
        break;
	}
	$('#selvw-dialog').dialog('close');
	$('#vw_contenido').load(
			page,
			function(response,status,xhr){
				if (status==='error') {
				    $('#vw_contenido').load('/agility/console/frm_notavailable.php');
                    return false;
                }
			}
		);
}

</script>
</body>
</html> 

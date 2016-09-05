<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::ordensalida");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vw_ordensalida.inc

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion del orden de salida de la jornada -->
<div id="vw_entrenamientos-window">
	<div id="vw_entrenamientos-layout" style="width:100%">
		<div id="vw_entrenamientos-Cabecera" data-options="region:'north',split:false" style="height:100px" class="vw_floatingheader">
            <img id="vw_header-logo" src="/agility/images/logos/rsce.png" style="float:left;width:75px" />
		    <span style="float:left;padding:10px;" id="vw_header-infoprueba"><?php _e('Header'); ?></span>
			<div style="float:right;padding:10px;text-align:right;">
                <span id="vw_header-texto"><?php _e('Training session'); ?></span>&nbsp;-&nbsp;
                <span id="vw_header-ring" style="display:none"><?php _e('Ring'); ?></span>
                <br />
                <span id="vw_header-infomanga" style="display:none;width:200px">(<?php _e('No round selected'); ?>)</span>
            </div>
		</div>
		<div class="vws_entrenamientos" id="vw_tabla" data-options="region:'center'">
            <table id="entrenamientos-datagrid"></table>
		</div>
        <div id="vw_entrenamientos-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
	</div>
</div> <!-- vw_entrenamientos-window -->

<script type="text/javascript">

$('#vw_entrenamientos-layout').layout({fit:true});

$('#vw_entrenamientos-window').window({
    fit:true,
    noheader:true,
    border:false,
    closable:false,
    collapsible:false,
    collapsed:false,
    resizable:true,
    callback: null,
    onOpen: function() {
        startEventMgr();
    }
});

$('#entrenamientos-datagrid').datagrid({
    columns: [[
        {field:'ID',     hidden:true},
        {field:'Prueba', hidden:true},
        {field:'Orden',       width:10,      align:'center', title:'#',     formatter: formatBoldBig},
        {field:'LogoClub',	  width:7,      align:'center', title:'',      formatter: formatLogo},
        {field:'NombreClub',  width:25,      align:'left',   title: '<?php _e('Club');?>' },
        {field:'Fecha',	      hidden:true, width:20,      align:'center', title: '<?php _e('Date');?>',formatter: formatYMD},
        {field:'Firma',       hidden:true, width:15,      align:'center', title: '<?php _e('Check-in');?>',formatter: formatHM},
        {field:'Veterinario', hidden:true, width:15,	  align:'center',  title: '<?php _e('Veterinary');?>',formatter: formatHM},
        {field:'Comienzo',    width:15,      align:'right',  title: '<?php _e('Start');?>',formatter: formatHM},
        {field:'Duracion',    hidden:true, width:20,      align:'right',  title: '<?php _e('End');?>',formatter: formatMinSecs},
        {field:'Key1',        hidden:true},
        {field:'Value1',      width:20,      align:'center', title: '<?php _e('Ring');?> 1',formatter: formatTrainingCell1 },
        {field:'Key2',        hidden:true},
        {field:'Value2',      width:20,      align:'center', title: '<?php _e('Ring');?> 2',formatter: formatTrainingCell2 },
        {field:'Key3',        hidden:true},
        {field:'Value3',      width:20,      align:'center', title: '<?php _e('Ring');?> 3',formatter: formatTrainingCell3 },
        {field:'Key4',        hidden:true},
        {field:'Value4',      width:20,      align:'center', title: '<?php _e('Ring');?> 4',formatter: formatTrainingCell4 },
        {field:'-',           hidden:true},
        {field:'Observaciones',hidden:true },
        {field:'Estado', hidden:true}
    ]],
    nowrap: false,
    fit: false, // on fake container, do not try to fit
    height: '100%',
    method: 'get',
    url: '/agility/server/database/trainingFunctions.php',
    queryParams: {
        Operation: 'window',
        Size: 10,
        ID:0,
        Prueba: workingData.prueba, // when used from direct access
        Sesion: workingData.sesion // when used from event handler
    },
    loadMsg: "<?php _e('retrieve next 10 teams to come');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    rowStyler:vws_rowStyler,
    onBeforeSelect: function(index) { return false; }, // dont allow row selection
    //onBeforeLoad:function(params) {
    //    // do not update until 'open' or 'init' received
    //    if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
    //    return true;
    //},
    onLoadSuccess: function(data) {
        if (data['total']!=0) return;
        $.messager.alert("No data",'<?php _e("This contest has no training session defined");?>','info');
    }

});

    var eventHandler= {
        'null': null,// null event: no action taken
        'init': function(event) { // operator starts tablet application
            vw_updateWorkingData(event,function(evt,data) {
                vw_updateHeaderAndFooter(evt, data, false);
                vw_setTrainingLayout($('#entrenamientos-datagrid'));
                vws_keyBindings('.vws_entrenamientos'); // make text higher/lower with up/down keys
            });
        },
        'open': function(event){ // operator select tanda
            vw_updateWorkingData(event,function(evt,data){
                vw_updateHeaderAndFooter(evt,data,false);
                vw_setTrainingLayout($('#entrenamientos-datagrid'));
            });
        },
        'close': null,    // no more dogs in tanda
        'datos': null,      // actualizar datos (si algun valor es -1 o nulo se debe ignorar)
        'llamada': null,    // llamada a pista
        'salida': null,     // orden de salida
        'start': null,      // start crono manual
        'stop': null,       // stop crono manual
        // nada que hacer aqui: el crono automatico se procesa en el tablet
        'crono_start':  null, // arranque crono automatico
		'crono_restart': null,// paso de tiempo intermedio a manual
        'crono_int':  	null, // tiempo intermedio crono electronico
		'crono_stop':  null, // parada crono electronico
		'crono_reset':  null, // puesta a cero del crono electronico
		'crono_error':  null, // fallo en los sensores de paso
        'crono_dat':    null, // datos desde crono electronico
        'crono_ready':    null, // chrono ready and listening
        'aceptar':	null, // operador pulsa aceptar
        'cancelar': null, // operador pulsa cancelar
        'camera':	null, // change video source
        'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
        'info':	null // click on user defined tandas
    };

</script>
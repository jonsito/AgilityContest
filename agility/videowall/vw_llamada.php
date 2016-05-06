<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::llamada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}
?>
<!--
vw_llamada.inc

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

<!-- Pantalla de llamada a pista -->

<div id="vw_llamada-window">
    <div id="vw_llamada-layout" style="width:100%">
        <div id="vw_llamada-Cabecera" data-options="region:'north',split:false" style="height:100px" class="vw_floatingheader">
            <img id="vw_header-logo" src="/agility/images/logos/rsce.png" width="75" style="float:left;"/>
            <span style="float:left;padding:10px" id="vw_header-infoprueba"><?php _e('Header'); ?></span>
            <div style="float:right;padding:10px;text-align:right;">
                <span id="vw_header-texto"><?php _e('Call to ring'); ?></span>&nbsp;-&nbsp;
                <span id="vw_header-ring"><?php _e('Ring'); ?></span>
            </div>
        </div>
        <div id="vw_llamada-data" data-options="region:'center'" >
            <table id="vw_llamada-datagrid"></table>
        </div>
        <div id="vw_llamada-footer" data-options="region:'south',split:false" class="vw_floatingfooter">
            <span id="vw_footer-footerData"></span>
        </div>
    </div>
</div> <!-- vw_llamada-window -->

<script type="text/javascript">

$('#vw_llamada-layout').layout({fit:true});

$('#vw_llamada-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:true,
	onOpen: function() {
        startEventMgr();
	}
});

$('#vw_llamada-datagrid').datagrid({
    // propiedades del panel asociado
    fit: true,
    border: false,
    closable: false,
    collapsible: false,
    collapsed: false,
    // propiedades del datagrid
    method: 'get',
    url: '/agility/server/database/resultadosFunctions.php',
    queryParams: {
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Manga: workingData.manga,
        Mode: (workingData.datosManga.Recorrido!=2)?0:4, // def to 'Large' or 'LMS' depending of datosmanga
        Operation: 'getResultados'
    },
    loadMsg: "<?php _e('Updating list of teams to be called to ring');?> ...",
    pagination: false,
    rownumbers: false,
    fitColumns: true,
    singleSelect: true,
    autoRowHeight: true,
    columns:[[
        { field:'Orden',		width:'5%', align:'center', title: '<?php _e('Order'); ?>', formatter:formatOrdenLlamadaPista},
        { field:'Logo', 		width:'5%', align:'center', title: '', formatter:formatLogo},
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
        { field:'Equipo',		hidden:true },
        { field:'NombreEquipo',	hidden:true },
        { field:'Dorsal',		width:'5%', align:'center', title: '<?php _e('Dorsal'); ?>'},
        { field:'Licencia',		width:'7%%', align:'center',  title: '<?php _e('License'); ?>'},
        { field:'Nombre',		width:'15%', align:'center',  title: '<?php _e('Name'); ?>',formatter:formatBoldBig},
        { field:'Raza',		    width:'10%', align:'center', title: '<?php _e('Breed'); ?>'},
        { field:'NombreGuia',	width:'22%', align:'right', title: '<?php _e('Handler'); ?>',formatter:formatLlamadaGuia },
        { field:'NombreClub',	width:'10%', align:'right', title: '<?php _e('Club'); ?>' },
        { field:'Categoria',	width:'4%', align:'center', title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
        { field:'Grado',	    width:'4%', align:'center', title: '<?php _e('Grade'); ?>', formatter:formatGrado },
        { field:'Celo',	        width:'4%', align:'center', title: '<?php _e('Heat'); ?>.',formatter:formatCelo },
        { field:'Observaciones',width:'8%', align:'center', title: '<?php _e('Comments'); ?>' }
    ]],
    rowStyler:myLlamadaRowStyler,
    onBeforeLoad: function(param) {
        var mySelf=$('#vw_llamada-datagrid');
        // show/hide team name
        if (isTeamByJornada(workingData.datosJornada) ) {
            mySelf.datagrid('hideColumn','Grado');
        } else  {
            mySelf.datagrid('showColumn','Grado');
        }
        mySelf.datagrid('fitColumns'); // expand to max width
        // do not update until 'open' received
        if( $('#vw_header-infoprueba').html()==='<?php _e('Header'); ?>') return false;
        return true;
    }
});

var eventHandler= {
    'null': null,// null event: no action taken
    'init': function(event) { // operator starts tablet application
        vw_updateWorkingData(event,function(e,d){
            $('#vw_header-infoprueba').html('<?php _e("Header"); ?>');
            vw_updateDataInfo(e,d);
        });
    },
    'open': function(event){ // operator select tanda
        vw_updateWorkingData(event,function(e,d){
            vw_updateDataInfo(e,d);
            vw_updateLlamada(e,d);
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
    'aceptar':	function(event){ // operador pulsa aceptar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
        });
    },
    'cancelar': function(event){  // operador pulsa cancelar
        vw_updateWorkingData(event,function(e,d){
            vw_updateLlamada(e,d);
        });
    },
    'camera':	null, // change video source
    'reconfig':	function(event) { loadConfiguration(); }, // reload configuration from server
    'info':	null // click on user defined tandas
};

</script>
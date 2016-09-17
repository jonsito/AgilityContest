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
		<div class="vws_results" id="vw_tabla" data-options="region:'center'" >
<?php for ($entry=9;$entry>=0; $entry--) {
            $type=($entry==0)?'text':'hidden';
            $type2=($entry==0)?'hidden':'text';
            $cls=($entry==0)?"vws_css_current_0":"vws_css_results_".($entry%2);
            ?>
            <form id="vw_entrenamientos_<?php echo $entry;?>" name="vw_entrenamientos_<?php echo $entry;?>" class="<?php echo $cls?> vws_entry">
                <input id="vw_training_Orden_<?php echo $entry;?>" name="Orden" type="<?php echo $type2;?>" value="Orden <?php echo $entry;?>" style="padding-left:2vw;"/>
                <input id="vw_training_Comienzo_<?php echo $entry;?>" name="Comienzo" type="<?php echo $type2;?>" value="Comienzo <?php echo $entry;?>"/>
                <input id="vw_training_Duracion_<?php echo $entry;?>" name="Duracion" type="<?php echo $type2;?>" value="Duracion" <?php echo $entry;?>"/>
                <!-- datos del ring 1 -->
                    <input id="vw_training_LogoClub1_<?php echo $entry;?>" name="LogoClub1" type="hidden" value="Logo1"/>
                    <img class="vws_css_results_<?php echo($entry%2);?> vws_imgpadding"
                         src="/agility/images/logos/agilitycontest.png"
                         id="vw_training_Logo1_<?php echo $entry;?>"
                         alt="Logo <?php echo $entry;?>"/>
                    <input id="vw_training_NombreClub1_<?php echo $entry;?>" name="NombreClub1" type="text" value="Club1"/>
                    <input id="vw_training_Duracion1_<?php echo $entry;?>" name="Duracion1" type="<?php echo $type;?>" value="Duracion1"/>
                    <input id="vw_training_Key1_<?php echo $entry;?>" name="Key1" type="<?php echo $type;?>" value="Key1"/>
                    <input id="vw_training_Value1_<?php echo $entry;?>" name="Value1" type="hidden" value="Value1"/>
                <!-- datos del ring 2 -->
                    <input id="vw_training_LogoClub2_<?php echo $entry;?>" name="LogoClub2" type="hidden" value="Logo2"/>
                    <img class="vws_css_results_<?php echo($entry%2);?> vws_imgpadding"
                         src="/agility/images/logos/agilitycontest.png"
                         id="vw_training_Logo2_<?php echo $entry;?>"
                         alt="Logo <?php echo $entry;?>"/>
                    <input id="vw_training_NombreClub2_<?php echo $entry;?>" name="NombreClub2" type="text" value="Club2"/>
                    <input id="vw_training_Duracion2_<?php echo $entry;?>" name="Duracion2" type="<?php echo $type;?>" value="Duracion2"/>
                    <input id="vw_training_Key2_<?php echo $entry;?>" name="Key2" type="<?php echo $type;?>" value="Key2"/>
                    <input id="vw_training_Value2_<?php echo $entry;?>" name="Value2" type="hidden"/>
                <!-- datos del ring 3 -->
                    <input id="vw_training_LogoClub3_<?php echo $entry;?>" name="LogoClub3" type="hidden" value="Logo3"/>
                    <img class="vws_css_results_<?php echo($entry%2);?> vws_imgpadding"
                         src="/agility/images/logos/agilitycontest.png"
                         id="vw_training_Logo3_<?php echo $entry;?>"
                         alt="Logo <?php echo $entry;?>"/>
                    <input id="vw_training_NombreClub3_<?php echo $entry;?>" name="NombreClub3" type="text" value="Club3"/>
                    <input id="vw_training_Duracion3_<?php echo $entry;?>" name="Duracion3" type="<?php echo $type;?>" value="Duracion3"/>
                    <input id="vw_training_Key3_<?php echo $entry;?>" name="Key3" type="<?php echo $type;?>" value="Key3"/>
                    <input id="vw_training_Value3_<?php echo $entry;?>" name="Value3" type="hidden"/>
                <!-- datos del ring 4 -->
                <!--
                    <input id="vw_training_LogoClub4_<?php echo $entry;?>" name="LogoClub4" type="hidden" value="Logo4"/>
                    <img class="vws_css_results_<?php echo($entry%2);?> vws_imgpadding"
                         src="/agility/images/logos/agilitycontest.png"
                         id="vw_training_Logo4_<?php echo $entry;?>"
                         alt="Logo <?php echo $entry;?>"/>
                    <input id="vw_training_NombreClub4_<?php echo $entry;?>" name="NombreClub4" type="text" value="Club4"/>
                    <input id="vw_training_Duracion4_<?php echo $entry;?>" name="Duracion4" type="<?php echo $type;?>" value="Duracion4"/>
                    <input id="vw_training_Key4_<?php echo $entry;?>" name="Key4" type="<?php echo $type;?>" value="Key4"/>
                    <input id="vw_training_Value4_<?php echo $entry;?>" name="Value4" type="hidden"/>
                -->
            </form>
<?php } ?>
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
var layout= {'rows':121,'cols':124};
// columnas de paises pendientes
for (var n=9; n>=1;n--) {
    doLayout(layout,"#vw_training_Orden_"+n,             1, 90-10*n,    6,10);
    doLayout(layout,"#vw_training_Comienzo_"+n,     2+   5, 90-10*n,    15,10);
    doLayout(layout,"#vw_training_Duracion_"+n,     2+  20, 90-10*n,    10,10);
    doLayout(layout,"#vw_training_Logo1_"+n,        2+  30, 90-10*n,    7,10);
    doLayout(layout,"#vw_training_NombreClub1_"+n,  2+  37, 90-10*n,    23,10);
    doLayout(layout,"#vw_training_Logo2_"+n,        2+  60, 90-10*n,    7,10);
    doLayout(layout,"#vw_training_NombreClub2_"+n,  2+  67, 90-10*n,    23,10);
    doLayout(layout,"#vw_training_Logo3_"+n,        2+  90, 90-10*n,    7,10);
    doLayout(layout,"#vw_training_NombreClub3_"+n,  2+  97 ,90-10*n,    24,10);
}
// columna principal (paises en pista )

doLayout(layout,"#vw_training_Logo1_0",        2+  30, 91,     10,14);
doLayout(layout,"#vw_training_NombreClub1_0",  2+  40, 91,     20, 9);
doLayout(layout,"#vw_training_Key1_0",         2+  30, 105,    10,14);
doLayout(layout,"#vw_training_Duracion1_0",    2+  40, 100,    20,19);
doLayout(layout,"#vw_training_Logo2_0",        2+  60, 91,     10,14);
doLayout(layout,"#vw_training_NombreClub2_0",  2+  70, 91,     20, 9);
doLayout(layout,"#vw_training_Key2_0",         2+  60, 105,    10,14);
doLayout(layout,"#vw_training_Duracion2_0",    2+  70, 100,    20,19);
doLayout(layout,"#vw_training_Logo3_0",        2+  90, 91,     10,14);
doLayout(layout,"#vw_training_NombreClub3_0",  2+  100,91,     21, 9);
doLayout(layout,"#vw_training_Key3_0",         2+  90, 105,    10,14);
doLayout(layout,"#vw_training_Duracion3_0",    2+  100,100,    21,19);

doLayout(layout,"#vw_footer-footerData",            1, 90,     31,29);

    var eventHandler= {
        'null': null,// null event: no action taken
        'init': function(event) { // operator starts tablet application
            vw_updateWorkingData(event,function(evt,data) {
                vw_updateHeaderAndFooter(evt, data, false);
                vw_setTrainingLayout($('#entrenamientos-datagrid'));
                vws_keyBindings(false); // make text higher/lower with up/down keys
            });
        },
        'open': function(event){ // operator select tanda
            vw_updateWorkingData(event,function(evt,data){
                vw_updateHeaderAndFooter(evt,data,false);
                vw_setTrainingLayout($('#entrenamientos-datagrid'));
                vws_trainingPopulate();
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
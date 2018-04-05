<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Public::entrenamientos");
if ( ! $am->allowed(ENABLE_PUBLIC)) { include_once("unregistered.php"); return 0; }
if ( ! $am->allowed(ENABLE_TRAINING)) { include_once("trainingnotallowed.php"); return 0;}
?>

<!--
pb_entrenamientos.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<!-- Presentacion de las entrenamientos de la jornada -->
<div id="pb_entrenamientos-window">
	<div id="pb_entrenamientos-layout" style="width:100%">
		<div id="pb_entrenamientos-Cabecera" data-options="region:'north',split:false" style="height:10%;" class="pb_floatingheader">
            <a id="pb_header-link" class="easyui-linkbutton" onClick="pb_updateEntrenamientos();" href="#" style="float:left">
                <img id="pb_header-logo" src="../images/logos/agilitycontest.png" width="50" />
            </a>
		    <span style="float:left;padding:10px" id="pb_header-infocabecera"><?php _e('Header'); ?></span>
			<span style="float:right;" id="pb_header-texto"><?php _e('Training session'); ?></span>
		</div>
		<div id="pb_entrenamientos-data" data-options="region:'center'" >
            <?php include_once(__DIR__."/../lib/templates/entrenamientos.inc.php");?>
		</div>
        <div id="pb_entrenamientos-footer" data-options="region:'south',split:false" style="height:10%;" class="pb_floatingfooter">
            <span id="pb_footer-footerData"></span>
        </div>
	</div>
</div> <!-- pb_entrenamientos-window -->

<script type="text/javascript">

addTooltip($('#pb_header-link').linkbutton(),'<?php _e("Update training session info"); ?>');
$('#pb_entrenamientos-layout').layout({fit:true});
$('#pb_entrenamientos-window').window({
	fit:true,
	noheader:true,
	border:false,
	closable:false,
	collapsible:false,
	collapsed:false,
	resizable:false,
	callback: null, 
	// 1 minute poll is enouth for this, as no expected changes during a session
	onOpen: function() {
        // generate header
        pb_getHeaderInfo(false);
        // generate footer
        pb_setFooterInfo();
	},
    onClose: function() {
        // do not auto-refresh in inscriptions
        // clearInterval($(this).window.defaults.callback);
    }
});

var pbdg= $('#entrenamientos-datagrid');
pbdg.datagrid({
   rowStyler:pbRowStyler // override default
});

pb_setTrainingLayout(pbdg);

// fire autorefresh if configured
var rtime=parseInt(ac_config.web_refreshtime);
if (rtime!==0) {
	function update() {
		pb_updateEntrenamientos();
		pb_config.Timeout= setTimeout(update,1000*rtime);
	}
	if (pb_config.Timeout!==null) clearTimeout(pb_config.Timeout);
	update();
}

</script>

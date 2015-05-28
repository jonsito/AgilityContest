<!-- 
frm_competicion.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
 
<?php 
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
$allowClosed=0; 
require_once("dialogs/dlg_selectJornada.inc")
?>

<script type="text/javascript">

// display prueba selection dialog
$('#seljornada-window').window({
	onClose: function () {
		// default values
		var extra="";
		var page="/agility/console/frm_main.php";
        // default dialogs
		
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents(page,'');
			return;
		}
        // default page and dialogs
		page="/agility/console/frm_competicion2.php?tipo=std";
        var dialogs= {'t':'#ordentandas-dialog','s':'#ordensalida-dialog','c':'#competicion-dialog','r':'#resultadosmanga-dialog'};
		if (workingData.datosJornada.Equipos3==1) {
			page="/agility/console/frm_competicion2.php?tipo=eq3";
			extra=" ( <?php _e('Equipos -3 mejores-');?> )";
            // mark default dialogs
		}
		if (workingData.datosJornada.Equipos4==1) {
			page="/agility/console/frm_competicion2.php?tipo=eq4";
			extra=" ( <?php _e('Equipos -conjunta-');?> )";
            // mark default dialogs
		}
		if (workingData.datosJornada.Open==1) {
			// an Open Contest is like a normal with no Grades but only categories
			page="/agility/console/frm_competicion2.php?tipo=open";
			extra=" ( <?php _e('Abierta');?> )";
			// use default dialogs
		}
		if (workingData.datosJornada.KO==1) {
			page="/agility/console/frm_competicion_ko.php";
			extra=" ( <?php _e('Mangas K.O.');?> )";
			dialogs= {};
		}
		loadContents( page, '<?php _e('Desarrollo de la jornada');?>'+extra, dialogs );
	} 
}).window('open');

</script>

<img class="mainpage" src="/agility/server/getRandomImage.php" alt="wallpaper" width="640" height="480" align="middle"/>

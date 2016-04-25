<!-- 
frm_clasificaciones.php

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
<?php
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/tools.php");
$config =Config::getInstance();
$allowClosed=1; 
require_once(__DIR__."/dialogs/dlg_selectJornada.inc");
?>

<script type="text/javascript">

// display prueba selection dialog

$('#seljornada-window').window({
	onClose: function () {
		var page="/agility/console/frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents(page,"");
			return;
		}
		page="/agility/console/frm_clasificaciones2.php";
		if (workingData.datosJornada.Equipos3!=0) page="/agility/console/frm_clasificaciones_eq3.php";
		if (workingData.datosJornada.Equipos4!=0) page="/agility/console/frm_clasificaciones_eq3.php"; // 4 & 3 are the same frame
		if (workingData.datosJornada.Open!=0) page="/agility/console/frm_clasificaciones2.php";
		if (workingData.datosJornada.KO!=0) page="/agility/console/resultados_ko.php";
		loadContents(page,'<?php _e('Results & Scores');?>');
	} 
}).window('open');

</script>

<img class="mainpage" src="/agility/images/wallpapers/clasificaciones.jpg" alt="<?php _e('Scores'); ?>" width="640" height="480" align="middle"/>

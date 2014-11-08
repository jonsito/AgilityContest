<!-- 
frm_competicion.php

Copyright 2013-2014 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
 
 <?php require_once("dialogs/dlg_selectJornada.inc")?>

<script type="text/javascript">

// display prueba selection dialog
$('#seljornada-window').window({
	onClose: function () {
		var extra="";
		var page="/agility/client/frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents(page,'');
			return;
		}
		page="/agility/client/frm_competicion2.php";
		if (workingData.datosJornada.Equipos3==1) {
			page="/agility/client/frm_competicion_eq3.php";
			extra=" ( Equipos -3 mejores- )";
		}
		if (workingData.datosJornada.Equipos4==1) {
			page="/agility/client/frm_competicion_eq4.php";
			extra=" ( Equipos -conjunta- )";
		}
		if (workingData.datosJornada.Open==1) {
			page="/agility/client/frm_competicion_open.php";
			extra=" ( Abierta )";
		}
		if (workingData.datosJornada.KO==1) {
			page="/agility/client/frm_competicion_ko.php";
			extra=" ( Mangas K.O. )";
		}
		loadContents(page,'Desarrollo de la jornada'+extra);
	} 
});

initWorkingData();
$('#seljornada-window').window('open');

</script>

<img class="mainpage" src="/agility/images/wallpapers/foto_dama.jpg" alt="Dama" width="800" height="400" align="middle"/>

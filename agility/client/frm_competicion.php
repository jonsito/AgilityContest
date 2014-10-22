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

<?php include_once("dialogs/dlg_selectJornada.inc")?>

<script type="text/javascript">

// display prueba selection dialog
$('#seljornada-window').window({
	onClose: function () {

		var page="frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents(page,'');
			return;
		}
		page="frm_competicion2.php";
		if (workingData.datosJornada.Equipos3==1) page="frm_competicion_eq3.php";
		if (workingData.datosJornada.Equipos4==1) page="frm_competicion_eq4.php";
		if (workingData.datosJornada.Open==1) page="frm_competicion_open.php";
		if (workingData.datosJornada.KO==1) page="frm_competicion_ko.php";
		loadContents(page,'Desarrollo de la jornada');
	} 
});

initWorkingData();
$('#seljornada-window').window('open');

</script>

<img class="mainpage" src="images/wallpapers/foto_dama.jpg" alt="Dama" width="800" height="400" align="middle"/>
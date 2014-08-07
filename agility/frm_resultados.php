<?php include_once("dialogs/dlg_selectManga.inc")?>

<script type="text/javascript">

// display prueba selection dialog

$('#selmanga-window').window({
	onClose: function () {
		var page="frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents(page,"");
			return;
		}
		page="frm_resultados2.php";
		if (workingData.datosJornada.Equipos3==1) page="resultados_eq3.php"
		if (workingData.datosJornada.Equipos4==1) page="resultados_eq4.php"
		if (workingData.datosJornada.Open==1) page="resultados_open.php"
		if (workingData.datosJornada.KO==1) page="resultados_ko.php"
		loadContents(page,'Resultados y Clasificaciones');
	} 
});

initWorkingData();
$('#selmanga-window').window('open');

</script>

<img class="mainpage" src="images/wallpapers/trio_de_ases.jpg" alt="Trio" align="middle"/>
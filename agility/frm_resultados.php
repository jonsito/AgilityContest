<?php include_once("dialogs/dlg_selectManga.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Resultados - Selecci&oacute;n de Prueba, Jornada y Ronda</p>');

// display prueba selection dialog

$('#selmanga-window').window({
	onClose: function () {
		var page="frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents('#contenido',page);
			return;
		}
		page="frm_resultados2.php";
		if (workingData.datosJornada.Equipos3==1) page="resultados2_equipos3.php"
		if (workingData.datosJornada.Equipos4==1) page="resultados2_equipos4.php"
		if (workingData.datosJornada.Open==1) page="resultados2_open.php"
		if (workingData.datosJornada.KO==1) page="resultados2_ko.php"
		loadContents('#contenido',page);
	} 
});

initWorkingData();
$('#selmanga-window').window('open');

</script>

<img class="mainpage" src="images/trio_de_ases.jpg" alt="Trio" align="middle"/>
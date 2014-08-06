<?php include_once("dialogs/dlg_selectJornada.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Competicion - Selecci&oacute;n de Prueba y Jornada</p>');

// display prueba selection dialog

$('#seljornada-window').window({
	onClose: function () {

		var page="frm_main.php";
		// no jornada selected load main menu
		if (workingData.jornada==0) {
			loadContents('#contenido',page);
			return;
		}
		page="frm_competicion2.php";
		if (workingData.datosJornada.Equipos3==1) page="frm_competicion2_equipos3.php"
		if (workingData.datosJornada.Equipos4==1) page="frm_competicion2_equipos4.php"
		if (workingData.datosJornada.Open==1) page="frm_competicion2_open.php"
		if (workingData.datosJornada.KO==1) page="frm_competicion2_ko.php"
		loadContents('#contenido',page);
	} 
});

initWorkingData();
$('#seljornada-window').window('open');

</script>

<img class="mainpage" src="images/foto_dama.jpg" alt="Dama" width="800" height="400" align="middle"/>
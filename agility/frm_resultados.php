<?php include_once("dialogs/dlg_selectManga.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Resultados - Selecci&oacute;n de Prueba, Jornada y Ronda</p>');

// display prueba selection dialog

$('#selmanga-window').window({
	onClose: function () {
		var page=(workingData.jornada!=0)?'frm_resultados2.php':'frm_main.php';
		loadContents('#contenido',page);
	} 
});

$('#selmanga-window').window('open');

</script>

<img class="mainpage" src="images/trio_de_ases.jpg" alt="Trio" align="middle"/>
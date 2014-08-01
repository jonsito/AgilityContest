<?php include_once("dialogs/dlg_selectJornada.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Competicion - Selecci&oacute;n de Prueba y Jornada</p>');

// display prueba selection dialog

$('#seljornada-window').window({
	onClose: function () {
		var page=(workingData.jornada!=0)?'frm_competicion2.php':'frm_main.php';
		loadContents('#contenido',page);
	} 
});

initWorkingData();
$('#seljornada-window').window('open');

</script>

<img class="mainpage" src="images/foto_dama.jpg" alt="Dama" width="800" height="400" align="middle"/>

<?php include_once("dialogs/dlg_selectPrueba.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Inscripciones - Selecci&oacute;n de prueba</p>');

// display prueba selection dialog

$('#selprueba-window').window({
	onClose: function () {
		var page=(workingData.prueba!=0)?'inscripciones2.php':'main.php';
		loadContents('#contenido',page);
	} 
});

$('#selprueba-window').window('open');

</script>

<img class="mainpage" src="images/foto_klein.jpg" alt="Klein" width="800" height="400" align="middle"/>
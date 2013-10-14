
<?php include_once("dialogs/dlg_selectPrueba.inc")?>

<script type="text/javascript">

// set up header title
$('#Header_Operation').html('<p>Inscripciones - Selecci&oacute;n de prueba</p>');

// display prueba selection dialog

$('#selprueba-window').window({
	onClose: function () {
		// TODO: check if prueba exists before opening form
		loadContents('#contenido','inscripciones2.php')
	} 
});

$('#selprueba-window').window('open');

</script>

<img class="mainpage" src="images/foto_klein.jpg" alt="Klein" width="800" heigt="400" align="center"/>
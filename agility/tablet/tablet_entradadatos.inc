<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="tdialog-panel" class="easyui-panel" style="width:1280px;height:800px;">
	<div class="dialog_forms">
        <form id="tdialog-form" method="get" novalidate="novalidate">
			<input id="tdialog-Session" name="Session" type="hidden" value="0"> <!-- Session ID -->  
			<input id="tdialog-Parent" name="Parent" type="hidden" value=""> <!-- name of parent datagrid -->       
        	<input id="tdialog-ID" name="ID" type="hidden">     <!-- Tanda ID -->
        	<input id="tdialog-Prueba" name="Prueba" type="hidden">
        	<input id="tdialog-Jornada" name="Jornada" type="hidden">
        	<input id="tdialog-Manga" name="Manga" type="hidden">
        	<input id="tdialog-Perro" name="Perro" type="hidden">
      		<input id="tdialog-Licencia" name="Licencia" type="hidden">
      		<input id="tdialog-Pendiente" name="Pendiente" type="hidden" value="0">
      		<input id="tdialog-Tanda" name="Tanda" type="hidden"> <!-- Tanda name -->
      		<input id="tdialog-Observacioens" name="Observaciones" type="hidden">
      		<input id="tdialog-Operation" name="Operation" type="hidden" value="update">
       		<input id="tdialog-FaltaUpBtn" type="button" value="Falta" onclick="tablet_up('#tdialog-Faltas');" class="tablet_button tb_falta">
       		<input id="tdialog-FaltaDownBtn" type="button" value="Falta -" onclick="tablet_down('#tdialog-Faltas');">
       		<input id="tdialog-RehuseUpBtn" type="button" value="Rehuse" onclick="tablet_up('#tdialog-Rehuses')" class="tablet_button tb_rehuse">
       		<input id="tdialog-RehuseDownBtn" type="button" value="Rehuse -" onclick="tablet_down('#tdialog-Rehuses');">
       		<input id="tdialog-TocadoUpBtn" type="button" value="Tocado" onclick="tablet_up('#tdialog-Tocados');" class="tablet_button tb_tocado">
       		<input id="tdialog-TocadoDownBtn" type="button" value="Tocado -" onclick="tablet_down('#tdialog-Tocados');">
       		<input id="tdialog-StartStopBtn" type="button" value="Start" onclick="tablet_startstop();" class="tablet_button tb_crono">
       		<input id="tdialog-SalidaBtn" type="button" value="Salida" onclick="tablet_salida();" class="tablet_button tb_salida">
       		<input id="tdialog-AcceptBtn" type="button" value="Aceptar" onclick="tablet_accept();" class="tablet_button tb_accept"> 
       		<input id="tdialog-CancelBtn" type="button" value="Cancelar" onclick="tablet_cancel();" class="tablet_button tb_cancel">
       		<input id="tdialog-NoPresentadoBtn" type="button" value="No Presentado" onclick="tablet_np();" class="tablet_button tb_nopresentado">
       		<input id="tdialog-EliminadoBtn" type="button" value="Eliminado" onclick="tablet_elim('#tdialog-Eliminado');" class="tablet_button tb_eliminado">
       		<input id="tdialog-0" type="button" value="0" class="tablet_numbers" onclick="tablet_add(0);">
       		<input id="tdialog-1" type="button" value="1" class="tablet_numbers" onclick="tablet_add(1);">
       		<input id="tdialog-2" type="button" value="2" class="tablet_numbers" onclick="tablet_add(2);">
       		<input id="tdialog-3" type="button" value="3" class="tablet_numbers" onclick="tablet_add(3);">
       		<input id="tdialog-4" type="button" value="4" class="tablet_numbers" onclick="tablet_add(4);">
       		<input id="tdialog-5" type="button" value="5" class="tablet_numbers" onclick="tablet_add(5);">
       		<input id="tdialog-6" type="button" value="6" class="tablet_numbers" onclick="tablet_add(6);">
       		<input id="tdialog-7" type="button" value="7" class="tablet_numbers" onclick="tablet_add(7);">
       		<input id="tdialog-8" type="button" value="8" class="tablet_numbers" onclick="tablet_add(8);">
       		<input id="tdialog-9" type="button" value="9" class="tablet_numbers" onclick="tablet_add(9);">
       		<input id="tdialog-Del" type="button" value="." class="tablet_numbers" onclick="tablet_dot();">
       		<input id="tdialog-Dot" type="button" value="Del" class="tablet_numbers" onclick="tablet_del();">
       		<label id="tdialog-DorsalLbl" for="Dorsal" class="tablet_info">Dorsal</label>
       		<input id="tdialog-Dorsal" type="text" readonly="readonly" name="Dorsal" class="tablet_info"/>
       		<label id="tdialog-NombreLbl" for="Nombre" class="tablet_info">Nombre</label>
       		<input id="tdialog-Nombre" type="text" readonly="readonly" name="Nombre" class="tablet_info"/>
       		<label id="tdialog-GuiaLbl" for="Guia" class="tablet_info">Gu&iacute;a</label>
       		<input id="tdialog-Guia" type="text" readonly="readonly" name="NombreGuia" class="tablet_info"/>
       		<label id="tdialog-ClubLbl" for="Club" class="tablet_info">Club</label>
       		<input id="tdialog-Club" type="text" readonly="readonly" name="NombreClub" class="tablet_info"/>
       		<label id="tdialog-CategoriaLbl" for="Categoria" class="tablet_info">Cat.</label>
       		<input id="tdialog-Categoria" type="text" readonly="readonly" name="Categoria" class="tablet_info"/>
       		<label id="tdialog-GradoLbl" for="Grado" class="tablet_info">Grado</label>
       		<input id="tdialog-Grado" type="text" readonly="readonly" name="Grado" class="tablet_info"/>
       		<label id="tdialog-CeloLbl" for="Celo" class="tablet_info">Celo</label>
       		<input id="tdialog-Celo" type="text" readonly="readonly" name="Celo" class="tablet_info"/>
       		<label id="tdialog-FaltasLbl" for="Faltas">Faltas</label>
       		<input id="tdialog-Faltas" type="text" readonly="readonly" value="0" name="Faltas" class="tablet_data"/>
       		<label id="tdialog-TocadosLbl" for="Tocados">Tocados</label>
       		<input id="tdialog-Tocados" type="text" readonly="readonly" value="0" name="Tocados" class="tablet_data"/>
       		<label id="tdialog-RehusesLbl" for="Rehuses">Rehuses</label>
       		<input id="tdialog-Rehuses" type="text" readonly="readonly" value="0" name="Rehuses" class="tablet_data"/>
       		<label id="tdialog-TiempoLbl" for="Tiempo">Tiempo</label>
       		<input id="tdialog-Tiempo" type="text" readonly="readonly" value="00.00" name="Tiempo" class="tablet_data"/>
       		<label id="tdialog-NoPresentadoLbl" for="NoPresentado">N. P.</label>
       		<input id="tdialog-NoPresentado" type="text" readonly="readonly" value="0" name="NoPresentado" class="tablet_data"/>
       		<label id="tdialog-EliminadoLbl" for="Eliminado">Elim.</label>
       		<input id="tdialog-Eliminado" type="text" readonly="readonly" value="0" name="Eliminado" class="tablet_data"/>
       		<label id="tdialog-Rectangulo" class="tablet_rectangulo">&nbsp;</label>
        </form> 
    </div>
</div>


<script type="text/javascript">
    // declaracion del dialogo
    $('#tdialog-panel').panel({
        iconCls: 'icon-notes',
        closed: true,
        modal: true,
        title: 'Entrada de datos',
		closable:		false,
    	collapsible:	false,
    	minimizable:	false,
    	maximizable:	false,
    	resizable:		false,
    	onClose:	function() { $('#tablet-panel').panel('open'); }
    });

    $('#tdialog-form').form({
        // tell session manager to update competitor's info
        onLoadSuccess: function() {
            $('#tdialog-StartStopBtn').val("Start");
            tablet_putEvent('llamada', 
        		{ // setup initial data for event
        		'NoPresentado'	:	$('#tdialog-NoPresentado').val(),
        		'Faltas'		:	$('#tdialog-Faltas').val(),
        		'Tocados'		:	$('#tdialog-Tocados').val(),
        		'Rehuses'		:	$('#tdialog-Rehuses').val(),
        		'Tiempo'		:	$('#tdialog-Tiempo').val(),
        		'Eliminado'		:	$('#tdialog-Eliminado').val(),
        		'Celo'			:	$('#tdialog-Celo').val(),
        		'Dorsal'		:	$('#tdialog-Dorsal').val(),
        		}
            ) ;
        }
    });

    // layout
    var dg= {'cols':210, 'rows':145}; // declare base datagrid as A5 sheet

    doLayout(dg,"#tdialog-FaltaUpBtn",		5,		10,		35,		90	);
    doLayout(dg,"#tdialog-FaltaDownBtn",	50,		10,		15,		20	);
    doLayout(dg,"#tdialog-RehuseUpBtn",		170,	10,		35,		90	);
    doLayout(dg,"#tdialog-RehuseDownBtn",	145,	10,		15,		20	);
    doLayout(dg,"#tdialog-TocadoUpBtn",		10,		110,	30,		30	);
    doLayout(dg,"#tdialog-TocadoDownBtn",	50,		120,	15,		20	);
    doLayout(dg,"#tdialog-SalidaBtn",		50,		85,		15,		15	);
    doLayout(dg,"#tdialog-StartStopBtn",	145,	85,		15,		15	);
    doLayout(dg,"#tdialog-AcceptBtn",		170,	120,	30,		20	);
    doLayout(dg,"#tdialog-CancelBtn",		145,	120,	15,		20	);
    doLayout(dg,"#tdialog-NoPresentadoBtn",	75,		10,		25,		20	);
    doLayout(dg,"#tdialog-EliminadoBtn",	110,	10,		25,		20	);
    doLayout(dg,"#tdialog-1",				75,		80,		20,		15	);
    doLayout(dg,"#tdialog-2",				95,		80,		20,		15	);
    doLayout(dg,"#tdialog-3",				115,	80,		20,		15	);
    doLayout(dg,"#tdialog-4",				75,		95,		20,		15	);
    doLayout(dg,"#tdialog-5",				95,		95,		20,		15	);
    doLayout(dg,"#tdialog-6",				115,	95,		20,		15	);
    doLayout(dg,"#tdialog-7",				75,		110,	20,		15	);
    doLayout(dg,"#tdialog-8",				95,		110,	20,		15	);
    doLayout(dg,"#tdialog-9",				115,	110,	20,		15	);
    doLayout(dg,"#tdialog-Del",				115,	125,	20,		15	);
    doLayout(dg,"#tdialog-0",				95,		125,	20,		15	);
    doLayout(dg,"#tdialog-Dot",				75,		125,	20,		15	);
    doLayout(dg,"#tdialog-DorsalLbl",		50,		31,		15,		8	);
    doLayout(dg,"#tdialog-Dorsal",			65,		31,		35,		8	);
    doLayout(dg,"#tdialog-NombreLbl",		100,	31,		15,		8	);
    doLayout(dg,"#tdialog-Nombre",			115,	31,		45,		8	);
    doLayout(dg,"#tdialog-GuiaLbl",			50,		39,		15,		8	);
    doLayout(dg,"#tdialog-Guia",			65,		39,		35,		8	);
    doLayout(dg,"#tdialog-ClubLbl",			100,	39,		15,		8	);
    doLayout(dg,"#tdialog-Club",			115,	39,		45,		8	);
    doLayout(dg,"#tdialog-CategoriaLbl",	50,		47,		15,		8	);
    doLayout(dg,"#tdialog-Categoria",		65,		47,		20,		8	);
    doLayout(dg,"#tdialog-GradoLbl",		90,		47,		15,		8	);
    doLayout(dg,"#tdialog-Grado",			105,	47,		20,		8	);
    doLayout(dg,"#tdialog-CeloLbl",			140,	47,		10,		8	);
    doLayout(dg,"#tdialog-Celo",			150,	47,		10,		8	);
    doLayout(dg,"#tdialog-FaltasLbl",		50,		70,		10,		5	);
    doLayout(dg,"#tdialog-Faltas",			50,		55,		10,		15	);
    doLayout(dg,"#tdialog-TocadosLbl",		65,		70,		10,		5	);
    doLayout(dg,"#tdialog-Tocados",			65,		55,		10,		15	);
    doLayout(dg,"#tdialog-RehusesLbl",		80,		70,		10,		5	);
    doLayout(dg,"#tdialog-Rehuses",			80,		55,		10,		15	);
    doLayout(dg,"#tdialog-TiempoLbl",		95,		70,		35,		5	);
    doLayout(dg,"#tdialog-Tiempo",			95,		55,		35,		15	);
    doLayout(dg,"#tdialog-NoPresentadoLbl",	135,	70,		10,		5	);
    doLayout(dg,"#tdialog-NoPresentado",	135,	55,		10,		15	);
    doLayout(dg,"#tdialog-EliminadoLbl",	150,	70,		10,		5	);
    doLayout(dg,"#tdialog-Eliminado",		150,	55,		10,		15	);
    doLayout(dg,"#tdialog-Rectangulo",		45,		32,		120,	46  );
</script>
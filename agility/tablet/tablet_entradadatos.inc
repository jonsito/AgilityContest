<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="tdialog-dialog" style="width:1000px;height:580px;padding:10px">
	<div class="dialog_forms">
        <form id="tdialog-form" method="get" novalidate="novalidate">
			<input id="tdialog-Parent" name="Parent" type="hidden" value="0"> <!-- row idx on parent datagrid -->       
        	<input id="tdialog-ID" name="ID" type="hidden">     
        	<input id="tdialog-Prueba" name="Prueba" type="hidden">
        	<input id="tdialog-Jornada" name="Jornada" type="hidden">
        	<input id="tdialog-Manga" name="Manga" type="hidden">
        	<input id="tdialog-Perro" name="Perro" type="hidden">
      		<input id="tdialog-Licencia" name="Licencia" type="hidden">
      		<input id="tdialog-Pendiente" name="Pendiente" type="hidden">
      		<input id="tdialog-Tanda" name="Tanda" type="hidden">
      		<input id="tdialog-Observacioens" name="Observaciones" type="hidden">
      		<input id="tdialog-Operation" name="Operation" type="hidden" value="update">
       		<input id="tdialog-FaltaUpBtn" type="button" value="Falta +" onclick="tablet_up('#tdialog-Faltas');">
       		<input id="tdialog-FaltaDownBtn" type="button" value="Falta -" onclick="tablet_down('#tdialog-Faltas');">
       		<input id="tdialog-RehuseUpBtn" type="button" value="Rehuse +" onclick="tablet_up('#tdialog-Rehuses');">
       		<input id="tdialog-RehuseDownBtn" type="button" value="Rehuse -" onclick="tablet_down('#tdialog-Rehuses');">
       		<input id="tdialog-TocadoUpBtn" type="button" value="Tocado +" onclick="tablet_up('#tdialog-Tocados');">
       		<input id="tdialog-TocadoDownBtn" type="button" value="Tocado -" onclick="tablet_down('#tdialog-Tocados');">
       		<input id="tdialog-StartStopBtn" type="button" value="Start/Stop" onclick="tablet_startstop();">
       		<input id="tdialog-LapResetBtn" type="button" value="Lap/Reset" onclick="tablet_lapreset();">
       		<input id="tdialog-AcceptBtn" type="button" value="Aceptar" onclick="tablet_accept();"> 
       		<input id="tdialog-CancelBtn" type="button" value="Cancelar" onclick="tablet_cancel();">
       		<input id="tdialog-NoPresentadoBtn" type="button" value="No Presentado" onclick="tablet_swap('#tdialog-NoPresentado');">
       		<input id="tdialog-EliminadoBtn" type="button" value="Eliminado" onclick="tablet_swap('#tdialog-Eliminado');">
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
       		<label id="tdialog-DorsalLbl" for="Dorsal">Dorsal</label>
       		<input id="tdialog-Dorsal" type="text" readonly="readonly" name="Dorsal" style="border:0;padding:0;"/>
       		<label id="tdialog-NombreLbl" for="Nombre">Nombre</label>
       		<input id="tdialog-Nombre" type="text" readonly="readonly" name="Nombre" style="border:0;padding:0;"/>
       		<label id="tdialog-GuiaLbl" for="Guia">Gu&iacute;a</label>
       		<input id="tdialog-Guia" type="text" readonly="readonly" name="NombreGuia" style="border:0;padding:0;"/>
       		<label id="tdialog-ClubLbl" for="Club">Club</label>
       		<input id="tdialog-Club" type="text" readonly="readonly" name="NombreClub" style="border:0;padding:0;"/>
       		<label id="tdialog-CategoriaLbl" for="Categoria">Cat.</label>
       		<input id="tdialog-Categoria" type="text" readonly="readonly" name="Categoria" style="border:0;padding:0;"/>
       		<label id="tdialog-GradoLbl" for="Grado">Grado</label>
       		<input id="tdialog-Grado" type="text" readonly="readonly" name="Grado" style="border:0;padding:0;"/>
       		<label id="tdialog-CeloLbl" for="Celo">Celo</label>
       		<input id="tdialog-Celo" type="text" readonly="readonly" name="Celo" style="border:0;padding:0;"/>
       		<label id="tdialog-FaltasLbl" for="Faltas">Faltas</label>
       		<input id="tdialog-Faltas" type="text" readonly="readonly" value="0" name="Faltas" class="tablet_data"/>
       		<label id="tdialog-TocadosLbl" for="Tocados">Tocados</label>
       		<input id="tdialog-Tocados" type="text" readonly="readonly" value="0" name="Tocados" class="tablet_data"/>
       		<label id="tdialog-RehusesLbl" for="Rehuses">Rehuses</label>
       		<input id="tdialog-Rehuses" type="text" readonly="readonly" value="0" name="Rehuses" class="tablet_data"/>
       		<label id="tdialog-TiempoLbl" for="Tiempo">Tiempo</label>
       		<input id="tdialog-Tiempo" type="text" readonly="readonly" value="00.00" name="Tiempo" class="tablet_data"/>
       		<label id="tdialog-NoPresentadoLbl" for="NoPresentado">N. P.</label>
       		<input id="tdialog-NoPresentado" type="text" readonly="readonly" value="N.P." name="NoPresentado" class="tablet_data"/>
       		<label id="tdialog-EliminadoLbl" for="Eliminado">Elim.</label>
       		<input id="tdialog-Eliminado" type="text" readonly="readonly" value="Elim" name="Eliminado" class="tablet_data"/>
        </form> 
    </div>
</div>


<script type="text/javascript">
    
    // declaracion del dialogo
    $('#tdialog-dialog').dialog({
        iconCls: 'icon-notes',
        closed: true,
        modal: true,
        title: 'Entrada de datos'
    });

    $('#tdialog-form').form();

    // layout
    var dg= {'cols':28, 'rows':22}; // declare base datagrid

    doLayout(dg,"#tdialog-FaltaUpBtn",		1,		1,		5,		3	);
    doLayout(dg,"#tdialog-FaltaDownBtn",	4,		5,		2,		1	);
    doLayout(dg,"#tdialog-RehuseUpBtn",		22,		1,		5,		3	);
    doLayout(dg,"#tdialog-RehuseDownBtn",	25,		5,		2,		1	);
    doLayout(dg,"#tdialog-TocadoUpBtn",		1,		15,		5,		3	);
    doLayout(dg,"#tdialog-TocadoDownBtn",	4,		19,		2,		1	);
    doLayout(dg,"#tdialog-StartStopBtn",	4,		12,		2,		1	);
    doLayout(dg,"#tdialog-LapResetBtn",		1,		12,		2,		1	);
    doLayout(dg,"#tdialog-AcceptBtn",		24,		17,		3,		3	);
    doLayout(dg,"#tdialog-CancelBtn",		19,		18,		3,		2	);
    doLayout(dg,"#tdialog-NoPresentadoBtn",	19,		12,		3,		2	);
    doLayout(dg,"#tdialog-EliminadoBtn",	24,		12,		3,		2	);
    doLayout(dg,"#tdialog-1",				8,		12,		3,		2	);
    doLayout(dg,"#tdialog-2",				11,		12,		3,		2	);
    doLayout(dg,"#tdialog-3",				14,		12,		3,		2	);
    doLayout(dg,"#tdialog-4",				8,		14,		3,		2	);
    doLayout(dg,"#tdialog-5",				11,		14,		3,		2	);
    doLayout(dg,"#tdialog-6",				14,		14,		3,		2	);
    doLayout(dg,"#tdialog-7",				8,		16,		3,		2	);
    doLayout(dg,"#tdialog-8",				11,		16,		3,		2	);
    doLayout(dg,"#tdialog-9",				14,		16,		3,		2	);
    doLayout(dg,"#tdialog-Dot",				8,		18,		3,		2	);
    doLayout(dg,"#tdialog-0",				11,		18,		3,		2	);
    doLayout(dg,"#tdialog-Del",				14,		18,		3,		2	);
    doLayout(dg,"#tdialog-DorsalLbl",		8,		1,		2,		1	);
    doLayout(dg,"#tdialog-Dorsal",			10,		1,		2,		1	);
    doLayout(dg,"#tdialog-NombreLbl",		12,		1,		2,		1	);
    doLayout(dg,"#tdialog-Nombre",			14,		1,		6,		1	);
    doLayout(dg,"#tdialog-GuiaLbl",			8,		2,		2,		1	);
    doLayout(dg,"#tdialog-Guia",			10,		2,		10,		1	);
    doLayout(dg,"#tdialog-ClubLbl",			8,		3,		2,		1	);
    doLayout(dg,"#tdialog-Club",			10,		3,		10,		1	);
    doLayout(dg,"#tdialog-CategoriaLbl",	8,		5,		2,		1	);
    doLayout(dg,"#tdialog-Categoria",		10,		5,		1,		1	);
    doLayout(dg,"#tdialog-GradoLbl",		12,		5,		2,		1	);
    doLayout(dg,"#tdialog-Grado",			14,		5,		2,		1	);
    doLayout(dg,"#tdialog-CeloLbl",			17,		5,		2,		1	);
    doLayout(dg,"#tdialog-Celo",			19,		5,		1,		1	);
    doLayout(dg,"#tdialog-FaltasLbl",		1,		7,		3,		1	);
    doLayout(dg,"#tdialog-Faltas",			1,		8,		3,		2	);
    doLayout(dg,"#tdialog-TocadosLbl",		5,		7,		3,		1	);
    doLayout(dg,"#tdialog-Tocados",			5,		8,		3,		2	);
    doLayout(dg,"#tdialog-RehusesLbl",		9,		7,		3,		1	);
    doLayout(dg,"#tdialog-Rehuses",			9,		8,		3,		2	);
    doLayout(dg,"#tdialog-TiempoLbl",		13,		7,		8,		1	);
    doLayout(dg,"#tdialog-Tiempo",			13,		8,		8,		2	);
    doLayout(dg,"#tdialog-NoPresentadoLbl",	22,		7,		2,		1	);
    doLayout(dg,"#tdialog-NoPresentado",	22,		8,		2,		2	);
    doLayout(dg,"#tdialog-EliminadoLbl",	25,		7,		2,		1	);
    doLayout(dg,"#tdialog-Eliminado",		25,		8,		2,		2	);
</script>
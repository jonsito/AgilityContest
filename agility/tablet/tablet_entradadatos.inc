<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="tdialog-dialog" style="width:1000px;height:580px;padding:10px">
	<div class="dialog_forms">
        <form id="tdialog-form" method="get" novalidate="novalidate">
       		<input id="tdialog-FaltaUpBtn" type="button" class="easyui-linkbutton" value="Falta +">
       		<input id="tdialog-FaltaDownBtn" type="button" class="easyui-linkbutton" value="Falta -">
       		<input id="tdialog-RehuseUpBtn" type="button" class="easyui-linkbutton" value="Rehuse +">
       		<input id="tdialog-RehuseDownBtn" type="button" class="easyui-linkbutton" value="Rehuse -">
       		<input id="tdialog-TocadoUpBtn" type="button" class="easyui-linkbutton" value="Tocado +">
       		<input id="tdialog-TocadoDownBtn" type="button" class="easyui-linkbutton" value="Tocado -">
       		<input id="tdialog-StartStopBtn" type="button" class="easyui-linkbutton" value="Start/Stop">
       		<input id="tdialog-LapResetBtn" type="button" class="easyui-linkbutton" value="Lap/Reset">
       		<input id="tdialog-AcceptBtn" type="button" class="easyui-linkbutton" value="Aceptar">
       		<input id="tdialog-CancelBtn" type="button" class="easyui-linkbutton" value="Cancelar">
       		<input id="tdialog-NoPresentadoBtn" type="button" class="easyui-linkbutton" value="No Presentado">
       		<input id="tdialog-EliminadoBtn" type="button" class="easyui-linkbutton" value="Eliminado">
       		<input id="tdialog-0" type="button" class="easyui-linkbutton" value="0">
       		<input id="tdialog-1" type="button" class="easyui-linkbutton" value="1">
       		<input id="tdialog-2" type="button" class="easyui-linkbutton" value="2">
       		<input id="tdialog-3" type="button" class="easyui-linkbutton" value="3">
       		<input id="tdialog-4" type="button" class="easyui-linkbutton" value="4">
       		<input id="tdialog-5" type="button" class="easyui-linkbutton" value="5">
       		<input id="tdialog-6" type="button" class="easyui-linkbutton" value="6">
       		<input id="tdialog-7" type="button" class="easyui-linkbutton" value="7">
       		<input id="tdialog-8" type="button" class="easyui-linkbutton" value="8">
       		<input id="tdialog-9" type="button" class="easyui-linkbutton" value="9">
       		<input id="tdialog-Del" type="button" class="easyui-linkbutton" value=".">
       		<input id="tdialog-Dot" type="button" class="easyui-linkbutton" value="Del">
       		<input id="tdialog-Nombre" type="text" value="Nombre"/>
       		<input id="tdialog-Guia" type="text" value="Guia"/>
       		<input id="tdialog-Club" type="text" value="Club"/>
       		<input id="tdialog-Categoria" type="text" value="Categoria"/>
       		<input id="tdialog-Grado" type="text" value="Grado"/>
       		<input id="tdialog-Celo" type="text" value="Celo"/>
       		<label id="tdialog-FaltasLbl" for="Faltas">Faltas</label>
       		<input id="tdialog-Faltas" type="text" value="0" name="Faltas"/>
       		<label id="tdialog-TocadosLbl" for="Tocados">Tocados</label>
       		<input id="tdialog-Tocados" type="text" value="0" name="Tocados"/>
       		<label id="tdialog-RehusesLbl" for="Rehuses">Rehuses</label>
       		<input id="tdialog-Rehuses" type="text" value="0" name="Rehuses"/>
       		<label id="tdialog-TiempoLbl" for="Tiempo">Tiempo</label>
       		<input id="tdialog-Tiempo" type="text" value="00.00" name="Tiempo"/>
       		<label id="tdialog-NoPresentadoLbl" for="NoPresentado">NoPresentado</label>
       		<input id="tdialog-NoPresentado" type="text" value="N.P." name="NoPresentado"/>
       		<label id="tdialog-EliminadoLbl" for="Eliminado">Eliminado</label>
       		<input id="tdialog-Eliminado" type="text" value="Elim" name="Elmininado"/>
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

    $('#tdialog-form').form({
        onLoadSuccess: function(data) {
            // fill form, labels and checkboxes
        }
    });
    
 	// acciones de los botones
    $('#tdialog-cancelBtn').bind('click',function() {
        $('#tdialog-dialog').dialog('close');
    });

    // layout
    var dg= {'cols':28, 'rows':22}; // declare base datagrid

    doLayout(dg,"#tdialog-FaltaUpBtn",		1,		1,		5,		3	);
    doLayout(dg,"#tdialog-FaltaDownBtn",	1,		5,		5,		1	);
    doLayout(dg,"#tdialog-RehuseUpBtn",		22,		1,		5,		3	);
    doLayout(dg,"#tdialog-RehuseDownBtn",	22,		5,		5,		1	);
    doLayout(dg,"#tdialog-TocadoUpBtn",		1,		15,		5,		3	);
    doLayout(dg,"#tdialog-TocadoDownBtn",	1,		19,		5,		1	);
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
    doLayout(dg,"#tdialog-Nombre",			8,		1,		12,		1	);
    doLayout(dg,"#tdialog-Guia",			8,		2,		12,		1	);
    doLayout(dg,"#tdialog-Club",			8,		3,		12,		1	);
    doLayout(dg,"#tdialog-Categoria",		8,		5,		4,		1	);
    doLayout(dg,"#tdialog-Grado",			13,		5,		3,		1	);
    doLayout(dg,"#tdialog-Celo",			17,		5,		3,		1	);
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
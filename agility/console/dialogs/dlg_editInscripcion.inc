<!-- 
dlg_editInscripcion.inc

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

<?php
require_once(__DIR__ . "/../../server/tools.php");
require_once(__DIR__ . "/../../server/auth/Config.php");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE EDICION DE INSCRIPCIONES -->
<div id="edit_inscripcion-dialog" style="width:525px;height:425px;padding:2px">
	<div class="easyui-panel" data-options="fit:true">
		<div class="dialog_forms">
        <form id="edit_inscripcion-form" method="get" novalidate="novalidate">
			<input id="edit_inscripcion-ID" type="hidden" name="ID"/>
        	<input id="edit_inscripcion-Prueba" type="hidden" name="Prueba"/>
        	<input id="edit_inscripcion-Jornadas" type="hidden" name="Jornadas"/>
        	<input id="edit_inscripcion-Operation" type="hidden" name="Operation" value="update"/>
            <input id="edit_inscripcion-LOE_RRC" name="LOE_RRC" type="hidden" />
            <input id="edit_inscripcion-Raza" name="Raza" type="hidden" />
           	<input id="edit_inscripcion-Perro" name="Perro" type="hidden" />
           	<span  id="edit_inscripcion-dogInfo" 	class="ftitle"><?php _e('Dog information'); ?>:</span>
           	<span  id="edit_inscripcion-inscInfo" 	class="ftitle" style="text-align:center"><?php _e('Inscription data'); ?>:</span>
           	<label id="edit_inscripcion-Nombrelbl"	for="edit_inscripcion-Nombre"><?php _e('Name'); ?>:</label>
           	<input id="edit_inscripcion-Nombre" 	name="Nombre" type="text" class="easyui-textbox" disabled="disabled" />
        	<label id="edit_inscripcion-Licencialbl" for="edit_inscripcion-Licencia"><?php _e('License'); ?>: </label>
        	<input id="edit_inscripcion-Licencia"	name="Licencia" type="text" class="easyui-textbox" disabled="disabled"/>
        	<label id="edit_inscripcion-Categorialbl" for="edit_inscripcion-Categoria"><?php _e('Category'); ?>: </label>
            <input id="edit_inscripcion-Categoria" 	name="Categoria" type="text" class="easyui-textbox" style="text-align:center" disabled="disabled"/>
        	<label id="edit_inscripcion-Gradolbl"	for="edit_inscripcion-Grado"><?php _e('Grade'); ?>: </label>
            <input id="edit_inscripcion-Grado" 		name="Grado" type="text" class="easyui-textbox" style="text-align:center" disabled="disabled" />
            <label id="edit_inscripcion-NombreGuialbl"	for="edit_inscripcion-NombreGuia"><?php _e('Handler'); ?>: </label>
            <input id="edit_inscripcion-NombreGuia" name="NombreGuia" type="text" class="easyui-textbox" disabled="disabled"/>
            <input id="edit_inscripcion-CatGuia" name="CatGuia" type="text" class="easyui-textbox" disabled="disabled"/>
            <label id="edit_inscripcion-NombreClublbl" for="edit_inscripcion-NombreClub"><?php _e('Club'); ?>: </label>
           	<input id="edit_inscripcion-NombreClub" name="NombreClub" type="text" class="easyui-textbox" disabled="disabled" />
			<input type="hidden" name="J1" value="0"/> <!-- stupid form handling of val==0 -->
           	<input id="edit_inscripcion-J1" 		name="J1" class="easyui-checkbox" type="checkbox" value="1" onclick="doMoney(this);"/>
           	<label id="edit_inscripcion-J1lbl" 		for="edit_inscripcion-J1">J1</label>
			<input type="hidden" name="J2" value="0"/>
            <input id="edit_inscripcion-J2" 		name="J2" class="easyui-checkbox" type="checkbox" value="2" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J2lbl" 		for="edit_inscripcion-J2">J2</label>
			<input type="hidden" name="J3" value="0"/>
            <input id="edit_inscripcion-J3"			name="J3" class="easyui-checkbox" type="checkbox" value="4" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J3lbl"		for="edit_inscripcion-J3">J3</label>
			<input type="hidden" name="J4" value="0"/>
            <input id="edit_inscripcion-J4"			name="J4" class="easyui-checkbox" type="checkbox" value="8" onclick="doMoney(this);"/>
 	        <label id="edit_inscripcion-J4lbl"		for="edit_inscripcion-J4">J4</label>
			<input type="hidden" name="J5" value="0"/>
            <input id="edit_inscripcion-J5"			name="J5" class="easyui-checkbox" type="checkbox" value="16" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J5lbl" 		for="edit_inscripcion-J5">J5</label>
			<input type="hidden" name="J6" value="0"/>
            <input id="edit_inscripcion-J6" 		name="J6" class="easyui-checkbox" type="checkbox" value="32" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J6lbl" 		for="edit_inscripcion-J6">J6</label>
			<input type="hidden" name="J7" value="0"/>
            <input id="edit_inscripcion-J7" 		name="J7" class="easyui-checkbox" type="checkbox" value="64" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J7lbl" 		for="edit_inscripcion-J7">J7</label>
			<input type="hidden" name="J8" value="0"/>
            <input id="edit_inscripcion-J8" 		name="J8" class="easyui-checkbox" type="checkbox" value="128" onclick="doMoney(this);"/>
            <label id="edit_inscripcion-J8lbl" 		for="edit_inscripcion-J8">J8</label>
            <!-- no clasifica -->
            <label id="edit_inscripcion-nclbl"	    for="edit_inscripcion-nc"><?php _e('Not Classify'); ?>:</label>
            <input id="edit_inscripcion-nc"		    name="nc" class="easyui-checkbox" type="checkbox" value="2" />
            <!-- celo -->
            <label id="edit_inscripcion-Celolbl"	for="edit_inscripcion-Celo2"><?php _e('Heat'); ?>:</label>
           	<input id="edit_inscripcion-Celo" 		name="Celo" type="hidden" value="0"/>
            <input id="edit_inscripcion-Celo2"		name="Celo2" class="easyui-checkbox" type="checkbox" value="1" />
            <!-- observaciones y pago -->
	        <label id="edit_inscripcion-Observacioneslbl" for="edit_inscripcion-Observaciones"><?php _e('Comments'); ?>:</label>
	        <input id="edit_inscripcion-Observaciones" type="text" class="easyui-textbox" name="Observaciones"/>
	        <label id="edit_inscripcion-Pagadolbl"	for="edit_inscripcion-Pagado"><?php _e('Paid out'); ?>: </label>
 	  		<input id="edit_inscripcion-Pagado" 	name="Pagado" type="text" class="easyui-textbox" style="text-align:right;padding-right:5px"/>
 	  		<span id="edit_inscripcion-PagadoEuro">&nbsp;&euro;</span>
        </form> 
    	</div>
	</div>
</div>

    
    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="edit_inscripcion-dlg-buttons" style="width:100%;display:inline-block;padding:5px">
		<span style="float:left;">
	   		<a id="edit_inscripcion-editDogBtn" href="#" class="easyui-linkbutton"
	   			data-options="iconCls:'icon-dog'"><?php _e('Modify'); ?></a>
	   	</span>
	   	<span style="float:right;">  	
			<a id="edit_inscripcion-cancelBtn" href="#" class="easyui-linkbutton"
				data-options="iconCls:'icon-cancel'" onclick="$('#edit_inscripcion-dialog').dialog('close');"><?php _e('Cancel'); ?></a>
       		<a id="edit_inscripcion-okBtn" href="#" class="easyui-linkbutton" onclick="saveInscripcion(true)" 
       			data-options="iconCls:'icon-save'"><?php _e('Accept'); ?></a>
       	</span>
    </div>
    
    <script type="text/javascript">

    $('#edit_inscripcion-dialog').dialog({
    	iconCls:'icon-notes',
    	closed:true,
    	modal:true,
    	buttons:'#edit_inscripcion-dlg-buttons',
    	title:'<?php _e('Edit inscription'); ?>',
		onClose:function() {
			// update row and scroll to it
			var form=$('#edit_inscripcion-form').form();
			var dg=$('#inscripciones-datagrid');
			var id=$('#edit_inscripcion-ID').val();
			var idx=dg.datagrid('getRowIndex',id);
			if (idx<0) return;
			var frm=formToObject(form);
			dg.datagrid('updateRow',{ index:idx , row:frm } );
			dg.datagrid('scrollTo',idx);
		}
    });
    
    // declaracion del formulario
    $('#edit_inscripcion-form').form({
        onLoadSuccess: function(data) {
            // retrieve info on jornadas
        	var jornadas=$('#inscripciones-jornadas').datagrid('getData')['rows'];
            // evaluate celo checkbox status
            $('#edit_inscripcion-nc').prop('checked',( (0x02 & parseInt(data.Celo)) !== 0));
            $('#edit_inscripcion-Celo2').prop('checked',((0x01 & parseInt(data.Celo)) !== 0));
            // evaluate checked status of jornadas
        	$('#edit_inscripcion-J1').prop('checked',(data.Jornadas&0x0001)?true:false);
        	$('#edit_inscripcion-J2').prop('checked',(data.Jornadas&0x0002)?true:false);
        	$('#edit_inscripcion-J3').prop('checked',(data.Jornadas&0x0004)?true:false);
        	$('#edit_inscripcion-J4').prop('checked',(data.Jornadas&0x0008)?true:false);
        	$('#edit_inscripcion-J5').prop('checked',(data.Jornadas&0x0010)?true:false);
        	$('#edit_inscripcion-J6').prop('checked',(data.Jornadas&0x0020)?true:false);
        	$('#edit_inscripcion-J7').prop('checked',(data.Jornadas&0x0040)?true:false);
        	$('#edit_inscripcion-J8').prop('checked',(data.Jornadas&0x0080)?true:false);
        	// fix text for jornadas
        	$('#edit_inscripcion-J1lbl').text('J1 - '+jornadas[0].Nombre);
        	$('#edit_inscripcion-J2lbl').text('J2 - '+jornadas[1].Nombre);
        	$('#edit_inscripcion-J3lbl').text('J3 - '+jornadas[2].Nombre);
        	$('#edit_inscripcion-J4lbl').text('J4 - '+jornadas[3].Nombre);
        	$('#edit_inscripcion-J5lbl').text('J5 - '+jornadas[4].Nombre);
        	$('#edit_inscripcion-J6lbl').text('J6 - '+jornadas[5].Nombre);
        	$('#edit_inscripcion-J7lbl').text('J7 - '+jornadas[6].Nombre);
        	$('#edit_inscripcion-J8lbl').text('J8 - '+jornadas[7].Nombre);
        	// evaluate enabled/disabled status by mean of check "Cerrada" and "-- Sin asignar --"
        	$('#edit_inscripcion-J1').prop('disabled', ! canInscribe(jornadas[0]) );
        	$('#edit_inscripcion-J2').prop('disabled', ! canInscribe(jornadas[1]) );
        	$('#edit_inscripcion-J3').prop('disabled', ! canInscribe(jornadas[2]) );
        	$('#edit_inscripcion-J4').prop('disabled', ! canInscribe(jornadas[3]) );
        	$('#edit_inscripcion-J5').prop('disabled', ! canInscribe(jornadas[4]) );
        	$('#edit_inscripcion-J6').prop('disabled', ! canInscribe(jornadas[5]) );
        	$('#edit_inscripcion-J7').prop('disabled', ! canInscribe(jornadas[6]) );
        	$('#edit_inscripcion-J8').prop('disabled', ! canInscribe(jornadas[7]) );
        	// fix handler category name
            $('#edit_inscripcion-CatGuia').val(formatCatGuia(data.CatGuia,0,0));
            $('#edit_inscripcion-Categoria').val(formatCategoria(data.Categoria,0,0));
            // solo el reglamento RSCE contempla perros fuera de categoria/altura/competicion
            let flag=(parseInt(workingData.datosFederation.ID)!==0);
            $('#edit_inscripcion-nc').prop('disabled',flag);
        }
    });
    
    // tooltips
    addTooltip($('#edit_inscripcion-editDogBtn').linkbutton(),'<?php _e("Change dog data"); ?>'+"<br/>"+'<?php _e("into database"); ?>');
    addTooltip($('#edit_inscripcion-okBtn').linkbutton(),'<?php _e("Accept changes"); ?>'+"<br/>"+'<?php _e("Update inscription data"); ?>');
    addTooltip($('#edit_inscripcion-cancelBtn').linkbutton(),'<?php _e("Cancel changes. Close window"); ?>');
    
 	// acciones de los botones

    $('#edit_inscripcion-editDogBtn').bind('click',function() {
        editInscribedDog();
    });
    
    // campos del formulario
    
    function doMoney(obj) {
        // current values
    	var jornadas=$('#edit_inscripcion-Jornadas').val();
        var money=Number($('#edit_inscripcion-Pagado').val());
        // evaluate jornadas
    	if (obj.checked) $('#edit_inscripcion-Jornadas').val(jornadas | obj.value);
    	else $('#edit_inscripcion-Jornadas').val(jornadas & ~obj.value);
    	// evaluate money
        if (obj.checked) $('#edit_inscripcion-Pagado').val(money+12);
        else $('#edit_inscripcion-Pagado').val(money-12);
    }

    // layout
    var dg= {'cols':8, 'rows':160}; // declare base datagrid
	doLayout(dg,'#edit_inscripcion-dogInfo',		0,	10,	3,	10	);
	doLayout(dg,"#edit_inscripcion-inscInfo",		4,	10,	3,	10	);
	doLayout(dg,"#edit_inscripcion-Nombrelbl",		0,	20,	2,	10	);
	doLayout(dg,"#edit_inscripcion-Nombre",			0,	30,	4,	10	);
	doLayout(dg,"#edit_inscripcion-Licencialbl",	0,	40,	2,	10	);
	doLayout(dg,"#edit_inscripcion-Licencia",		0,	50,	4,	10	);
	doLayout(dg,"#edit_inscripcion-Categorialbl",	0,	60,	1,	10	);
	doLayout(dg,"#edit_inscripcion-Categoria",		0,	70,	1,	10	);
	doLayout(dg,"#edit_inscripcion-Gradolbl",		2,	60,	1,	10	);
	doLayout(dg,"#edit_inscripcion-Grado",			2,	70,	1,	10	);
	doLayout(dg,"#edit_inscripcion-NombreGuialbl",	0,	80,	2,	10	);
    doLayout(dg,"#edit_inscripcion-NombreGuia",		0,	90,	3,	10	);
    doLayout(dg,"#edit_inscripcion-CatGuia",		3,	90,	1,	10	);
	doLayout(dg,"#edit_inscripcion-NombreClublbl",	0,	100,2,	10	);
	doLayout(dg,"#edit_inscripcion-NombreClub",		0,	110,4,	10	);
	doLayout(dg,"#edit_inscripcion-J1",				4,	21,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J1lbl",			5,	20,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J2",				4,	31,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J2lbl",			5,	30,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J3",				4,	41,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J3lbl",			5,	40,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J4",				4,	51,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J4lbl",			5,	50,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J5",				4,	61,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J5lbl" ,			5,	60,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J6",				4,	71,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J6lbl",			5,	70,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J7" ,			4,	81,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J7lbl",			5,	80,	3,	10	);
	doLayout(dg,"#edit_inscripcion-J8",				4,	91,	1,	8	);
	doLayout(dg,"#edit_inscripcion-J8lbl",			5,	90,	3,	10	);
    doLayout(dg,"#edit_inscripcion-nclbl",	    	5,	100,2,	10	);
    doLayout(dg,"#edit_inscripcion-nc",	    		6,	100,1,	8	);
    doLayout(dg,"#edit_inscripcion-Celolbl",		5,	110,2,	10	);
    doLayout(dg,"#edit_inscripcion-Celo2",			6,	111,1,	8	);
	doLayout(dg,"#edit_inscripcion-Pagadolbl",		5,	120,1,	10	);
	doLayout(dg,"#edit_inscripcion-Pagado",			6,	121,1,	8	);
	doLayout(dg,"#edit_inscripcion-PagadoEuro",		7,	120,1,	10	);
	doLayout(dg,"#edit_inscripcion-Observacioneslbl",0,	120,3,	10	);
	doLayout(dg,"#edit_inscripcion-Observaciones",	0,	130,7,	10	);
    </script>
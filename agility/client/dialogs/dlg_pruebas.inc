<!-- 
dlg_pruebas.inc

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->

   <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE PRUEBAS -->
    <div id="pruebas-dialog" style="width:450px;height:450px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n de la prueba</div>
        <form id="pruebas-form" method="get" novalidate>
            <div class="fitem">
                <label for="pruebas-Nombre" style="width:325px">Denominaci&oacute;n de la prueba:</label>
                <input id="pruebas-Nombre" 
                	name="Nombre" 
                	type="text" 
                	class="easyui-validatebox" 
                	style="width:350px" />
                <input id="pruebas-ID" name="ID" type="hidden" /> <!-- primary key -->
                <input id="pruebas-Operation" name="Operation" type="hidden" /> <!-- used to store operation type -->
                <input id="pruebas-Cerrada" name="Cerrada" type="hidden" /> <!-- preserve "closed" status -->
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
            	Federaci&oacute;n:
            	<input id="pruebas-RSCE" type="radio" name="RSCE" value="0" checked="checked" />RSCE
            	<input id="pruebas-RFEC" type="radio" name="RSCE" value="1" />RFEC
            	<input id="pruebas-UCA" type="radio" name="RSCE" value="2" />UCA
            	<span style="float:right">
            		<input id="pruebas-Selectiva" type="checkbox" name="Selectiva" value="1"/>Selectiva
            	</span>
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
                <label for="pruebas-Club">Club:</label>
                <select id="pruebas-Club" name="Club" style="width:175px"></select>
                <a id="pruebas-newClubBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-flag'" style="float:right">Nuevo Club</a>
            </div>
            <div class="fitem">
                <label for="pruebas-Ubicacion">Lugar de realizaci&oacute;n</label>
                <input id="pruebas-Ubicacion" name="Ubicacion" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Triptico">URL del tr&iacute;ptico</label>
                <input id="pruebas-Triptico" name="Triptico" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Cartel">URL del cartel</label>
                <input id="pruebas-Cartel" name="Cartel" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Observaciones">Observaciones:</label>
                <textarea id="pruebas-Observaciones" name="Observaciones" style="height:50px;width:250px"></textarea>
            </div>
        </form>
    </div>

    
	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
	<div id="pruebas-dlg-buttons">
       	<a id="pruebas-okBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-ok'" onclick="savePrueba()">Guardar</a>
       	<a id="pruebas-cancelBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-cancel'" onclick="$('#pruebas-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    // - declaracion del formulario
    $('#pruebas-form').form({
    	onLoadSuccess: function(data) {
        	// alert(JSON.stringify(data));
    	    $('#pruebas-Operation').val('update');
    	    $('#pruebas-Selectiva').prop('checked',(data.Selectiva == 1));
    	    $('#pruebas-RSCE').prop('checked',(data.RSCE == 0));
    	    $('#pruebas-RFEC').prop('checked',(data.RSCE == 1));
    	    $('#pruebas-UCA').prop('checked',(data.RSCE == 2));
    	    hasInscripciones(
    	    	    data.ID,
    	    	    function(flag) {
        	    		$('#pruebas-RSCE').attr('disabled',flag);
        	    		$('#pruebas-RFEC').attr('disabled',flag);
        	    		$('#pruebas-UCA').attr('disabled',flag);
    	    		}
    	    	);
    	}
    });
    
    // - botones
	addTooltip($('#pruebas-okBtn').linkbutton(),"Aceptar datos. Registrar prueba en la BBDD");
	addTooltip($('#pruebas-cancelBtn').linkbutton(),"Anular operaci&oacute;n. Cerrar ventana");
	addTooltip($('#pruebas-newClubBtn').linkbutton(),"Registrar un nuevo club en la BBDD");
    $('#pruebas-newClubBtn').bind('click',function() {
        $('#pruebas-Club').combogrid({ 'setValue' : '' });
        newClub();
    });
    
    // campos del formulario
    $('#pruebas-dialog').dialog({
        closed:true,
        buttons:'#pruebas-dlg-buttons'
    });
    $('#pruebas-Nombre').validatebox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#pruebas-Triptico').validatebox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Cartel').validatebox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Club').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: '/agility/server/database/clubFunctions.php?Operation=enumerate',
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID',hidden:true}, 
			{field:'Nombre',title:'Nombre del club',width:80,align:'right'},
			{field:'Provincia',title:'Provincia',width:40,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false
    });

    </script>
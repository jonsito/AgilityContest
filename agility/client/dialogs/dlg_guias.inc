 <!-- 
dlg_hguias.inc

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

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE GUIAS -->
    <div id="guias-dialog" style="width:500px;height:350px;padding:10px 20px">
        <div class="ftitle">Informaci&oacute;n del guia</div>
        <form id="guias-form" method="get" novalidate>
            <div class="fitem">
                <label for="Nombre">Nombre:</label>
                <input id="guias-Nombre" name="Nombre" type="text" style="width:300px" />
                <input id="guias-Federation" name="Federation" type="hidden" />
                <input id="guias-ID" name="ID" type="hidden" /> <!-- primary key for update -->
                <input id="guias-Parent" name="Parent" type="hidden" /> <!-- used to store parent datagrid -->
                <input id="guias-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            </div>
            <div class="fitem">
                <label for="Email">Correo electr&oacute;nico:</label>
                <input id="guias-Email" name="Email" class="easyui-validatebox" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="Telefono">Tel&eacute;fono:</label>
                <input id="guias-Telefono" name="Telefono" type="text" />
            </div>
            <div class="fitem">
                <label for="Club">Club:</label>
                <select id="guias-Club" name="Club" style="width:200px"></select>
            	<a id="guias-newClubBtn" href="#" class="easyui-linkbutton" 
            		data-options="iconCls:'icon-flag'" style="float:right">Nuevo Club</a>
            </div>
            <div class="fitem">
                <label for="Observaciones">Comentarios:</label>
                <textarea id="guias-Observaciones" name="Observaciones" style="height:50px;width:300px"></textarea>
            </div>
        </form>
    </div>

    <!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
    <div id="guias-dlg-buttons">
        <a id="guias-okBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-ok'" onclick="saveGuia()">Guardar</a>
        <a id="guias-cancelBtn" href="#" class="easyui-linkbutton" 
        	data-options="iconCls:'icon-cancel'" onclick="javascript:$('#guias-dialog').dialog('close')">Cancelar</a>
    </div>
    
    <script type="text/javascript">
        // datos del formulario de nuevo/edit guia
        // - declaracion del formulario
        $('#guias-form').form({
        	onLoadSuccess: function(data) {
        	    $('#guias-Club').combogrid('clear');
        	    $('#guias-Club').combogrid('setValue',data.Club);
        	    $('#guias-Club').combogrid('setText',data.NombreClub);
        	}   
        });
        // - botones
        
		addTooltip($('#guias-okBtn').linkbutton(),"Aceptar datos del gu&iacute;a y registrarlos en la BBDD");
		addTooltip($('#guias-cancelBtn').linkbutton(),"Anular operaci&oacute;n. Cerrar ventana");
		addTooltip($('#guias-newClubBtn').linkbutton(),"Registrar nuevo club");
        $('#guias-newClubBtn').bind('click',function() {
            newClub( 
                $('#guias-Club').combogrid('getText'), // default name
                function() { // what to do on accept button pressed
                	$('#guias-Club').combogrid('reset');
                });    
        });
        $('#guias-dialog').dialog({
        	closed:true,
        	buttons:'#guias-dlg-buttons',
        	iconCls:'icon-users'
        });
        // campos del formulario
        $('#guias-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#guias-dialog').dialog('close');
        	}
        });
        $('#guias-Nombre').validatebox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#guias-Email').validatebox({
            required: false,
            validType: 'email'
        });
        $('#guias-Club').combogrid({
			panelWidth: 350,
			panelHeight: 200,
			idField: 'ID',
			textField: 'Nombre',
			url: '/agility/server/database/clubFunctions.php?Operation=enumerate',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
			    {field:'ID', hidden:true},
    			{field:'Nombre',title:'Nombre del club',width:80,align:'right'},
    			{field:'Provincia',title:'Provincia',width:40,align:'right'},
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });
</script>
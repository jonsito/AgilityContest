 <!-- 
dlg_clubes.inc

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
<!-- FORMULARIO DE SELECCION DE LOGO -->
 <div id="clubes-logo-dialog" style="width:400px;height:250px;padding:10px">
 	<div style="width=100%">
		<input type="file" name="logoPreview" value="" id="clubes-logo-filePreview" 
			class="required borrowerImageFile" data-errormsg="PhotoUploadErrorMsg" onchange="setLogoPreview(this);">
		<hr />
	</div>
	<div style="display:table;margin: 0 auto">
		<img id="clubes-logo-preview" alt="vista preliminar del logotipo" width="120px" height="120px" src=""/>
	</div>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
<div id="clubes-logo-buttons">
    <a id="clubes-logo-okBtn" href="#" class="easyui-linkbutton" 
      	data-options="iconCls: 'icon-ok'" onclick="acceptLogoPreview()">Actualizar</a>
    <a id="clubes-logo-cancelBtn" href="#" class="easyui-linkbutton" 
       	data-options="iconCls: 'icon-cancel'" onclick="$('#clubes-logo-dialog').dialog('close')">Cancelar</a>
</div>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE CLUBES -->
 <div id="clubes-dialog" style="width:550px;height:600px;padding:10px 20px">
    <div class="ftitle">
    Informaci&oacute;n del club
    </div>
    <form id="clubes-form" method="get" novalidate>
        <div class="fitem">
            <label for="clubes-Nombre">Nombre:</label>
            <input id="clubes-Nombre" name="Nombre"	type="text" style="width:350px"/>
            <input id="clubes-ID" name="ID" type="hidden" /> <!-- used to allow operator change club name -->
            <input id="clubes-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
        </div>
        <div class="fitem">
            <label for="clubes-Direccion1">Direcci&oacute;n 1</label>
            <input id="clubes-Direccion1" name="Direccion1" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Direccion2">Direcci&oacute;n 2</label>
            <input id="clubes-Direccion2" name="Direccion2" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Provincia">Provincia</label>
            <select id="clubes-Provincia" name="Provincia" style="width:200px"></select>
            <a href="#" onClick="$('#clubes-logo-dialog').dialog('open');">
    	        <img id="clubes-Logo"
    	           	alt="rsce"
    	          	name="Logo" 
    	           	src="/agility/server/database/clubFunctions.php?Operation=getlogo&Nombre=rsce"
    	           	width="120" style="float:right;margin-right:50px"/>
            </a>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto1">Contacto 1</label>
            <input id="clubes-Contacto1" name="Contacto1" type="text" style="width:200px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto2">Contacto 2</label>
            <input id="clubes-Contacto2" name="Contacto2" type="text" style="width:200px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto3">Contacto 3</label>
            <input id="clubes-Contacto3" name="Contacto3" type="text" style="width:200px"/>
        </div>
        <div class="fitem">
            <label for="clubes-GPS">Coordenadas GPS</label>
            <input id="clubes-GPS" name="GPS" type="text" style="width:200px"/>
        </div>
        <div class="fitem">
            <label for="clubes-RSCE">RSCE</label>
            <input id="clubes-RSCE" type="checkbox" value="1" name="RSCE"/>
            <label for="clubes-RFEC">RFEC</label>
            <input id="clubes-RFEC" type="checkbox" value="2" name="RFEC"/>
            <label for="clubes-UCA">UCA</label>
            <input id="clubes-UCA" type="checkbox" value="4" name="UCA"/>
            <input id="clubes-Federations" name="Federations" type="hidden"/>
        </div>
        <div class="fitem">
            <label for="clubes-Web">P&aacute;gina Web</label>
            <input id="clubes-Web" name="Web" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Email">Correo electr&oacute;nico:</label>
            <input id="clubes-Email" name="Email" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Facebook">Cuenta de Facebook:</label>
            <input id="clubes-Facebook" name="Facebook" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Google">Cuenta de Google+:</label>
            <input id="clubes-Google" name="Google" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Twitter">Cuenta de Twitter:</label>
            <input id="clubes-Twitter" name="Twitter" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Observaciones">Observaciones:</label>
            <textarea id="clubes-Observaciones" name="Observaciones" style="height:50px;width:350px"></textarea>
        </div>
        <div class="fitem">
            <label for="clubes-Baja">Baja del club:</label>
            <input id="clubes-Baja" name="Baja" class="easyui-checkbox" type="checkbox" value="1" />
        </div>
	</form>  
</div>  
        
<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
<div id="clubes-dlg-buttons">
    <a id="clubes-okBtn" href="#" class="easyui-linkbutton" 
      	data-options="iconCls: 'icon-ok'" onclick="saveClub()">Guardar</a>
    <a id="clubes-cancelBtn" href="#" class="easyui-linkbutton" 
       	data-options="iconCls:'icon-cancel'" onclick="$('#clubes-dialog').dialog('close')">Cancelar</a>
</div>
    
<script type="text/javascript">
        // - declaracion del formulario
        $('#clubes-form').form();
        // - botones
    	addTooltip($('#clubes-okBtn').linkbutton(),"Aceptar datos del club y actualizar la BBDD");
    	addTooltip($('#clubes-cancelBtn').linkbutton(),"Cancelar operaci&oacute;n. Cerrar ventana");
    	addTooltip($('#clubes-logo-okBtn').linkbutton(),"Aceptar datos. Actualizar el logo del club");
    	addTooltip($('#clubes-logo-cancelBtn').linkbutton(),"Cancelar operaci&oacute;n. Cerrar ventana");
    	addTooltip($('#clubes-logo-filePreview'),"La imagen seleccionada debe ser tipo '.png', <br />tama&ntilde;o 150x150 pixels y con fondo transparente");
    	addTooltip($('#clubes-Logo'),"Pulsa sobre la imagen para cambiar el logo del club");

    	// despliegue del selector de provincias
        $('#clubes-Provincia').combogrid({
			panelWidth: 300,
			panelHeight: 200,
			idField: 'Provincia',
			textField: 'Provincia',
			url: '/agility/server/database/enumerate_Provincias.php?Operation=select',
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Provincia',title:'Provincia',width:20,align:'right'},
    			{field:'Comunidad',title:'Comunidad Autonoma',width:40,align:'right'}
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });

        $('#clubes-logo-dialog').dialog( {
            title:'Nuevo Logotipo',
            closed:true,
            modal:true,
            buttons:'#clubes-logo-buttons',
            iconCls:'icon-palette',
            onOpen: function() {
            	$('#clubes-logo-filePreview').val('');
            	$('#clubes-logo-preview').attr('src', $('#clubes-Logo').attr('src'));
            }
        } );
        $('#clubes-dialog').dialog( {
            closed:true,
            modal:true,
            buttons:'#clubes-dlg-buttons',
            iconCls:'icon-flag'
        } );
        // Add "Esc" key to abor editing and close dialog
        $('#clubes-dialog').dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#clubes-dialog').dialog('close');
        	}
        });
        // validadores
        $('#clubes-Nombre').validatebox({required:true,validType:'length[1,255]'});
        $('#clubes-Email').validatebox({required:false,validType:'email'}); 
</script>

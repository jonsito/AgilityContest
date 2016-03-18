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

 <?php
 require_once(__DIR__ . "/../../server/tools.php");
 require_once(__DIR__ . "/../../server/auth/Config.php");
 $config =Config::getInstance();
 ?>

 <!-- FORMULARIO DE SELECCION DE LOGO -->
 <div id="clubes-logo-dialog" style="width:400px;height:250px;padding:10px">
 	<div style="width=100%">
		<input type="file" name="logoPreview" value="" id="clubes-logo-filePreview" accept="image/*"
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
      	data-options="iconCls: 'icon-ok'" onclick="acceptLogoPreview()"><?php _e('Update'); ?></a>
    <a id="clubes-logo-cancelBtn" href="#" class="easyui-linkbutton" 
       	data-options="iconCls: 'icon-cancel'" onclick="$('#clubes-logo-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>

<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE CLUBES -->
 <div id="clubes-dialog" style="width:550px;height:600px;padding:10px 20px">
    <div class="ftitle">
    <?php _e('Club information'); ?>
    </div>
    <form id="clubes-form" method="get" novalidate>
        <div class="fitem">
            <label for="clubes-Nombre"><?php _e('Name'); ?>:</label>
            <input id="clubes-Nombre" name="Nombre"	type="text" style="width:350px"/>
            <input id="clubes-ID" name="ID" type="hidden" /> <!-- used to allow operator change club name -->
            <input id="clubes-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
        </div>
        <div class="fitem">
            <label for="clubes-Direccion1"><?php _e('Address'); ?> 1</label>
            <input id="clubes-Direccion1" name="Direccion1" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Direccion2"><?php _e('Address'); ?> 2</label>
            <input id="clubes-Direccion2" name="Direccion2" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Provincia"><?php _e('Province'); ?></label>
            <select id="clubes-Provincia" name="Provincia" style="width:175px"></select>
            <a href="#" onClick="$('#clubes-logo-dialog').dialog('open');">
    	        <img id="clubes-Logo"
    	           	alt="rsce"
    	          	name="Logo" 
    	           	src=""
    	           	width="120" style="float:right;margin-right:20px"/>
            </a>
        </div>
        <div class="fitem">
            <label for="clubes-Pais"><?php _e('Country'); ?></label>
            <select id="clubes-Pais" name="Pais" class="easyui-validatebox" style="width:175px"></select>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto1"><?php _e('Contact'); ?> 1</label>
            <input id="clubes-Contacto1" name="Contacto1" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto2"><?php _e('Contact'); ?> 2</label>
            <input id="clubes-Contacto2" name="Contacto2" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Contacto3"><?php _e('Contact'); ?> 3</label>
            <input id="clubes-Contacto3" name="Contacto3" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="clubes-GPS"><?php _e('GPS coordinates'); ?></label>
            <input id="clubes-GPS" name="GPS" type="text" style="width:250px"/>
        </div>
        <div class="fitem">
            <label for="clubes-RSCE" style="text-align:right;"><?php _e('RSCE'); ?></label>
            <input id="clubes-RSCE" type="checkbox" value="1" name="RSCE"/>
            <label for="clubes-RFEC" style="text-align:right;"><?php _e('RFEC'); ?></label>
            <input id="clubes-RFEC" type="checkbox" value="2" name="RFEC"/>
            <label for="clubes-UCA" style="text-align:right;"><?php _e('UCA'); ?></label>
            <input id="clubes-UCA" type="checkbox" value="4" name="UCA"/>
            <input id="clubes-Federations" name="Federations" type="hidden"/>
        </div>
        <div class="fitem">
            <label for="clubes-Web"><?php _e('Web page'); ?></label>
            <input id="clubes-Web" name="Web" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Email"><?php _e('Electronic mail'); ?>:</label>
            <input id="clubes-Email" name="Email" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Facebook"><?php _e('Facebook account'); ?>:</label>
            <input id="clubes-Facebook" name="Facebook" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Google"><?php _e('Google+ account'); ?>:</label>
            <input id="clubes-Google" name="Google" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Twitter"><?php _e('Twitter account'); ?>:</label>
            <input id="clubes-Twitter" name="Twitter" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="clubes-Observaciones"><?php _e('Comments'); ?>:</label>
            <textarea id="clubes-Observaciones" name="Observaciones" style="height:50px;width:350px"></textarea>
        </div>
        <div class="fitem">
            <label for="clubes-Baja"><?php _e('Unsubscribed'); ?>:</label>
            <input id="clubes-Baja" name="Baja" class="easyui-checkbox" type="checkbox" value="1" />
        </div>
	</form>  
</div>  
        
<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
<div id="clubes-dlg-buttons">
    <a id="clubes-okBtn" href="#" class="easyui-linkbutton" 
      	data-options="iconCls: 'icon-ok'" onclick="saveClub()"><?php _e('Accept'); ?></a>
    <a id="clubes-cancelBtn" href="#" class="easyui-linkbutton" 
       	data-options="iconCls:'icon-cancel'" onclick="$('#clubes-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>
    
<script type="text/javascript">
        // - declaracion del formulario
        $('#clubes-form').form();
        // - botones
    	addTooltip($('#clubes-okBtn').linkbutton(),'<?php _e("Register data on new club. Update database"); ?>');
    	addTooltip($('#clubes-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    	addTooltip($('#clubes-logo-okBtn').linkbutton(),'<?php _e("Validate logo. Store and update database"); ?>');
    	addTooltip($('#clubes-logo-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    	addTooltip($('#clubes-logo-filePreview'),'<?php _e("Choosen image should be of type <em>.png</em>"); ?>'+", <br />"+
                                                       '<?php _e( "150x150 pixel size and transparent background"); ?>');
    	addTooltip($('#clubes-Logo'),'<?php _e("Press on image to change club logo"); ?>');

        // load default image on club dialog
        $('#clubes-Logo').attr('src',"/agility/server/database/clubFunctions.php?Operation=getlogo&Nombre=rsce&Federation="+workingData.federation);

        // despliegue del selector de paises
        $('#clubes-Pais').combogrid({
            panelWidth: 250,
            panelHeight: 200,
            idField: 'ID',
            textField: 'Country',
            url: '/agility/server/database/clubFunctions.php',
            queryParams: {
                Operation: 'countries'
            },
            method: 'get',
            mode: 'remote',
            required: true,
            columns: [[
                {field:'ID',title:'ID',width:10,align:'right'},
                {field:'Country',title:'<?php _e('Country'); ?>',width:40,align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            selectOnNavigation: false,
            onChange: function(newvalue,oldvalue) {
                $('#clubes-Provincia').combogrid('grid').datagrid('load',{Operation:'select',Country:newvalue});
            }
        });

    	// despliegue del selector de provincias
        $('#clubes-Provincia').combogrid({
			panelWidth: 300,
			panelHeight: 200,
			idField: 'Provincia',
			textField: 'Provincia',
			url: '/agility/server/database/enumerate_Provincias.php',
            queryParams:{
                Operation: 'select',
                Country: 'ES'
            },
			method: 'get',
			mode: 'remote',
			required: true,
			columns: [[
    			{field:'Provincia',title:'<?php _e('Province'); ?>',width:20,align:'right'},
    			{field:'Comunidad',title:'<?php _e('Region/State'); ?>',width:40,align:'right'}
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });

        $('#clubes-logo-dialog').dialog( {
            title:'<?php _e('New logo'); ?>',
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

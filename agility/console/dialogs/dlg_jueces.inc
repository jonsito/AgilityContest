 <!-- 
dlg_jueces.inc

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

    <!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE LA BBDD DE JUECES -->
    <div id="jueces-dialog" style="padding:10px 20px">
        <div class="ftitle"><?php _e('Judge information'); ?></div>
        <form id="jueces-form" method="get" novalidate>
            <div class="fitem">
                <label for="jueces-Nombre"><?php _e('Name'); ?>:</label>
                <input id="jueces-Nombre" name="Nombre" type="text"	style="width:250px" />
                <input id="jueces-ID" name="ID" type="hidden" /> 
                <input id="jueces-Operation" name="Operation" type="hidden" /> <!-- to store insert/update/delete operation -->
            </div>
            <div class="fitem">
                <label for="jueces-Direccion1"><?php _e('Address'); ?> 1:</label>
                <input id="jueces-Direccion1" name="Direccion1" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="jueces-Direccion2"><?php _e('Address'); ?> 2:</label>
                <input id="jueces-Direccion2" name="Direccion2" type="text" style="width:250px" />
            </div>
            <div class="fitem">
                <label for="jueces-Pais"><?php _e('Country'); ?>:</label>
                <select id="jueces-Pais" name="Pais" style="width:200px"></select>
            </div>
            <div class="fitem">
                <label for="jueces-Telefono"><?php _e('Telephone'); ?>:</label>
                <input id="jueces-Telefono" name="Telefono" type="text"  style="width:200px"/>
            </div>
            <div class="fitem">
                <label for="jueces-Email"><?php _e('Electronic mail'); ?>:</label>
                <input id="jueces-Email" name="Email" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="jueces-Internacional"><?php _e('International judge'); ?>:</label>
                <input id="jueces-Internacional" name="Internacional" class="easyui-checkbox" type="checkbox" value="1" />
                <span style="display:inline-block;width:50px;text-align:right">&nbsp;</span>
                <label for="jueces-Practicas"><?php _e('Learning judge'); ?>: </label>
                <input id="jueces-Practicas" name="Practicas" class="easyui-checkbox" type="checkbox" value="1" />
            </div>
        	<div class="fitem">
        	    <label for="jueces-Federations"><?php _e('Federations'); ?>:</label><br/>
        	    RSCE <input id="jueces-RSCE" type="checkbox" value="1" name="RSCE"/>
        	    RFEC <input id="jueces-RFEC" type="checkbox" value="2" name="RFEC"/>
                CPC<input id="jueces-CPC" type="checkbox" value="16" name="CPC"/>
                Nat3<input id="jueces-Nat4" type="checkbox" value="8" name="Nat3"/>
                Nat4<input id="jueces-Nat4" type="checkbox" value="4" name="Nat4"/>
        	    <input id="jueces-Federations" name="Federations" type="hidden"/>
        	</div>
            <div class="fitem">
                <label for="jueces-Observaciones"><?php _e('Comments'); ?>:</label>
                <textarea id="jueces-Observaciones" name="Observaciones" style="height:50px;width:350px"></textarea>
            </div>
        </form>
    </div>
    
   	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
   	<div id="jueces-dlg-buttons">
   	    <a id="jueces-okBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-ok'" onclick="saveJuez()"><?php _e('Save'); ?></a>
   	    <a id="jueces-cancelBtn" href="#" class="easyui-linkbutton" 
   	    	data-options="iconCls:'icon-cancel'" onclick="$('#jueces-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
   	</div>
   	
	<script type="text/javascript">
        // datos del formulario de nuevo/edit jueces

        // - declaracion del formulario
        $('#jueces-form').form({
            onLoadSuccess: function(data) {
                // take care on internacional & practice checkbox
                $('#jueces-Internacional').prop('checked',(data.Internacional == 1));
                $('#jueces-Practicas').prop('checked',(data.Practicas == 1));
            }
        });


        // despliegue del selector de paises
        $('#jueces-Pais').combogrid({
            panelWidth: 400,
            panelHeight: 'auto',
            idField: 'ID',
            textField: 'Country',
            url: '/agility/server/database/juezFunctions.php?Operation=countries',
            method: 'get',
            mode: 'remote',
            required: true,
            columns: [[
                {field:'ID',title:'ID',width:10,align:'right'},
                {field:'Country',title:'<?php _e('Country'); ?>',width:40,align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            selectOnNavigation: false
        });

        // - botones
		addTooltip($('#jueces-okBtn').linkbutton(),'<?php _e("Register judge information into database"); ?>');
		addTooltip($('#jueces-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
        // campos del formulario
        $('#jueces-dialog').dialog({
            width:450,
            height:'auto',
            closed: true,
            buttons: '#jueces-dlg-buttons',
            iconCls: 'icon-whistle'
        }).dialog('dialog').attr('tabIndex','-1').bind('keydown',function(e){
        	if (e.keyCode == 27){ $('#jueces-dialog').dialog('close');
        	}
        });
        $('#jueces-Nombre').textbox({
            required: true,
            validType: 'length[1,255]'
        });
        $('#jueces-Email').textbox({
            required: false,
            validType: 'email'
        });
        $('#jueces-Direccion1').textbox();
        $('#jueces-Direccion2').textbox();
        $('#jueces-Telefono').textbox();
        </script>
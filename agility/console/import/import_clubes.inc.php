 <!-- 
import_clubes.inc

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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


<!-- FORMULARIO DE ALTA/BAJA/MODIFICACION DE importclubes -->
 <div id="importclubes-dialog" style="width:550px;height:600px;padding:10px 20px" >

    <div class="ftitle">
        <?php _e('Club data Import'); ?>
    </div>
     <form id="importclubes-header">
         <p id="importclubes_header-Text"> </p> <!-- to be filled -->
         <div class="fitem">
             <label for="importclubes-Search"><?php _e('Search'); ?>: </label>
             <select id="importclubes-Search" name="Search" style="width:250px"></select>&nbsp;
             <a id="importclubes-clearBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls: 'icon-brush'"><?php _e('Clear'); ?></a>
         </div>
     </form>
     <hr/>
    <form id="importclubes-form" method="get" novalidate>
        <div class="fitem">
            <label for="importclubes-Nombre"><?php _e('Name'); ?>:</label>
            <input id="importclubes-Nombre" name="Nombre"	type="text" style="width:350px"/>
            <input id="importclubes-ID" name="ID" type="hidden" /> <!-- used to allow operator change club name -->
            <input id="importclubes-Operation" name="Operation" type="hidden" /> <!-- inser/update/delete -->
            <input id="importclubes-Logo" name="Logo" type="hidden" value="none.png" /> <!-- logo is not shown here -->
        </div>
        <div class="fitem">
            <label for="importclubes-Direccion1"><?php _e('Address'); ?> 1</label>
            <input id="importclubes-Direccion1" name="Direccion1" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Direccion2"><?php _e('Address'); ?> 2</label>
            <input id="importclubes-Direccion2" name="Direccion2" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Provincia"><?php _e('Province'); ?></label>
            <select id="importclubes-Provincia" name="Provincia" style="width:175px"></select>
        </div>
        <div class="fitem">
            <label for="importclubes-Pais"><?php _e('Country'); ?></label>
            <select id="importclubes-Pais" name="Pais" class="easyui-validatebox" style="width:175px"></select>
        </div>
        <div class="fitem">
            <label for="importclubes-Contacto1"><?php _e('Contact'); ?> 1</label>
            <input id="importclubes-Contacto1" name="Contacto1" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Contacto2"><?php _e('Contact'); ?> 2</label>
            <input id="importclubes-Contacto2" name="Contacto2" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Contacto3"><?php _e('Contact'); ?> 3</label>
            <input id="importclubes-Contacto3" name="Contacto3" type="text" style="width:175px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-GPS"><?php _e('GPS coordinates'); ?></label>
            <input id="importclubes-GPS" name="GPS" type="text" style="width:250px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-RSCE" style="text-align:right;"><?php _e('RSCE'); ?></label>
            <input id="importclubes-RSCE" type="checkbox" value="1" name="RSCE"/>
            <label for="importclubes-RFEC" style="text-align:right;"><?php _e('RFEC'); ?></label>
            <input id="importclubes-RFEC" type="checkbox" value="2" name="RFEC"/>
            <label for="importclubes-UCA" style="text-align:right;"><?php _e('UCA'); ?></label>
            <input id="importclubes-UCA" type="checkbox" value="4" name="UCA"/>
            <input id="importclubes-Federations" name="Federations" type="hidden"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Web"><?php _e('Web page'); ?></label>
            <input id="importclubes-Web" name="Web" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Email"><?php _e('Electronic mail'); ?>:</label>
            <input id="importclubes-Email" name="Email" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Facebook"><?php _e('Facebook account'); ?>:</label>
            <input id="importclubes-Facebook" name="Facebook" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Google"><?php _e('Google+ account'); ?>:</label>
            <input id="importclubes-Google" name="Google" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Twitter"><?php _e('Twitter account'); ?>:</label>
            <input id="importclubes-Twitter" name="Twitter" type="text" style="width:350px"/>
        </div>
        <div class="fitem">
            <label for="importclubes-Observaciones"><?php _e('Comments'); ?>:</label>
            <textarea id="importclubes-Observaciones" name="Observaciones" style="height:50px;width:350px"></textarea>
        </div>
        <div class="fitem">
            <label for="importclubes-Baja"><?php _e('Unsubscribed'); ?>:</label>
            <input id="importclubes-Baja" name="Baja" class="easyui-checkbox" type="checkbox" value="1" />
        </div>
	</form>  
</div>  
        
<!-- BOTONES DE IMPORTAR / SELECCIONAR / IGNORAR DEL CUADRO DE DIALOGO -->
<div id="importclubes-dlg-buttons" style="display:inline-block">
    <span style="float:left">
        <a id="importclubes-newBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls: 'icon-new'" onclick="importClubes('new');"><?php _e('New'); ?></a>
    </span>
    <span style="float:right">
        <a id="importclubes-selectBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls: 'icon-ok'" onclick="importClubes('select');"><?php _e('Select'); ?></a>
        <a id="importclubes-ignoreBtn" href="#" class="easyui-linkbutton"
            data-options="iconCls:'icon-cancel'" onclick="importClubes('ignore');"><?php _e('Ignore'); ?></a>
    </span>
</div>
    
<script type="text/javascript">

        $('#importclubes-dialog').dialog( {
            closed:true,
            modal:true,
            buttons:'#importclubes-dlg-buttons',
            iconCls:'icon-flag'
        } );

        // - declaracion del formulario
        $('#importclubes-form').form();
        // - botones
    	addTooltip($('#importclubes-newBtn').linkbutton(),'<?php _e("Import as new club with provided data"); ?>');
    	addTooltip($('#importclubes-selectBtn').linkbutton(),'<?php _e("Use selected existing club and updata club info"); ?>');
    	addTooltip($('#importclubes-ignoreBtn').linkbutton(),'<?php _e("Ignore entry. do not import into database"); ?>');
        addTooltip($('#importclubes-clearBtn').linkbutton(),'<?php _e("Clear club search and reset data to import"); ?>')
        $('#importclubes-clearBtn').linkbutton().bind('click',function() {
            $('#importclubes-header').form('clear'); // empty form
            $('#importclubes-form').form('reset'); // restore to initial values
        });

        // combogrid de seleccion de clubes/paises
        $('#importclubes-Search').combogrid({
            panelWidth: 350,
            panelHeight: 200,
            delay: 500,
            idField: 'ID',
            textField: 'Nombre',
            url: '/agility/server/database/clubFunctions.php',
            queryParams: { Operation:'enumerate', Federation: workingData.federation },
            method: 'get',
            mode: 'remote',
            columns: [[
                {field:'ID',hidden:'true'},
                {field:'Nombre',    title:'<?php _e("Club name"); ?>',  width:'60%',align:'left'},
                {field:'Provincia', title:'<?php _e("Province"); ?>',   width:'25%',align:'right'},
                {field:'Pais',      title:'<?php _e("Country"); ?>',    width:'15%',align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            singleSelect: true,
            selectOnNavigation: false ,
            onSelect: function(index,row) {
                var idclub=row.ID;
                if (!idclub) return;
                $('#importclubes-form').form('load','/agility/server/database/clubFunctions.php?Operation=selectbyid&ID='+idclub); // load form with json retrieved data
            }
        });

        // despliegue del selector de paises
        $('#importclubes-Pais').combogrid({
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
                {field:'Country',title:'<?php _e("Country"); ?>',width:40,align:'right'}
            ]],
            multiple: false,
            fitColumns: true,
            selectOnNavigation: false,
            onChange: function(newvalue,oldvalue) {
                $('#importclubes-Provincia').combogrid('grid').datagrid('load',{Operation:'select',Country:newvalue});
            }
        });

    	// despliegue del selector de provincias
        $('#importclubes-Provincia').combogrid({
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
    			{field:'Provincia',title:'<?php _e("Province"); ?>',width:20,align:'right'},
    			{field:'Comunidad',title:'<?php _e("Region/State"); ?>',width:40,align:'right'}
			]],
			multiple: false,
			fitColumns: true,
			selectOnNavigation: false
        });

        // validadores
        $('#importclubes-Nombre').validatebox({required:true,validType:'length[1,255]'});
        $('#importclubes-Email').validatebox({required:false,validType:'email'});
</script>

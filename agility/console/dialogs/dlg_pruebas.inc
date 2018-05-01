<!--
dlg_pruebas.inc

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
    <!-- FORMULARIO DE ENVIO DE CORREO CON INFORMACION DE UNA PRUEBA LOS CLUBES -->
    <div id="pruebas_email-dialog" style="width:640px;height:auto;padding:5px;">
        <p><?php _e("Before sending email with contest info to clubs");?>:</p>
        <ul>
            <li><?php _e("Make sure that triptych and poster URL are declared");?></li>
            <li><?php _e("Revise/Edit email text to be sent with attachements");?></li>
            <li><?php _e("Decide if you want to send empty inscription template");?></li>
            <li><?php _e("Select club(s) to send email to, or choose all");?></li>
            <li><?php _e("Make sure that every selected club has a valid email address declared");?></li>
            <li><?php _e("Press double-click on club to set/change email");?></li>
        </ul>
        <form id="pruebas_email-form" method="get" novalidate="novalidate">
            <div class="fitem">
                <input name="SendToMe" type="hidden" value="0"/>
                <label for="pruebas_email-SendToMe" style="width:300px;"><?php _e('Send email copy back to sender'); ?></label>
                <input id="pruebas_email-SendToMe" type="checkbox" name="SendToMe" value="1"/><br/>

                <input name="EmptyTemplate" type="hidden" value="0"/>
                <label for="pruebas_email-EmptyTemplate" style="width:300px;"><?php _e('Send empty inscription template'); ?></label>
                <input id="pruebas_email-EmptyTemplate" type="checkbox" name="EmptyTemplate" value="1"/><br/>

                <input name="ReSend" type="hidden" value="0"/>
                <label for="pruebas_email-ReSend" style="width:300px;"><?php _e('Ignore sent mark and send anyway'); ?></label>
                <input id="pruebas_email-ReSend" type="checkbox" name="ReSend" value="1"/><br/>

                <input name="SendToAll" type="hidden" value="0"/>
                <span style="display:inline-block;width:300px;"><?php _e('Select'); ?></span>
                <input id="pruebas_email-All" type="checkbox" name="All" value="1"/>
                <label for="pruebas_email-All" style="width:50px;"><?php _e('All'); ?></label>
                <input id="pruebas_email-None" type="checkbox" name="None" value="1"/>
                <label for="pruebas_email-None"  style="width:50px;"><?php _e('None'); ?></label><br/>
            </div>
            <div class="fitem" style="height:250px">
                <table id="pruebas_email-Clubs" name="Club"></table>
            </div>
        </form>
    </div>

    <!-- barra de progreso de actualizacion de envio de correo -->
    <div id="pruebas_email-progresswindow" class="easyui-window"
         data-options="title:'<?php _e('Sending mail'); ?>...',width:300,modal:true,collapsable:false,minimizable:false,maximizable:false,closable:false,closed:true">
        <p id="pruebas_email-progresslabel" style="text-align:center"><?php _e('Sending mail to'); ?>:</p>
        <div id="pruebas_email-progressbar" class="easyui-progressbar" style="width:300px;text-align:center;" data-options="value:0"></div>
    </div>

    <!-- area de edicion del mensaje a enviar por correo -->
    <div id="pruebas_email-editor" title="<?php _e('Text editor');?>">
        <textarea id="pruebas_email-Contents" name="Contents" style="width:700px;height:400px;padding:5px;">
            <h3> !Hola!</h3>
            <p>
            Os invitamos invitamos a nuestra próxima prueba, que se celebrará los días XXXXXX  en
            XXXXXXXX
            </p>
            <p>
            Os adjuntamos el cartel y el tríptico de la prueba, así como una plantilla Excel para que efectuéis las inscripciones<br/>
            La plantilla contiene la lista de perros y guías que figuran en nuestra base de datos como pertenecientes a vuestro club.
            Por favor, os pedimos que cumplimentéis la plantilla:
            </p>
            <ul>
                <li>Eliminar los equipos perro/guía que ya no pertenezcan a vuestro club</li>
                <li>Añadir los equipos perro/guía nuevos</li>
                <li>Actualizar los datos que estén incompletos o incorrectos, especialmente licencia, categoría, grado; y
                    en el caso de las licencias nuevas (números de cinco cifras), si el perro tiene LOE/RRC o no.</li>
                <li>Cumplimentar las inscripciones:
                    <ol>
                        <li>Marcar con una X las jornadas a las que el equipo asistirá</li>
                        <li>Indicar con una X las perras que estén en celo</li>
                        <li>Indicar la cantidad a abonar por cada equipo</li>
                        <li>En el apartado observaciones, cualquier otra información de interés</li>
                    </ol>
                </li>
            </ul>
            <p>
                Una vez rellena la plantilla, por favor devolvedla por correo electrónico a ésta dirección<br/>
                Si tenéis alguna duda, podéis contactar con nosotros en los teléfonos XXXXXXXXXX
            </p>
            <p>
                Agradeciendo de antemano vuestra colaboración, os esperamos en la prueba con los brazos abiertos<br/>
                Un saludo
            </p>
        </textarea>
    </div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO de EMAIL DE INVITACION A LAS PRUEBAS-->
    <div id="pruebas_email-dlg-buttons" style="width:100%;display:inline-block">
        <span style="float:left">
            <a id="pruebas_email-textBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls:'icon-notes'" onclick="$('#pruebas_email-editor').dialog('open');"><?php _e('Message'); ?></a>
            <a id="pruebas_email-clearBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-brush'" onclick="prueba_clearSentMark();"><?php _e('Clear'); ?></a>
        </span>
        <span style="float:right">
            <a id="pruebas_email-okBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls:'icon-mail'" onclick="perform_emailPrueba();"><?php _e('Send'); ?></a>
            <a id="pruebas_email-cancelBtn" href="#" class="easyui-linkbutton"
                data-options="iconCls:'icon-cancel'" onclick="$('#pruebas_email-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
        </span>
    </div>

   <!-- FORMULARIO DE DECLARACION Y MODIFICACION DE PRUEBAS -->
    <div id="pruebas-dialog" style="padding:10px 20px">
        <div class="ftitle"><?php _e('Contest information'); ?></div>
        <form id="pruebas-form" method="get" novalidate>
            <div class="fitem">
                <label for="pruebas-Nombre" style="width:325px"><?php _e('Name of this contest'); ?>:</label>
                <input id="pruebas-Nombre" name="Nombre" type="text" class="easyui-validatebox" style="width:350px" />
                <input id="pruebas-ID" name="ID" type="hidden" /> <!-- primary key -->
                <input id="pruebas-Operation" name="Operation" type="hidden" /> <!-- used to store operation type -->
                <input id="pruebas-Cerrada" name="Cerrada" type="hidden" /> <!-- preserve "closed" status -->
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
            	<label for="pruebas-Federation"><?php _e('Federation'); ?>:</label>
                <select id="pruebas-Federation" name="RSCE" style="width:200px"></select>
            	<span style="float:right">
            		<input id="pruebas-Selectiva" type="checkbox" name="Selectiva" value="1"/><?php _e('Selective'); ?>
            	</span>
            </div>
            <div>&nbsp;<br /></div>
            <div class="fitem">
                <label for="pruebas-Club"><?php _e('Club'); ?>:</label>
                <select id="pruebas-Club" name="Club" style="width:175px"></select>
                <a id="pruebas-newClubBtn" href="#" class="easyui-linkbutton" 
                	data-options="iconCls:'icon-flag'" style="float:right"><?php _e('New Club'); ?></a>
            </div>
            <div class="fitem">
                <label for="pruebas-OpeningReg" style="width:120px;text-align:left"><?php _e('Inscriptions Period'); ?>:</label>
                <select id="pruebas-OpeningReg" name="OpeningReg"  style="text-align:right"></select>
                <label for="pruebas-ClosingReg" style="width:20px;text-align:center"><?php _e('to'); ?>:</label>
                <select id="pruebas-ClosingReg" name="ClosingReg"  style="text-align:right"></select>
            </div>
            <div class="fitem">
                <label for="pruebas-Ubicacion"><?php _e('Event location'); ?></label>
                <input id="pruebas-Ubicacion" name="Ubicacion" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Triptico"><?php _e('Triptych URL'); ?></label>
                <input id="pruebas-Triptico" name="Triptico" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Cartel"><?php _e('Brochure URL'); ?></label>
                <input id="pruebas-Cartel" name="Cartel" type="text" style="width:250px"/>
            </div>
            <div class="fitem">
                <label for="pruebas-Observaciones"><?php _e('Comments'); ?>:</label>
                <textarea id="pruebas-Observaciones" name="Observaciones" style="height:50px;width:250px"></textarea>
            </div>
        </form>
    </div>

    
	<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO de CREACION DE PRUEBAS-->
	<div id="pruebas-dlg-buttons">
       	<a id="pruebas-okBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-ok'" onclick="savePrueba()"><?php _e('Save'); ?></a>
       	<a id="pruebas-cancelBtn" href="#" class="easyui-linkbutton" 
       		data-options="iconCls:'icon-cancel'" onclick="$('#pruebas-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
    </div>
    
    <script type="text/javascript">
    // datos del formulario de nuevo/edit prueba
    // - declaracion del formulario
    $('#pruebas-form').form({
    	onLoadSuccess: function(data) {

            // stupid combogrid that doesn't display right data after form load
            var pc=$('#pruebas-Club');
            pc.combogrid('clear');
            pc.combogrid('setValue',data.Club);
            pc.combogrid('setText',data.NombreClub);

        	// alert(JSON.stringify(data));
    	    $('#pruebas-Operation').val('update');
    	    $('#pruebas-Selectiva').prop('checked',(data.Selectiva == 1));
    	    hasInscripciones(
    	    	    data.ID,
    	    	    function(flag) {
                        $('#pruebas-Federation').combogrid('readonly',flag);
    	    		}
    	    	);
    	}
    });
    
    // - botones
    addTooltip($('#pruebas_email-textBtn').linkbutton(),'<?php _e("Edit mail template text to be sent"); ?>');
    addTooltip($('#pruebas_email-clearBtn').linkbutton(),'<?php _e("Remove mail sent mark from every clubs"); ?>');
    addTooltip($('#pruebas_email-okBtn').linkbutton(),'<?php _e("Accept data. Start mailing procedure"); ?>');
    addTooltip($('#pruebas_email-cancelBtn').linkbutton(),"<?php _e('Cancel operation. Close window'); ?>");
	addTooltip($('#pruebas-okBtn').linkbutton(),'<?php _e("Accept data. Register contest into database"); ?>');
	addTooltip($('#pruebas-cancelBtn').linkbutton(),"<?php _e('Cancel operation. Close window'); ?>");
	addTooltip($('#pruebas-newClubBtn').linkbutton(),'<?php _e("Register new club into database"); ?>');


    $('#pruebas-newClubBtn').bind('click',function() {
        newClub(
            null,
            $('#pruebas-Club').combogrid('getText'), // default name
            function() { // what to do on accept button pressed
                $('#pruebas-Club').combogrid('reset');
            }
        );
    });


    // campos del formulario de generacion de correo
    $('#pruebas_email-editor').dialog({
        area: null, // to handle editor instance
        modal:true,
        closable:true,
        closed:true,
        onBeforeOpen: function (){
            this.area=new nicEditor({
                fullPanel : true,
                iconsPath:'../lib/nicEdit/nicEditorIcons.gif'
            }).panelInstance('pruebas_email-Contents');
            return true;
        },
        onClose: function() {
            this.area.removeInstance('pruebas_email-Contents');
        }
    });


    $('#pruebas_email-dialog').dialog({
        closed:true,
        buttons:'#pruebas_email-dlg-buttons',
        onBeforeOpen: function() {
            if (workingData.prueba==0) return false; // it's an error: no contest declared. so don't try to load clubs
            $('#pruebas_email-Clubs').datagrid('load',{Operation:'enumerate',Prueba:workingData.prueba,Federation:workingData.federation});
            return true;
        }
    });

    $('#pruebas_email-Clubs').datagrid({
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        expansible: false,
        collapsed: false,
        title: '<?php _e('Club selection'); ?>',
        loadMsg: '<?php _e('Updating club list'); ?> ...',
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        multiple: true,
        selectOnNavigation: false,
        rowStyler:myRowStyler,
        fitcolumns: true,
        idField: 'ID',
        textField: 'Nombre',
        url: '../ajax/mailFunctions.php',
        queryParams: {
            'Operation': 'enumerate',
            'Prueba': workingData.prueba,
            'Federation': workingData.federation
        },
        method: 'get',
        mode: 'remote',
        multiSort: true,
        remoteSort: true,
        columns: [[
            {field:'ID',hidden:true},
            {field:'Nombre',    sortable:true, title:'<?php _e('Club name'); ?>',width:40,align:'right'},
            {field:'Email',     sortable:true, title:'<?php _e('Electronic mail'); ?>',width:60,align:'right'},
            {field:'Provincia', sortable:true, title:'<?php _e('Province'); ?>',width:30,align:'right'},
            {field:'Sent',  title:'<?php _e('Sent'); ?>',width:5,align:'center',formatter:formatOk}
        ]],
        onBeforeLoad: function(params) {
            return (workingData.prueba>0);
        },
        onUnselect: function(index,row) {
            $('#pruebas_email-All').prop('checked',false);
        },
        onSelect: function(index,row) {
            $('#pruebas_email-None').prop('checked',false);
        },
        onDblClickRow: function (index,row) {
            pruebas_emailEditClub(index,row);
        }
    });

    $('#pruebas_email-All').change(function(){
        if ($(this).prop('checked')) $('#pruebas_email-Clubs').datagrid('selectAll');
        $('#pruebas_email-None').prop('checked',false);
    });
    $('#pruebas_email-None').change(function(){
        if ($(this).prop('checked')) $('#pruebas_email-Clubs').datagrid('unselectAll');
        $('#pruebas_email-All').prop('checked',false);
    });

    // campos del formulario de nueva/edicion prueba
    $('#pruebas-dialog').dialog({
        width:500,
        height:'auto',
        closed:true,
        buttons:'#pruebas-dlg-buttons'
    });
    $('#pruebas-Nombre').textbox({
        required: true,
        validType: 'length[1,255]'
    });
    $('#pruebas-Triptico').textbox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Cartel').textbox({
        required: false,
        validType: 'url'
    });
    $('#pruebas-Ubicacion').textbox();

    $('#pruebas-Federation').combogrid({
        panelWidth: 300,
        panelHeight: 150,
        idField: 'ID',
        textField: 'LongName',
        url: '../ajax/modules/moduleFunctions.php',
        queryParams: { 'Operation' : 'enumerate' },
        method: 'get',
        mode: 'remote',
        required: true,
        multiple: false,
        fitColumns: true,
        singleSelect: true,
        editable: false,  // to disable tablet keyboard popup
        selectOnNavigation: true, // let use cursor keys to interactive select
        columns: [[
            {field:'ID',  title:'<?php _e('ID'); ?>',width:'20',align:'left'},
            {field:'Name',hidden:true},
            {field:'LongName',        title:'<?php _e('Name'); ?>',width:'250',align:'right'},
            {field:'Logo',hidden:true},
            {field:'ParentLogo',hidden:true}
        ]],
        onChange: function(newvalue,oldvalue) {
            $('#pruebas-Club').combogrid('clear');
            $('#pruebas-Club').combogrid('grid').datagrid('load', {'Operation':'enumerate','Federation': newvalue } );
        }
    });

    $('#pruebas-Club').combogrid({
		panelWidth: 350,
		panelHeight: 200,
		idField: 'ID',
		textField: 'Nombre',
		url: '../server/database/clubFunctions.php',
        queryParams: {
            'Operation': 'enumerate',
            'Federation': $('#pruebas-Federation').combogrid('getValue')
        },
		method: 'get',
		mode: 'remote',
		required: true,
		columns: [[
		    {field:'ID',hidden:true}, 
			{field:'Nombre',title:'<?php _e('Club name'); ?>',width:80,align:'right'},
			{field:'Provincia',title:'<?php _e('Province'); ?>',width:40,align:'right'}
		]],
		multiple: false,
		fitColumns: true,
		selectOnNavigation: false,
        onBeforeLoad: function(params) { params.Federation=$('#pruebas-Federation').combogrid('getValue'); return true; }
    });

    // fechas
    $('#pruebas-OpeningReg').datebox({
        width:100,
        required: true,
        formatter: function(date) {
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:  function (s) {
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    });
    $('#pruebas-OpeningReg').datebox('calendar').calendar({ firstDay:1 });
    $('#pruebas-OpeningReg').datebox('options').keyHandler.query = function(q) {
        if (q.indexOf('-') >= 0) { q = replaceAll('-', '/',q);	}
        $(this).datebox('setValue', q);
    };

    $('#pruebas-ClosingReg').datebox({
        width:100,
        required: true,
        formatter: function(date) {
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        },
        parser:  function (s) {
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
    });
    $('#pruebas-ClosingReg').datebox('calendar').calendar({ firstDay:1 });
    $('#pruebas-ClosingReg').datebox('options').keyHandler.query = function(q) {
        if (q.indexOf('-') >= 0) { q = replaceAll('-', '/',q);	}
        $(this).datebox('setValue', q);
    };
    </script>
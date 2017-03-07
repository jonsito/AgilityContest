<!--
scores_mail.inc.php

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

<!-- FORMULARIO DE ENVIO DE CORREO CON LOS RESULTADOS DE UNA JORNADA A JUECES/FEDERACION-->
<div id="scores_email-dialog" style="width:640px;height:auto;padding:5px;">
    <p><?php _e("Before sending email with contest results to judge(s), please:");?></p>
    <ul>
        <li><?php _e("Revise email addresses and select judge(s) to send email to");?></li>
        <li><?php _e("Also decide whether to send mail copy to Federation");?></li>
        <li><?php _e("Choose also if you want to send partial (round) scores");?></li>
        <li><?php _e("Revise and edit message body");?></li>
        <li><?php _e("Press double-click on judge to set/change email");?></li>
    </ul>
    <form id="scores_email-form" method="get" novalidate="novalidate">
        <div class="fitem">
            <input name="PartialScores" type="hidden" value="0"/>
            <label for="scores_email-PartialScores" style="width:300px;"><?php _e('Also send scores for each separate round'); ?></label>
            <input id="scores_email-PartialScores" type="checkbox" name="PartialScores" value="1"/><br/>

            <input name="SendToFederation" type="hidden" value="0"/>
            <label for="scores_email-SendToFederation" style="width:300px;"><?php _e('Send a copy to federation'); ?></label>
            <input id="scores_email-SendToFederation" type="checkbox" name="SendToFederation" value="1" /><br/>
            <label for="scores_email-FedAddress" style="width:300px;"><?php _e('Federation Email');?>:</label>
            <input id="scores_email-FedAddress" type="text" name="FedAddress" value="" style="width:250px;"/>
            <br/>
        </div>
        <div class="fitem" style="height:125px">
            <table id="scores_email-Jueces" name="Jueces"></table>
        </div>
    </form>
</div>

<!-- area de edicion del mensaje a enviar por correo -->
<div id="scores_email-editor" title="<?php _e('Text editor');?>">
        <textarea id="scores_email-Contents" name="Contents" style="width:700px;height:400px;padding:5px;">
            <h3> !Hola!</h3>
            <dl>
                Se adjuntan los resultados y clasificaciones en formato PDF correspondientes a nuestra prueba:
                <dt>Prueba:</dt><dd> XXXXXXXXXXXXXXX </dd>
                <dt>Jornada:</dt><dd> XXXXXXXXXXXXXXX </dd>
            </dl>
            <p>
                Se adjuntan tambien los ficheros en formato Excel para facilitar el procesado informatico de los datos
            </p>
            <p>
                Se recuerda que los resultados deben ser validados por el juez para poder ser contabilizados en la clasificacion general
            </p>
        </textarea>
</div>

<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO de EMAIL DE INVITACION A LAS PRUEBAS-->
<div id="scores_email-dlg-buttons" style="width:100%;display:inline-block">
        <span style="float:left">
            <a id="scores_email-textBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-notes'" onclick="$('#scores_email-editor').dialog('open');"><?php _e('Message'); ?></a>
        </span>
    <span style="float:right">
            <a id="scores_email-okBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-mail'" onclick="perform_emailScores();"><?php _e('Send'); ?></a>
            <a id="scores_email-cancelBtn" href="#" class="easyui-linkbutton"
               data-options="iconCls:'icon-cancel'" onclick="$('#scores_email-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
        </span>
</div>

<script type="text/javascript">
    // - botones
    addTooltip($('#scores_email-textBtn').linkbutton(),'<?php _e("Edit mail template text to be sent"); ?>');
    addTooltip($('#scores_email-okBtn').linkbutton(),'<?php _e("Accept data. Start mailing procedure"); ?>');
    addTooltip($('#scores_email-cancelBtn').linkbutton(),"<?php _e('Cancel operation. Close window'); ?>");

    // campos del formulario de generacion de correo
    $('#scores_email-editor').dialog({
        area: null, // to handle editor instance
        modal:true,
        closable:true,
        closed:true,
        onBeforeOpen: function (){
            this.area=new nicEditor({
                fullPanel : true,
                iconsPath:'/agility/lib/nicEdit/nicEditorIcons.gif'
            }).panelInstance('scores_email-Contents');
            return true;
        },
        onClose: function() {
            this.area.removeInstance('scores_email-Contents');
        }
    });

    $('#scores_email-FedAddress').textbox({
        validType: 'email',
        disabled: true
    });

    $('#scores_email-SendToFederation').change(function(){
        $('#scores_email-FedAddress').textbox(($(this).prop('checked'))?'enable':'disable');
    });

    $('#scores_email-dialog').dialog({
        closed:true,
        buttons:'#scores_email-dlg-buttons',
        onBeforeOpen: function() {
            if (workingData.prueba==0) return false; // it's an error: no contest declared
            $('#scores_email-SendToFederation').prop('checked',false);
            // retrieve default email address to contact federation
            $.ajax({
                url:"/agility/modules/moduleFunctions.php",
                dataType:'json',
                data: {
                    Operation: 'moduleinfo',
                    Federation: workingData.federation,
                    Competition: workingData.datosJornada.Tipo_Competicion
                },
                success: function(data) {
                    $('#scores_email-FedAddress').textbox('setValue',data.Email);
                }
            });
            // retrieve judge list
            $('#scores_email-Jueces').datagrid(
                'load',
                {
                    Operation:'enumerateJueces',
                    Prueba:workingData.prueba,
                    Jornada:workingData.jornada,
                    Federation:workingData.federation
                }
            );
            return true;
        }
    });

    $('#scores_email-Jueces').datagrid({
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        expansible: false,
        collapsed: false,
        title: '<?php _e('Judges for this journey'); ?>',
        loadMsg: '<?php _e('Updating judge(s) list'); ?> ...',
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        multiple: true,
        selectOnNavigation: false,
        rowStyler:myRowStyler,
        fitcolumns: true,
        idField: 'ID',
        textField: 'Nombre',
        url: '/agility/server/mailFunctions.php',
        queryParams: {
            'Operation': 'enumerateJueces',
            'Prueba': workingData.prueba,
            'Jornada': workingData.jornada,
            'Federation': workingData.federation
        },
        method: 'get',
        mode: 'remote',
        multiSort: true,
        remoteSort: true,
        columns: [[
            {field:'ID',hidden:true},
            {field:'Nombre',    sortable:true, title:'<?php _e('Name'); ?>',width:35,align:'right'},
            {field:'Email',     sortable:true, title:'<?php _e('Electronic mail'); ?>',width:55,align:'right'},
            {field:'Internacional',  title:'<?php _e('International'); ?>',width:5,align:'center',formatter:formatOk},
            {field:'Practicas',   title:'<?php _e('Learning'); ?>',width:5,align:'center',formatter:formatOk}
        ]],
        onBeforeLoad: function(params) {
            return (workingData.prueba>0);
        },
        onDblClickRow: function (index,row) {
            scores_emailEditJuez(index,row);
        }
    });

</script>
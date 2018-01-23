 <!-- 
dlg_jornadas.inc

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
require_once(__DIR__."/../../server/auth/Config.php");
require_once(__DIR__."/../../server/tools.php");
require_once(__DIR__."/../../server/auth/AuthManager.php");
$am = new AuthManager("Chrono");
$config =Config::getInstance();
?>

<!-- FORMULARIO DE DECLARACION Y MODIFICACION DE JORNADAS -->
<div id="jornadas-dialog" style="padding: 5px 15px">
		<div class="ftitle"><?php _e('Journey information');?><br /></div>
        <form id="jornadas-form" method="get" novalidate>
            <div class="fitem">
                <label for="jornadas-Nombre" style="width:250px"><?php _e('Name'); ?> / <?php _e('Comments'); ?>:</label>
                <input id="jornadas-Nombre" name="Nombre" type="text" style="width:325px"/>
            </div>
        	<div class="fitem">
				<!-- ID de la prueba asociada al formulario -->
				<input id="jornadas-Prueba" type="hidden" name="Prueba"/>
				<!-- ID de jornada; usado para updates y deletes -->
				<input id="jornadas-ID" type="hidden" name="ID"/> 
				<!-- Operacion a realizar con el formulario -->
				<input id="jornadas-Operation" type="hidden" name="Operation"/>
        	</div>
            <div class="fitem">
                <label for="jornadas-Fecha"style="width:150px"><?php _e('Date'); ?></label>
                <select id="jornadas-Fecha" name="Fecha"  style="text-align:right"></select>
            </div>
            <div class="fitem">
                <label for="jornadas-Hora" style="width:150px"><?php _e('Hour'); ?></label>
                <input id="jornadas-Hora" name="Hora"  style="text-align:right"/>
            </div>
            <div class="ftitle">
            	<label style="width:250px"><?php _e('Rounds on this journey'); ?>:</label>
            </div>
            
            <div class="fitem">
                <input type="hidden" id="jornadas-PreAgility" name="PreAgility" value="1">
                <label for="jornadas-PreAgilityChk" style="width:150px" ><span id="jornadas-PreAgilityName">Pre-Agility</span></label>
                <input id="jornadas-PreAgilityChk" name="PreAgilityChk" class="easyui-checkbox" 
                	type="checkbox" checked="checked" value="1"  onClick="checkPrueba('#jornadas-PreAgilityChk',0x0003)"/>
                <input id="jornadas-MangasPreAgility" name="MangasPreAgility" style="width:140px"/>
            </div>

            <div class="fitem">
                <label for="jornadas-Junior" style="text-align:left;width:150px" ><span id="jornadas-JuniorName">Junior</span></label>
                <input id="jornadas-Junior" name="Junior" class="easyui-checkbox"
                       type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Junior',0x4000)"/>
                <span style="width:25px">&nbsp;</span>
                <label for="jornadas-Senior" style="text-align:right;width:110px" ><span id="jornadas-SeniorName">Senior</span></label>
                <input id="jornadas-Senior" name="Senior" class="easyui-checkbox"
                       type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Senior',0x8000)"/>
            </div>

            <div class="fitem">
                <label for="jornadas-Grado1" style="width:150px" ><span id="jornadas-Grado1Name">Grade I</span></label>
                <input type="hidden" id="jornadas-Grado1" name="Grado1" value="1">
                <input id="jornadas-Grado1Chk" name="Grado1Chk" class="easyui-checkbox"
                    type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado1',0x0004)"/>
                <input id="jornadas-MangasGrado1" name="MangasGrado1" style="width:140px"/>
            </div>
            
            <div class="fitem">
                <label for="jornadas-Grado2" style="width:150px" ><span id="jornadas-Grado2Name">Grade II</span></label>
                <input id="jornadas-Grado2" name="Grado2" class="easyui-checkbox" 
                	type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado2',0x0008)"/>
            </div>

            <div class="fitem">
                <label for="jornadas-Grado3" style="width:150px" ><span id="jornadas-Grado3Name">Grade III</span></label>
                <input id="jornadas-Grado3" name="Grado3" class="easyui-checkbox" 
                	type="checkbox" checked="checked" value="1" onClick="checkPrueba('#jornadas-Grado3',0x0010)"/>
            </div>
            
            <div class="fitem">
                <label for="jornadas-Open" style="width:150px" ><?php _e('No grades - Open'); ?></label>
                <input id="jornadas-Open" name="Open" class="easyui-checkbox" 
                	type="checkbox" value="1"  onClick="checkPrueba('#jornadas-Open',0x0020)"/>
            </div>
    
             <div class="fitem">
            	<input type="hidden" id="jornadas-Equipos3" name="Equipos3" value="4">
            	<input type="hidden" id="jornadas-Equipos4" name="Equipos4" value="7">
                <label for="jornadas-EquiposChk" style="width:150px" ><?php _e('Teams'); ?></label>
                <input id="jornadas-EquiposChk" name="EquiposChk" class="easyui-checkbox" 
                	type="checkbox" checked="checked" value="1"  onClick="checkPrueba('#jornadas-EquiposChk',0x1CC0)"/>
                <input id="jornadas-MangasEquipos" name="MangasEquipos" style="width:140px"/>
            </div>
            
            <div class="fitem">
                <label for="jornadas-KO" style="text-align:left;width:150px" ><?php _e('K.O. Round'); ?></label>
                <input id="jornadas-KO" name="KO" class="easyui-checkbox" 
                	type="checkbox" value="1" onClick="checkPrueba('#jornadas-KO',0x0100);"/>
                <span style="width:25px">&nbsp;</span>
                <label for="jornadas-Games" style="text-align:right;width:110px" ><?php _e('Games / WAO'); ?></label>
                <input id="jornadas-Games" name="Games" class="easyui-checkbox"
                       type="checkbox" value="1" onClick="checkPrueba('#jornadas-Games',0x2000);"/>
            </div>

            <div class="fitem">
                <label for="jornadas-Especial" style="width:150px" ><?php _e('Special round'); ?></label>
                <input id="jornadas-Especial" name="Especial" class="easyui-checkbox"
                       type="checkbox" value="1"  onClick="checkPrueba('#jornadas-Especial',0x0200)"/>
                <input id="jornadas-Observaciones" name="Observaciones" type="text" value="(Especificar)" style="width:140px"/>
            </div>

            <div class="fitem">
                <label for="jornadas-SlaveOf_List" style="width:150px" ><?php _e('Subordinate of'); ?></label>
                <input id="jornadas-SlaveOf" name="SlaveOf" type="hidden" value="0"/>
                <input id="jornadas-SlaveOf_List" name="SlaveOf_List" value="0" />
            </div>

            <div class="fitem">
                <label for="jornadas-Tipo_Competicion_List" style="width:150px" ><?php _e('Competition Mode'); ?></label>
                <input id="jornadas-Tipo_Competicion" name="Tipo_Competicion" type="hidden" value="0"/>
                <input id="jornadas-Tipo_Competicion_List" name="Tipo_Competicion_List" value="0" />
            </div>
        </form>
            
</div>
    
<!-- BOTONES DE ACEPTAR / CANCELAR DEL CUADRO DE DIALOGO -->
<div id="jornadas-dlg-buttons" style="padding:5px">
	<a id="jornadas-okBtn" href="#" class="easyui-linkbutton" 
		data-options="iconCls:'icon-ok'" onclick="saveJornada()"><?php _e('Save'); ?></a>
	<a id="jornadas-cancelBtn" href="#" class="easyui-linkbutton" 
		data-options="iconCls:'icon-cancel'" onclick="$('#jornadas-dialog').dialog('close')"><?php _e('Cancel'); ?></a>
</div>
	
<script type="text/javascript">
    // datos del formulario de nuevo/edit jornada
    $('#jornadas-dialog').dialog({
        width:390,
        height:'auto',
        top:50,
        modal:true,
        closed:true,
        buttons:'#jornadas-dlg-buttons',
        onBeforeOpen: function() {
            // fix names for PreAgility, Grade 1,II and III
            $('#jornadas-PreAgilityName').html(workingData.datosFederation.ListaGrados['P.A.']);
            $('#jornadas-JuniorName').html(workingData.datosFederation.ListaGrados['Jr']);
            $('#jornadas-SeniorName').html(workingData.datosFederation.ListaGrados['Sr']);
            $('#jornadas-Grado1Name').html(workingData.datosFederation.ListaGrados['GI']);
            $('#jornadas-Grado2Name').html(workingData.datosFederation.ListaGrados['GII']);
            $('#jornadas-Grado3Name').html(workingData.datosFederation.ListaGrados['GIII']);
            return true;
        }
    });
    // - declaracion del formulario
    $('#jornadas-form').form({
        onLoadSuccess : function(data) {
            // fix number of g1 rounds according federation
            $('#jornadas-MangasGrado1').combobox(
                'loadData',
                (workingData.datosFederation.RoundsG1==3)?
                    [{text:'<?php _e("Single round");?>',value:'2'},{text:'<?php _e("Double round");?>',value:'1'},{text:'<?php _e("Triple round");?>',value:'3'}]
                    :
                    [{text:'<?php _e("Single round"); ?>',value:'2'},{text:'<?php _e("Double round"); ?>',value:'1'}]
            );

            // fix date and checkboxes value into datebox in "onLoadSuccess" event declaration
            switch (parseInt(data.PreAgility)) {
                case 0: // no pre-agility rounds
                    $('#jornadas-PreAgilityChk').prop('checked',false);
                    $('#jornadas-MangasPreAgility').combobox('disable');
                    break;
                case 1: // Pre-Agility 1 round
                    $('#jornadas-PreAgilityChk').prop('checked',true);
                    $('#jornadas-MangasPreAgility').combobox('enable').combobox('setValue',1);
                    break;
                case 2: // Pre-Agility 2 rounds
                    $('#jornadas-PreAgilityChk').prop('checked',true);
                    $('#jornadas-MangasPreAgility').combobox('enable').combobox('setValue',2);
                    break;
                default: // error: disable and notify
                    $('#jornadas-PreAgilityChk').prop('checked',false);
                    $('#jornadas-MangasPreAgility').combobox('disable');
                    console.log("Invalid number of pre-agility rounds: "+data.PreAgility);
                    break;
            }

            // grado 1
            if (parseInt(data.Grado1)!==0) {
                $('#jornadas-Grado1Chk').prop('checked',true);
                $('#jornadas-MangasGrado1').combobox('enable').combobox('setValue',data.Grado1);
            } else {
                $('#jornadas-Grado1Chk').prop('checked',false);
                $('#jornadas-MangasGrado1').combobox('disable').combobox('setValue',1); // on disable, set default to 2 rounds
            }

            // (des)habilitar Mangas Junior
            if (hasJunior(workingData.datosPrueba.RSCE)) {
                $('#jornadas-Junior').prop('disabled',false).prop('checked',(parseInt(data.Junior) === 1));
            } else {
                $('#jornadas-Junior').prop('disabled',true).prop('checked',false);
            }

            // (des)habilitar Mangas Senior
            if (hasSenior(workingData.datosPrueba.RSCE)) {
                $('#jornadas-Senior').prop('disabled',false).prop('checked',(parseInt(data.Senior) === 1));
            } else {
                $('#jornadas-Senior').prop('disabled',true).prop('checked',false);
            }

            // (des)habilitar juegos
            if (hasGames(workingData.datosPrueba.RSCE)) {
                $('#jornadas-Games').prop('disabled',false).prop('checked',(parseInt(data.Games) === 1));
            } else {
                $('#jornadas-Games').prop('disabled',true).prop('checked',false);
            }

            // Ajuste del grado 2
            $('#jornadas-Grado2').prop('checked',(parseInt(data.Grado2) === 1));

            // (des)Habilitar grado 3
            if(howManyGrades(workingData.datosPrueba.RSCE)===3) {
            	$('#jornadas-Grado3').prop('disabled',false).prop('checked',(parseInt(data.Grado3) === 1));
            } else {
            	$('#jornadas-Grado3').prop('disabled',true).prop('checked',false);
            }

            // equipos-3
            if (parseInt(data.Equipos3)!==0) {
                $('#jornadas-EquiposChk').prop('checked',true);
                $('#jornadas-MangasEquipos').combobox('enable').combobox('setValue',(parseInt(data.Equipos3)===2)?3:4); // 0:noteam 1:3of4 2:2of3 3:3of4
            } else if (parseInt(data.Equipos4)!==0) {
                var val=parseInt(data.Equipos4); // 0:noteam 1:4combined 2:2combined 3:3combined 4:4combined
                $('#jornadas-EquiposChk').prop('checked',true);
            	$('#jornadas-MangasEquipos').combobox('enable').combobox('setValue',(val===1)?7:(val===2)?5:(val===3)?6:7);
            } else {
                $('#jornadas-EquiposChk').prop('checked',false);
            	$('#jornadas-MangasEquipos').combobox('disable');
            }

            <?php    if ( ! $am->allowed(ENABLE_TEAMS)) { ?>
            // si la licencia no tiene permisos para jornadas por equipos, desactivamos la opcion
            $('#jornadas-EquiposChk').prop('disabled',true);
            $('#jornadas-MangasEquipos').combobox('disable');
            <?php    } ?>

            // el resto de mangas son "normales" ( estan siempre presentes )
            $('#jornadas-Equipos3').prop('checked',(parseInt(data.Equipos3) !== 0));
            $('#jornadas-Equipos4').prop('checked',(parseInt(data.Equipos4) !== 0));
            $('#jornadas-Open').prop('checked',(parseInt(data.Open) === 1));

            $('#jornadas-KO').prop('checked',(parseInt(data.KO) === 1));
            $('#jornadas-Games').prop('checked',(parseInt(data.Games) === 1));

            <?php    if ( ! $am->allowed(ENABLE_KO)) { ?>
            // si la licencia no tiene permisos para jornadas KO o Games, desactivamos la opcion
            $('#jornadas-Games').prop('disabled',true);
            $('#jornadas-KO').prop('disabled',true);
            <?php    } ?>

            $('#jornadas-Especial').prop('checked',(parseInt(data.Especial) === 1));
        	$('#jornadas-Observaciones').textbox( (parseInt(data.Especial)===0)?'disable':'enable');
            $('#jornadas-Cerrada').prop('checked',(parseInt(data.Cerrada)=== 1));
        	$('#jornadas-Prueba').val(data.Prueba);
        	$('#jornadas-Operation').val('update');
            // fix slaveof combobox according value
            $('#jornadas-SlaveOf_List').combobox('reload');
            $('#jornadas-Tipo_Competicion_List').combobox('reload');
        }
    });

    // fechas
    $('#jornadas-Fecha').datebox({
        width:170,
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
    $('#jornadas-Fecha').datebox('calendar').calendar({ firstDay:1 });
    $('#jornadas-Fecha').datebox('options').keyHandler.query = function(q) {
    	if (q.indexOf('-') >= 0) { q = replaceAll('-', '/',q);	}
    	$(this).datebox('setValue', q);
    };

    // hora
    $('#jornadas-Hora').timespinner({
        width: 170,
        required: true
    });

	// Nombre/comentarios
    $('#jornadas-Nombre').textbox({
        required: true,
        validType: 'length[1,255]'
    });

    $('#jornadas-MangasPreAgility').combobox({
        valueField:'value',
        panelHeight:'auto',
        textField: 'text',
        data: [
            {text:'<?php _e("Single round"); ?>',value:'1'},
            {text:'<?php _e("Double round"); ?>',value:'2',selected:true}
        ]
    });

    $('#jornadas-MangasGrado1').combobox({
        panelHeight:'auto',
        valueField: 'value',
        textField: 'text',
        data: [
            {text:'<?php _e("Single round"); ?>',value:'2'},
            {text:'<?php _e("Double round"); ?>',value:'1',selected:true},
            {text:'<?php _e("Triple round"); ?>',value:'3'}
            ]
    });

    $('#jornadas-MangasEquipos').combobox({
        valueField:'value',
        panelHeight:'auto',
        textField: 'text',
        data: [
            // reserve values 1,2 for backward compatibility
            {text:'<?php _e("2 best of 3"); ?>',value:'3'},
            {text:'<?php _e("3 best of 4"); ?>',value:'4',selected:true},
            {text:'<?php _e("2 combined"); ?>',value:'5'},
            {text:'<?php _e("3 combined"); ?>',value:'6'},
            {text:'<?php _e("4 combined"); ?>',value:'7'}
        ]
    });

    $('#jornadas-Observaciones').textbox();

    $('#jornadas-SlaveOf_List').combobox({
        panelHeight: 'auto',
        width:180,
        panelWidth: 275,
        valueField:'ID',
        textField:'Nombre',
        method: 'get',
        mode: 'remote',
        required: true,
<?php    if ( ! $am->allowed(ENABLE_SPECIAL)) { ?>
        disabled: true,
<?php    } ?>
        url:'/agility/server/database/jornadaFunctions.php',
        onChange:function(newval,oldval) {
            $('#jornadas-SlaveOf').val(newval);
        },
        onLoadSuccess: function() {
            $(this).combobox('select',$('#jornadas-SlaveOf').val())
        },
        onBeforeLoad:function(param) {
            param.Operation='getAvailableParents';
            param.Prueba= $('#jornadas-Prueba').val();
            param.ID= $('#jornadas-ID').val();
            return true;
        }
    });

    $('#jornadas-Tipo_Competicion_List').combobox({
        panelHeight: 'auto',
        width:180,
        panelWidth: 275,
        valueField:'ID',
        textField:'Nombre',
        method: 'get',
        mode: 'remote',
        required: true,
        url:'/agility/server/modules/moduleFunctions.php',
        onChange:function(newval,oldval) {
            $('#jornadas-Tipo_Competicion').val(newval);
        },
        onLoadSuccess: function() {
            // force user to select rignt competition type
            if ($('#jornadas-Nombre').textbox('getValue')!=="-- Sin asignar --")
                $(this).combobox('select',$('#jornadas-Tipo_Competicion').val());
            else $(this).combobox('clear');
        },
        onBeforeLoad:function(param) {
            param.Operation='competitions';
            param.Federation= workingData.federation;
            return true;
        }
    });

	addTooltip($('#jornadas-okBtn').linkbutton(),'<?php _e("Register journey into database"); ?>');
	addTooltip($('#jornadas-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    
</script>

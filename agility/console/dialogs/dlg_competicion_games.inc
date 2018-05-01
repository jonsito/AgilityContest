<!-- 
dlg_competicion_games.inc

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

<!-- dialogo de entrada de datos para snooker -->
<div id="competicion-dialog-snooker" style="width:360px;padding:5px">
    <form id="competicion-dialog-snooker-header">
        <input type="hidden" id="cds-Index"/>
        <table>
            <!-- datos del perro y guia -->
            <tr>
                <th colspan="11"><br/><?php _e('Competitor data');?></th>
            </tr>
            <tr>
                <th colspan="1"><label for="cds-Dog"><?php _e('Dog');?></label></th>
                <td colspan="5"><input id="cds-Dog" name="cds-Dog" readonly="readonly" data-options="width:175"></td>
            </tr>
            <tr>
                <th colspan="1"><label for="cds-Handler"><?php _e('Handler');?></label></th>
                <td colspan="5"><input id="cds-Handler" name="cds-Handler" readonly="readonly" data-options="width:175"></td>
            </tr>
        </table>
    </form>
    <form id="competicion-dialog-snooker-form">
        <table>
            <!-- datos de la secuencia de apertura -->
            <tr><th colspan="6"><br/><?php _e('Opening Sequence');?></th></tr>
            <tr>
                <td><img src="../ajax/images/getNumberedBall.php?Number=1&Color=F00&Background=FFF" alt="red" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=%20&Color=FFF&Background=888" alt="white" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=1&Color=F00&Background=FFF" alt="red" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=%20&Color=FFF&Background=888" alt="white" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=1&Color=F00&Background=FFF" alt="red" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=%20&Color=FFF&Background=888" alt="white" width="40"/></td>
            </tr>
            <tr>
                <td align="center"><input type="checkbox" id="cds-Red1" value="1"/></td>
                <td><input type="number" id="cds-White1" value="0" data-options="width:40"/></td>
                <td align="center"><input type="checkbox" id="cds-Red2" value="1"/></td>
                <td><input type="number" id="cds-White2" value="0"data-options="width:40"/></td>
                <td align="center"><input type="checkbox" id="cds-Red3" value="1"/></td>
                <td><input type="number" id="cds-White3" value="0"data-options="width:40"/></td>
            </tr>
            <!-- datos de la secuencia de cierre -->
            <tr><th colspan="6"><br/><?php _e('Closing Sequence');?></th></tr>
            <tr>
                <td><img src="../ajax/images/getNumberedBall.php?Number=2&Color=FF0&Background=FFF" alt="yellow" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=3&Color=0F0&Background=888" alt="green" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=4&Color=841&Background=FFF" alt="brown" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=5&Color=00F&Background=888" alt="blue" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=6&Color=F19&Background=FFF" alt="pink" width="40"/></td>
                <td><img src="../ajax/images/getNumberedBall.php?Number=7&Color=000&Background=888" alt="black" width="40"/></td>
            </tr>
            <tr>
                <td align="center"><input type="checkbox" id="cds-Yellow" value="2"/></td>
                <td align="center"><input type="checkbox" id="cds-Green" value="3"/></td>
                <td align="center"><input type="checkbox" id="cds-Brown" value="4"/></td>
                <td align="center"><input type="checkbox" id="cds-Blue" value="5"/></td>
                <td align="center"><input type="checkbox" id="cds-Pink" value="6"/></td>
                <td align="center"><input type="checkbox" id="cds-Black" value="7"/></td>
            </tr>

            <!-- tiempo -->
            <tr><th colspan="6"><br/>&nbsp;</th></tr>
            <tr>
                <th colspan="1"><label for="cds-Time"><?php _e('Time');?></label></th>
                <td colspan="5"><input id="cds-Time" name="cds-Time" data-options="width:50,min:0,precision:3"></td>
            </tr>
        </table>
    </form>
</div>

<!-- botones del dialogo de snooker-->
<div id="competicion-dialog-snooker-Buttons" data-options="border:false" style="text-align:right;padding:5px 0 0;">
    <a id="cds-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-db_backup'" onclick="saveSnooker()"><?php _e('Accept'); ?></a>
    <a id="cds-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#competicion-dialog-snooker').dialog('close');"><?php _e('Cancel'); ?></a>
</div>

<!-- dialogo de entrada de datos para gambler -->
<div id="competicion-dialog-gambler" style="width:640px;padding:5px">
    <form id="competicion-dialog-gambler-header">
        <input type="hidden" id="cdg-Index"/>
        <table>
            <!-- datos del perro y guia -->
            <tr>
                <th colspan="11"><br/><?php _e('Competitor data');?></th>
            </tr>
            <tr>
                <th colspan="2"><label for="cdg-Dog"><?php _e('Dog');?></label></th>
                <td colspan="6"><input id="cdg-Dog" name="cdg-Dog" readonly="readonly" data-options="width:175"></td>
            </tr>
            <tr>
                <th colspan="2"><label for="cdg-Handler"><?php _e('Handler');?></label></th>
                <td colspan="6"><input id="cdg-Handler" name="cdg-Handler" readonly="readonly" data-options="width:175"></td>
            </tr>
        </table>
    </form>
    <form id="competicion-dialog-gambler-form">
        <table>
            <!-- datos de la secuencia de apertura -->
            <tr><th colspan="11"><br/><?php _e('Opening Sequence');?></th></tr>
            <!-- datos de la secuencia de cierre -->
            <?php
            // primera fila
            echo "<tr>";
            $a1=array(
                array("1",5,"00F"),array("2",5,"00F"),array("3",4,"841"),array("4",3,"0F0"),array("5",3,"0F0"),
                array("6",2,"FF0"),array("7",2,"FF0"),array("8",2,"FF0"),array("9",2,"FF0"),array("10",2,"FF0"),array("11",2,"FF0")
            );
            for($n=0;$n<count($a1);$n++){
                echo '<td style="width:60px;" align="center"><img src="../ajax/images/getNumberedBall.php?Number='.$a1[$n][0].'&Color='.$a1[$n][2].'&Background=888" alt="img'.$n.'" width="40"/></td>';
            }
            echo "</tr><tr>";
            for($n=0;$n<count($a1);$n++){
                echo '<th style="width:60px;" align="center">'.$a1[$n][1].' Pt.</th>';
            }
            echo "</tr><tr>";
            for($n=0;$n<count($a1);$n++){
                echo '<td style="width:60px;" align="center">';
                echo '<input type="checkbox" value="'.$a1[$n][1].'" onclick="computeGambler(this);">';
                echo '<input type="checkbox" value="'.$a1[$n][1].'" onclick="computeGambler(this);">';
                echo '</td>';
            }
            echo "</tr>";
            // segunda fila
            echo "<tr><td>&nbsp;</td></tr><tr>";
            $a2=array(
                array("12",1,"F00"),array("13",1,"F00"),array("14",1,"F00"),array("15",1,"F00"),array("16",1,"F00"),
                array("17",1,"F00"),array("18",1,"F00"),array("19",1,"F00"),array("20",1,"F00"),array("21",1,"F00")
            );
            for($n=0;$n<count($a2);$n++){
                echo '<td style="width:60px;" align="center"><img src="../ajax/images/getNumberedBall.php?Number='.$a2[$n][0].'&Color='.$a2[$n][2].'&Background=888" alt="img'.$n.'" width="40"/></td>';
            }
            echo "</tr><tr>";
            for($n=0;$n<count($a2);$n++){
                echo '<th style="width:60px;" align="center">'.$a2[$n][1].' Pt.</th>';
            }
            echo "</tr><tr>";
            for($n=0;$n<count($a2);$n++){
                echo '<td style="width:60px;" align="center">';
                echo '<input type="checkbox" value="'.$a2[$n][1].'" onclick="computeGambler(this);">';
                echo '<input type="checkbox" value="'.$a2[$n][1].'" onclick="computeGambler(this);">';
                echo '</td>';
            }
            echo "</tr>";
            // tercera fila
            echo "<tr><td>&nbsp;</td></tr><tr>";
            $a3=array(
                array("A",$config->getEnv('gambler_bonus1'),"0F0"),
                array("B",$config->getEnv('gambler_bonus2'),"841"),
                array("C",$config->getEnv('gambler_bonus3'),"00F"),
                array("D",$config->getEnv('gambler_bonus4'),"819"),
                array("E",$config->getEnv('gambler_bonus5'),"000"),
                array("",_('Bonus'),""),
                array("X",$config->getEnv('gambler_extra'),"F00")
            );
            for($n=0;$n<count($a3);$n++){
                if ($a3[$n][0]==="") echo '<td style="width:55px;text-align:right;">'.$a3[$n][1].':</td>';
                else echo '<td style="width:55px;" align="center"><img src="../ajax/images/getNumberedBall.php?Number='.$a3[$n][0].'&Color='.$a3[$n][2].'&Background=888" alt="img'.$n.'" width="40"/></td>';
            }
            echo '<th colspan="2" rowspan="3" align="right">'._('Total').':<br/>&nbsp;<br/>'._('Time').':</th>';
            echo '<th colspan="2" rowspan="3" align="left"><input type="text" style="width:40px" id="cdg-Score" value="0" readonly="readonly"/>&nbsp;'._('Points').'<br/>&nbsp;<br/><input id="cdg-Time" name="cdg-Time" data-options="width:50,min:0,precision:3">'._('Seg').'.</th>';
            echo "</tr><tr>";
            for($n=0;$n<count($a3);$n++){
                if ($a3[$n][0]==="") echo '<td style="width:55px">&nbsp;</td>';
                else echo '<th style="width:55px;" align="center">'.$a3[$n][1].' Pt.</th>';
            }
            echo "</tr><tr>";
            for($n=0;$n<count($a3);$n++){
                echo '<td style="width:60px;" align="center">';
                if ($a3[$n][0]==="") echo '&nbsp;';
                else {
                    echo '<input type="checkbox" value="'.$a3[$n][1].'" onclick="computeGambler(this);">';
                    echo '<input type="checkbox" value="'.$a3[$n][1].'" onclick="computeGambler(this);">';
                }
                echo '</td>';
            }
            echo "</tr>";
            ?>
            <tr><th colspan="11"><br/><?php _e('Gambler Scoring');?></th></tr>
            <tr>
                <th><label for="cdg-Gambler1">No Gambler</label></th>
                <td colspan="2"><input id="cdg-Gambler1" type="radio" name="cdg-Gambler" value="0" checked="checked"/></td>
                <th><label for="cdg-Gambler2"><?php echo $config->getEnv('gambler_seq1');?> <?php _e('Points');?></label></th>
                <td colspan="2"><input id="cdg-Gambler2" type="radio" name="cdg-Gambler" value="<?php echo $config->getEnv('gambler_seq1');?>"/></td>
                <th><label for="cdg-Gambler3"><?php echo $config->getEnv('gambler_seq2');?> <?php _e('Points');?></label></th>
                <td colspan="2"><input id="cdg-Gambler3" type="radio" name="cdg-Gambler" value="<?php echo $config->getEnv('gambler_seq2');?>"/></td>
            </tr>
        </table>
    </form>
</div>

<!-- botones del dialogo de gambler-->
<div id="competicion-dialog-gambler-Buttons" data-options="border:false" style="text-align:right;padding:5px 0 0;">
    <a id="cdg-okBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-db_backup'" onclick="saveGambler()"><?php _e('Accept'); ?></a>
    <a id="cdg-cancelBtn" href="#" class="easyui-linkbutton"
       data-options="iconCls: 'icon-cancel'" onclick="$('#competicion-dialog-gambler').dialog('close');"><?php _e('Cancel'); ?></a>
</div>

<!--  dialogo de orden de salida e introduccion de datos de una competicion -->
<div id="competicion-dialog" class="editable-datagrid" style="width:900px;height:450px;padding:5px">
	<!-- TABLA DE INTRODUCCION DE RESULTADOS DE CADA MANGA -->
	<table id="competicion-datagrid"></table>
</div>

<!-- BARRA DE TAREAS DE ORDEN DE SALIDA -->
<div id="competicion-toolbar" style="width:100%;display:inline-block">
	<span style="float:left;padding:5px">
	   	<a id="competicion-reloadBtn" href="#" class="easyui-linkbutton" 
	   		data-options="iconCls:'icon-reload'" onclick="reloadCompeticion()"><?php _e('Update'); ?></a>
	   	<label for="competicion-autoUpdateBtn"><?php _e('Auto'); ?>:</label>
	   	<input id="competicion-autoUpdateBtn" type="checkbox" class="easyui-checkbox" value="0" onclick="autoUpdateCompeticion()"/>
   		<input id="competicion-search" type="text" value="--- Search ---" class="search_textfield"  onchange="competicionSelectByDorsal();"/>
		<label for="competicion-categoria"><?php _e('Cat');?></label>
        <select id="competicion-categoria" style="width:100px"></select>
   	</span>
   	<span style="float:right;padding:5px">
   		<!-- estos elementos deben estar alineados a la derecha -->
	   	<a id="competicion-swapBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-updown'" onclick="swapMangas()"><?php _e('Swap'); ?></a>
	   	<a id="competicion-resetBtn" href="#" class="easyui-linkbutton"
		   data-options="iconCls:'icon-brush'" onclick="resetCompeticion()"><?php _e('Reset'); ?></a>
   		<a id="competicion-printBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-print'" onclick="print_commonDesarrollo(8,'#competicion-categoria');"><?php _e('Printer forms'); ?></a>
	</span>
</div>

<script type="text/javascript">

    $('#competicion-dialog-snooker').dialog({
        icon: 'edit',
        title: "Snooker",
        collapsible:false,
        minimizable:false,
        maximizable:false,
        resizable:true,
        iconCls:'icon-table',
        closed:true,
        closable:false,
        modal:true,
        buttons:'#competicion-dialog-snooker-Buttons',
        onBeforeOpen: function() {
            $('#competicion-dialog-snooker-form').form('reset');
            return true;
        }
    });

    $('#competicion-dialog-gambler').dialog({
        icon: 'edit',
        title: "Gambler",
        collapsible:false,
        minimizable:false,
        maximizable:false,
        resizable:true,
        iconCls:'icon-table',
        closed:true,
        closable:false,
        modal:true,
        buttons:'#competicion-dialog-gambler-Buttons',
        onBeforeOpen: function() {
            $('#competicion-dialog-gambler-form').form('reset');
            return true;
        }
    });

    $('#cds-Dog').textbox();
    $('#cds-Handler').textbox();
    $('#cds-Time').numberbox();
    $('#cds-White1').numberbox();
    $('#cds-White2').numberbox();
    $('#cds-White3').numberbox();
    $('#cdg-Dog').textbox();
    $('#cdg-Handler').textbox();
    $('#cdg-Time').numberbox();
    $('#cdg-Score').numberbox();

$('#competicion-dialog').dialog({
	collapsible:false,
	minimizable:false,
	maximizable:false,
	resizable:true,
	iconCls:'icon-table',
	closed:true,
	modal:true,
	onBeforeClose: function() {
		var dg=$('#competicion-datagrid');
		var editIndex=dg.datagrid('options').editIndex;
		if (editIndex<0) {
			// disable autoupdate if any
			$('#competicion-autoUpdateBtn').prop('checked',false);
			autoUpdateCompeticion();
			return true;
		}
		// do not allow close on editing active
		$.messager.alert('<?php _e('Error'); ?>','<?php _e('Please cancel or save changes before closing window'); ?>','error');
		return false;
	}
});

$('#competicion-categoria').combobox({
	value: '-',
	panelHeight: 'auto',
	valueField: 'Categoria',
	textField: 'Nombre',
	data: getOrdenSalidaCategorias(),
	onChange: function(newValue,oldValue) { reloadCompeticion(); }
});

$('#competicion-datagrid').datagrid({
	// propiedades del panel asociado
	fit: true,
	border: false,
	closable: false,
	collapsible: false,
	collapsed: false,
	// propiedades del datagrid
	method: 'get',
	url: '../server/database/ordenSalidaFunctions.php',
    queryParams: {
        Operation: 'getData',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
		Manga: workingData.manga,
		Categorias: $('#competicion-categoria').combobox('getValue'),
		TeamView: isTeam(workingData.datosManga.Tipo)?'true':'false'
    },
    loadMsg: '<?php _e("Updating competition data"); ?> ....',
    toolbar: '#competicion-toolbar',
    pagination: false,
	// these two fields are required in 1.4.2 editable datagrid, due to bad rownumber cell height evaluation
	// and poor rendering performance. To be removed in a newer jeasyui version
	autoRowHeight:false, // as specific css is needed for rownumbers, do not autoevaluate row height
    rownumbers: true, // fix rownumber cell height to take care on editable datagrid
    fitColumns: true,
	idField: 'Dorsal',
    singleSelect: true,
    editIndex: -1, // nonstandard added by me :-)
    // toolbar: '#competicion-toolbar',
    columns:[[
        { field:'Manga',		hidden:true },
        { field:'Perro',		hidden:true },
		{ field:'Equipo',		hidden:true },
      	{ field:'Licencia',		hidden:true },
      	{ field:'Pendiente',	hidden:true },
        { field:'Dorsal',		width:12, align:'right',  title: '<?php _e('Dorsal'); ?>', styler:checkPending },
        { field:'Nombre',		width:20, align:'left',  title: '<?php _e('Name'); ?>',formatter:formatBold},
        { field:'NombreGuia',	width:40, align:'right', title: '<?php _e('Handler'); ?>' },
        { field:'NombreClub',	width:30, align:'right', title: '<?php _e('Club'); ?>' },
      	{ field:'Categoria',	width:10, align:'center',title: '<?php _e('Cat'); ?>.',formatter:formatCategoria },
        { field:'Grado',        hidden:true},
        // datos a editar
      	{ field:'Faltas',		width:15, align:'right', title: '<?php _e('Opening'); ?>',	editor:'numberbox' },
      	{ field:'Tocados',		width:15, align:'right', title: '<?php _e('Closing'); ?>',editor:'numberbox' },
        { field:'Rehuses',       hidden:true},
      	{ field:'Tiempo',		width:15, align:'right', title: '<?php _e('Time'); ?>',
          	editor:{
          		type:'numberbox',
          		options:{		
              		formatter:function(v){	return toFixedT( parseFloat(v),ac_config.numdecs); },
        			parser:function(s){	var v = parseFloat(s);	return isNaN(v)?'':v; }
            	}
          	}
        },
        { field:'Eliminado',hidden:true},
      	{ field:'NoPresentado',	width:10, align:'center',title: '<?php _e('N.P.'); ?>',
            formatter:formatNoPresentado, editor:{type:'checkbox',options:{on:1,off:0}}	},
      	{ field:'Observaciones',width:17, align:'left',	 title: '<?php _e('Comments'); ?>',	editor:'text'  },
        // botones de edicion
      	{ field:'action',		width:48, align:'center',title: '',
      		formatter:function(value,row,index){
				if(row.Dorsal==='*') return "&nbsp;";
          		if (row.editing){
          		    var h = '<a href="#" id="lnkb3_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-notes\'" onclick="showAssistantSheet(this);"></a>';
    				var s = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-save\'" onclick="saverow(this);"><?php _e('Save'); ?></a> ';
    				var c = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-undo\'" onclick="cancelrow(this);"><?php _e('Cancel'); ?></a>';
    				return h+s+c;
    			} else {
    				var e = '<a href="#" id="lnkb1_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-edit\'" onclick="editrow(this);"><?php _e('Edit'); ?></a> ';
    				var d = '<a href="#" id="lnkb2_'+index+'" class="easyui-linkbutton" data-options="iconCls:\'icon-trash\'" onclick="deleterow(this);"><?php _e('Clear'); ?></a>';
    				return "&nbsp;"+e+d;
    			}
      		}
      	}	
    ]],
    rowStyler:competicionRowStyler,
    onBeforeLoad: function(param) {
        // do not load if no manga selected
        if (parseInt(workingData.manga) <= 0) return false;
        var name=( parseInt(workingData.datosManga.Tipo)===29)?"<?php _e('Closing'); ?>":'Gambler';
        // adjust field to "Closing" or "Gambler" acording mode
        setTimeout(function() {
            $('#competicion-datagrid').datagrid('setFieldTitle',{'field':'Tocados','title':name});
        },0);
        return true;
    },
	onLoadSuccess:function(){
		// mark no row is being edited
		$(this).datagrid('options').editIndex=-1;
		// render datagrid buttons
		$(this).datagrid('getPanel').find('a.easyui-linkbutton').linkbutton();
		// enter focus to allow key binding
		$(this).datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
	},
	onDblClickRow:function(index,row) {
		if(row.Dorsal==='*') return; // not on editable row
		if( $(this).datagrid('options').editIndex!==-1) return; // on editing ignore double click
		$(this).datagrid('beginEdit',index);
		// focus and first field and select contents
		var ed = $(this).datagrid('getEditor', {index:index,field:'Faltas'});
		var input=$(ed.target).next().find('input');
		input.focus();
		input.select();
		// input.setSelectionRange(0, input.value.length);
	},
	// actualizacion de los iconos save/edit/delete/cancel
	onBeforeEdit:function(index,row){
		if(row.Dorsal==='*') return false;
		$(this).datagrid('options').editIndex=index;
		row.editing = true; 
		updateActions(index);
		return true;
	},
	onAfterEdit:function(index,row){
		$(this).datagrid('options').editIndex=-1;
		row.editing = false;
		updateActions(index);
	},
	onCancelEdit:function(index,row){
		$(this).datagrid('options').editIndex=-1;
		row.editing = false;
		updateActions(index);
	},
	onBeforeSelect: function(index,row) {
		return ($(this).datagrid('options').editIndex===-1);
	}
});

// update row contents on action requested: just force repaint row and reassign buttons
function updateActions(index){
	var cdg=$('#competicion-datagrid');
    var editIndex=cdg.datagrid('options').editIndex;
	cdg.datagrid('updateRow',{
		index: index,
		row:{}
	});
	$('#lnkb1_'+index).linkbutton();
    $('#lnkb2_'+index).linkbutton();
    $('#lnkb3_'+index).linkbutton();
	// enable/disable search textbox acording edit state
	$('#competicion-search').prop('disabled',( editIndex===-1)?false:true);
	$('#competicion-categoria').combobox( (editIndex===-1)?'enable':'disable');
}

function getRowIndex(target){
	var tr = $(target).closest('tr.datagrid-row');
	return parseInt(tr.attr('datagrid-row-index'));
}

function editrow(target){
	$('#competicion-datagrid').datagrid('beginEdit', getRowIndex(target));
}

function deleterow(target){
	$.messager.confirm('<?php _e('Confirm'); ?>','<?php _e('Clear competitor results');?>'+'<br/>'+'<?php _e('Sure?'); ?>',function(r){
		if (r){
			var editIndex=getRowIndex(target);
			var data=$('#competicion-datagrid').datagrid('getRows')[editIndex];
			data.Faltas=0;
			data.Tocados=0;
			data.Rehuses=0;
			data.Games=0;
            if (isJornadaKO()) data.Games=1; // trick to mark dog is runing in this round
			data.Tiempo=0.0;
			data.Eliminado=0;
			data.NoPresentado=0;
			data.Observaciones="";
			data.Pendiente=1;
			saveCompeticionData(editIndex,data);	
		}
	});
}

function saverow(target){
    var dg=$('#competicion-datagrid');
	var editIndex=getRowIndex(target);
	dg.datagrid('endEdit', editIndex );
	var data=dg.datagrid('getRows')[editIndex];
	data.Pendiente=0;
	if (isJornadaKO()) data.Games=1; // trick to mark dog is competing in this ko round
	saveCompeticionData(editIndex,data);
}

function cancelrow(target){
	$('#competicion-datagrid').datagrid('cancelEdit', getRowIndex(target));
}

function showAssistantSheet(target) {
    var dg=$('#competicion-datagrid');
    // si no estoy en edicion, ignorar
    var idx=getRowIndex(target);
    if (dg.datagrid('options').editIndex!==idx) return false;
    var data=dg.datagrid('getRows')[idx];
    // en funcion del tipo de juego abrimos dialogo de snooker o de gambler
    if (parseInt(workingData.datosManga.Tipo)===29) { // snooker
        $('#cds-Dog').textbox('setText',data.Nombre);
        $('#cds-Handler').textbox('setText',data.NombreGuia);
        $('#cds-Index').val(idx);
        $('#competicion-dialog-snooker').dialog('open');
    }
    if (parseInt(workingData.datosManga.Tipo)===30) { // gambler
        $('#cdg-Dog').textbox('setText',data.Nombre);
        $('#cdg-Handler').textbox('setText',data.NombreGuia);
        $('#cdg-Index').val(idx);
        $('#cdg-Score').val(0);
        $('#competicion-dialog-gambler').dialog('open');
    }
}

function computeGambler(cb) {
    var cur=$('#cdg-Score').numberbox('getValue');
    var inc=parseInt(cb.value) * ( (cb.checked)?1:-1 );
    $('#cdg-Score').numberbox('setValue',parseInt(cur)+inc);
}

function saveGambler() {
    var dg=$('#competicion-datagrid');
    // guardamos los puntos de la secuencia de apertura
    var idx=$('#cdg-Index').val();
    var ed=dg.datagrid('getEditor',{index:idx,field:'Faltas'});
    var score=($('#cdg-Score').numberbox('getValue'));
    $(ed.target).numberbox('setValue',score);

    // guardamos los puntos de la secuencia gambler
    ed=dg.datagrid('getEditor',{index:idx,field:'Tocados'});
    score=$('input[name=cdg-Gambler]:checked').val();
    $(ed.target).numberbox('setValue',score);

    // guardamos el tiempo
    ed=dg.datagrid('getEditor',{index:idx,field:'Tiempo'});
    $(ed.target).numberbox('setValue',$('#cdg-Time').numberbox('getValue'));
    // finally close gambler dialog
    $('#competicion-dialog-gambler').dialog('close');
}

function saveSnooker() {
    var dg=$('#competicion-datagrid');
    // guardamos los puntos de la secuencia de apertura
    var idx=$('#cds-Index').val();
    var ed=dg.datagrid('getEditor',{index:idx,field:'Faltas'});
    var score= parseInt( $('#cds-Red1').prop('checked')?$('#cds-Red1').val():0 ) +
            parseInt($('#cds-White1').numberbox('getValue')) +
            parseInt( $('#cds-Red2').prop('checked')?$('#cds-Red2').val():0 ) +
            parseInt($('#cds-White2').numberbox('getValue')) +
            parseInt( $('#cds-Red3').prop('checked')?$('#cds-Red3').val():0 ) +
            parseInt($('#cds-White3').numberbox('getValue'));
    $(ed.target).numberbox('setValue',score);

    // guardamos los puntos de la secuencia de cierre
    ed=dg.datagrid('getEditor',{index:idx,field:'Tocados'});
    score= parseInt( $('#cds-Yellow').prop('checked')?$('#cds-Yellow').val():0 ) +
            parseInt( $('#cds-Green').prop('checked')?$('#cds-Green').val():0 ) +
            parseInt( $('#cds-Brown').prop('checked')?$('#cds-Brown').val():0 ) +
            parseInt( $('#cds-Blue').prop('checked')?$('#cds-Blue').val():0 ) +
            parseInt( $('#cds-Pink').prop('checked')?$('#cds-Pink').val():0 ) +
            parseInt( $('#cds-Black').prop('checked')?$('#cds-Black').val():0 );
    $(ed.target).numberbox('setValue',score);

    // guardamos el tiempo
    ed=dg.datagrid('getEditor',{index:idx,field:'Tiempo'});
    $(ed.target).numberbox('setValue',$('#cds-Time').numberbox('getValue'));

    // finally close snooker dialog
    $('#competicion-dialog-snooker').dialog('close');
}

    //- botones del panel de entrada de datos
    addTooltip($('#competicion-reloadBtn').linkbutton(),'<?php _e("Reload competition data on this round"); ?>');
    addTooltip($('#competicion-swapBtn').linkbutton(),'<?php _e("Swap data and results from Agility/Jumping matching round"); ?>');
    addTooltip($('#competicion-resetBtn').linkbutton(),'<?php _e("Delete every competitors data on this round"); ?>');
    addTooltip($('#competicion-printBtn').linkbutton(),'<?php _e("Print judge assistant forms"); ?>');
    addTooltip($('#competicion-autoUpdateBtn'),'<?php _e("(de)activate periodic refresh of results for this round"); ?>');
    addTooltip($('#competicion-search'),'<?php _e("Search a competitor by entering dorsal"); ?>');
    addTooltip($('#competicion-categoria').combobox('textbox'),'<?php _e("Select categories to work with"); ?>');
    // botones de dialogos de snooker y gambler
    addTooltip($('#cds-okBtn').linkbutton(),'<?php _e("Validate data. Store into row being editing"); ?>');
    addTooltip($('#cds-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');
    addTooltip($('#cdg-okBtn').linkbutton(),'<?php _e("Validate data. Store into row being editing"); ?>');
    addTooltip($('#cdg-cancelBtn').linkbutton(),'<?php _e("Cancel operation. Close window"); ?>');

// key handling
$('#competicion-datagrid').datagrid('getPanel').panel('panel').attr('tabindex',0).bind('keydown',competicionKeyEventHandler);

</script>
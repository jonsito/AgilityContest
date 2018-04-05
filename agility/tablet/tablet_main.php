<!-- 
tablet_main.php

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
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>

<div id="tablet-window" style="margin:0;padding:0">
    <div id="tablet-layout">
        <div data-options="region:'west',split:true,minWidth:50" title="<?php _e('Activities on this journey');?>" style="width:50%;">
            <!-- Tabla desplegable para la entrada de datos desde el tablet -->
            <table id="tablet-datagrid" style="margin:0;padding:0;"></table>
        </div>
        <div data-options="region:'center',fit:true">
            <div class="dialog_forms" style="background:white;">
                <form id="tdialog-form" method="get" novalidate="novalidate">
                    <fieldset id="tdialog-fieldset" style="margin:0;padding:0;border:0">
                        <input id="tdialog-RowIndex" name="RowIndex" type="hidden" value="0"> <!-- datagrid row index -->
                        <input id="tdialog-Session" name="Session" type="hidden" value="0"> <!-- Session ID -->
                        <input id="tdialog-Parent" name="Parent" type="hidden" value=""> <!-- name of parent datagrid -->
                        <input id="tdialog-ID" name="ID" type="hidden">     <!-- Tanda ID -->
                        <input id="tdialog-Prueba" name="Prueba" type="hidden">
                        <input id="tdialog-Jornada" name="Jornada" type="hidden">
                        <input id="tdialog-Manga" name="Manga" type="hidden">
                        <input id="tdialog-Perro" name="Perro" type="hidden">
                        <input id="tdialog-Licencia" name="Licencia" type="hidden">
                        <input id="tdialog-Equipo" name="Equipo" type="hidden">
                        <input id="tdialog-NombreEquipo" name="NombreEquipo" type="hidden">
                        <input id="tdialog-Pendiente" name="Pendiente" type="hidden" value="0">
                        <input id="tdialog-Tanda" name="Tanda" type="hidden"> <!-- Tanda name -->
                        <input id="tdialog-Observacioens" name="Observaciones" type="hidden">
                        <input id="tdialog-Operation" name="Operation" type="hidden" value="update">
                        <input id="tdialog-User0Btn" type="button" value="<?php _e('Fn');?> 1" onclick="tablet_userfn(0);" class="tablet_button tb_fnkey">
                        <input id="tdialog-User1Btn" type="button" value="<?php _e('Fn');?> 2" onclick="tablet_userfn(1);" class="tablet_button tb_fnkey">
                        <input id="tdialog-User2Btn" type="button" value="<?php _e('Fn');?> 3" onclick="tablet_userfn(2);" class="tablet_button tb_fnkey">
                        <input id="tdialog-User3Btn" type="button" value="<?php _e('Fn');?> 4" onclick="tablet_userfn(3);" class="tablet_button tb_fnkey">
                        <input id="tdialog-FaltaUpBtn" type="button" value="<?php _e('Fault');?>" onclick="tablet_up('#tdialog-Faltas',true);" class="tablet_button tb_falta">
                        <input id="tdialog-FaltaDownBtn" type="button" value="<?php _e('Fault');?> -" onclick="tablet_down('#tdialog-Faltas',true);">
                        <input id="tdialog-RehuseUpBtn" type="button" value="<?php _e('Refusal');?>" onclick="tablet_up('#tdialog-Rehuses',true)" class="tablet_button tb_rehuse">
                        <input id="tdialog-RehuseDownBtn" type="button" value="<?php _e('Refusal');?> -" onclick="tablet_down('#tdialog-Rehuses',true);">
                        <input id="tdialog-TocadoUpBtn" type="button" value="<?php _e('Touch');?>" onclick="tablet_up('#tdialog-Tocados',true);" class="tablet_button tb_tocado">
                        <input id="tdialog-TocadoDownBtn" type="button" value="<?php _e('Touch');?> -" onclick="tablet_down('#tdialog-Tocados',true);">
                        <input id="tdialog-StartStopBtn" type="button" value="Start" onclick="tablet_startstop();" class="tablet_button tb_crono">
                        <input id="tdialog-ResetBtn" type="button" value="     " onclick="tablet_resetchrono();" class="tablet_button tb_reset">
                        <input id="tdialog-SalidaBtn" type="button" value="<?php _e('Begin');?>" onclick="tablet_salida();" class="tablet_button tb_salida">
                        <input id="tdialog-AcceptBtn" type="button" value="<?php _e('Accept');?>" onclick="tablet_accept();" class="tablet_button tb_accept">
                        <input id="tdialog-CancelBtn" type="button" value="<?php _e('Cancel');?>" onclick="tablet_cancel();" class="tablet_button tb_cancel">
                        <input id="tdialog-NoPresentadoBtn" type="button" value="<?php _e('Not Present');?>" onclick="tablet_np(true);" class="tablet_button tb_nopresentado">
                        <input id="tdialog-EliminadoBtn" type="button" value="<?php _e('Eliminated');?>" onclick="tablet_elim(true);" class="tablet_button tb_eliminado">
                        <input id="tdialog-0" type="button" value="0" class="tablet_numbers" onclick="tablet_add(0);">
                        <input id="tdialog-1" type="button" value="1" class="tablet_numbers" onclick="tablet_add(1);">
                        <input id="tdialog-2" type="button" value="2" class="tablet_numbers" onclick="tablet_add(2);">
                        <input id="tdialog-3" type="button" value="3" class="tablet_numbers" onclick="tablet_add(3);">
                        <input id="tdialog-4" type="button" value="4" class="tablet_numbers" onclick="tablet_add(4);">
                        <input id="tdialog-5" type="button" value="5" class="tablet_numbers" onclick="tablet_add(5);">
                        <input id="tdialog-6" type="button" value="6" class="tablet_numbers" onclick="tablet_add(6);">
                        <input id="tdialog-7" type="button" value="7" class="tablet_numbers" onclick="tablet_add(7);">
                        <input id="tdialog-8" type="button" value="8" class="tablet_numbers" onclick="tablet_add(8);">
                        <input id="tdialog-9" type="button" value="9" class="tablet_numbers" onclick="tablet_add(9);">
                        <input id="tdialog-Del" type="button" value="." class="tablet_numbers" onclick="tablet_dot();">
                        <input id="tdialog-Dot" type="button" value="Del" class="tablet_numbers" onclick="tablet_del();">
                        <label id="tdialog-InfoLbl" for="tdialog-InfoLbl" class="tablet_infoheader">Informacion de prueba, jornada y manga</label>
                        <label id="tdialog-NumberLbl" for="tdialog-NumberLbl" class="tablet_infoheader"><br/>Num<br/></label>
                        <label id="tdialog-DorsalLbl" for="tdialog-Dorsal" class="tablet_info">Dorsal</label>
                        <input id="tdialog-Dorsal" type="text" readonly="readonly" name="<?php _e('Dorsal');?>" class="tablet_info"/>
                        <label id="tdialog-NombreLbl" for="tdialog-Nombre" class="tablet_info"><?php _e('Name'); ?></label>
                        <input id="tdialog-Nombre" type="text" readonly="readonly" name="Nombre" class="tablet_info"/>
                        <input id="tdialog-NombreLargo" name="NombreLargo" type="hidden">
                        <label id="tdialog-GuiaLbl" for="tdialog-Guia" class="tablet_info"><?php _e('Handler'); ?></label>
                        <input id="tdialog-Guia" type="text" readonly="readonly" name="NombreGuia" class="tablet_info"/>
                        <label id="tdialog-ClubLbl" for="tdialog-Club" class="tablet_info"><?php _e('Club'); ?></label>
                        <input id="tdialog-Club" type="text" readonly="readonly" name="NombreClub" class="tablet_info"/>
                        <label id="tdialog-CategoriaLbl" for="tdialog-Categoria" class="tablet_info"><?php _e('Cat'); ?>.</label>
                        <input id="tdialog-Categoria" type="text" readonly="readonly" name="Categoria" class="tablet_info"/>
                        <label id="tdialog-GradoLbl" for="tdialog-Grado" class="tablet_info"><?php _e('Grade'); ?></label>
                        <input id="tdialog-Grado" type="text" readonly="readonly" name="Grado" class="tablet_info"/>
                        <label id="tdialog-CeloLbl" for="tdialog-Celo" class="tablet_info"><?php _e('Heat'); ?></label>
                        <input id="tdialog-Celo" type="text" readonly="readonly" name="Celo" class="tablet_info"/>
                        <label id="tdialog-FaltasLbl" for="tdialog-Faltas"><?php _e('Faults'); ?></label>
                        <input id="tdialog-Faltas" type="text" readonly="readonly" value="0" name="Faltas" class="tablet_data"/>
                        <label id="tdialog-TocadosLbl" for="tdialog-Tocados"><?php _e('Touchs'); ?></label>
                        <input id="tdialog-Tocados" type="text" readonly="readonly" value="0" name="Tocados" class="tablet_data"/>
                        <label id="tdialog-RehusesLbl" for="tdialog-Rehuses"><?php _e('Refusals'); ?></label>
                        <input id="tdialog-Rehuses" type="text" readonly="readonly" value="0" name="Rehuses" class="tablet_data"/>
                        <label id="tdialog-TiempoLbl" for="tdialog-Tiempo"><?php _e('Time'); ?></label>
                        <span id="tdialog-timestamp" style="display:none"></span>
                        <input id="tdialog-Tiempo" type="text" readonly="readonly" value="00.00" name="Tiempo" class="tablet_data"/>
                        <input id="tdialog-TIntermedio" name="TIntermedio" type="hidden">
                        <label id="tdialog-NoPresentadoLbl" for="tdialog-NoPresentadoStr"><?php _e('No Pr'); ?>.</label>
                        <input id="tdialog-NoPresentado" type="hidden" name="NoPresentado" value="0"/>
                        <input id="tdialog-NoPresentadoStr" type="text" readonly="readonly" value="" name="NoPresentadoStr" class="tablet_data"/>
                        <label id="tdialog-EliminadoLbl" for="tdialog-EliminadoStr"><?php _e('Elim'); ?>.</label>
                        <input id="tdialog-Eliminado" type="hidden" value="0" name="Eliminado"/>
                        <input id="tdialog-EliminadoStr" type="text" readonly="readonly" value="" name="EliminadoStr" class="tablet_data"/>
                        <label id="tdialog-Rectangulo" class="tablet_rectangulo">&nbsp;</label>
                    </fieldset>
                </form>
            </div> <!-- dialog forms -->
            <div class="nextdog-datagrid" id="tdialog-Next">
                <table id="tdialog-tnext"></table>
            </div>
        </div> <!-- region: center -->
    </div> <!-- tablet layout -->
</div>

<!-- toolbar para orden de tandas -->
<div id="tablet-toolbar" style="width:100%;display:inline-block">
    <span style="float:left">
        <a id="tablet-reloadBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-reload'" onclick="doBeep();$('#tablet-datagrid').datagrid('reload');"><?php _e('Refresh'); ?></a>
   		<input id="tablet-datagrid-search" type="text" value="--- Dorsal ---" class="search_textfield"
            onchange="tablet_editByDorsal();"/>
    </span>
    <span style="float:right">        
        <a id="tablet-whiteBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-dog'" onclick="tablet_perroEnBlanco();"><?php _e('Test dog'); ?></a>
        <a id="tablet-recoBtn" href="#" class="easyui-linkbutton"
           data-options="iconCls:'icon-huella'" onclick="tablet_reconocimiento();"><?php _e('Course walk'); ?></a>
    </span>
</div>

<!-- declare a tag to attach a chrono object to -->
<div id="cronometro"></div>


<script type="text/javascript">
    $('#tablet-window').window({
        fit:true,
        noheader:true,
        border:false,
        collapsible:false,
        minimizable:false,
        maximizable:false,
        resizable:false,
        closable:false,
        maximized:true,
        closed:false
    });

    $('#tablet-layout').layout({fit:true});
    $('#tablet-layout').layout('panel','west').panel({
        onExpand: function() {
            ac_clientOpts.DataEntryEnabled=false;
            $('#tdialog-fieldset').prop('disabled',true);
            // retrieve original data from parent datagrid
            var dgname=$('#tdialog-Parent').val();
            var dg=$(dgname);
            // refresh layout
            var h=dg.datagrid('getPanel').panel('options').height;
            var w=dg.datagrid('getPanel').panel('options').width;
            setTimeout(function() {dg.datagrid('resize',{height:h,width:w})},0);
        },
        onCollapse: function () {
            ac_clientOpts.DataEntryEnabled=true;
            $('#tdialog-fieldset').prop('disabled',false);
        }
    });


    $('#tablet-datagrid').datagrid({
        // propiedades del panel asociado
        expandedRow: -1, // added by jamc
        fit: true,
        border: false,
        closable: false,
        collapsible: false,
        collapsed: false,
        // propiedades del datagrid
        method: 'get',
        url: '../server/database/tandasFunctions.php',
        queryParams: {
            Operation: 'getTandas',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Sesion: (workingData.sesion==1)?1:-(workingData.sesion)
        },
        toolbar:'#tablet-toolbar',
        loadMsg: "<?php _e('Updating series order');?>"+" ...",
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: false,
        view: detailview,
        pageSize: 100, // enought bit to make it senseless
        columns:[[
            { field:'ID',		hidden:true },
            { field:'Sesion',	hidden:true },
            { field:'Prueba',	hidden:true },
            { field:'Jornada',	hidden:true },
            { field:'Manga',	hidden:true },
            { field:'Categoria',hidden:true },
            { field:'Grado',	hidden:true },
            { field:'Sesion',	hidden:true },
            { field:'Tipo',	    hidden:true },
            { field:'Horario',	width:125, sortable:false, align:'center', title:'<?php _e('Hour');?>',styler:tandasStyler },
            { field:'Nombre',	width:225, sortable:false, align:'left',title:'<?php _e('Activity');?>',styler:tandasStyler},
            { field:'Comentario',	width:75, sortable:false, align:'left',title:'<?php _e('Comments');?>',styler:tandasStyler}
        ]],
        rowStyler: myRowStyler,
        // especificamos un formateador especial para desplegar la tabla de perros por tanda
        detailFormatter:function(idx,row){
            var dg="tablet-datagrid-" + parseInt(row.ID);
            return '<div style="padding:2px"><table id="' + dg + '"></table></div>';
        },
        onClickRow: function(idx,row) {
            $('#tablet-datagrid').datagrid('expandRow',idx); // fire up onExpandRow event
        },
        onExpandRow: function(idx,row) {
            row.expanded=1;
            doBeep();
            var dg=$('#tablet-datagrid');
            // collapse previous expanded row
            var oldRow=dg.datagrid('options').expandedRow;
            if ( (oldRow!==-1) && (oldRow!==idx) )  {
                var old=dg.datagrid('getRows')[oldRow];
                if (old['Tipo']!=="0") { // rows with type 0 has no subgrid
                    $("#tablet-datagrid-"+old['ID']).datagrid('unselectAll');
                }
                dg.datagrid('collapseRow',oldRow);
            }
            dg.datagrid('options').expandedRow=idx;
            // update session dataassistant
            tablet_updateSession(row);
            if (row.Tipo!=="0") setTimeout( function(){tablet_showPerrosByTanda(idx,row)},0);
        },
        onCollapseRow: function(idx,row) {
            row.expanded=0;
            if (row.Tipo!=="0") { // rows with type=0 has no subgrid
                var dg="#tablet-datagrid-" + parseInt(row.ID);
                $(dg).datagrid('unselectAll');
            }
            doBeep();
        }
    });

    // mostrar los perros de una tanda
    function tablet_showPerrosByTanda(index,row){
        // - sub tabla orden de salida de una tanda
        var tbt_dg=$('#tablet-datagrid');
        var mySelfstr='#tablet-datagrid-'+row.ID;
        var mySelf=$(mySelfstr);
        mySelf.datagrid({
            method: 'get',
            url: '../server/database/tandasFunctions.php',
            queryParams: {
                Operation: 'getDataByTanda',
                Prueba: row.Prueba,
                Jornada: row.Jornada,
                Sesion: row.Sesion,
                ID:row.ID
            },
            loadMsg: '<?php _e("Update starting order");?>'+" ...",
            pagination: false,
            rownumbers: true,
            // expand to all 800pixels, do not fitColums to available space
            width: '1000',
            height: '400',
            fit:false,
            fitColumns: true,
            singleSelect: true,
            autoRowHeight: false,
            remote:true,
            idField:'Dorsal',
            columns:[[
                { field:'Parent',		width:0, hidden:true }, // self reference to row index
                { field:'Prueba',		width:0, hidden:true }, // extra field to be used on form load/save
                { field:'Jornada',		width:0, hidden:true }, // extra field to be used on form load/save
                { field:'Manga',		width:0, hidden:true },
                { field:'Tanda',		width:0, hidden:true }, // string with tanda's name
                { field:'ID',			width:0, hidden:true }, // tanda ID
                { field:'Perro',		width:0, hidden:true },
                { field:'Licencia',		width:0, hidden:true },
                { field:'Pendiente',	width:0, hidden:true },
                { field:'Equipo',		width:0, hidden:true },
                { field:'NombreEquipo',	width:20, align:'center',	title: '<?php _e('Team');?>' },
                { field:'Dorsal',		width:10, align:'center',	title: '<?php _e('Dorsal');?>', styler:checkPending },
                { field:'Nombre',		width:20, align:'left',		title: '<?php _e('Name');?>', formatter:formatBold},
                { field:'NombreLargo',	width:0, hidden:true },
                { field:'Celo',			width:8, align:'center',	title: '<?php _e('Heat');?>', formatter:formatCelo},
                { field:'NombreGuia',	width:35, align:'right',	title: '<?php _e('Handler');?>' },
                { field:'NombreClub',	width:25, align:'right',	title: '<?php _e('Club');?>' },
                { field:'Categoria',	width:10, align:'center',	title: '<?php _e('Cat');?>.' ,formatter:formatCategoria},
                { field:'Grado',		width:10, align:'center',	title: '<?php _e('Grade');?>', formatter:formatGrado },
                { field:'Faltas',		width:5, align:'center',	title: 'F'},
                { field:'Rehuses',		width:5, align:'center',	title: 'R'},
                { field:'Tocados',		width:5, align:'center',	title: 'T'},
                { field:'Tiempo',		width:15, align:'right',	title: '<?php _e('Time');?>'	},
                { field:'TIntermedio',	width:0, hidden:true },
                { field:'Eliminado',	width:5, align:'center',	formatter:formatEliminado,	title: 'EL.'},
                { field:'NoPresentado',	width:5, align:'center',	formatter:formatNoPresentado,	title: 'NP'},
                { field:'Observaciones',width:0, hidden:true }
            ]],
            // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
            rowStyler:myRowStyler,
            onClickRow: function(idx,data) {
                doBeep();
                data.Session=workingData.sesion;
                data.Parent=mySelfstr; // store datagrid reference
                data.RowIndex=idx; // store row index
                $('#tdialog-form').form('load',data);
                setDataEntryEnabled(true);
                tablet_markSelectedDog(data.RowIndex);
            },
            onResize:function(){
                tbt_dg.datagrid('fixDetailRowHeight',index);
            },
            onLoadSuccess:function(data){
                if (!data.total) return; // subgrid returns an empty array. Do nothing
                // populate data entry datagrid with loaded data
                $('#tdialog-tnext').datagrid('loadData',data.rows);
                // show/hide team name
                if (isJornadaEquipos(null) ) mySelf.datagrid('showColumn','NombreEquipo');
                else  mySelf.datagrid('hideColumn','NombreEquipo');
                // auto resize columns
                setTimeout(function(){ tbt_dg.datagrid('fixDetailRowHeight',index); },0);
                <?php if (toBoolean($config->getEnv('tablet_dnd'))) { ?>
                    mySelf.datagrid('enableDnd');
                    mySelf.datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
                <?php } else { /* if dnd is off enable only on PC */?>
                    if (! isMobileDevice() ) {
                        mySelf.datagrid('enableDnd');
                        mySelf.datagrid('getPanel').panel('panel').attr('tabindex',0).focus();
                    }
                <?php } ?>
            },
            onDragEnter: function(dst,src) {
                if (dst.Manga!=src.Manga) return false;
                if (dst.Categoria!=src.Categoria) return false;
                if (dst.Grado!=src.Grado) return false;
                if (dst.Celo!=src.Celo) return false;
                return true;
            },
            onDrop: function(dst,src,updown) {
                // reload el orden de salida en la manga asociada
                workingData.prueba=src.Prueba;
                workingData.jornada=src.Jornada;
                workingData.manga=src.Manga;
                dragAndDropOrdenSalida(
                        src.Perro,
                        dst.Perro,
                        (updown==='top')?0:1,
                        function()  { mySelf.datagrid('reload'); }
                );
                return false;
            }
        });
        tbt_dg.datagrid('fixDetailRowHeight',index);
    }

    $('#tdialog-form').form({
        // tell session manager to update competitor's info
        onLoadSuccess: function() {
            var el=parseInt($('#tdialog-Eliminado').val());
            var np=parseInt($('#tdialog-NoPresentado').val());
            $('#tdialog-EliminadoStr').val((el===0)?"":"EL");
            $('#tdialog-NoPresentadoStr').val((np===0)?"":"NP");
            tablet_putEvent('llamada',
                    { // setup initial data for event,
                        'TimeStamp'     : Math.floor(Date.now()/1000),
                        'NoPresentado'	: np,
                        'Faltas'		: $('#tdialog-Faltas').val(),
                        'Tocados'		: $('#tdialog-Tocados').val(),
                        'Rehuses'		: $('#tdialog-Rehuses').val(),
                        'Tiempo'		: $('#tdialog-Tiempo').val(),
                        'Eliminado'		: el,
                        'Celo'			: $('#tdialog-Celo').val(),
                        'Dorsal'		: $('#tdialog-Dorsal').val(),
                        'Value'         : 0,
                        // include additional textual info
                        'Numero'        : 1+parseInt($('#tdialog-RowIndex').val()),
                        'Nombre'        : $('#tdialog-Nombre').val(),
                        'NombreLargo'   : $('#tdialog-NombreLargo').val(),
                        'NombreGuia'    : $('#tdialog-Guia').val(),
                        'NombreClub'    : $('#tdialog-Club').val(),
                        'NombreEquipo'  : $('#tdialog-NombreEquipo').val(),
                        'Categoria'     : $('#tdialog-Categoria').val(),
                        'Grado'         : $('#tdialog-Grado').val()
                    }
            ) ;
        }
    });

    //create a Chronometer instance
    if (ac_config.tablet_chrono) {
        $('#cronometro').Chrono( {
            seconds_sel: '#tdialog-timestamp',
            auto: false,
            interval: 50,
            showMode: 2,
            onUpdate: function(elapsed,running,pause) {
                $('#tdialog-Tiempo').val(toFixedT(parseFloat(elapsed/1000),(running)?1:ac_config.numdecs));
                return true;
            }
        });
    }

    // creamos la tabla de proximos a salir
    $('#tdialog-tnext').datagrid({
        pagination: false,
        rownumbers: true,
        fit:true,
        fitColumns: true,
        singleSelect: true,
        autoRowHeight: true,
        columns:[[
            { field:'Dorsal',width:'10%', align:'right',	title: '<?php _e('Dorsal');?>' },
            { field:'Nombre',width:'25%', align:'right',	title: '<?php _e('Name');?>' },
            { field:'NombreGuia',	width:'35%', align:'right',	title: '<?php _e('Handler');?>' },
            { field:'NombreClub',	width:'25%', align:'right',	title: '<?php _e('Club');?>' }
        ]],
        onDblClickRow: function(index,row) {
            // check for store before change dog
            if (parseInt(ac_config.tablet_dblclick)==1){
                // retrieve parent datagrid to update results
                var dgname = $('#tdialog-Parent').val();
                var dg = $(dgname);
                tablet_save(dg);
            }
            // and go to selected dog
            $('#tablet-datagrid-search').val(row.Dorsal);
            tablet_editByDorsal();
        }
    });

    addTooltip($('#tablet-reloadBtn').linkbutton(),'<?php _e("Update session data");?>');
    addTooltip($('#tablet-whiteBtn').linkbutton(),'<?php _e("Mark test dog enter into ring");?>');
    addTooltip($('#tablet-recoBtn').linkbutton(),'<?php _e("Tell chronometer to start Course walk");?>');
    addTooltip($('#tablet-datagrid-search'),'<?php _e("Activate data entry panel on selected dorsal");?>');

    // layout
    var dg= {'cols':210, 'rows':145}; // declare base datagrid as A5 sheet
    doLayout(dg,"#tdialog-fieldset",        0,      0,      210,    145 );
    doLayout(dg,"#tdialog-FaltaUpBtn",		5,		18,		30,		62	);
    doLayout(dg,"#tdialog-FaltaDownBtn",	5,		5,		30,		10	);
    doLayout(dg,"#tdialog-RehuseUpBtn",		171,	18,		30,		62	);
    doLayout(dg,"#tdialog-RehuseDownBtn",	171,	5,		30,		10	);
    doLayout(dg,"#tdialog-TocadoUpBtn",		40,		5,      22,		20	);
    doLayout(dg,"#tdialog-TocadoDownBtn",	65,		5,      15,		20	);
    doLayout(dg,"#tdialog-User0Btn",	    83,		5,      16,		9	);
    doLayout(dg,"#tdialog-User1Btn",	    100,	5,      16,		9	);
    doLayout(dg,"#tdialog-User2Btn",	    83,		16,     16,		9	);
    doLayout(dg,"#tdialog-User3Btn",	    100,	16,     16,		9	);
    doLayout(dg,"#tdialog-SalidaBtn",		163,	87,		17,		22	);
    doLayout(dg,"#tdialog-ResetBtn",		144,    87,	    17,		22	);
    doLayout(dg,"#tdialog-StartStopBtn",	182,	87,		19,		22	);
    doLayout(dg,"#tdialog-AcceptBtn",		171,	115,	30,		25	);
    doLayout(dg,"#tdialog-CancelBtn",		147,	115,	18,		25	);
    doLayout(dg,"#tdialog-NoPresentadoBtn",	119,    5,		22,		20	);
    doLayout(dg,"#tdialog-EliminadoBtn",	144,	5,		22,		20	);
    doLayout(dg,"#tdialog-Next",	        5,	    87, 	70,		53	);
    doLayout(dg,"#tdialog-1",				80,		80,		20,		15	);
    doLayout(dg,"#tdialog-2",				100,	80,		20,		15	);
    doLayout(dg,"#tdialog-3",				120,	80,		20,		15	);
    doLayout(dg,"#tdialog-4",				80,		95,		20,		15	);
    doLayout(dg,"#tdialog-5",				100,	95,		20,		15	);
    doLayout(dg,"#tdialog-6",				120,	95,		20,		15	);
    doLayout(dg,"#tdialog-7",				80,		110,	20,		15	);
    doLayout(dg,"#tdialog-8",				100,	110,	20,		15	);
    doLayout(dg,"#tdialog-9",				120,	110,	20,		15	);
    doLayout(dg,"#tdialog-Del",				120,	125,	20,		15	);
    doLayout(dg,"#tdialog-0",				100,	125,	20,		15	);
    doLayout(dg,"#tdialog-Dot",				80,		125,	20,		15	);
    doLayout(dg,"#tdialog-DorsalLbl",		53,		38,		10,		7	);
    doLayout(dg,"#tdialog-Dorsal",			65,		37,		18,		7	);
    doLayout(dg,"#tdialog-NombreLbl",		85,	    38,		20,		7   );
    doLayout(dg,"#tdialog-Nombre",			100,	37,		63,		7	);
    doLayout(dg,"#tdialog-GuiaLbl",			53,		45,		10,		7	);
    doLayout(dg,"#tdialog-Guia",			65,		44,		45,		7	);
    doLayout(dg,"#tdialog-ClubLbl",			110,	45,		15,		7	);
    doLayout(dg,"#tdialog-Club",			120,	44,		43,		7	);
    doLayout(dg,"#tdialog-CategoriaLbl",	53,		52,		10,		7	);
    doLayout(dg,"#tdialog-Categoria",		65,		51,		15,		7	);
    doLayout(dg,"#tdialog-GradoLbl",		85,		52,		10,		7	);
    doLayout(dg,"#tdialog-Grado",			100,	51,		10,		7	);
    doLayout(dg,"#tdialog-CeloLbl",			110,	52,		15,		7	);
    doLayout(dg,"#tdialog-Celo",			120,	51,		20,		7	);
    doLayout(dg,"#tdialog-FaltasLbl",		50,		74,		10,		5	);
    doLayout(dg,"#tdialog-Faltas",			50,		60,		10,		15	);
    doLayout(dg,"#tdialog-TocadosLbl",		65,		74,		10,		5	);
    doLayout(dg,"#tdialog-Tocados",			65,		60,		10,		15	);
    doLayout(dg,"#tdialog-RehusesLbl",		80,		74,		10,		5	);
    doLayout(dg,"#tdialog-Rehuses",			80,		60,		10,		15	);
    doLayout(dg,"#tdialog-TiempoLbl",		95,		74,		35,		5	);
    doLayout(dg,"#tdialog-Tiempo",			95,		60,		35,		15	);
    doLayout(dg,"#tdialog-NoPresentadoLbl",	135,	74,		10,		5	);
    doLayout(dg,"#tdialog-NoPresentadoStr",	135,	60,		10,		15	);
    doLayout(dg,"#tdialog-EliminadoLbl",	150,	74,		10,		5	);
    doLayout(dg,"#tdialog-EliminadoStr",	150,	60,		10,		15	);
    doLayout(dg,"#tdialog-InfoLbl",		    42,		30,		121,	5   );
    doLayout(dg,"#tdialog-NumberLbl",	    42,		38,		9,	    15  );
    doLayout(dg,"#tdialog-Rectangulo",		40,		28,		126,	51  );

</script>

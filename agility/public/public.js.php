/*
public.js

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/*********************************************** funciones de formateo de pantalla */
<?php
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
$config =Config::getInstance();
?>
/**
 * Presenta el logo en pantalla
 * @param {int} val nombre delo logo
 * @param {Object} row datos de la fila
 * @param {int} idx indice de la fila
 * @returns {string} texto html a imprimir
 */
function formatLogoPublic(val,row,idx) {
    // TODO: no idea why idx:0 has no logo declared
    if (typeof(val)==='undefined') return '<img height="30" alt="empty.png" src="/agility/images/logos/empty.png"/>';
    return '<img height="30" alt="'+val+'" src="/agility/images/logos/'+val+'"/>';
}

/**
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 */
function pb_getHeaderInfo() {
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/publicFunctions.php",
        data: {
            Operation: 'infodata',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            var str='<?php _e("Contest"); ?>' + ': ' + data.Prueba.Nombre + '<br />' + '<?php _e("Journey"); ?>' + ': '+ data.Jornada.Nombre;
            $('#pb_header-infocabecera').html(str);
            // on international competitions, use federation Organizer logo
            var logo='/agility/images/logos/'+data.Club.Logo;
            if ( (data.Club.logo==="") || isInternational(data.Prueba.RSCE)) {
                logo=ac_fedInfo[data.Prueba.RSCE].OrganizerLogo
            }
            $('#pb_header-logo').attr('src',logo);
        }
    });
}

function pb_setFooterInfo() {
    var logo=ac_fedInfo[workingData.federation].Logo;
    var logo2=ac_fedInfo[workingData.federation].ParentLogo;
    var url=ac_fedInfo[workingData.federation].WebURL;
    var url2=ac_fedInfo[workingData.federation].ParentWebURL;
    $('#pb_footer-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
        $('#pb_footer-logoFederation').attr('src',logo);
        $('#pb_footer-urlFederation').attr('href',url);
        $('#pb_footer-logoFederation2').attr('src',logo2);
        $('#pb_footer-urlFederation2').attr('href',url2);
    });
}

function pb_updateOrdenSalida2(id) {
    $('#pb_ordensalida-datagrid').datagrid('reload',{
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID:  id // Tanda ID
    });
}

/**
 * Imprime el orden de salida de la prueba y jornada seleccionada por el usuario
 * ejecutada desde la ventana con combogrid
 */
function pb_updateOrdenSalida() {
    var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
    if (row) pb_updateOrdenSalida2(row.ID);
}

/**
 * Imprime los inscritos en la prueba y jornada seleccionada por el usuario
 */
function pb_updateInscripciones() {
    $('#pb_inscripciones-datagrid').datagrid('reload', {
        Operation:'inscritosbyjornada',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada
    });
}

/**
 * Imprime los inscritos en la prueba y jornada por equipos seleccionada por el usuario
 */
function pb_updateInscripciones_eq3() {
    $('#pb_inscripciones_eq3-datagrid').datagrid('reload', {
        Operation:'select',
        Prueba:workingData.prueba,
        Jornada:workingData.jornada,
        where:''
    });
}

/**
 * imprime el programa de la jornada
 */
function pb_updatePrograma() {
    $('#pb_programa-datagrid').datagrid('reload',{
        Operation: 'getTandas',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 0 // Set Session ID to 0 to include everything
    });
}

/**
 * Actualiza los datos de TRS y TRM de la fila especificada
 * Rellena tambien el datagrid de resultados parciales
 */
function pb_updateParciales2(mode) {
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	'getResultados',
            Prueba:		workingData.prueba,
            Jornada:	workingData.jornada,
            Manga:		workingData.manga,
            Mode:       parseInt(mode)
        },
        success: function(dat) {
            // informacion de la manga
            // use html() instead of text() to handle html special chars
            $('#pb_parciales-NombreManga').html(workingData.nombreManga);
            $('#pb_parciales-Juez1').html((dat['manga'].Juez1<=1)?"":'<?php _e('Judge');?> 1: ' + dat['manga'].NombreJuez1);
            $('#pb_parciales-Juez2').html((dat['manga'].Juez2<=1)?"":'<?php _e('Judge');?> 2: ' + dat['manga'].NombreJuez2);
            // datos de TRS
            $('#pb_parciales-Distancia').html('<?php _e('Distance');?>: ' + dat['trs'].dist + 'm.');
            $('#pb_parciales-Obstaculos').html('<?php _e('Obstacles');?>: ' + dat['trs'].obst);
            $('#pb_parciales-TRS').html('<?php _e('Standard C. Time');?>: ' + dat['trs'].trs + 's.');
            $('#pb_parciales-TRM').html('<?php _e('Maximum C. Time');?>: ' + dat['trs'].trm + 's.');
            $('#pb_parciales-Velocidad').html('<?php _e('Speed');?>: ' + dat['trs'].vel + 'm/s');
            // actualizar datagrid
            workingData.teamCounter=1; // reset team's puesto counter
            $('#pb_parciales-datagrid').datagrid('loadData',dat);
        }
    });
}

function pb_updateParciales() {
    // obtenemos la manga seleccionada. if no selection return
    var row=$('#pb_enumerateParciales').combogrid('grid').datagrid('getSelected');
    if (!row) return;
    setManga(row);
    pb_updateParciales2(row.Mode);
}

/**
 * Actualiza datos de la clasificacion general
 */
function pb_updateFinales2(ronda) {
    // do not call pb_doResults cause expected json data
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {
            Operation: (isJornadaEquipos())?'clasificacionEquipos':'clasificacionIndividual',
			Prueba:	ronda.Prueba,
			Jornada:ronda.Jornada,
			Manga1:	ronda.Manga1,
			Manga2:	ronda.Manga2,
			Rondas: ronda.Rondas,
			Mode: 	ronda.Mode
		},
		success: function(dat) {
            // nombres de las mangas
            $('#pb_finales-NombreRonda').html(ronda.Nombre);
            $('#pb_resultados_thead_m1').html(ronda.NombreManga1);
            $('#pb_resultados_thead_m2').html(ronda.NombreManga2);
            // datos de los jueces
            $('#pb_finales-Juez1').html((dat['jueces'][0]=="-- Sin asignar --")?"":'<?php _e('Judge');?> 1: ' + dat['jueces'][0]);
            $('#pb_finales-Juez2').html((dat['jueces'][1]=="-- Sin asignar --")?"":'<?php _e('Judge');?> 2: ' + dat['jueces'][1]);
            // datos de trs manga 1
            $('#pb_finales-Ronda1').html(ronda.NombreManga1);
            $('#pb_finales-Distancia1').html('<?php _e('Distance');?>: ' + dat['trs1'].dist + 'm.');
            $('#pb_finales-Obstaculos1').html('<?php _e('Obstacles');?>: ' + dat['trs1'].obst);
            $('#pb_finales-TRS1').html('<?php _e('Standard C. Time');?>: ' + dat['trs1'].trs + 's.');
            $('#pb_finales-TRM1').html('<?php _e('Maximum C. Time');?>: ' + dat['trs1'].trm + 's.');
            $('#pb_finales-Velocidad1').html('<?php _e('Speed');?>: ' + dat['trs1'].vel + 'm/s');
            // datos de trs manga 2
            if (ronda.Manga2==0) { // single round
                $('#pb_finales-Ronda2').html("");
                $('#pb_finales-Distancia2').html("");
                $('#pb_finales-Obstaculos2').html("");
                $('#pb_finales-TRS2').html("");
                $('#pb_finales-TRM2').html("");
                $('#pb_finales-Velocidad2').html("");
            } else {
                $('#pb_finales-Ronda2').html(ronda.NombreManga2);
                $('#pb_finales-Distancia2').html('<?php _e('Distance');?>: ' + dat['trs2'].dist + 'm.');
                $('#pb_finales-Obstaculos2').html('<?php _e('Obstacles');?>: ' + dat['trs2'].obst);
                $('#pb_finales-TRS2').html('<?php _e('Standard C. Time');?>: ' + dat['trs2'].trs + 's.');
                $('#pb_finales-TRM2').html('<?php _e('Maximum C. Time');?>: ' + dat['trs2'].trm + 's.');
                $('#pb_finales-Velocidad2').html('<?php _e('Speed');?>: ' + dat['trs2'].vel + 'm/s');
            }
            // clasificaciones
            var dg=$('#pb_finales-datagrid');
            if ( isJornadaEquipos() ) {
                workingData.individual=dat.individual;
                dg.datagrid('options').expandCount = 0;
                dg.datagrid('loadData',dat.equipos);
            } else {
                workingData.individual=dat.rows;
                dg.datagrid('loadData',dat.rows);
            }
		}
	});
}

function pb_updateFinales() {
    var ronda=$('#pb_enumerateFinales').combogrid('grid').datagrid('getSelected');
    if (ronda==null) {
        // $.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
        return; // no way to know which ronda is selected
    }
    pb_updateFinales2(ronda);
}

function pb_showClasificacionesByTeam(idx,row) {

    var parent="#pb_finales-datagrid";
    var mySelf="#pb_finales-datagrid-"+parseInt(row.ID);
    // retrieve datagrid contents
    var datos=[];
    for(var n=0; n<workingData.individual.length;n++) {
        var competitor=workingData.individual[n];
        if (competitor['Equipo']!=row.ID) continue;
        datos.push(competitor);
    }
    // deploy datagrid
    $(mySelf).datagrid({
        fit:false,
        pagination: false,
        rownumbers: false,
        fitColumns: true,
        singleSelect: true,
        width: '100%',
        height: 'auto',
        remote:false,
        idField: 'Perro',
        data: datos,
        columns: [[                
            {field:'Perro',		hidden:true },
            {field:'Equipo',	hidden:true },
            {field:'Dorsal',	width:'3%', align:'left',     title:"<?php _e('Dors'); ?>" },
            {field:'LogoClub',	hidden:true },
            {field:'Nombre',	width:'9%', align:'center',   title:"<?php _e('Name'); ?>",   formatter:formatBold},
            {field:'Licencia',	width:'4%', align:'center',   title:"<?php _e('Lic'); ?>." },
            {field:'Categoria',	width:'4%', align:'center',   title:"<?php _e('Cat'); ?>.",   formatter:formatCategoria },
            {field:'Grado',	hidden:true },
            {field:'NombreEquipo',hidden:true },
            {field:'NombreGuia',width:'13%', align:'right',    title:"<?php _e('Handler'); ?>" },
            {field:'NombreClub',width:'12%', align:'right',    title:"<?php _e('Club'); ?>" },
            {field:'F1',		width:'2%', align:'center',   title:"<?php _e('F/T'); ?>",    styler:formatBorder },
            {field:'R1',		width:'2%', align:'center',   title:"R." },
            {field:'T1',		width:'5%', align:'right',    title:"<?php _e('Time'); ?>.",  formatter:formatT1 },
            {field:'V1',		width:'3%', align:'right',    title:"<?php _e('Vel'); ?>.",   formatter:formatV1 },
            {field:'P1',		width:'5%', align:'right',    title:"<?php _e('Penal'); ?>.", formatter:formatP1},
            {field:'C1',		width:'3%', align:'center',   title:"<?php _e('Cal'); ?>."},
            {field:'F2',		width:'2%', align:'center',   title:"<?php _e('F/T'); ?>",    styler:formatBorder },
            {field:'R2',		width:'2%', align:'center',   title:" <?php _e('R'); ?>." },
            {field:'T2',		width:'5%', align:'right',    title:"<?php _e('Time'); ?>.",  formatter:formatT2 },
            {field:'V2',		width:'3%', align:'right',    title:"<?php _e('Vel'); ?>.",   formatter:formatV2 },
            {field:'P2',		width:'5%', align:'right',    title:"<?php _e('Penal'); ?>.", formatter:formatP2 },
            {field:'C2',		width:'3%', align:'center',   title:"<?php _e('Cal'); ?>." },
            {field:'Tiempo',	width:'4%', align:'right',    title:"<?php _e('Time'); ?>",   formatter:formatTF,styler:formatBorder },
            {field:'Penalizacion',width:'5%',align:'right',   title:"<?php _e('Penaliz'); ?>.",formatter:formatPenalizacionFinal },
            {field:'Calificacion',width:'3%',align:'center',  title:"<?php _e('Calif'); ?>." },
            {field:'Puesto',	width:'3%', align:'center',   title:"<?php _e('Position'); ?>",formatter:formatBold }
        ]],
        // colorize rows. notice that overrides default css, so need to specify proper values on datagrid.css
        rowStyler:myRowStyler,
        onResize:function(){
            $(parent).datagrid('fixDetailRowHeight',idx);
        },
        onLoadSuccess:function(data){
            setTimeout(function(){ $(parent).datagrid('fixDetailRowHeight',idx); },0);
        }
    });
    $(parent).datagrid('fixDetailRowHeight',idx);
}
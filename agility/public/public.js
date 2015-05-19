/*
public.js

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

/**
 * Obtiene la informacion de la prueba para cabecera y pie de pagina
 */
function pb_getHeaderInfo() {
    $.ajax( {
        type: "GET",
        dataType: 'json',
        url: "/agility/server/web/public.php",
        data: {
            Operation: 'infodata',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            var str='Prueba: ' + data.Prueba.Nombre+" <br /> Jornada: "+ data.Jornada.Nombre;
            $('#pb_header-infocabecera').html(str);
            // TODO: fix logo when undefined or invalid
            $('#pb_header-logo').attr('src','/agility/images/logos/'+data.Club.Logo);
        }
    });
}

function pb_setFooterInfo() {
    var logo=nombreCategorias[workingData.federation]['logo'];
    var logo2=nombreCategorias[workingData.federation]['logo2'];
    var url=nombreCategorias[workingData.federation]['url'];
    var url2=nombreCategorias[workingData.federation]['url2'];
    $('#pb_footer-footerData').load("/agility/public/pb_footer.php",{},function(response,status,xhr){
        $('#pb_footer-logoFederation').attr('src','/agility/images/logos/'+logo);
        $('#pb_footer-urlFederation').attr('href',url);
        $('#pb_footer-logoFederation2').attr('src','/agility/images/logos/'+logo2);
        $('#pb_footer-urlFederation2').attr('href',url2);
    });
}

/**
 * Funcion generica para efectuar todas las llamadas al servidor
 * @param {string} url direccion web
 * @param {string} id Identificador jquery donde insertar el resultado
 */
function pb_doRequest(url,operation,id) {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: url,
        data: {
            Operation: operation,
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            $(id).html(data);
        }
    });
}

/**
 * Imprime el orden de salida de la prueba y jornada seleccionada por el usuario
 */
function pb_updateOrdenSalida() {
    var row=$('#pb_enumerateMangas').combogrid('grid').datagrid('getSelected');
    if (!row) return;
    $('#pb_ordensalida-datagrid').datagrid('reload',{
        Operation: 'getDataByTanda',
        Prueba: workingData.prueba,
        Jornada: workingData.jornada,
        Sesion: 1, // defaults to "-- sin asignar --"
        ID:  row.ID // Tanda ID
    });
};

/**
 * Imprime los inscritos en la prueba y jornada seleccionada por el usuario
 */
function pb_updateInscripciones() {
    pb_doRequest("/agility/server/web/public.php",'inscripciones','#pb_inscripcionesJornada');
}

function pb_updateInscripciones_eq3() {
    $('#pb_equipos3-datagrid').datagrid('reload', {
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
        Sesion: 1 // Set Session ID to 1 to include all
    });
}

/**
 * Actualiza los datos de TRS y TRM de la fila especificada
 * Rellena tambien el datagrid de resultados parciales
 */
function pb_updateResults() {
    // obtenemos la manga seleccionada. if no selection return
    var row=$('#pb_enumerateParciales').combogrid('grid').datagrid('getSelected');
    if (!row) return;
    workingData.manga=row.Manga;
    workingData.datosManga=row;
    workingData.tanda=0; // fake tanda. use manga+mode to evaluate results
    workingData.mode=row.Mode;
    // en lugar de invocar al datagrid, lo que vamos a hacer es
    // una peticion ajax, para obtener a la vez los datos tecnicos de la manga
    // y de los jueces
    $.ajax({
        type:'GET',
        url:"/agility/server/database/resultadosFunctions.php",
        dataType:'json',
        data: {
            Operation:	'getResultados',
            Prueba:		row.Prueba,
            Jornada:	row.Jornada,
            Manga:		row.Manga,
            Mode:       row.Mode
        },
        success: function(dat) {
            $('#pb_parciales-NombreManga').text(row.Nombre);
            $('#pb_parciales-Juez1').text((dat['manga'].Juez1<=1)?"":'Juez 1: ' + dat['manga'].NombreJuez1);
            $('#pb_parciales-Juez2').text((dat['manga'].Juez2<=1)?"":'Juez 2: ' + dat['manga'].NombreJuez2);
            $('#pb_parciales-NombreManga').text(row.Nombre);
            $('#pb_parciales-Distancia').text('Distancia: ' + dat['trs'].dist + 'm.');
            $('#pb_parciales-Obstaculos').text('Obstaculos: ' + dat['trs'].obst);
            $('#pb_parciales-TRS').text('T.R.Standard: ' + dat['trs'].trs + 's.');
            $('#pb_parciales-TRM').text('T.T.Maximo: ' + dat['trs'].trm + 's.');
            $('#pb_parciales-Velocidad').text('Velocidad: ' + dat['trs'].vel + 'm/s');
            $('#pb_parciales-datagrid').datagrid('loadData',dat);
        }
    });
}

/**
 * Actualiza datos de la clasificacion general
 */
function pb_updateFinales() {
	var ronda=$('#pb_enumerateFinales').combogrid('grid').datagrid('getSelected');
	if (ronda==null) {
    	// $.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return; // no way to know which ronda is selected
	}
    // do not call pb_doResults cause expected json data
	$.ajax({
		type:'GET',
		url:"/agility/server/database/clasificacionesFunctions.php",
		dataType:'json',
		data: {	
			Prueba:	ronda.Prueba,
			Jornada:ronda.Jornada,
			Manga1:	ronda.Manga1,
			Manga2:	ronda.Manga2,
			Rondas: ronda.Rondas,
			Mode: 	ronda.Mode
		},
		success: function(dat) {
			$('#pb_resultados_thead_m1').text(ronda.NombreManga1);
			$('#pb_resultados_thead_m2').text(ronda.NombreManga2);
			$('#pb_resultados-datagrid').datagrid('loadData',dat);
		}
	});
}
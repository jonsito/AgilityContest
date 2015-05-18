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
 * Actualiza la informacion sobre resultados parciales
 */
function pb_updateResults() {
    pb_doRequest("/agility/server/web/public.php",'resultados','#pb_resultadosParciales');
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
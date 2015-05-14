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
 * Imprime el orden de salida de la prueba y jornada seleccionada por el usuario
 */
function pb_updateOrdenSalida() {
    $.ajax( {
        type: "GET",
        dataType: 'html',
        url: "/agility/server/web/public.php",
        data: {
            Operation: 'ordensalida',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada,
            Manga: workingData.manga,
            Tanda: workingData.tanda,
            Mode: workingData.mode
        },
        success: function(data,status,jqxhr) {
            $('#pb_ordensalidaData').html(data);
            var str='Prueba: ' + $('#pb_NombrePrueba').val()+" <br /> Jornada: "+$('#pb_NombreJornada').val();
            $('#pb_ordensalida-infocabecera').html(str);
            $('#pb_ordensalida-logo').attr('src',$('#pb_LogoClub').val());
        }
    });
}
/**
 * Imprime los inscritos en la prueba y jornada seleccionada por el usuario
 */
function pb_updateInscripciones() {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/web/public.php",
		data: {
			Operation: 'inscripciones',
			Prueba: workingData.prueba,
			Jornada: workingData.jornada
		},
		success: function(data,status,jqxhr) {
			$('#pb_inscripcionesJornada').html(data);
            var str='Prueba: ' + $('#pb_NombrePrueba').val()+" <br /> Jornada: "+$('#pb_NombreJornada').val();
			$('#pb_inscripciones-infocabecera').html(str);
            $('#pb_inscripciones-logo').attr('src',$('#pb_LogoClub').val());
		}
	});
}

/**
 * imprime el programa de la jornada
 */
function pb_updatePrograma() {
    $.ajax( {
        type: "GET",
        dataType: 'html',
        url: "/agility/server/web/public.php",
        data: {
            Operation: 'programa',
            Prueba: workingData.prueba,
            Jornada: workingData.jornada
        },
        success: function(data,status,jqxhr) {
            $('#pb_programa-Jornada').html(data);
            var str='Prueba: ' + $('#pb_NombrePrueba').val()+" <br /> Jornada: "+$('#pb_NombreJornada').val();
            $('#pb_programa-infocabecera').html(str);
            $('#pb_programa-logo').attr('src',$('#pb_LogoClub').val());
        }
    });
}
function pb_updateResults() {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/web/public.php",
		data: {
			Operation: 'resultados',
			Prueba: workingData.prueba,
			Jornada: workingData.jornada,
			Manga: workingData.manga,
			Tanda: workingData.tanda,
			Mode: workingData.mode
		},
		success: function(data,status,jqxhr) {
			$('#pb_resultadosParciales').html(data);
		}
	});
}

function pb_updateFinales() {
	var ronda=$('#pb_enumerateFinales').combogrid('grid').datagrid('getSelected');
	if (ronda==null) {
    	// $.messager.alert("Error:","!No ha seleccionado ninguna ronda de esta jornada!","warning");
    	return; // no way to know which ronda is selected
	}
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
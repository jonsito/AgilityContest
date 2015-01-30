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
 * Imprime los inscritos en la jornada marcada por la sesion activa
 * @param jornada
 */
function pb_updateInscripciones() {
	// var t=new Date().getTime();
	// $('#vw_inscripcionesJornada').html('Jornada:'+jornada+' '+t);
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
		data: {
			Operation: 'inscripciones',
			Prueba: workingData.prueba,
			Jornada: workingData.jornada
		},
		success: function(data,status,jqxhr) {
			$('#pb_inscripcionesJornada').html(data);
			var str=$('#vw_NombrePrueba').val()+" - "+$('#vw_NombreJornada').val();
			$('#pb_inscripciones-infocabecera').html(str);
		}
	});
}

function pb_updateResults() {
	$.ajax( {
		type: "GET",
		dataType: 'html',
		url: "/agility/server/videowall.php",
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
	alert("public::updateFinales() TODO: write");
}
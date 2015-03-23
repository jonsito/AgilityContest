/*
events.js

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

/*
ID,
Session,
Source,
TimeStamp,
Data { // MUST be lesser than 254 bytes
	"EID": // ID - Event ID
	"Ses": // Session - Session (Ring) ID
	"Tst": // TimeStamp - event time
	"Typ": // Type - "llamada",
	"Src": // Source - "tablet_1",
	"Pru": // Prueba,
	"Jor": // Jornada,
	"Mng": // Manga,
	"Tnd": // Tanda,
	"Dog": // Perro,
	"Drs": // Dorsal,
	"Hot": // Celo,
	"Flt": // Faltas,
	"Toc": // Tocados,
	"Reh": // Rehuses,
	"NPr": // NoPresentado,
	"Eli": // Eliminado,
	"Tim": // Tiempo,
	"Val": // Value
}
*/

/**
 * Coge un array y lo "comprime" para que quepa en los 
 * 254 caracteres del campo "data" de la tabla de eventos de la BBDD
 * 
 * @param {object} data data to be compressed
 * @return compressed data
 */
function compressEventData(data) {
	// TODO: write
}

/**
 * Coge el campo "data" de la tabla de eventos de la BBDD
 * Y lo convierte a un array de datos "estandard"
 */
function expandEventData(data) {
	// TODO: write
}
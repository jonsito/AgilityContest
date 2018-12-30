<?php
/*
Tandas.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once("DBObject.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once("OrdenSalida.php");
require_once("Clubes.php");

class Tandas extends DBObject {
	
	protected $prueba;
	protected $jornada;
	protected $sesiones; // used to store current sesions
	protected $mangas; // used to store mangas of this journey
    protected $federation;
	
	/**
	 * Tandas database only contains 'Tipo' field. Extract remaining data from this table
	 * Tipo: 		Tanda type
	 * TipoManga:	Manga type. From Database and Mangas::$tipo_manga
	 * From:		Starting point from Mangas::OrdenSalida
	 * To:			Ending point from Mangas::OrdenSalida
	 * Nombre:		Tanda's name: User defined if 'Tipo'==0
	 * Categoria:	List of supported categorias in this tanda
	 * Grado:		Tanda's grado 
	 */
	 static $tipo_tanda = array (
			0	=> array('Tipo'=>0,		'TipoManga'=>0,		'Nombre'=>'-- Sin especificar --',  'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-',	'Grado'=>'-'),
			// en pre-agility no hay categorias
			1	=> array('Tipo'=>1,		'TipoManga'=> 1,	'Nombre'=>'Pre-Agility 1',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'P.A.'),
			2	=> array('Tipo'=>2,		'TipoManga'=> 2,	'Nombre'=>'Pre-Agility 2',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'P.A.'),
			3	=> array('Tipo'=>3,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'),
			4	=> array('Tipo'=>4,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
			5	=> array('Tipo'=>5,		'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
			6	=> array('Tipo'=>6,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'),
			7	=> array('Tipo'=>7,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
			8	=> array('Tipo'=>8,		'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
			9	=> array('Tipo'=>9,		'TipoManga'=> 5,	'Nombre'=>'Agility GII Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GII'),
			10	=> array('Tipo'=>10,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Medium',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GII'),
			11	=> array('Tipo'=>11,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GII'),
			12	=> array('Tipo'=>12,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GIII'),
			13	=> array('Tipo'=>13,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GIII'),
			14	=> array('Tipo'=>14,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GIII'),
			15	=> array('Tipo'=>15,	'TipoManga'=> 7,	'Nombre'=>'Agility Large',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'), // Individual-Open
			16	=> array('Tipo'=>16,	'TipoManga'=> 7,	'Nombre'=>'Agility Medium',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'), //  Individual-Open
			17	=> array('Tipo'=>17,	'TipoManga'=> 7,	'Nombre'=>'Agility Small',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'), //  Individual-Open
			18	=> array('Tipo'=>18,	'TipoManga'=> 8,	'Nombre'=>'Agility team Large',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team best
			19	=> array('Tipo'=>19,	'TipoManga'=> 8,	'Nombre'=>'Agility team Medium',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'M',	'Grado'=>'-'), // team best
			20	=> array('Tipo'=>20,	'TipoManga'=> 8,	'Nombre'=>'Agility team Small',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'S',	'Grado'=>'-'), // team best
        	// en jornadas por equipos conjunta tres alturas se mezclan categorias M y S
			21	=> array('Tipo'=>21,	'TipoManga'=> 9,	'Nombre'=>'Ag. Teams Large',		'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team combined
			22	=> array('Tipo'=>22,	'TipoManga'=> 9,	'Nombre'=>'Ag. Teams Med/Small',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'MS',	'Grado'=>'-'), // team combined
			23	=> array('Tipo'=>23,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GII'),
			24	=> array('Tipo'=>24,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Medium',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GII'),
			25	=> array('Tipo'=>25,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GII'),
			26	=> array('Tipo'=>26,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GIII'),
			27	=> array('Tipo'=>27,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GIII'),
			28	=> array('Tipo'=>28,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GIII'),
			29	=> array('Tipo'=>29,	'TipoManga'=> 12,	'Nombre'=>'Jumping Large',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'), //  Individual-Open
			30	=> array('Tipo'=>30,	'TipoManga'=> 12,	'Nombre'=>'Jumping Medium',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'), //  Individual-Open
			31	=> array('Tipo'=>31,	'TipoManga'=> 12,	'Nombre'=>'Jumping Small',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'), //  Individual-Open
			32	=> array('Tipo'=>32,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Large',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team best
			33	=> array('Tipo'=>33,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Medium',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'M',	'Grado'=>'-'), // team best
			34	=> array('Tipo'=>34,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Small',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'S',	'Grado'=>'-'), // team best
			// en jornadas por equipos conjunta 3 alturas se mezclan categorias M y S
			35	=> array('Tipo'=>35,	'TipoManga'=> 14,	'Nombre'=>'Jp. Teams Large',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'L',	'Grado'=>'-'), // team combined
			36	=> array('Tipo'=>36,	'TipoManga'=> 14,	'Nombre'=>'Jp. Teams Med/Small',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'MS',	'Grado'=>'-'), // team combined
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array('Tipo'=>37,	'TipoManga'=> 15,	'Nombre'=>'Manga K.O. 1',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
			38	=> array('Tipo'=>38,	'TipoManga'=> 16,	'Nombre'=>'Special Round Large',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'-'),
			39	=> array('Tipo'=>39,	'TipoManga'=> 16,	'Nombre'=>'Special Round Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'-'),
			40	=> array('Tipo'=>40,	'TipoManga'=> 16,	'Nombre'=>'Special Round Small',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'-'),
	
			// "Tiny" support for Pruebas de cuatro alturas
			41	=> array('Tipo'=>41,	'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI'),
			42	=> array('Tipo'=>42,	'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI'),
			43	=> array('Tipo'=>43,	'TipoManga'=> 5,	'Nombre'=>'Agility GII Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GII'),
			44	=> array('Tipo'=>44,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GIII'), // no existe
			45	=> array('Tipo'=>45,	'TipoManga'=> 7,	'Nombre'=>'Agility Tiny',			'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), //  Individual-Open
			46	=> array('Tipo'=>46,	'TipoManga'=> 8,	'Nombre'=>'Agility team Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), // team best
			// en equipos4  cuatro alturas  agrupamos por LM y ST
			47	=> array('Tipo'=>47,	'TipoManga'=> 9,	'Nombre'=>'Ag. team Large/Medium', 'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'LM',	'Grado'=>'-'), // team combined
			48	=> array('Tipo'=>48,	'TipoManga'=> 9,	'Nombre'=>'Ag. team Small/Tiny',	'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'ST',	'Grado'=>'-'), // team combined
			49	=> array('Tipo'=>49,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GII'),
			50	=> array('Tipo'=>50,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII Tiny',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GIII'), // no existe
			51	=> array('Tipo'=>51,	'TipoManga'=> 12,	'Nombre'=>'Jumping Tiny',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'), //  Individual-Open
			52	=> array('Tipo'=>52,	'TipoManga'=> 13,	'Nombre'=>'Jumping team Tiny',		'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'T',	'Grado'=>'-'), // team best
			53	=> array('Tipo'=>53,	'TipoManga'=> 14,	'Nombre'=>'Jp. teams Large/Medium', 'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'LM',	'Grado'=>'-'), // team combined
			54	=> array('Tipo'=>54,	'TipoManga'=> 14,	'Nombre'=>'Jp. teams Small/Tiny',	'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'ST',	'Grado'=>'-'), // team combined
			55	=> array('Tipo'=>55,	'TipoManga'=> 16,	'Nombre'=>'Special round Tiny',	    'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'-'),
            56	=> array('Tipo'=>56,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L',	'Grado'=>'GI'), // extra rounds for GI RFEC
            57	=> array('Tipo'=>57,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M',	'Grado'=>'GI'),
            58	=> array('Tipo'=>58,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S',	'Grado'=>'GI'),
            59	=> array('Tipo'=>59,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI Tiny',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T',	'Grado'=>'GI'),
            // resto de las rondas KO. Los perros compiten todos contra todos
            60	=> array('Tipo'=>60,	'TipoManga'=> 18,	'Nombre'=>'Manga K.O. 2',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            61	=> array('Tipo'=>61,	'TipoManga'=> 19,	'Nombre'=>'Manga K.O. 3',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            62	=> array('Tipo'=>62,	'TipoManga'=> 20,	'Nombre'=>'Manga K.O. 4',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            63	=> array('Tipo'=>63,	'TipoManga'=> 21,	'Nombre'=>'Manga K.O. 5',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            64	=> array('Tipo'=>64,	'TipoManga'=> 22,	'Nombre'=>'Manga K.O. 6',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            65	=> array('Tipo'=>65,	'TipoManga'=> 23,	'Nombre'=>'Manga K.O. 7',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            66	=> array('Tipo'=>66,	'TipoManga'=> 24,	'Nombre'=>'Manga K.O. 8',			'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'-LMST','Grado'=>'-'),
            // tandas para games ( cuatro categorias, siete mangas distintas
            67	=> array('Tipo'=>67,	'TipoManga'=> 25,	'Nombre'=>'Agility A 650',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            68	=> array('Tipo'=>68,	'TipoManga'=> 25,	'Nombre'=>'Agility A 525',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            69	=> array('Tipo'=>69,	'TipoManga'=> 25,	'Nombre'=>'Agility A 400',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            70	=> array('Tipo'=>70,	'TipoManga'=> 25,	'Nombre'=>'Agility A 300',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            71	=> array('Tipo'=>71,	'TipoManga'=> 26,	'Nombre'=>'Agility B 650',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            72	=> array('Tipo'=>72,	'TipoManga'=> 26,	'Nombre'=>'Agility B 525',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            73	=> array('Tipo'=>73,	'TipoManga'=> 26,	'Nombre'=>'Agility B 400',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            74	=> array('Tipo'=>74,	'TipoManga'=> 26,	'Nombre'=>'Agility B 300',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            75	=> array('Tipo'=>75,	'TipoManga'=> 27,	'Nombre'=>'Jumping A 650',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            76	=> array('Tipo'=>76,	'TipoManga'=> 27,	'Nombre'=>'Jumping A 525',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            77	=> array('Tipo'=>77,	'TipoManga'=> 27,	'Nombre'=>'Jumping A 400',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            78	=> array('Tipo'=>78,	'TipoManga'=> 27,	'Nombre'=>'Jumping A 300',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            79	=> array('Tipo'=>79,	'TipoManga'=> 28,	'Nombre'=>'Jumping B 650',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            80	=> array('Tipo'=>80,	'TipoManga'=> 28,	'Nombre'=>'Jumping B 525',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            81	=> array('Tipo'=>81,	'TipoManga'=> 28,	'Nombre'=>'Jumping B 400',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            82	=> array('Tipo'=>82,	'TipoManga'=> 28,	'Nombre'=>'Jumping B 300',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            83	=> array('Tipo'=>83,	'TipoManga'=> 29,	'Nombre'=>'Snooker 650',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            84	=> array('Tipo'=>84,	'TipoManga'=> 29,	'Nombre'=>'Snooker 525',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            85	=> array('Tipo'=>85,	'TipoManga'=> 29,	'Nombre'=>'Snooker 400',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            86	=> array('Tipo'=>86,	'TipoManga'=> 29,	'Nombre'=>'Snooker 300',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            87	=> array('Tipo'=>87,	'TipoManga'=> 30,	'Nombre'=>'Gambler 650',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            88	=> array('Tipo'=>88,	'TipoManga'=> 30,	'Nombre'=>'Gambler 525',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            89	=> array('Tipo'=>89,	'TipoManga'=> 30,	'Nombre'=>'Gambler 400',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            90	=> array('Tipo'=>90,	'TipoManga'=> 30,	'Nombre'=>'Gambler 300',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            91	=> array('Tipo'=>91,	'TipoManga'=> 31,	'Nombre'=>'SpeedStakes 650',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'-'),
            92	=> array('Tipo'=>92,	'TipoManga'=> 31,	'Nombre'=>'SpeedStakes 525',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'-'),
            93	=> array('Tipo'=>93,	'TipoManga'=> 31,	'Nombre'=>'SpeedStakes 400',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'-'),
            94	=> array('Tipo'=>94,	'TipoManga'=> 31,	'Nombre'=>'SpeedStakes 300',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'-'),
            // tandas para categoria Junior
            95	=> array('Tipo'=>95,	'TipoManga'=> 32,	'Nombre'=>'Junior 1 Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'Jr'),
            96	=> array('Tipo'=>96,	'TipoManga'=> 32,	'Nombre'=>'Junior 1 Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'Jr'),
            97	=> array('Tipo'=>97,	'TipoManga'=> 32,	'Nombre'=>'Junior 1 Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'Jr'),
            98	=> array('Tipo'=>98,	'TipoManga'=> 32,	'Nombre'=>'Junior 1 Toy',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'Jr'),
            99	=> array('Tipo'=>99,	'TipoManga'=> 33,	'Nombre'=>'Junior 2 Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'Jr'),
            100 => array('Tipo'=>100,	'TipoManga'=> 33,	'Nombre'=>'Junior 2 Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'Jr'),
            101	=> array('Tipo'=>101,	'TipoManga'=> 33,	'Nombre'=>'Junior 2 Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'Jr'),
            102	=> array('Tipo'=>102,	'TipoManga'=> 33,	'Nombre'=>'Junior 2 Toy',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'Jr'),
            // tandas para categoria Senior
            103	=> array('Tipo'=>103,	'TipoManga'=> 34,	'Nombre'=>'Senior 1 Large',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'Sr'),
            104 => array('Tipo'=>104,	'TipoManga'=> 34,	'Nombre'=>'Senior 1 Medium',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'Sr'),
            105 => array('Tipo'=>105,	'TipoManga'=> 34,	'Nombre'=>'Senior 1 Small',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'Sr'),
            106	=> array('Tipo'=>106,	'TipoManga'=> 34,	'Nombre'=>'Senior 1 Toy',		'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'Sr'),
            107	=> array('Tipo'=>107,	'TipoManga'=> 35,	'Nombre'=>'Senior 2 Large',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'L','Grado'=>'Sr'),
            108 => array('Tipo'=>108,	'TipoManga'=> 35,	'Nombre'=>'Senior 2 Medium',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'M','Grado'=>'Sr'),
            109	=> array('Tipo'=>109,	'TipoManga'=> 35,	'Nombre'=>'Senior 2 Small',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'S','Grado'=>'Sr'),
         110	=> array('Tipo'=>110,	'TipoManga'=> 35,	'Nombre'=>'Senior 2 Toy',		'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'T','Grado'=>'Sr'),
         // tandas para cinco alturas (X-Large
         111	=> array('Tipo'=>111,	'TipoManga'=> 35,	'Nombre'=>'Junior 1 XLarge',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'Jr'),
         112	=> array('Tipo'=>112,	'TipoManga'=> 35,	'Nombre'=>'Junior 2 XLarge',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'Jr'),
         113	=> array('Tipo'=>113,	'TipoManga'=> 34,	'Nombre'=>'Senior 1 XLarge',	'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'Sr'),
         114	=> array('Tipo'=>114,	'TipoManga'=> 34,	'Nombre'=>'Senior 2 XLarge',	'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'Sr'),
         115	=> array('Tipo'=>115,	'TipoManga'=> 3,	'Nombre'=>'Agility-1 GI XLarge','isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GI'),
         116	=> array('Tipo'=>116,	'TipoManga'=> 4,	'Nombre'=>'Agility-2 GI XLarge','isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GI'),
         117	=> array('Tipo'=>117,	'TipoManga'=> 17,	'Nombre'=>'Agility-3 GI XLarge','isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GI'),
         118	=> array('Tipo'=>118,	'TipoManga'=> 5,	'Nombre'=>'Agility GII XLarge', 'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GII'),
         119	=> array('Tipo'=>119,	'TipoManga'=> 10,	'Nombre'=>'Jumping GII XLarge', 'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GII'),
         120	=> array('Tipo'=>120,	'TipoManga'=> 6,	'Nombre'=>'Agility GIII XLarge','isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GIII'),
         121	=> array('Tipo'=>121,	'TipoManga'=> 11,	'Nombre'=>'Jumping GIII XLarge','isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'GIII'),
         122	=> array('Tipo'=>122,	'TipoManga'=> 7,	'Nombre'=>'Agility XLarge',     'isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'-'),
         123	=> array('Tipo'=>123,	'TipoManga'=> 12,	'Nombre'=>'Jumping XLarge',     'isAgility'=> false, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'-'),
         124	=> array('Tipo'=>124,	'TipoManga'=> 8,	'Nombre'=>'Agility Team XLarge','isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'X','Grado'=>'-'),
         125	=> array('Tipo'=>125,	'TipoManga'=> 13,	'Nombre'=>'Jumping Team XLarge','isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'X','Grado'=>'-'),
         126	=> array('Tipo'=>126,	'TipoManga'=> 16,	'Nombre'=>'Special Round XLarge','isAgility'=> true, 'isTeam'=>false, 'Categoria'=>'X','Grado'=>'-'),
         // jornadas team mixtas extras para cinco alturas
         127	=> array('Tipo'=>127,	'TipoManga'=> 9,	'Nombre'=>'Ag. team XLarge/Large', 'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'XL',	'Grado'=>'-'), // team combined
         128	=> array('Tipo'=>128,	'TipoManga'=> 14,	'Nombre'=>'Jp. team XLarge/Large', 'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'XL',	'Grado'=>'-'), // team combined
         129	=> array('Tipo'=>129,	'TipoManga'=> 9,	'Nombre'=>'Ag. team Med/Small/Toy', 'isAgility'=> true, 'isTeam'=>true, 'Categoria'=>'MST','Grado'=>'-'), // team combined
         130	=> array('Tipo'=>130,	'TipoManga'=> 14,	'Nombre'=>'Jp. team Med/Small/Toy', 'isAgility'=> false, 'isTeam'=>true, 'Categoria'=>'MST','Grado'=>'-'), // team combined
     );

    /**
     * Translate requested tanda index to federation dependent i18n'd Tanda Name
     * @param {integer} $tipo tanda index as declared in Tandas.php
     * @param {integer|object} $fed federation id or federation object
     * @return string resulting i18n'd string
     */
	static function getNombreByTipo($tipo,$fed) {
	    if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return "(unknown)"; // key not found should notice error
        if (is_int($fed)) $fed=Federations::getFederation($fed);
        $config=$fed->getConfig();
        if (!array_key_exists('NombreTandas',$config)) return _(Tandas::$tipo_tanda[$tipo]['Nombre']);
        if (!array_key_exists($tipo,$config['NombreTandas'])) return _(Tandas::$tipo_tanda[$tipo]['Nombre']);
        return $config['NombreTandas'][$tipo]; // already i18n'd
    }

	static function getCategoriaByTipo($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return "-"; // key not found: return default (any)
        return Tandas::$tipo_tanda[$tipo]['Categoria'];
    }

    static function getGradoByTipo($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return "-"; // key not found: return default (any)
        return Tandas::$tipo_tanda[$tipo]['Grado'];
    }

    static function isAgility($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return false; // key not found: return false
        return Tandas::$tipo_tanda[$tipo]['isAgility'];
    }

    static function isTeam($tipo) {
        if (!array_key_exists($tipo,Tandas::$tipo_tanda)) return false; // key not found: return false
        return Tandas::$tipo_tanda[$tipo]['isTeam'];
    }

    // obtiene la lista de tipos de tanda que coinciden con un tipo de manga determinado
    // usado para evaluar el orden de las categorias segun el programa de la jornada
    static function getTandasByTipoManga($tipo){
        $res=array();
        foreach(Tandas::$tipo_tanda as $key => $value) {
            if ($value['TipoManga']==$tipo) array_push($res,$key);
        }
        return $res;
    }

	/**
	 * return every array items that matches with provided key
     * usage example: getTandasInfo("TipoManga",9); // to get every teambest agility tandas
	 * @param {string} $key Item to search
	 * @param {value} $value value to match in key
	 * @return {array} List of Tandas that matches with requested key/value pair
	*/
	static function getTandasInfo($key,$value) {
		$res=array();
		if (!array_key_exists($key,Tandas::$tipo_tanda[0])) { // use index 0 to check valid key
            // key not found: notify and return empty array
		    do_log("Invalid search key for Tandas array:$key");
		    return $res;
        }
		foreach(Tandas::$tipo_tanda as $item) {
			if ($item[$key]==$value) array_push($res,$item);
		}
		return $res;
	}
	
	private function getSessionName($id){
		foreach($this->sesiones as $sesion) {
			if ($sesion['ID']==$id) return $sesion['Nombre'];
		}
		$this->myLogger->error("No session found with ID:$id");
		return ""; // no session name found
	}
	
	private function getMangaByTipo($tipomanga) {
		foreach($this->mangas as $manga) {
			if ($manga['Tipo']==$tipomanga) return $manga;
		}
		$this->myLogger->error("No mangas found with Tipo:$tipomanga");
		return null;
	}
	
	/**
	 * Constructor
	 * @param {string} $file Caller's indentification
	 * @param {integer} $prueba Prueba ID
	 * @param {integer} $jornada Jornada ID
	 * @throws Exception on invalid data or database connection error
	 */
	function __construct($file,$prueba,$jornada) {
		parent::__construct($file);
		if ( $prueba<=0 ) {
			$this->errormsg="$file::construct() invalid prueba:$prueba ID";
			throw new Exception($this->errormsg);
		}
		if ( $jornada<=0 ) {
			$this->errormsg="$file::construct() invalid jornada:$jornada ID";
			throw new Exception($this->errormsg);
		}
		$this->prueba=$this->__getObject("pruebas",$prueba);
		$this->jornada=$this->__getObject("jornadas",$jornada);
		if ($this->jornada->Prueba!=$prueba) {
			$this->errormsg="$file::construct() jornada:$jornada is not owned by prueba:$prueba";
			throw new Exception($this->errormsg);
		}
		$s=$this->__select("*","sesiones","","","");
		$this->sesiones=$s['rows'];
		$m=$this->__select("*","mangas","(Jornada=$jornada)","","");
		$this->mangas=$m['rows'];
		$this->federation=Federations::getFederation($this->prueba->RSCE);
	}
	
	function getHttpData() {
		$data=array();
		$data['Prueba']=$this->prueba->ID;
		$data['Jornada']=$this->jornada->ID;
		$data['ID']=http_request("ID","i",0);
		$data['InsertID']=http_request("InsertID","i",0);
		$data['Tipo']=http_request("Tipo","i",0);
		$data['Nombre']=http_request("Nombre","s","-- Sin nombre --");
		$data['Sesion']=http_request("Sesion","i",2); // defaults to Ring 1
		$data['Horario']=http_request("Horario","s","");
        $data['Comentario']=http_request("Comentario","s","");
        // type 0 (user defined ) has no category nor grade declared
        if ($data['Tipo']==0) {
            $data['Categoria']=http_request("Categoria","s","-");
            $data['Grado']=http_request("Grado","s","-");
        }
		return $data;
	}
	
	/**
	 * Insert a new 'Tipo=0' data into database
	 * @param {array} $data
	 */
	function insert($data) {
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		// arriving here means update and/or insert
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// locate latest order in manga
		$obj=$this->__selectObject("MAX(Orden) AS Last","tandas","(Prueba=$p) AND (Jornada=$j)");
		$o=($obj!==null)?1+intval($obj->Last):1; // evaluate latest in order
		$s=$data['Sesion'];
		$n=$data['Nombre'];
		$h=$data['Horario'];
        $c=$data['Comentario'];
        $ct=$data['Categoria'];
        $g=$data['Grado'];
		$str="INSERT INTO tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre,Horario,Categoria,Grado,Comentario) ".
            "VALUES (0,$p,$j,$s,$o,'{$n}','{$h}','{$ct}','{$g}','{$c}')";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		// obtenemos el ID del registro insertado
		$from=$this->conn->insert_id;
		$to=$data['InsertID'];
		if ($to==0) {
			// buscamos el id del programa con menor orden
			$str="SELECT ID From tandas ORDER BY Orden ASC LIMIT 0,1";
			$rs=$this->query($str);
			if (!$rs) return $this->error($this->conn->error);
			$obj=$rs->fetch_object();
			$rs->free();
			$to=intval($obj->ID);
		}
		// insertamos DELANTE del la tanda seleccionada
		if( ($to!=0) && ($from!=$to) )return $this->dragAndDrop($from,$to,false);
		$this->myLogger->info("Tandas::insert() WARN: cannot insert Tanda $from before requested one");
		return "";
	}
	
	/**
	 * Update Tanda in database
	 * Only allow change "Nombre" field when tipo==0
	 * @param {array} $data
	 * @throws Exception on invalid tanda ID
	 */
	function update($id,$data){
		if ($id<=0) throw new Exception ("Invalid Tanda ID:$id");
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$s=$data['Sesion'];
		$n=$data['Nombre'];
		$h=$data['Horario'];
		$c=$data['Comentario'];
        $str= "UPDATE tandas SET Sesion=$s, Horario='$h', Comentario='$c' WHERE (ID=$id)";
        $rs=$this->query($str);
        if (!$rs) return $this->error($this->conn->error);
        // if tipo!=0 cannot change name
        $str= "UPDATE tandas SET Nombre='$n' WHERE (ID=$id) AND (Tipo=0)";
        $rs=$this->query($str);
        if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	function delete($id){
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		// only remove those tandas with "Tipo"=0
		// for remaining tipos, removeFromList must be issued
		$rs=$this->__delete("tandas","(ID={$id}) AND (Tipo=0)");
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
	
	function removeFromList($tipo) {
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		$rs=$this->__delete("tandas","(Prueba={$p}) AND (Jornada={$j}) AND (Tipo={$tipo})");
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
	
	function select($id){
		return $this->__getArray("tandas",$id);
	}
	
	/**
	 * insert $from before(where==false) or after(where=true) $to
	 * This dnd routine uses a Orden shift'ng: increase every remaining row order, 
	 * and assign moved row orden to created hole 
	 * @param {integer} $from id to move
	 * @param {integer} $to id to insert arounn
	 * @param {boolean} $where false:insert before  / true:insert after
	 */
	function dragAndDrop($from,$to,$where) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$torder=$t->Orden;
		$neworder=($where)?$torder+1/*after*/:$torder/*before*/;
		$comp=($where)?">"/*after*/:">="/*before*/;
		$str="UPDATE tandas SET Orden=Orden+1 WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( Orden $comp $torder )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		$str="UPDATE tandas SET Orden=$neworder WHERE ( Prueba = $p ) AND ( Jornada = $j ) AND ( ID = $from )";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error);
		return "";
	}
	
	/**
	 * Swap orden between requested tandas
	 * @param {integer} $from Tanda ID 1
	 * @param {integer} $to Tanda ID 2
	 * @return {string} error message or "" on success
	 */
	function swap($from,$to) {
		$this->myLogger->enter();
        assertClosedJourney($this->jornada); // throw exception on closed journeys
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		// get from/to Tanda's ID
		$f=$this->__selectObject("*","tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$from)");
		$t=$this->__selectObject("*","tandas","(Prueba=$p) AND (Jornada=$j) AND (ID=$to)");
		if(!$f || !$t) {
			$this->myLogger->error("Error: no ID for tanda's order '$from' and/or '$to' on prueba:$p jornada:$j");
			return $this->errormsg;
		}
		$forden=$f->Orden;
		$torden=$t->Orden;
		// perform swap update. 
		// TODO: make it inside a transaction
		$str="UPDATE tandas SET Orden=$torden WHERE (ID=$from)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$str="UPDATE tandas SET Orden=$forden WHERE (ID=$to)";
		$rs=$this->query($str);
		if (!$rs) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return ""; // mark success
	}

    /**
     * In RFEC categories order is TSMLX instead of XLMST.
     * This is a dirty hack to reverse categories order
     */
	function swapXLMST() {
        $p=$this->prueba->ID;
        $j=$this->jornada->ID;
        $l="( 3,  6,   9, 12, 15, 18, 23, 26, 29, 32, 38, 56, 95,  99 )";
	    $m="( 4,  7,  10, 13, 16, 19, 24, 27, 30, 33, 39, 57, 96, 100 )";
	    $s="( 5,  8,  11, 14, 17, 20, 25, 28, 31, 34, 40, 58, 97, 101 )";
	    $t="( 41, 42, 43, 44, 45, 46, 49, 50, 51, 52, 55, 59, 98, 102 )";
	    $cmds=array(
	        // swap large and toy
            "UPDATE tandas SET Orden=Orden+3 WHERE (Prueba={$p}) AND (Jornada={$j}) AND (Tipo IN {$l})",
            "UPDATE tandas SET Orden=Orden-3 WHERE (Prueba={$p}) AND (Jornada={$j}) AND (Tipo IN {$t})",
            // swap small and medium
            "UPDATE tandas SET Orden=Orden+1 WHERE (Prueba={$p}) AND (Jornada={$j}) AND (Tipo IN {$m})",
            "UPDATE tandas SET Orden=Orden-1 WHERE (Prueba={$p}) AND (Jornada={$j}) AND (Tipo IN {$s})"
        );
        $this->query("START TRANSACTION");
	    foreach ($cmds as $cmd) {
	        $res=$this->query($cmd);
	        if (!$res) {
	            $this->myLogger->error($this->conn->error);
                $this->query("ROLLBACK");
	            return;
            }
        }
        $this->query("COMMIT");
    }

	/**
	 * Obtiene el programa de la jornada
	 * @param {integer} $s session id.
     *    0: ANY sesion
     *    1: ANY BUT User defined sessions
     *   -1: User defined sessions
     *    n: Session number "n"
     *   -n: Session number "n" PLUS User defined sessions
	 * @return {array} easyui-aware array or string on error
	 */
	function getTandas($sessid=0){
        $s=intval($sessid);
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
        if ($s==0) $ses="";
        if ($s==1) $ses= " AND (Tipo!=0)";
        if ($s==-1) $ses= " AND (Tipo=0)";
        if ($s>1) $ses= " AND (Sesion=$s)";
        if ($s<(-1)) {
            $s=-$s;
            $ses= " AND ( (Sesion=$s) OR (Sesion=1) )";
        }
		// Ask dadabase to retrieve list of Tandas
		$res= $this->__select(
				/* SELECT */	"*",
				/* FROM */		"tandas",
				/* WHERE */		"(Prueba=$p) AND (Jornada=$j) $ses",
				/* ORDER BY */	"Orden ASC",
				/* LIMIT */		""
		);
		if(!is_array($res)){
			return $this->error("No encuentro tandas para la prueba:$p jornada:$j sesion:$sessid");
		}
		
		// merge retrieved data with tipotanda info
		foreach ($res['rows'] as $key => $item) {
			// merge tipo_tanda info into result
			$res['rows'][$key]=array_merge(Tandas::$tipo_tanda[$item['Tipo']],$item);
			// evaluate and insert Manga ID
			if ($res['rows'][$key]['TipoManga']==0) { // User-Provided tandas has no Manga ID
                $res['rows'][$key]['Manga']=0;
                $res['rows'][$key]['Participantes']='';
			} else {
			    // retrieve Manga ID and merge into result
				$manga=$this->getMangaByTipo($res['rows'][$key]['TipoManga']);	

				// add extra info to result
				$res['rows'][$key]['Manga']=(is_array($manga))? $manga['ID']: 0 ;

                // and finally add number of participants
                $str="( Prueba={$this->prueba->ID} ) AND ( Jornada={$this->jornada->ID} ) AND (Manga={$res['rows'][$key]['Manga']})";
                $result=$this->__select("*","resultados",$str,"","");
                if (!is_array($result)) {
                    $this->myLogger->error($result); return $result;
                }
                $count=0;
                foreach($result['rows'] as $itm) { // comparamos categoria y grado
                    // si el grado es '-' se contabiliza. else si coincide grado se contabiliza
                    if (($res['rows'][$key]['Grado']!=='-') && ($itm['Grado']!==$res['rows'][$key]['Grado']) ) continue;
                    // comparamos categorias
                    if ( strstr($res['rows'][$key]['Categoria'],$itm['Categoria'])===false ) continue;
                    $count++;
                }
                $res['rows'][$key]['Participantes']=strval($count);// datos del participacion
			}
			$res['rows'][$key]['NombreSesion']=$this->getSessionName($res['rows'][$key]['Sesion']);
		}
		return $res;
	}
	
	/**
	 * Obtiene la lista ordenada de perros de esta jornada asociadas a la sesion, y tandas especificadas
     * @param {integer} $s session id.
     *    0: ANY sesion
     *    1: ANY BUT User defined sessions
     *   -1: User defined sessions
     *    n: Session number "n"
     *   -n: Session number "n" PLUS User defined sessions
	 * @param {number} $t Tanda ID.
	 *     $t=0; mira todos los perros de todas las tandas de la sesion indicada
	 *     $t>0; mira SOLO los perros de la tanda
	 *     $t<0; mira todos los perros A PARTIR DE la tanda (-$t)
	 * @param {number} $pendientes Pendientes $p==0 -> muestra todos los perros; else muestra los $p primeros pendientes de salir
	 */
	private function getListaPerros($s=0,$t=0,$pendientes=0){
		$count=$pendientes;	// contador de perros pendientes de listar
		$manga=0;		// variable para controlar manga "activa"
		$perrosmanga=null;	// {array} lista de perros ordenada segun ordensalida de la manga
		$do_iterate=false;	// indica si debe analizar los perros de la tanda
		$rows=array();		// donde iremos guardando los resultados
		$result=array();	// resultado a devolver en formato json
		
		// obtenemos la lista de tandas
		$lista_tandas=$this->getTandas($s);
		$club= new Clubes("Tandas::getListaPerros",$this->prueba->RSCE);
		// iteramos la lista de tandas
		foreach ($lista_tandas['rows'] as $tanda) {
			// $this->myLogger->info("Analizando tanda \n".json_encode($tanda));
			// Comprobamos si debemos analizar la tanda
			if ($t>0) { $do_iterate= ( $tanda['ID'] == abs($t) )? true:false; } // iterar solo la tanda
			if ($t<0) { if ( $tanda['ID'] == abs($t) ) $do_iterate=true; } 		// iterar a partir de la tanda
			if ($t==0) $do_iterate=true;										// iterar TODAS las tandas
			if (!$do_iterate) continue; // this tanda is not the one we are looking for
			if ($tanda['Manga']==0) continue; // user defined tandas, has no manga associated
			// comprobamos ahora si hay cambio de manga
			if ($manga!=$tanda['Manga']) { // cambio de manga
				$manga=$tanda['Manga'];
				// en cada manga cogemos  los perros ordenados segun el orden de salida
				$os=Competitions::getOrdenSalidaInstance("Tandas::getListaPerros()",$manga);
				$perrosmanga=$os->getData(false); // false: do not include extra team information row
			}
			// OK ya tenemos la lista ordenada de los perros de cada manga
			// Ahora vamos a sacar la lista por cada tanda
            foreach($perrosmanga['rows'] as &$perro) {
                // si el perro no pertenece a la tanda skip (comprobar categoria)
                if (strpos($tanda['Categoria'],$perro['Categoria'])===false) continue;
                $perro['Tanda']=$tanda['Nombre'];
                $perro['ID']=$tanda['ID']; // replace resultadoID por tandaID TODO: revise why
                if ($pendientes==0) { // include all
                    $perro['LogoClub']=$club->getLogoName('NombreClub',$perro['NombreClub']);
                    array_push($rows,$perro);
                    continue;
                }
                if ($perro['Pendiente']==0) continue; // not pendiente: skip
                if ($count > 0) {  // not yet at count: insert
                    $count--;
                    $perro['LogoClub']=$club->getLogoName('NombreClub',$perro['NombreClub']);
                    array_push($rows,$perro);
                    continue;
                }
                // arriving here means that every requested dogs are filled
                $this->myLogger->debug("Tandas::getListaPerros() Already have $pendientes dogs");
                // so return
                $result['rows']=$rows;
                $result['total']=count($rows);
                $this->myLogger->leave();
                return $result;
            }
            // no more dogs in this tanda. go to next
		}
		$result['rows']=$rows;
		$result['total']=count($rows);
		$this->myLogger->leave();
		return $result;
	}

	function getData($s,$t,$p) {
		return $this->getListaPerros($s,-($t),$p);
	}
	
	function getDataByTanda($s,$t) {
		$page=http_request("page","i",0);
		$rows=http_request("rows","i",0);
		$res=$this->getListaPerros($s,$t,0);
		if (($page==0) || ($rows==0)) return $res;
		if($res['total']==0) return  $res;
		// on scrollview, $res['total'] returns total number of rows
		// but only $rows starting at $page*$rows are returned
		$r=array_slice($res['rows'],($page-1)*$rows,$rows,false);
		$res['rows']=$r;
		return $res;
	}

	function getDataByDorsal($s,$t,$d) {
		$res=$this->getListaPerros($s,$t,0);
		if($res['total']==0) return  $res; // no data
		$count=0;
		foreach ($res['rows'] as $row) {
			if ($row['Dorsal']==$d) { $row['RowIndex']="$count"; return $row; }
			$count++;
		}
		// arriving here means that dorsal is not found in this tanda; notify error
		$this->myLogger->info("Requested Dorsal:$d not found in Tanda:$t");
		return array('RowIndex' => "-1" );
	}

	// fed: federation data
    // tipomanga: manga type:
    // oper: false:remove true:insert
	private function insert_remove($fed,$tipomanga,$oper) {
		$heights=intval($fed->get('Heights'));
		$grados=intval($fed->get('Grades'));
		// obtenemos las tandas cuyo tipo de manga coincide con el indicado
        $tandas=$this->getTandasInfo('TipoManga',$tipomanga);
		foreach( $tandas as $item) {
            // hacemos limpieza de la lista de tandas obtenidas, eliminando las que no nos interesan
            $tipo=$item['Tipo'];
			if( ($heights==3) && ($item['Categoria']==='T') ) {
				// remove every "tiny" tandas on RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
			if( ($grados==2) && ($item['Grado']==='GIII') ) {
				// remove every "Grado III" tandas on non RSCE contests
				$this->removeFromList($tipo);
				continue;
			}
            if( (!$this->federation->hasJunior()) && ($item['Grado']==='Jr') ) {
                // remove every Junior Rounds in RSCE contests
                $this->removeFromList($tipo);
                continue;
            }
            if( (!$this->federation->hasSenior()) && ($item['Grado']==='Sr') ) {
                // remove every Junior Rounds in RSCE contests
                $this->removeFromList($tipo);
                continue;
            }
            // si estamos en equipos conjunta, hay que tener en cuenta las alturas
            // pues las tandas van com L-MS (3 alturas) o bien LM-ST (4 alturas)
            if( in_array($tipomanga,array(9,14)) ) {
			    // en RSCE eliminanos todas las tandas con tipomanga 9(Ag) 14(Jp) relacionadas con RFEC y 5heights
                if ( ($heights==3) && (in_array($tipo,array(45,47,48,52,53,54))) ) { // Ag (T,LM,ST,XL,MST) Jp (T,LM,ST,XL,MST)
                    $this->removeFromList($tipo);
                    continue;
                }
                // en RFEC eliminanos todas las tandas con tipomanga 9(Ag) 14(Jp) relacionadas con RSCE y 5heights
                if ( ($heights==4) && (in_array($tipo,array(21,22,35,36))) ) { // Ag (L,MS,XL,MST) Jp (L,MS,XL,MST)
                    $this->removeFromList($tipo);
                    continue;
                }
                // en cinco alturas eliminamos las tandas relacionadas con rfec y rsce (L LM MS)
                if ( ($heights==5) && (in_array($tipo,array()))) {

                }
            }
            // explicit remove requested
			if ($oper==false) {
				$this->removeFromList($tipo);
				continue;
			} 
			// arriving here means update and/or insert
			$p=$this->prueba->ID;
			$j=$this->jornada->ID;
			// locate latest order in manga
			$obj=$this->__selectObject("MAX(Orden) AS Last","tandas","(Prueba=$p) AND (Jornada=$j)");
			$last=1;
			if ($obj!==null) $last=1+intval($obj->Last); // evaluate latest in order
			// check for already inserted into Tandas
			$obj=$this->__selectObject("*","tandas","(Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)");
			if ($obj===null) { // insert into list at end.
                $n=Tandas::getNombreByTipo($tipo,$fed); // use fed module to retrieve tanda name
                $c=Tandas::getCategoriaByTipo($tipo);
                $g=Tandas::getGradoByTipo($tipo);
				$str="INSERT INTO tandas (Tipo,Prueba,Jornada,Sesion,Orden,Nombre,Categoria,Grado) 
					VALUES ($tipo,$p,$j,2,$last,'$n','$c','$g')"; // Default session is 2->Ring 1
				$rs=$this->query($str);
				if (!$rs) return $this->error($this->conn->error); 
			} else { // move to the end of the list
				$str="UPDATE tandas SET Orden=$last WHERE (Prueba=$p) AND (Jornada=$j) AND (Tipo=$tipo)";
				$rs=$this->query($str);
				if (!$rs) return $this->error($this->conn->error); 
			}
		} /* foreach $tandas as $item */
		return ""; // success
	}
	
	/**
	 * Insert or update Tandas according Jornada Data
	 */
	function populateJornada(){
		$this->myLogger->enter();
		// obtenemos datos de la jornada y prueba
		$j=$this->jornada;
		$p=$this->prueba;
		$f=Federations::getFederation(intval(intval($p->RSCE)));
		// $this->myLogger->trace("call to getFederation({$p->RSCE}) returns: ".print_r($f,true));
		// actualizamos la lista de tandas de cada ronda
        switch($j->PreAgility) {
            case 2:
                $this->insert_remove($f,1,true);	// add Pre-Agility Manga 1
                $this->insert_remove($f,2,true);	// add añadir Pre-Agility Manga 2
                break;
            case 1:
                $this->insert_remove($f,1,true);	// add Pre-Agility Manga 1
                $this->insert_remove($f,2,false);	// remove Pre-Agility Manga 2
                break;
            default:
                if ($j->PreAgility>2) $this->myLogger->error("PreAgility: invalid number of rounds: {$j->PreAgility}");
                $this->insert_remove($f,1,false);	// remove Pre-Agility Manga 1
                $this->insert_remove($f,2,false);	// remove Pre-Agility Manga 2
                break;
        }
        // Junior
        $this->insert_remove($f,32,($j->Junior != 0)?true:false);		// add/remove Junior Manga1
        $this->insert_remove($f,33,($j->Junior != 0)?true:false);		// add/remove Junior Manga2

        // Senior
        $this->insert_remove($f,34,($j->Senior != 0)?true:false);		// add/remove Senior Manga1
        $this->insert_remove($f,35,($j->Senior != 0)?true:false);		// add/remove Senior Manga2

        // grado 1 puede tener 1, 2 o 3 mangas.
        // Por compatibilidad los posibles valores son 1:2mangas 2:1manga 3:3mangas 0:nogrado1
        switch($j->Grado1) {
            case 3: // 3- round grado1
                $this->insert_remove($f,3,true); // add Grado1 manga 1
                $this->insert_remove($f,4,true); // add Grado1 manga 2
                $this->insert_remove($f,17,true); // add Grado1 manga 3
                break;
            case 2: // 1- round grado1
                $this->insert_remove($f,3,true); // add grado1 manga 1
                $this->insert_remove($f,4,false); // remove grado1 manga 2
                $this->insert_remove($f,17,false); // remove grado1 manga 3
                break;
            case 1: // 2- round grado1
                $this->insert_remove($f,3,true); // add grado1 manga 1
                $this->insert_remove($f,4,true); // add grado1 manga 2
                $this->insert_remove($f,17,false); // remove grado1 manga 3
                break;
            default: // no grado1
            $this->insert_remove($f,3,false); // remove grado1 manga 1
            $this->insert_remove($f,4,false); // remove grado1 manga 2
            $this->insert_remove($f,17,false); // remove grado1 manga 3
        }
        // grado 2
        $this->insert_remove($f,5,($j->Grado2 != 0)?true:false);		// Agility Grado II
		$this->insert_remove($f,10,($j->Grado2 != 0)?true:false);		// Jumping Grado II
        // grado 3
		$this->insert_remove($f,6,($j->Grado3 != 0)?true:false);		// Agility Grado III
		$this->insert_remove($f,11,($j->Grado3 != 0)?true:false);		// Jumping Grado III
        // open
		$this->insert_remove($f,7,($j->Open != 0)?true:false);			// Agility Abierta
		$this->insert_remove($f,12,($j->Open != 0)?true:false);			// Jumping Abierta
        // equipos (mejores)
        // a efectos practicos las mangas de equipos en modalidad tres mejores
        // son como pruebas open, en las que los resultados se agrupan
		$this->insert_remove($f,8,($j->Equipos3 != 0)?true:false);		// Agility Equipos (3 mejores)
		$this->insert_remove($f,13,($j->Equipos3 != 0)?true:false);		// Jumping Equipos (3 mejores)
        // equipos (conjunta)
        // en cambio las pruebas de equipos conjunta necesitan tratamiento
        // especial para agrupar las categorias en función de las alturas
		$this->insert_remove($f,9,($j->Equipos4 != 0)?true:false);		// Agility Equipos (Conjunta)
		$this->insert_remove($f,14,($j->Equipos4 != 0)?true:false);		// Jumping Equipos (Conjunta)
        // mangas para prueba ko
        $this->insert_remove($f,15,($j->KO != 0)?true:false);			// Ronda K.O. 1
        $this->insert_remove($f,18,($j->KO != 0)?true:false);			// Ronda K.O. 2
        $this->insert_remove($f,19,($j->KO != 0)?true:false);			// Ronda K.O. 3
        $this->insert_remove($f,20,($j->KO != 0)?true:false);			// Ronda K.O. 4
        $this->insert_remove($f,21,($j->KO != 0)?true:false);			// Ronda K.O. 5
        $this->insert_remove($f,22,($j->KO != 0)?true:false);			// Ronda K.O. 6
        $this->insert_remove($f,23,($j->KO != 0)?true:false);			// Ronda K.O. 7
        $this->insert_remove($f,24,($j->KO != 0)?true:false);			// Ronda K.O. 8
        // mangas para prueba games/wao
        $this->insert_remove($f,25,($j->Games != 0)?true:false);			// Agility A
        $this->insert_remove($f,26,($j->Games != 0)?true:false);			// Agility B
        $this->insert_remove($f,27,($j->Games != 0)?true:false);			// Jumping A
        $this->insert_remove($f,28,($j->Games != 0)?true:false);			// Jumping B
        $this->insert_remove($f,29,($j->Games != 0)?true:false);			// Snooker
        $this->insert_remove($f,30,($j->Games != 0)?true:false);			// Gambler
        $this->insert_remove($f,31,($j->Games != 0)?true:false);			// SpeedStakes
        // manga especial
		$this->insert_remove($f,16,($j->Especial != 0)?true:false);		// Manga especial

        // finally, if requested reverse XLMST to TSMLX
        if ($f->get('ReverseXLMST')===true) $this->swapXLMST();

		$this->myLogger->leave();
	}
	
	/**
	 * Remove all associated Tandas on provided Jornada/Prueba
	 */
	function removeJornada(){
		$p=$this->prueba->ID;
		$j=$this->jornada->ID;
		$rs=$this->__delete("tandas","(Prueba={$p}) AND (Jornada={$j})");
		if (!$rs) return $this->error($this->conn->error);
		return ""; // mark success
	}
}
?>
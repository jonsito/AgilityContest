<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 14/02/17
 * Time: 10:19
 */
/*
 * THIS FILE IS NOT USED AT ALL, JUST HERE FOR POEDIT IMPORT TOOL
 *
 * DO NOT INCLUDE ANYWHERE INTO RUNNING CODE
 *
 * As static data cannot Run-Time parsed by i18n tools, need to be declared outside everything to allow poedit map them
 */

// From Mangas.php
$tipo_manga= array(
        0 =>	array( 0, 'Nombre Manga largo',	'Grado corto',	'Nombre manga',	'Grado largo',  'IsAgility'),
		1 =>	array( 1, _('Pre-Agility Round 1'), 	'P.A.',	_('PreAgility 1'),	_('Pre-Agility'),  true),
		2 => 	array( 2, _('Pre-Agility Round 2'), 	'P.A.',	_('PreAgility 2'),	_('Pre-Agility'),  false),
		3 =>	array( 3, _('Agility Grade I Round 1'),	'GI',	_('Agility-1 GI'),	_('Grade I'),      true),
		4 => 	array( 4, _('Agility Grade I Round 2'),	'GI',	_('Agility-2 GI'),	_('Grade I'),      true),
		5 =>	array( 5, _('Agility Grade II'), 	    'GII',	_('Agility GII'),	_('Grade II'),     true),
		6 =>	array( 6, _('Agility Grade III'), 		'GIII',	_('Agility GIII'),	_('Grade III'),    true),
		7 =>	array( 7, _('Agility'), 	        	'-',	_('Agility'),		_('Individual'),   true), // Open
		8 =>	array( 8, _('Agility Teams'),			 '-',	_('Ag. Teams'),	    _('Teams'),        true), // team best
		9 =>	array( 9, _('Agility Teams')			,'-',	_('Ag. Teams'),	    _('Teams'),        true), // team combined
		10 =>	array( 10,_('Jumping Grade II'),		'GII',	_('Jumping GII'),	_('Grade II'),     false),
		11 =>	array( 11,_('Jumping Grade III'),		'GIII',	_('Jumping GIII'),	_('Grade III'),    false),
		12 =>	array( 12,_('Jumping'),    				'-',	_('Jumping'),		_('Individual'),   false), // Open
		13 =>	array( 13,_('Jumping Teams')			,'-',   _('Jmp Teams'),	    _('Teams'),        false), // team best
		14 =>	array( 14,_('Jumping Teams')			,'-',  	_('Jmp Teams'),	    _('Teams'),        false), // team combined
		15 =>	array( 15,_('K.O. Round'), 				'-',	_('K.O. Round'),	_('K.O.'),         false),
		16 =>	array( 16,_('Special Round'), 			'-',	_('Special Round'), _('Individual'),   true), // special round, no grades
		17 => 	array( 17,_('Agility Grade I Round 3'),	'GI',	_('Agility-3 GI'),	_('Grade I'),      true) // on RFEC special G1 3rd round
);

?>
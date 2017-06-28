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
		17 => 	array( 17,_('Agility Grade I Round 3'),	'GI',	_('Agility-3 GI'),	_('Grade I'),      true), // on RFEC special G1 3rd round

        // mangas extra para K.O.
        18 =>	array( 18,_('K.O. Second round'),		'-',	_('K.O. Round 2'),	_('K.O. R2'),      false),
        19 =>	array( 19,_('K.O. Third round'),		'-',	_('K.O. Round 3'),	_('K.O. R3'),      false),
        20 =>	array( 20,_('K.O. Fourth round'),		'-',	_('K.O. Round 4'),	_('K.O. R4'),      false),
        21 =>	array( 21,_('K.O. Fifth round'),		'-',	_('K.O. Round 5'),	_('K.O. R5'),      false),
        22 =>	array( 22,_('K.O. Sixth round'),		'-',	_('K.O. Round 6'),	_('K.O. R6'),      false),
        23 =>	array( 23,_('K.O. Seventh round'),		'-',	_('K.O. Round 7'),	_('K.O. R7'),      false),
        24 =>	array( 24,_('K.O. Eight round'),		'-',	_('K.O. Round 8'),	_('K.O. R8'),      false),
        // mandas extras para wao
        25 =>	array( 25,_('Agility A'),			    '-',	_('Agility A'),	    _('Ag. A'),        true),
        26 =>	array( 26,_('Agility B'),			    '-',	_('Agility B'),	    _('Ag. B'),        true),
        27 =>	array( 27,_('Jumping A'),			    '-',	_('Jumping A'),	    _('Jp. A'),        false),
        28 =>	array( 28,_('Jumping B'),			    '-',	_('Jumping B'),	    _('Jp. B'),        false),
        29 =>	array( 29,_('Snooker'),			        '-',	_('Snooker'),	    _('Snkr'),         true),
        30 =>	array( 30,_('Gambler'),			        '-',	_('Gambler'),	    _('Gmblr'),        false),
        31 =>	array( 31,_('SpeedStakes'),			    '-',	_('SpeedStakes'),	_('SpdStk'),       true), // single round
        // junior ( really should be a separate journey with every cats and grades, but people doesn't follow rules... )
        // PENDING: revise grade. perhaps need to create an specific 'Jr' grade for them
        32 =>	array( 32,_('Junior Round 1'),	        '-',	_('Junior 1'),	    _('Jr. 1'),        true),
        33 => 	array( 33,_('Junior Round 2'),	        '-',	_('Junior 2'),	    _('Jr. 2'),        false),
);

?>
<?php
/*
PrintPodium.php

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
/**
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../fpdf.php");
require_once(__DIR__."/../../tools.php");
require_once(__DIR__."/../../logging.php");
require_once(__DIR__.'/../../database/classes/DBObject.php');
require_once(__DIR__.'/../../database/classes/Clubes.php');
require_once(__DIR__.'/../../database/classes/Pruebas.php');
require_once(__DIR__.'/../../database/classes/Jueces.php');
require_once(__DIR__.'/../../database/classes/Jornadas.php');
require_once(__DIR__.'/../../database/classes/Mangas.php');
require_once(__DIR__.'/../../database/classes/Resultados.php');
require_once(__DIR__.'/../../database/classes/Clasificaciones.php');
require_once(__DIR__."/../print_common.php");
require_once(__DIR__."/PrintClasificacionGeneral.php");

class PrintPodium extends PrintClasificacionGeneral {

	 /** Constructor
     *@param {int} $prueba
     *@param {int} $jornada
	 *@param {array} $mangas lista de mangaid's
	 *@param {array} $results resultados asociados a la manga pedidas
	 *@throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$results) {
		parent::__construct($prueba,$jornada,$mangas,$results);

        // set file name
        $grad=$this->federation->getTipoManga($this->manga1->Tipo,4); // nombre de la serie
        $res=normalize_filename($grad);
        $this->set_FileName("Podium_{$res}.pdf");
	}

    function Header() {
        $grado = _(Mangas::getTipoManga($this->manga1->Tipo, 4, $this->federation)); // same for every round
        $this->print_commonHeader(_("Podium") . " $grado");
    }

	function composeTable() {
		$this->myLogger->enter();
        $len=(($this->manga3)!==null)?115+(59*3+42)*0.75:115+59*2+42; // lenght of closing line

		$this->AddPage();
		$this->print_InfoJornada();
		foreach($this->resultados as $data) {
			$rowcount=0;
			foreach($data['Data'] as $row) {
				if($rowcount==0) $this->writeTableHeader($data['Mode']);
				if($rowcount>2) break; // only print 3 first results
				$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
				$this->writeCell( $rowcount,$row);
				$rowcount++;
			}
			// pintamos linea de cierre final
			$this->setX(10);
			$this->ac_SetDrawColor($this->config->getEnv('pdf_linecolor')); // line color
			$this->cell($len,0,'','T'); // celda sin altura y con raya
			$this->Ln(2); // 3 mmts to next box
		}
		$this->myLogger->leave();
	}
}
?>
<?php
/*
excel_ClubesWriter.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
 * genera fichero excel de perros seleccionada desde el menu de la base de datos en el orden especificado en la pantalla
*/

require_once(__DIR__ . "/../../tools.php");
require_once(__DIR__ . "/../../logging.php");
require_once(__DIR__ . '/../../database/classes/Clubes.php');
require_once(__DIR__ . "/XLSXWriter.php");

class ClubesWriter extends XLSX_Writer {

	protected $lista; // listado de perros
    protected $cols    = array( 'Name',  'Address 1','Address 2','Province','Country',
                                'Contact 1','Contact 2','Contact 3',
                                'GPS','Email','Web page','Facebook','Twitter',
                                'RSCE','RFEC','CPC','Intl 4','Intl 3','Out' );
    protected $fields = array( 'Nombre','Direccion1','Direccion2','Provincia','Pais',
                                'Contacto1','Contacto2','Contacto3',
                                'GPS','Email','Web','Facebook','Twitter',
                                'RSCE','RFEC','CPC','Intl4', 'Intl3', 'Out');
    /**
	 * Constructor
     * @param {integer} $fed Federation ID
	 * @throws Exception
	 */
	function __construct($fed) {
		parent::__construct("clublist.xlsx");
		setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        $this->fedID=$fed;
        $d=new Clubes("excel_listaClubes");
        $res=$d->select();
        if (!is_array($res)){
			$errormsg="excel_listaClubes: select() failed";
			throw new Exception($errormsg);
		}
        $this->lista=$res['rows'];
	}

    /**
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
	private function writeTableHeader() {
		// internationalize header texts
		for($n=0;$n<count($this->cols);$n++) {
			$this->cols[$n]=_utf($this->cols[$n]);
		}
		// send to excel
		$this->myWriter->addRowWithStyle($this->cols,$this->rowHeaderStyle);
	}

	function composeTable() {
		$this->myLogger->enter();
        // create inrormational page
        $this->createInfoPage(_utf("Club Listing"));
		// Create data page
		$clubspage=$this->myWriter->addNewSheetAndMakeItCurrent();
		$clubspage->setName(_("Clubs"));
		// write header
		$this->writeTableHeader();
        // populate table
		foreach($this->lista as $club) {
		    if ($club['ID']<=1) continue; // skip default club
            // extract federation info
            $club['RSCE']= (( $club['Federations']& 0x0001) == 0 )? "":"X";
            $club['RFEC']= (( $club['Federations']& 0x0002) == 0 )? "":"X";
            $club['CPC']=  (( $club['Federations']& 0x0010) == 0 )? "":"X";
            $club['Nat5']= (( $club['Federations']& 0x0020) == 0 )? "":"X";
            $club['Intl4']=(( $club['Federations']& 0x0100) == 0 )? "":"X";
            $club['Intl3']=(( $club['Federations']& 0x0200) == 0 )? "":"X";
            $club['Out']=( $club['Baja'] == 0 )? "":"X";
 			$row=array();
			// extract relevant information from database received dog
			for($n=0;$n<count($this->fields);$n++) array_push($row,$club[$this->fields[$n]]);
			$this->myWriter->addRow($row);
		}
		$this->myLogger->leave();
	}
}

?>
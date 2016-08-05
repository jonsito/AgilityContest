<?php
/*
excel_listaPerros.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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

require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__.'/../i18n/Country.php');
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__."/common_writer.php");

class excel_ordenSalida extends XLSX_Writer {
    protected $errormsg;
    protected $prueba;
    protected $federation;
    protected $manga; // datos de la manga
    protected $orden; // orden de salida
    protected $categoria; // categoria que estamos listando
    protected $validcats; // categorias que nos han pedido listar
    protected $teams; // lista de equipos de esta jornada
    protected $team4; // tell to print standard or team4 mode

    protected $header;
    protected $fields;
    /**
     * Constructor
     * @throws Exception
     */
    function __construct($prueba,$jornada,$manga,$categorias='',$team4=0) {
        parent::__construct("starting_order.xlsx");
        setcookie('fileDownload','true',time()+30,"/"); // tell browser to hide "downloading" message box
        if ( ($prueba<=0) || ($jornada<=0) || ($manga<=0) ) {
            $this->errormsg="excel_OrdenDeSalida: either prueba/jornada/ manga/orden data are invalid";
            throw new Exception($this->errormsg);
        }
        $myDBObject= new DBObject("excel_ordenDeSalida");
        $this->prueba= $myDBObject->__getArray("Pruebas",$prueba);
        $this->federation=Federations::getFederation(intval($this->prueba['RSCE']));
        // set up fields according international or national contests
        if ($this->federation->isInternational()) {
            $this->header = array( 'Order','Name','Pedigree Name','Gender','Breed','Category','Grade','Handler','Country');
            $this->fields = array( 'Orden','Nombre','NombreLargo','Genero','Raza','Categoria','Grado','NombreGuia','Pais');
        } else {
            $this->header   = array( 'Order','Name','Gender','Breed','License','Category','Grade','Handler','Club','Country');
            $this->fields = array( 'Orden','Nombre','Genero','Raza','Licencia','Categoria','Grado','NombreGuia','NombreClub','Pais');
        }
        // Datos de la manga
        $m = new Mangas("excel_OrdenDeSalida",$jornada);
        $this->manga= $m->selectByID($manga);
        // Datos del orden de salida
        $o = new OrdenSalida("excel_OrdenDeSalida",$manga);
        $os= $o->getData();
        $this->orden=$os['rows'];
        $this->categoria="L";
        // obtenemos los datos de equipos de la jornada indexados por el ID del equipo
        $eq=new Equipos("excel_ordenDeSalida",$prueba,$jornada);
        $this->teams=array();
        foreach($eq->getTeamsByJornada() as $team) $this->teams[$team['ID']]=$team;
        $this->validcats=$categorias;
    }

    private function isTeam() {
        switch ($this->manga->Tipo) {
            case 8: case 9: case 13: case 14: return true;
            default: return false;
        }
    }
    public function open() {
        parent::open();
        $this->createInfoPage(_utf("Starting order"),$this->federation->get('ID'));
    }

    private function writeTableHeader() {
        // internationalize header texts
        $cols=array();
        for($n=0;$n<count($this->header);$n++) {
            $cols[$n]=_utf($this->header[$n]);
        }
        // send to excel
        $this->myWriter->addRowWithStyle($cols,$this->rowHeaderStyle);
    }

    function composeTable() {
        $this->myLogger->enter();
        // Create page
        $dogspage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $dogspage->setName(_("Starting order"));
        // write header
        $this->writeTableHeader();

        $categoria="";
        $equipo="-- Sin asignar --";
        $index=1;
        foreach($this->orden as $perro) {
            if ($categoria!=$perro['Categoria']) {
                if ($categoria!="") $this->myWriter->addRow(array()); // add empty row
                $row=array();
                $index=1;
                $categoria=$perro['Categoria'];
                array_push($row,_utf('Category') . ':');
                array_push($row,$this->federation->get('ListaCategorias')[$categoria]);
                $this->myWriter->addRow($row);
            }
            if($equipo!=$perro['NombreEquipo']) {
                $equipo=$perro['NombreEquipo'];
                $row=array();
                array_push($row,_utf('Team') . ':');
                array_push($row,$equipo);
                $this->myWriter->addRow($row);
            }
            // extract relevant information from database received dog
            $row=array();
            array_push($row,$index);
            for($n=1;$n<count($this->fields);$n++) {
                $val=$perro[$this->fields[$n]];
                // some fields require federation specific translations
                switch($this->fields[$n]) {
                    case 'Categoria':
                        $val=$this->federation->getCategory($categoria);
                        break;
                    case 'Grado':
                        $val=$this->federation->getGrade($val);
                        break;
                    case 'Pais': // use long iso names
                        if (array_key_exists($val,Country::$countryList)) $val=Country::$countryList[$val];
                        break;
                }
                array_push($row,$val);
            }
            $this->myWriter->addRow($row);
            $index++;
        }
        $this->myLogger->leave();
    }
}

// Consultamos la base de datos
try {
    $prueba=http_request("Prueba","i",0);
    $jornada=http_request("Jornada","i",0);
    $manga=http_request("Manga","i",0);
    $categorias=http_request("Categorias","s","-");
    $conjunta=http_request("EqConjunta","i",0);
    // 	Creamos generador de documento
    $excel = new excel_ordenSalida($prueba,$jornada,$manga,$categorias,$conjunta);
    $excel->open();
    $excel->composeTable();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die ("Error accessing database: ".$e->getMessage());
}
?>
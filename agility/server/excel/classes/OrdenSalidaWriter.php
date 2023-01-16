<?php
/*
OrdenSalidaWriter.php

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
require_once(__DIR__ . '/../../i18n/Country.php');
require_once(__DIR__ . '/../../database/classes/DBObject.php');
require_once(__DIR__ . '/../../database/classes/Dogs.php');
require_once(__DIR__ . "/XLSXWriter.php");

class OrdenSalidaWriter extends XLSX_Writer {
    protected $errormsg;
    protected $manga; // datos de la manga
    protected $orden; // orden de salida
    protected $validcats; // categorias que nos han pedido listar
    protected $equipos; // tell to print standard or team4 mode
    protected $useLongNames;
    protected $team4;
    protected $header;
    protected $fields;
    protected $heights;
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
        $this->prueba= $myDBObject->__getArray("pruebas",$prueba);
        $this->jornada= $myDBObject->__getArray("jornadas",$jornada);
        $this->heights=Competitions::getHeights($prueba,$jornada,$manga);
        $this->federation=Federations::getFederation(intval($this->prueba['RSCE']));
        $p=json_decode (json_encode ($this->prueba));
        $j=json_decode (json_encode ($this->jornada));
        $this->useLongNames=Competitions::getCompetition($p,$j)->useLongNames();
        $this->validcats=$categorias;
        // set up fields according international or national contests
        if ($this->federation->isInternational()) {
            $this->header = array( 'Order','Dorsal','Name','LongName','Gender','Breed','Category','Grade','Handler','Country','Heat','Comments','LOE_RRC','NumDogs');
            $this->fields = array( 'Orden','Dorsal','Nombre','NombreLargo','Genero','Raza','Categoria','Grado','NombreGuia','Pais','Celo','Observaciones','LOE_RRC','PerrosPorGuia');
        } else if ($this->useLongNames) {
            $this->header   = array( 'Order','Dorsal','Name','LongName','Gender','Breed','License','Category','Grade','Handler','Club','Country','Heat','Coments','LOE_RRC','NumDogs');
            $this->fields = array( 'Orden','Dorsal','Nombre','NombreLargo','Genero','Raza','Licencia','Categoria','Grado','NombreGuia','NombreClub','Pais','Celo','Observaciones','LOE_RRC',"PerrosPorGuia");
        } else {
             $this->header   = array( 'Order','Dorsal','Name','Gender','Breed','License','Category','Grade','Handler','Club','Country','Heat','Coments','LOE_RRC','NumDogs');
             $this->fields = array( 'Orden','Dorsal','Nombre','Genero','Raza','Licencia','Categoria','Grado','NombreGuia','NombreClub','Pais','Celo','Observaciones','LOE_RRC',"PerrosPorGuia");
        }
        if (intval($this->myConfig->getEnv("pdf_grades"))==0) { // if not grades, remove it from output
            array_splice($this->header,7,1);
            array_splice($this->fields,7,1);
        }
        // Datos de la manga
        $m = new Mangas("excel_OrdenDeSalida",$jornada);
        $this->manga= $m->selectByID($manga);
        // orden de salida de los perros
        $o = Competitions::getOrdenSalidaInstance("excel_OrdenDeSalida",$manga);
        $os= $o->getData();
        $this->orden=$os['rows'];
        // orden de salida de los equipos de la jornada
        $this->team4=$team4;
        $teams= $o->getTeams();
        $this->equipos=$teams['rows'];

        // para el modo "equipos conjunta" agrupamos los perros por equipos
        // el orden de equipos y de perros viene dado por el orden de salida,
        // con la diferencia en que no se hace "split" del equipos sino que van todos seguidos

        // this anidated loop is a bit dirty, and perhaps a bit slow on large team contests
        // but as used rarely no real need to improve
        foreach($this->equipos as &$equipo) { $equipo['Perros']=array(); } // create dog array entry
        // add each dog in their matching team dog array
        foreach($this->orden as $perro) {
            foreach($this->equipos as &$equipo) {
                if ($perro['Equipo']==$equipo['ID']) {
                    array_push($equipo['Perros'],$perro);
                    break;
                }
            }
        }
    }

    private function isTeam() {
        switch ($this->manga->Tipo) {
            case 8: case 9: case 13: case 14: return true;
            default: return false;
        }
    }
    public function open($file=null) {
        parent::open($file);
        $this->createInfoPage(_utf("Starting order"),$this->federation->get('ID'));
    }

    private function writeTableHeader() {
        // add round information
        $row=array();
        array_push($row,_('Date'));
        array_push($row,$this->jornada['Fecha']);
        $this->myWriter->addRow($row);
        $row=array();
        array_push($row,_('Journey'));
        array_push($row,$this->jornada['Nombre']);
        $this->myWriter->addRow($row);
        $row=array();
        array_push($row,_('Round'));
        array_push($row,_(Mangas::getTipoManga($this->manga->Tipo,1,$this->federation)));
        $this->myWriter->addRow($row);

        // now add table header
        $cols=array();
        for($n=0;$n<count($this->header);$n++) {
            $cols[$n]=_utf($this->header[$n]);
        }
        // send to excel
        $this->myWriter->addRowWithStyle($cols,$this->rowHeaderStyle);
    }

    function composeTableConjunta() {
        $this->myLogger->enter();
        $index=1; // starting order

        // Create page
        $dogspage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $dogspage->setName(_("Starting order"));
        // write header
        $this->writeTableHeader();
        foreach ($this->equipos as $equipo) {
            // skip "-- Sin asignar --" team. Do not print team on unrequested categories
            if ($equipo['Nombre']==="-- Sin asignar --") continue;
            // $this->myLogger->trace("Team:{$equipo['Nombre']} cats:{$equipo['Categorias']} compare to:{$this->validcats}");
            if (!category_match($equipo['Categorias'],$this->heights,$this->validcats)) continue;
            // print team name:
            $row=array();
            array_push($row,_utf('Team') . ':');
            array_push($row,$equipo['Nombre']);
            $this->myWriter->addRow($row);
            // imprimimos los perros del equipo
            foreach ($equipo['Perros'] as $perro ) {
                // extract relevant information from database received dog
                $row=array();
                array_push($row,$index); // starting order
                for($n=1;$n<count($this->fields);$n++) {
                    $val=$perro[$this->fields[$n]];
                    // some fields require federation specific translations
                    switch($this->fields[$n]) {
                        case 'CatGuia': $val=$this->federation->getHandlerCategory($perro['CatGuia']);
                            break;
                        case 'Categoria':
                            $val=$this->federation->getCategoryShort($perro['Categoria']);
                            break;
                        case 'Grado':
                            $val=$this->federation->getGrade($val);
                            break;
                        case 'Pais': // use long iso names
                            if (array_key_exists($val,Country::$countryList)) $val=Country::$countryList[$val];
                            break;
                        case 'Celo':
                            $val=(intval($val)==0)?"":_('Heat');
                    }
                    array_push($row,$val);
                }
                $this->myWriter->addRow($row);
                $index++;
            }
        }
        $this->myLogger->leave();
    }

    function composeTableIndividual() {
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
            if (!category_match($perro['Categoria'],$this->heights,$this->validcats)) continue;
            if ($categoria!=$perro['Categoria']) {
                if ($categoria!="") $this->myWriter->addRow(array()); // add empty row
                $row=array();
                $index=1;
                $categoria=$perro['Categoria'];
                array_push($row,_utf('Category') . ':');
                array_push($row,$this->federation->getCategoryShort($categoria));
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
                        $val=$this->federation->getCategoryShort($categoria);
                        break;
                    case 'Grado':
                        $val=$this->federation->getGrade($val);
                        break;
                    case 'Pais': // use long iso names
                        if (array_key_exists($val,Country::$countryList)) $val=Country::$countryList[$val];
                        break;
                    case 'Celo':
                        $val=(intval($val)==0)?"":_('Heat');
                }
                array_push($row,$val);
            }
            $this->myWriter->addRow($row);
            $index++;
        }
        $this->myLogger->leave();
    }

    function composeTable() {
        if ($this->team4===0) return $this->composeTableIndividual();
        else return $this->composeTableConjunta();
    }
}
?>

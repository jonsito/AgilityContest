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
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Dogs.php');
require_once(__DIR__."/common_writer.php");

class excel_ordenSalida extends XLSX_Writer {

    protected $manga; // datos de la manga
    protected $orden; // orden de salida
    protected $categoria; // categoria que estamos listando
    protected $validcats; // categorias que nos han pedido listar
    protected $teams; // lista de equipos de esta jornada
    protected $team4; // tell to print standard or team4 mode
    protected $cellHeader;

    protected $cols = array( 'Order','Name','Pedigree Name','Gender','Breed','License','KC id','Category','Grade','Handler','Club','Province','Country');
    protected $fields = array( 'Orden','Nombre','NombreLargo','Genero','Raza','Licencia','LOE_RRC','Categoria','Grado','NombreGuia','NombreClub','Provincia','Pais');

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
        }		$this->federation=Federations::getFederation(0); // defaults to RSCE
        $myDBObject= new DBObject("excel_ordenDeSalida");
        $pruebaObj= $myDBObject->__getObject("Pruebas",$prueba);
        $federation=Federations::getFederation(intval($pruebaObj->RSCE));
        $strClub=($federation->isInternational())?_('Country'):_('Club');
        // Datos de la manga
        $m = new Mangas("excel_OrdenDeSalida",$jornada);
        $this->manga= $m->selectByID($manga);
        // Datos del orden de salida
        $o = new OrdenSalida("excel_OrdenDeSalida",$manga);
        $os= $o->getData();
        $this->orden=$os['rows'];
        $this->categoria="L";
        $this->cellHeader =
            array(_('Order'),_('Dorsal'),_('Name'),_('Breed'),_('Lic'),_('Handler'),$strClub,_('Heat'),_('Comments'));
        // obtenemos los datos de equipos de la jornada indexados por el ID del equipo
        $eq=new Equipos("print_ordenDeSalida",$prueba,$jornada);
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

        // Create page
        $dogspage=$this->myWriter->addNewSheetAndMakeItCurrent();
        $dogspage->setName(_("Starting order"));
        // write header
        $this->writeTableHeader();

        foreach($this->lista as $perro) {
            $row=array();
            // extract relevant information from database received dog
            for($n=0;$n<count($this->fields);$n++) array_push($row,$perro[$this->fields[$n]]);
            $this->myWriter->addRow($row);
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
    $excel->createInfoPage(_utf("Starting order"),0);
    $excel->composeTable();
    $excel->close();
    return 0;
} catch (Exception $e) {
    die ("Error accessing database: ".$e->getMessage());
}
?>
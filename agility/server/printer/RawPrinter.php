<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 11/01/16
 * Time: 11:13
 */
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../logging.php");
require_once(__DIR__."/../tools.php");
require_once(__DIR__."/../database/classes/Resultados.php");
require_once(__DIR__."/Escpos.php");

/*
RawPrinter.php

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
 * Class RawPrinter
 * Used to translate "Done" event to raw printer if available and enabled
 * So the user can obtain a raw non-modifiable copy of what's is being done from tablet
 */
class RawPrinter {

    protected $myConfig;
    protected $myLogger;
    protected $printerName;

    function __construct() {
        // initialize
        $this->myConfig=Config::getInstance();
        $l=$this->myConfig->getEnv("debug_level");
        $this->myLogger= new Logger("RawPrinter",$l);
        $this->printerName=$this->myConfig->getEnv("event_printer");
        if($this->printerName==="") { // no printer declared
            $this->myLogger->info("No printer declared. raw printing is disabled");
            return;
        }
    }

    private function rawprinter_Open() {
        if ($this->printerName=="") return null;
        // fix parameters, enable printer and return
        try{
            $os=substr(strtoupper(PHP_OS),0,3);
            if ($os==="WIN") {
                $connector = new WindowsPrintConnector($this->printerName);
                return new Escpos($connector);
            } else {
                $connector = new FilePrintConnector($this->printerName);
                return new Escpos($connector /* ,SimpleCapabilityProfile::getInstance() */ );
            }
        } catch (Exception $e) {
            $this->myLogger->error("Cannot connect to printer $this->printerName");
            return null;
        }
    }

    private function rawprinter_Close($printer) {
        $printer->close();
    }

    private function rawprinter_retrieveData($event){
        $obj=new Resultados("RawPrinter",$event['Pru'],$event['Mng']);
        $data=array(
            'Prueba' =>     $obj->getDatosPrueba(),
            'Jornada' =>    $obj->getDatosJornada(),
            'Manga' =>      $obj->getDatosManga(),
            'Resultados' => $obj->select($event['Dog'])
        );
        return $data;
    }

    private function rawprinter_writeData($printer,$data) {
        // una impresora de TPV tipica tiene 48 caracteres por linea
        // el simbolo "_" significa un espacio
        /*
        000000000011111111112222222222333333333344444444
        012345678901234567890123456789012345678901234567
        ------------------------------------------------
        PRUEBA        _JORNADA  _MANGA         _HH:MM:SS
        DRS_-_PERRO                  _LICN_C_-_GRDO_Celo
        GUIA                          _CLUB
        F:ff T:tt R:r TI:xxx.xxx TF:xxx.xxx ELimin/NoPre
        ------------------------------------------------
        */
        $p=$data['Prueba']->Nombre;
        $j=$data['Jornada']->Nombre;
        $m=Mangas::$tipo_manga[$data['Manga']->Tipo][3];
        $d=date('H:i:s');
        $l1=sprintf("% -14s % -9s % 14s %s",substr(toASCII($p),0,14),substr(toASCII($j),0,9),toASCII($m),$d);
        $printer->text($l1);
        $printer->feed(1);
        $drs=$data['Resultados']['Dorsal'];
        $dog=$data['Resultados']['Nombre'];
        $cat=$data['Resultados']['Categoria'];
        $grd=$data['Resultados']['Grado'];
        $lic=$data['Resultados']['Licencia'];
        $cel=(($data['Resultados']['Celo'])!=0)?"Celo":"";
        $l2=sprintf("%03d - % -24s % 4s %1s-% -4s %4s",$drs,substr(toASCII($dog),0,24),substr($lic,-5),$cat,$grd,$cel);
        $printer->text($l2);
        $printer->feed(1);
        $guia=$data['Resultados']['NombreGuia'];
        $club=$data['Resultados']['NombreClub'];
        $l3=sprintf("% -30s % 17s",toASCII($guia),toASCII($club));
        $printer->text($l3);
        $printer->feed(1);
        $f=$data['Resultados']['Faltas'];
        $t=$data['Resultados']['Tocados'];
        $r=$data['Resultados']['Rehuses'];
        $ti=$data['Resultados']['TIntermedio'];
        $tf=$data['Resultados']['Tiempo'];
        $e=($data['Resultados']['Eliminado']!=0)?_("Eliminated"):"";
        $n=($data['Resultados']['NoPresentado']!=0)?"Not Present":"";
        $m=($n!=="")?$n:$e; // Not present has precedence over eliminated
        $l4=sprintf("F:%02d T:%02d R:%02d IT:%03.3f FT:%03.3f % -9s",$f,$t,$r,$ti,$tf,$m);
        $printer->setDoubleStrike(true);
        $printer->text($l4);
        $printer->setDoubleStrike(false);
        $printer->feed(1);
        // and finally add a separation line
        $printer->text("________________________________________________");
        $printer->feed(1);
        $this->myLogger->trace("\n'012345678901234567890123456789012345678901234567'\n'$l1'\n'$l2'\n'$l3'\n'$l4'");
    }

    function rawprinter_Print($event) {
        if ($event['Type']!="aceptar") {
            $this->myLogger->error("Call to rawprinter_Print() with invalid event Type: {$event['Type']}");
            return;
        }
        $printer=$this->rawprinter_Open();
        if (!$printer) return;
        // extract data from event
        $data=$this->rawprinter_retrieveData($event);
        $printer->initialize();
        $this->rawprinter_writeData($printer,$data);
        $printer->close();
    }
}
?>
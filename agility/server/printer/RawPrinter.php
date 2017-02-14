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

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
    protected $widePrinter;
    protected $cronoInter;
    protected $cronoMillis;

    function __construct($printer="",$wide=-1) {
        // initialize
        $this->myConfig=Config::getInstance();
        $l=$this->myConfig->getEnv("debug_level");
        $this->myLogger= new Logger("RawPrinter",$l);
        // retrieve configuration
        $this->printerName=($printer=="")?$this->myConfig->getEnv("event_printer"):$printer;
        $this->cronoInter= intval($this->myConfig->getEnv("crono_intermediate"));
        $this->cronoMillis= intval($this->myConfig->getEnv("crono_milliseconds"));
        $this->widePrinter= ($wide<0)? intval($this->myConfig->getEnv("wide_printer")):$wide;
        // on empty name disable raw printing. notify and return
        if($this->printerName==="") { // no printer declared
            $this->myLogger->info("No printer declared. raw printing is disabled");
            return;
        }
        $this->myLogger->trace("RawPrinter: {$this->printerName} wide:{$this->widePrinter} itime:{$this->cronoInter} millis:{$this->cronoMillis}");
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
        // una impresora de TPV tipica tiene 42 caracteres por linea
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
        $m=Mangas::getTipoManga($data['Manga']->Tipo,3,null); // do not "federationize" to retain width
        $d=date('H:i:s');
        $l1=sprintf("% -12s % -7s % 12s %s",substr(toASCII($p),0,12),substr(toASCII($j),0,7),substr(toASCII($m),0,12),$d);
        $printer->text($l1);
        $printer->feed(1);
        $drs=$data['Resultados']['Dorsal'];
        $dog=$data['Resultados']['Nombre'];
        $cat=$data['Resultados']['Categoria'];
        $grd=$data['Resultados']['Grado'];
        $lic=$data['Resultados']['Licencia'];
        $cel=(($data['Resultados']['Celo'])!=0)?"Celo":"";
        $l2=sprintf("%03d - % -18s % 4s %1s-% -4s %4s",$drs,substr(toASCII($dog),0,24),substr($lic,-5),$cat,$grd,$cel);
        $printer->text($l2);
        $printer->feed(1);
        $guia=$data['Resultados']['NombreGuia'];
        $club=$data['Resultados']['NombreClub'];
        $l3=sprintf("% -27s % 14s",substr(toASCII($guia),0,27),substr(toASCII($club),0,14));
        $printer->text($l3);
        $printer->feed(1);
        $f=$data['Resultados']['Faltas'];
        $t=$data['Resultados']['Tocados'];
        $r=$data['Resultados']['Rehuses'];

        $ti=$data['Resultados']['TIntermedio'];
        $tf=$data['Resultados']['Tiempo'];
        // set up text according milliseconds precision and intermediate time status
        $tistr=sprintf("IT:%06.2f ",$ti);
        $tfstr=sprintf("FT:%06.2f ",$tf);
        if ($this->cronoMillis!=0) { $tistr=sprintf("IT:%07.3f",$ti); $tfstr=sprintf("FT:%07.3f",$tf); }
        if ($this->cronoInter!=0) $tistr="          "; // no intermediate time: replace with spaces

        $e=($data['Resultados']['Eliminado']!=0)?_("Eliminated"):"";
        $n=($data['Resultados']['NoPresentado']!=0)?"Not Present":"";
        $m=($n!=="")?$n:$e; // Not present has precedence over eliminated
        $l4=sprintf("F:%02d T:%02d R:%02d %s %s % -5s",$f,$t,$r,$tistr,$tfstr,substr($m,0,5));
        $printer->setDoubleStrike(true);
        $printer->text($l4);
        $printer->feed(1);
        // and finally add a separation line
        $printer->text("------------------------------------------");
        $printer->feed(1);
        $printer->setDoubleStrike(false);
        $this->myLogger->trace("\n'012345678901234567890123456789012345678901'\n'$l1'\n'$l2'\n'$l3'\n'$l4'");
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
        // set up char size according printer width 58/80mmts
        $printer->setFont(($this->widePrinter==0)?Escpos::FONT_B:Escpos::FONT_A);
        $this->rawprinter_writeData($printer,$data);
        $printer->close();
    }

    function rawprinter_Check() {
        $printer=$this->rawprinter_Open();
        if (!$printer) return;
        $printer->initialize();
        $data= array(
            "Prueba" => (object) ['Nombre' => "Printer test" ],
            "Jornada" => (object) ['Nombre' => "Journey" ],
            "Manga" => (object) ['Tipo' => 0 ],
            "Resultados" => array(
                "Dorsal" => "Dors",
                "Nombre" => "Dog Name",
                "Categoria" => "C",
                "Grado" => "Grd",
                "Licencia" => "Lic",
                "Celo" => "Heat",
                "NombreGuia" => "Handler name.......",
                "NombreClub" => "Club name.........",
                "Faltas" => 3,
                "Tocados" => 2,
                "Rehuses" => 1,
                "TIntermedio" => 43.210,
                "Tiempo" => 54.321,
                "Eliminado" => 0,
                "NoPresentado" => 0,
            )
        );
        // set up char size according printer width 58/80mmts
        $printer->setFont(($this->widePrinter==0)?Escpos::FONT_B:Escpos::FONT_A);
        $this->rawprinter_writeData($printer,$data);
        $printer->close();
    }
}
?>
<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 11/01/16
 * Time: 11:13
 */
require_once(__DIR__."/../auth/Config.php");
require_once(__DIR__."/../logging.php");
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

    function rawprinter_Open() {
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
    function rawprinter_Close($printer) {
        $printer->close();
    }

    function isEnabled() { return $this->myPrinter!=null;  }

    function rawprinter_Print($event) {
        $printer=$this->rawprinter_Open();
        if (!$printer) return;
        // TODO: prettyprint this item
        $data=json_encode($event);
        $printer->initialize();
        $printer->text($data."\n");
        $printer->cut();
        $printer->close();
    }
}
?>
<?php
require_once(__DIR__."/../server/printer/Escpos.php");
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 11/01/16
 * Time: 13:58
 */

/* bash$ sudo mkfifo -m 0666 /tmp/rawprint.fifo */
$connector = new FilePrintConnector(__DIR__."/../../logs/rawprint.fifo");
$printer = new Escpos($connector,SimpleCapabilityProfile::getInstance());
if (!$printer) {  echo "Failed"; return; }
$data=json_encode(array("a"=>"Hello","b"=>"World"));
$printer->initialize();
$printer->text($data."\n");
$printer->cut();
$printer->close();

echo "Done";
?>
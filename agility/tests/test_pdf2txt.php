<?php

/*
$a=array();
exec(__DIR__."/../server/pdf/bin/pdftotext.linux -layout /home/jantonio/Descargas/Catalogo_inscripciones.pdf -",$a);
foreach($a as $line) {
        if (strstr( $line,"NombreLargo")!==false) {
                $data=substr($line,strpos($line,'{"'));
                echo $data."\n";
        }
}
*/

require_once __DIR__ . '/../server/excel/Spout/Autoloader/autoload.php';
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$sheet=null;
$reader = ReaderFactory::create(Type::PDF);
$reader->open("/home/jantonio/pCloudDrive/Public Folder/agility/Pozuelo_CuadrupleSS_2021/Catalogo_inscripciones.pdf");

// getCurrentSheet() is not available for reader. so dirty trick
// $sheet=$reader->getCurrentSheet();
foreach ($reader->getSheetIterator() as $sheet) break; // only one sheet
$rowiterator=$sheet->getRowIterator();
foreach ( $rowiterator as $row) {
        echo "Leido: ".json_encode($row).PHP_EOL;
}
$reader->close();
?>

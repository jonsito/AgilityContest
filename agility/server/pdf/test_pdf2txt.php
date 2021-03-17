<?php
require('pdf2txt.php');
$a=new PDF2Text();
$a->setFilename( '/home/jantonio/Descargas/Catalogo_inscripciones.pdf');
// $a->setUnicode(true);
$a->decodePDF();
// try to remove zeroes from UTF16 when pdf charset is longer than 8bits
$decoded=$a->output(true);

$from=str_split($decoded,1);
var_dump($from);
$to=array();
foreach($from as $item) if ($item!=0) $to[]=$item;
$string = "";
foreach ($to as $chr) $string .= chr($chr);
$b=explode(PHP_EOL,$string);
foreach($b as $line) {
        echo $line;
        if (!is_null(json_decode($line))) echo $line."\n";
}
?>

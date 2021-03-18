<?php
$a=array();
exec(__DIR__."/../server/pdf/bin/pdftotext.linux -layout /home/jantonio/Descargas/Catalogo_inscripciones.pdf -",$a);
foreach($a as $line) {
        if (strstr( $line,"NombreLargo")!==false) {
                $data=substr($line,strpos($line,'{"'));
                echo $data."\n";
        }
}
?>

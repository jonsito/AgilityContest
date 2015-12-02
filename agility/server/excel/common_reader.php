<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
require_once __DIR__.'/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

$reader->open(__DIR__."/test.xlsx");
echo "<html><body>";
foreach ($reader->getSheetIterator() as $sheet) {
    echo "<hr /><table>\n";
    echo "<tr><th>".$sheet->getName()."</th></tr>\n";
    foreach ($sheet->getRowIterator() as $row) {
        echo "<tr>";
        // do stuff with the row
        foreach ($row as $item){
            echo "<td>";
            print_r($item);
            echo "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>";
}
echo"</body></html>";
$reader->close();
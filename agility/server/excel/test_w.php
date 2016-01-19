<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
date_default_timezone_set("Europe/Madrid");
require_once __DIR__.'/Spout/Autoloader/autoload.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

$writer = WriterFactory::create( Type::XLSX );
$writer->openToBrowser( $filename );
// Headers
$writer->addRow( array( 'Order #', 'Customer name', 'Total ordered', 'Date' ) );
// Then a foreach
$writer->addRow( array( (int) 0000, 'Customer name', (float) 23.12, '20-01-2016' ) );
$writer->close();
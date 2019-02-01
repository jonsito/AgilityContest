<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 1/02/19
 * Time: 12:04
 */

require_once(__DIR__."/../server/pdf/fpdf.php");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/logging.php");
require_once(__DIR__."/../server/pdf/print_common.php");
require_once(__DIR__."/../server/pdf/classes/PrintEtiquetasCNEAC.php");

$pdf=new PrintEtiquetasCNEAC(1,1);
$pdf->set_FileName("hoja_cneac.pdf");

$pdf->AliasNbPages();
$pdf->composeTable();
$pdf->Output($pdf->get_FileName(),"D"); // "D" means open download dialog
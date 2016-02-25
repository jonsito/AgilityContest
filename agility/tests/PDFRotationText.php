<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 25/02/16
 * Time: 11:25
 */
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/pdf/print_common.php");

class PDFRotationText extends PrintCommon {
    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle,$x,$y);
        $this->Image($file,$x,$y,$w,$h);
        $this->Rotate(0);
    }
}

$pdf=new PDFRotationText('Portrait',"prueba_rotacion",0,0);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',20);
$pdf->RotatedImage(getIconPath(0,'unregistered.png'),85,60,40,16,45);
$pdf->RotatedText(100,60,'Hello!',45);
$pdf->Output("pruebarotacion.pdf","D"); // "D" means open download dialog
?>
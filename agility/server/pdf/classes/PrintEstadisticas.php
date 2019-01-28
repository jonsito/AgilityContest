<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/01/19
 * Time: 16:39
 */

class PrintEstadisticas {

    protected $pdf;     // gestor de pdf asociado
    protected $nmangas; // nombre de las mangas

    /**
     * PrintEstadisticas constructor.
     * @param {PrintCommon} $pdf pointer to pdf handler
     */
    function __construct($pdf) {
        $this->pdf=$pdf;
    }

    function addItem($item) {

    }

    function print_statsHeader($nmangas) {
        $this->nmangas=$nmangas;
        $this->pdf->ac_header(1,12);
		$this->pdf->Cell(90,8,_("Calification"),'LRTB',0,'C',true);
		foreach ($nmangas as $nmanga) {
		    if ($nmanga==="") continue;
            $this->pdf->Cell(60,8,$nmanga,'LRTB',0,'C',true);
        }
		$this->pdf->Ln(8);
    }

    function print_statsData() {
        $data=array(
            _('Excellent (0)'),
            _('Excellent'),
            _('Very good'),
            _('Good'),
            _('Not Clasified'),
            _('Eliminated'),
            _('Not Present'),
            _('Total')
            );
        $count=0;
        foreach($data as $item) {
            $b=($item===_('Total'))?'B':'';
            $this->pdf->ac_row($count,10);
            $this->pdf->Cell(90,7,$item,"LR$b",0,'L',true);
            foreach ($this->nmangas as $nmanga) {
                if ($nmanga==="") continue;
                $this->pdf->Cell(30,7,$count,"LR$b",0,'R',true);
                $percent="$count%";
                $this->pdf->Cell(30,7,$percent,"R$b",0,'R',true);
            }
            $this->pdf->Ln(7);
            $count++;
        }
    }
}
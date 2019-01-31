<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/01/19
 * Time: 16:39
 */

class PrintEstadisticas {

    protected $pdf;     // gestor de pdf asociado
    protected $stats; // array donde guardar las estadisticas

    /**
     * PrintEstadisticas constructor.
     * @param {PrintCommon} $pdf pointer to pdf handler
     */
    function __construct($pdf) {
        $this->pdf=$pdf;
        $this->stats=array();
        for ($n=0;$n<8;$n++) {
            $item=array(
                "Nombre" => "",
                _('Excellent (0)') => array('C'=>0,'T'=>0,'V'=>0),
                _('Excellent') => array('C'=>0,'T'=>0,'V'=>0),
                _('Very good') => array('C'=>0,'T'=>0,'V'=>0),
                _('Good') => array('C'=>0,'T'=>0,'V'=>0),
                _('Not Clasified') => array('C'=>0,'T'=>0,'V'=>0),
                _('Eliminated') => array('C'=>0,'T'=>0,'V'=>0),
                _('Not Present') => array('C'=>0,'T'=>0,'V'=>0),
                _('Total') => array('C'=>0,'T'=>0,'V'=>0)
            );
            array_push($this->stats,$item);
        }
    }

    // data = array (0..7) de arrays( t,v,p )
    function addItem($data) {
        for ($n=0;$n<8;$n++) {
            $p=$data[$n]['P'];
            if ( $p === "" ) continue; // no hay datos para la manga $n
            $p=floatval($p);
            $t=($data[$n]['T']==="-")?0:floatval($data[$n]['T']);
            $v=($data[$n]['V']==="-")?0:floatval($data[$n]['V']);
            if($p>=200) {
                $this->stats[$n][_('Not Present')] ['C']++;
                $this->stats[$n][_('Not Present')] ['T']+=$t;
                $this->stats[$n][_('Not Present')] ['V']+=$v;
            }
            else if($p>=100) {
                $this->stats[$n][_('Eliminated')] ['C']++;
                $this->stats[$n][_('Eliminated')] ['T']+=$t;
                $this->stats[$n][_('Eliminated')] ['V']+=$v;
            }
            else if($p>=26) {
                $this->stats[$n][_('Not Clasified')] ['C']++;
                $this->stats[$n][_('Not Clasified')] ['T']+=$t;
                $this->stats[$n][_('Not Clasified')] ['V']+=$v;
            }

            else if($p>=16) {
                $this->stats[$n][_('Good')] ['C']++;
                $this->stats[$n][_('Good')] ['T']+=$t;
                $this->stats[$n][_('Good')] ['V']+=$v;
            }
            else if($p>=6) {
                $this->stats[$n][_('Very good')] ['C']++;
                $this->stats[$n][_('Very good')] ['T']+=$t;
                $this->stats[$n][_('Very good')] ['V']+=$v;
            }
            else if($p>0) {
                $this->stats[$n][_('Excellent')] ['C']++;
                $this->stats[$n][_('Excellent')] ['T']+=$t;
                $this->stats[$n][_('Excellent')] ['V']+=$v;
            }
            else if ( $p == 0) {
                $this->stats[$n][_('Excellent (0)')] ['C']++;
                $this->stats[$n][_('Excellent (0)')] ['T']+=$t;
                $this->stats[$n][_('Excellent (0)')] ['V']+=$v;
            }
            $this->stats[$n][_('Total')] ['C']++;
            $this->stats[$n][_('Total')] ['T']+=$t;
            $this->stats[$n][_('Total')] ['V']+=$v;
        }

    }

    function print_statsHeader($nmangas) {
        // primera parte de la cabecera: nombre de las mangas
        $this->pdf->ac_header(1,12);
		$this->pdf->Cell(90,8,_("Calification"),'LRT',0,'C',true);
		for ($n=0;$n<8;$n++) {
		    $nombre=$nmangas[$n];
		    if ($nombre==="") continue;
            $this->stats[$n]['Nombre']=$nombre;
            $this->pdf->Cell(60,8,$nombre,'LRTB',0,'C',true);
        }
		$this->pdf->Ln(8);
		// segunda parte: datos de las estadisticas
        $this->pdf->ac_header(1,8);
        $this->pdf->Cell(90,5,"",'LRB',0,'C',true);
        for ($n=0;$n<8;$n++) {
            if ($nmangas[$n]==="") continue;
            $this->pdf->Cell(15,5,_("Dogs"),'LRB',0,'C',true);
            $this->pdf->Cell(15,5,_("Percent"),'LRB',0,'C',true);
            $this->pdf->Cell(15,5,_("S med"),'LRB',0,'C',true);
            $this->pdf->Cell(15,5,_("T med"),'LRB',0,'C',true);
        }
        $this->pdf->Ln(5);
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
        $line=0;
        foreach($data as $item) {
            $b=($item===_('Total'))?'B':'';
            $this->pdf->ac_row($line,9);
            $this->pdf->Cell(90,7,$item,"LR$b",0,'L',true);
            $this->pdf->ac_row($line,8);
            for ($n=0;$n<8;$n++) { // PENDING: EVAL HOW MANY ROUNDS
                if ($this->stats[$n]['Nombre']==="") continue;
                $count=$this->stats[$n][$item]['C'];
                $total=$this->stats[$n][_('Total')]['C']; // prevent divide by zero
                $percent="-";
                if ($total!=0) $percent=intval(100*$count/$total)."%";
                // numero y porcentaje
                $this->pdf->Cell(15,7,$count,"LR$b",0,'R',true);
                $this->pdf->Cell(15,7,$percent,"R$b",0,'R',true);
                // medias de tiempo y velocidad por categoria
                if ( ($count==0) || in_array($item, array(_("Eliminated"),_("Not Present"),_("Total")) ) ) {
                    $tmed="-";
                    $smed="-";
                } else {
                    $smed=number_format2($this->stats[$n][$item]['V']/$count,2) . " m/s";
                    $tmed=number_format2($this->stats[$n][$item]['T']/$count,2) . " seg";
                }
                $this->pdf->Cell(15,7,$smed,"LR$b",0,'R',true);
                $this->pdf->Cell(15,7,$tmed,"R$b",0,'R',true);
            }
            $this->pdf->Ln(7);
            $line++;
        }
    }
}
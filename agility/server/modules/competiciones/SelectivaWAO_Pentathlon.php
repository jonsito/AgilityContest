<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */
class SelectivaWAO_Pentathlon extends Competitions {
    function __construct() {
        parent::__construct("Selectiva WAO - Pentathlon");
        $this->federationID=2;
        $this->competitionID=1;
        $this->moduleVersion="1.0.0";
        $this->moduleRevision="20170506_1929";
        $this->federationLogoAllowed=true;
    }

    function useLongNames() { return true; }

    /**
     * Gets Course penalization, Time, and SCT data and compose penalization
     *
     * WAO Penthatlon resolves penalization as PTime+PCourse+Time
     *
     * @param {array} $perro dog data . Passed by reference
     * @param {array} $tdata sct data
     */
    public function evalPartialPenalization(&$perro,$tdata) {
        $trs=floatval($tdata['trs']);
        $trm=floatval($tdata['trm']);
        $tiempo=floatval($perro['Tiempo']);
        // perro eliminado
        if ($perro['Eliminado']!=0) {
            // la norma indica que eliminado tiene PT=50 y PR=50. raroraroraro, pero es lo que hay
            $perro['PTiempo']=50.0;
            $perro['PRecorrido']=50.0;
            $perro['Penalizacion']=	100.0;
            return;
        }
        // perro no presentado
        if ($perro['NoPresentado']!=0) {
            $perro['PTiempo']=0.0;
            $perro['PRecorrido']=0.0;
            $perro['Penalizacion']=	200.0;
            return;
        }
        // las penalizaciones por tiempo y recorrido no pueden superar 50 puntos
        if (($perro['PRecorrido']>50.0) &&($perro['PRecorrido']<100.0)) $perro['PRecorrido']=50.0;
        // ahora vamos a evaluar la penalizacion por tiempo
        if ($trs==0) { // si TRS==0 no hay penalizacion por tiempo
            $perro['PTiempo']		= 	0.0;
            $perro['Penalizacion']=	floatval($perro['PRecorrido']+$perro['Tiempo']);
        } else { // TRS>0 evaluamos penalizacion por tiempo y penalizacion final
            if ($tiempo>=$trm) { // Superado TRM: eliminado
                // la norma indica que eliminado tiene PT=50 y PR=50. raroraroraro, pero es lo que hay
                $perro['PTiempo']=50.0;
                $perro['PRecorrido']=50.0;
                $perro['Penalizacion']=	100.0;
                $perro['Eliminado']=1;
            } else if ($tiempo>=$trs) { // Superado TRS
                $perro['PTiempo']		=	min(50.0,$tiempo - $trs);
                $perro['Penalizacion']=	floatval($perro['PRecorrido'])	+	$perro['PTiempo'] + $perro['Tiempo'];
            } else { // Por debajo del TRS
                $perro['PTiempo']		= 	0.0;
                $perro['Penalizacion']=	floatval($perro['PRecorrido']+$perro['Tiempo']);
            }
        }
    }

    /**
     * Evalua la calificacion parcial del perro
     *
     * En el wao el tiempo se suma a la penalizacion,
     * por lo que para obtener excelente, muy bien, bien, etc
     * es necesario descontar el tiempo
     *
     * @param {object} $m datos de la manga
     * @param {array} $perro datos de puntuacion del perro. Passed by reference
     * @param {array} $puestocat puesto en funcion de la categoria
     */
    public function evalPartialCalification($m,&$perro,$puestocat) {
        // datos para la exportacion de parciales en excel
        $perro['Puntos'] = 0;
        $perro['Estrellas']= 0;
        $perro['Extras']= 0;
        $ptiempo=$perro['Penalizacion']-$perro['Tiempo'];
        if ($perro['Penalizacion']>=400)  { // pending
            $perro['Penalizacion']=400.0;
            $perro['Calificacion'] = "-";
            $perro['CShort'] = "-";
        }
        else if ($perro['Penalizacion']>=200)  {
            $perro['Penalizacion']=200.0;
            $perro['Calificacion'] = _("Not Present");
            $perro['CShort'] = _("N.P.");
        }
        else if ($perro['Penalizacion']>=100) {
            $perro['Penalizacion']=100.0;
            $perro['Calificacion'] = _("Eliminated");
            $perro['CShort'] = _("Elim");
        }
        else if ( $ptiempo>=26)	{
            $perro['Calificacion'] = _("Not Clasified");
            $perro['CShort'] = _("N.C.");
        }
        else if ($ptiempo>=16)	{
            $perro['Calificacion'] = _("Good");
            $perro['CShort'] = _("Good");
        }
        else if ($ptiempo>=6)	{
            $perro['Calificacion'] = _("Very good");
            $perro['CShort'] = _("V.G.");
        } else {
            // en wao penthatlon no hay concepto de cero
            $perro['Calificacion'] = _("Excellent");
            $perro['CShort'] = _("Exc");
        }
    }
}
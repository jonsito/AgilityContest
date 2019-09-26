<?php
/*
Resultados_KO.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Resultados_KO extends Resultados {
	/**
	 * Constructor
	 * @param {string} $file caller for this object
     * @param {object} $prueba Prueba
     * @param {object} $jornada Jornada
     * @param {object} $manga Manga
	 * @throws Exception when
	 * - cannot contact database
	 * - invalid manga ID
	 * - manga is closed
	 */
	function __construct($file,$prueba,$jornada,$manga) {
		parent::__construct($file,$prueba,$jornada,$manga);
	}

    /**
     * Inserta perro en la lista de resultados de la manga
     * los datos del perro se toman de la tabla perroguiaclub
     * @param {array} $objperro datos perroguiaclub
     * @param {array} $inscripcion datos de la inscripcion
     * @param {array} $eqdata datos del equipo por defecto de la jornada
     * @return "" on success; else error string
     */
    function insertByData($objperro,$inscripcion,$eqdata){
        $res=parent::insertByData($objperro,$inscripcion,$eqdata);
        if ($res!=="") return $res;
        // en pruebas ko, la insercion/borrado de un perro obliga a reorganizar toda la prueba,
        // por lo que hay que reajustar el campo games de todos los participantes a 1(primera manga) o cero(resto de mangas)
        $firstround=($this->getDatosManga()->Tipo==15)?1:0;
        $mid=$this->getDatosManga()->ID;
        $str="UPDATE resultados SET Games={$firstround} WHERE Manga={$mid}";
        $res=$this->query($str);
        if (!$res)$this->myLogger->error("insertByData(KO): ".$this->conn->error);
        return "";
    }

    /**
     * Borra el idperro de la lista de resultados de la manga
     * @param {integer} $idperro
     * @return "" on success; null on error
     */
    function delete($idperro) {
        $res=parent::delete($idperro);
        if ($res!=="") return $res;
        // en pruebas ko, la insercion/borrado de un perro obliga a reorganizar toda la prueba,
        // por lo que hay que reajustar el campo games de todos los participantes a 1(primera manga) o cero(resto de mangas)
        $firstround=($this->getDatosManga()->Tipo==15)?1:0;
        $mid=$this->getDatosManga()->ID;
        $str="UPDATE resultados SET Games={$firstround} WHERE Manga={$mid}";
        $res=$this->query($str);
        if (!$res)$this->myLogger->error("insertByData(KO): ".$this->conn->error);
        return "";
    }

    /**
     * Presenta una tabla ordenada segun los resultados de la manga
     *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8:L+M+S+T 9:X 10:X+L 11:M+S+T 12:X+L+M+S+T
     *@return {array} requested data or error
     */
    function getResultadosIndividual($mode) {
        // en una prueba KO los resultados individuales realmente no cuentan:
        // solo se tiene en cuenta cual de los perros que compiten por parejas pasa a la siguiente ronda.
        //
        // El metodo standard getResultadosIndividual() retorna los perros ordenados por resultados, con indicacion
        // del puesto que han obtenido por individual.
        // Lo que vamos a hacer es reordenar dicho resultado en función del orden de salida, combinando las funciones
        // Resultados::getResultadosIndividual() y Mangas::getData(), de manera que la salida va a estar ordenada
        // segun el orden de parejas

        // adicionalmente evaluaremos la calificacion: "Siguiente ronda" / "Eliminado"
        // obviando la llamada a evalPartialCalification, pues es dependiente de competición/federacion,
        // mientras que el KO va "por libre"

        $this->myLogger->enter();
        $res=parent::getResultadosIndividual(11); // ignore categories, just group all

        // le pasamos estos datos a OrdenSalida::getData()
        $osobj=Competitions::getOrdenSalidaInstance("Resultados KO",$this->getDatosManga());
        $osres=$osobj->getData(true,8,$res);

        // y ahora evaluamos las calificaciones dos a dos
        for ($n=1;$n<$osres['total'];$n+=3) {
            $p1=$osres['rows'][$n]['Puesto'];
            $p2=isset($osres['rows'][$n+1])?$osres['rows'][$n+1]['Puesto']:0;
            $osres['rows'][$n]['Puntos']=0;
            $osres['rows'][$n+1]['Puntos']=0;
            if ($p2==0) { // el ultimo perro no tiene resultados
                $osres['rows'][$n]['Calificacion'] = _("Promote to next")/*." &#x2714;"*/;
                $osres['rows'][$n]['CShort'] = _("Pass");
            } else if ($p1<$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Promote to next")/*." &#x2714;"*/;
                $osres['rows'][$n]['CShort'] = _("Pass");
                $osres['rows'][$n+1]['Calificacion'] = _("Eliminated");
                $osres['rows'][$n+1]['CShort'] = _("Out");
            } else if ($p1==$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Need to run again");
                $osres['rows'][$n]['CShort'] = _("Again");
                $osres['rows'][$n+1]['Calificacion'] = _("Need to run again");
                $osres['rows'][$n+1]['CShort'] = _("Again");
            } else if ($p1>$p2) {
                $osres['rows'][$n]['Calificacion'] = _("Eliminated");
                $osres['rows'][$n]['CShort'] = _("Out");
                $osres['rows'][$n+1]['Calificacion'] = _("Promote to next")/*." &#x2714;"*/;
                $osres['rows'][$n+1]['CShort'] = _("Pass");
            }
        }
        $res['rows']=&$osres['rows'];
        $res['total']=count($res['rows']);

        $this->myLogger->leave();
        return $res;
    }

}
?>
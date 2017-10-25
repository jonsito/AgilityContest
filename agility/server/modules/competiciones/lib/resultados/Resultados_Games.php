<?php
/*
Resultados_KO.php

Copyright  2013-2017 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

class Resultados_Games extends Resultados {
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

    // En Snooker y Gambler no hay faltas ni rehuses,
    // solo puntuaciones en secuencia de apertura y cierre
    // junto con el tiempo
    //
    // AgilityContest usa los campos Faltas y Tocados para almacenar los puntos de cada secuencia
    // En PRecorrido guardamos los puntos de la secuencia de apertura
    // En PTiempo guardamos los puntos de la secuencia de cierre/Gambler
    // En Penalizacion guardamos la suma de PTiempo y PRecorrido
    // los campos puntos y calificacion no se usan
    // por consiguiente El metodo standard getResultadosIndividual() no es valido en este caso
    //

    /**
     * Presenta una tabla ordenada segun los resultados de la manga
     *@param {integer} $mode 0:L 1:M 2:S 3:MS 4:LMS 5:T 6:L+M 7:S+T 8 L+M+S+T
     *@return {array} requested data or error
     */
    function getResultadosIndividual($mode) {
        $this->myLogger->enter();
        $idmanga=$this->IDManga;

        // FASE 0: en funcion del tipo de recorrido y modo pedido
        // ajustamos el criterio de busqueda de la tabla de resultados
        $where="(Manga=$idmanga) AND (Pendiente=0) AND (PerroGuiaClub.ID=Resultados.Perro) ";
        $cat="";
        if ($mode!=8) $cat=sqlFilterCategoryByMode($mode,"Resultados."); // notice the ending dot '.'
        if ($cat===null) return $this->error("modo de recorrido desconocido:$mode");
        // FASE 1: recogemos resultados ordenados por precorrido y tiempo
        // como en este caso se puntua de mas puntos a menos, vamos a poner eliminado y no presentado
        // como numeros negativos :-)
        $res=$this->__select(
            "Resultados.Dorsal,Resultados.Perro,Resultados.Nombre,NombreLargo,Resultados.Raza,Equipo,Resultados.Licencia,Resultados.Categoria,Resultados.Grado,
				    Resultados.NombreGuia,Resultados.NombreClub,PerroGuiaClub.LOE_RRC,PerroGuiaClub.CatGuia,
				    Faltas,Tocados,Rehuses,Tiempo,Eliminado,NoPresentado,Resultados.Celo, Resultados.Games,
					0 AS PRecorrido, 0 AS PTiempo, Faltas+Tocados AS Penalizacion, '' AS Calificacion, 0 AS Velocidad",
            "Resultados,PerroGuiaClub",
            "$where $cat",
            " Penalizacion DESC, Tiempo ASC",
            "");
        if (!is_array($res)){
            $this->myLogger->leave();
            return $this->error($this->conn->error);
        }

        $table=$res['rows'];
        // FASE 2: evaluamos TRS Y TRM
        $tdata=$this->evalTRS($mode,$table); // array( 'dist' 'obst' 'trs' 'trm', 'vel')
        $res['trs']=$tdata; // store trs data into result

        // FASE 3: añadimos ptiempo, puntuacion, clasificacion y logo
        $clubes=new Clubes("Resultados::getResultadosIndividual",$this->getDatosPrueba()->RSCE);
        $size=count($table);
        $comp=$this->getCompetitionObject();
        for ($idx=0;$idx<$size;$idx++ ){
            // evaluate penalization
            // si eliminado o no presentado PTiempo,PRecorrido y Penalizacion a cero
            $comp->evalPartialPenalization($table[$idx],$tdata);
            // anyadimos nombre del equipo
            $dequipos=$this->getDatosEquipos();
            $eqinfo=$dequipos[$table[$idx]['Equipo']];
            $table[$idx]['NombreEquipo']=$eqinfo['Nombre'];
            // anyadimos logotipo del club
            $table[$idx]['LogoClub']=$clubes->getLogoName('NombreClub',$table[$idx]['NombreClub']);
        }
        // FASE 4: re-ordenamos los datos en base a la puntuacion y calculamos campo "Puesto"
        // recuerda que en games a mayor puntuacion(penalizacion) queda primero
        usort($table, function($a, $b) {
            if ( $a['Penalizacion'] == $b['Penalizacion'] )	return ($a['Tiempo'] > $b['Tiempo'])? 1:-1;
            return ( $a['Penalizacion'] < $b['Penalizacion'])?1:-1;
        });

        // format output data and take care con duplicated penalizacion and time
        // calculamos campo "Puesto", "Calificacion" y Puntos
        $puestocat=array( 'C'=>1, 'L' => 1, 'M'=>1, 'S'=>1, 'T'=>1); // ultimo puesto por cada categoria
        $lastcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0);  // ultima puntuacion por cada categoria
        $countcat=array( 'C'=>0, 'L' => 0, 'M'=>0, 'S'=>0, 'T'=>0); // perros contabilizados de cada categoria

        for($idx=0;$idx<$size;$idx++) {
            // vemos la categoria y actualizamos contadores de categoria
            $cat=$table[$idx]['Categoria'];
            $countcat['C']++; // Conjunta
            $countcat[$cat]++; // Por categoria

            // obtenemos la penalizacion del perro actual
            $now=100*$table[$idx]['Penalizacion']+$table[$idx]['Tiempo'];

            // ajustamos puesto conjunto y guardamos resultado
            if ($lastcat['C']!=$now) { $lastcat['C']=$now; $puestocat['C']=$countcat['C']; }
            $table[$idx]['Puesto']=$puestocat['C'];

            // ajustamos puesto por categoria y guardamos resultado
            if ($lastcat[$cat]!=$now) { $lastcat[$cat]=$now; $puestocat[$cat]=$countcat[$cat]; }
            $table[$idx]['Pcat']=$puestocat[$cat];

            // finalmente llamamos al modulo de la competicion para evaluar la calificacion
            $comp->evalPartialCalification($this->getDatosManga(),$table[$idx],$puestocat);
        }

        // componemos datos del array a retornar
        $res['rows']=$table;
        $res['manga']=$this->getDatosManga();
        $res['trs']=$tdata;
        $this->myLogger->leave();
        return $res;
    }

}
?>
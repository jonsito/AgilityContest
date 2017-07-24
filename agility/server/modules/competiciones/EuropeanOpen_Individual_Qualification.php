<?php

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 16/11/16
 * Time: 10:58
 */

/*
From: http://www.eo2017.it/files/eo2017_regulations_en.pdf
The qualification for the Individual Event is held in the categories Large, Medium and Small.

In the Individual qualification an Agility round and a Jumping round is run in all
3 height categories. Both rounds are scored separately for faults and time for all
competitors.

In the category Large the first 30 placed competitors in both the Agility as well
as the Jumping round will have qualified for the Final, in the categories Small and
Medium each time the first 18 placed competitors. Where a team qualifies for
the Final in both the Agility as well as the Jumping round, the worse of the 2
scores will be scratched and the next ranking team in that class will move up until
all 60 places in Large and all 36 places in Small and in Medium have been filled
for the Finals. In case of a double qualification with the same score in both rounds
the Jumping result is scratched.

In addition, the competitors of every country will be ranked separately according to
the total of faults and time of both runs in each height category. This ranking
determines the internal, country-specific selection order for the EO final. If no team
(handler + dog) of a country finished the qualification without elimination in one of
the 2 runs, the lower number of faults in the other run decides. In the case of a tie
(elimination in one run + same number of faults in the other run, no matter in
which run the elimination happened) the better score in the overall result list of the
run without elimination counts. In the case of a further tie the better Agility-run will
break the tie, finally a draw will decide. A team with 2 eliminations will not qualify
for the Final.

The top team from a country qualifies in each height category for the EO Final, as
long as no dog from this country has already qualified directly for the EO Final as
“Top 30” (L) resp. “Top 18” (S/M) in this height category. The qualification for the
Final is not transferable to another team.

Furthermore the EO Winners of previous EO edition in all 3 height categories have
qualified for the EO Finals of this one, without imposing on their country’s
number allocation.
*/


/*
En resumen es una prueba generica Open de tres alturas, en la que no hay clasificacion
conjunta.
Existe adicionalmente un sistema de repesca por paises, pero a efectos del programa
no es necesario que sea tenido en cuenta, salvo para los listados de resultados
*/
class EuropeanOpen_Individual_Qualification extends Competitions {
    function __construct() {
        parent::__construct("European Open - Qualification Series - Individual");
        $this->federationID=9;
        $this->competitionID=4;
        $this->moduleRevision="20170623_1151";
    }

    function useLongNames() { return true; }
}
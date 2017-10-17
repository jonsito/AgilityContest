<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 11/10/17
 * Time: 13:28
 */

class Clasificaciones_SelWAO extends Clasificaciones {

    /**
     * Constructor
     * @param {string} $file caller for this object
     * @param {object} $prueba prueba object
     * @param {object} $jornada jornada object
     * @param {integer} $perro Dog id used to evaluate position
     * @throws Exception if cannot contact database or invalid prueba/jornada ID
     */
    function __construct($file,$prueba,$jornada,$perro=0) {
        parent:: __construct($file,$prueba,$jornada,$perro);
    }

    /**
     * Method to short final scores based in penalization/time
     *
     * for Penthatlon use Penalization scores
     *  - on same score decide by speedstakes
     *  - on same decide by AgilityB, Then JumpingB, then AgilityA and last JumpingA
     *
     * for biathlon use score points
     *  - on same scores decide AgilityA+B, then Jumping A+B points
     *
     * for games use score points as sorting method
     *  - on same score decide Gamblers
     *
     * @param {array} $final scores
     * @param {array} $c1 scores for round 1
     * @param {array} $c2 scores for round 2
     * @param {array} $c3 scores for round 3
     * @param {array} $c4 scores for round 4
     * @param {array} $c5 scores for round 5
     * @param {array} $c6 scores for round 6
     * @param {array} $c7 scores for round 7
     * @param {array} $c8 scores for round 8
     */
    protected function sortFinal(&$final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8) {
        $tj= intval($this->jornada->Tipo_Competicion);
        switch ($tj) {
            case 1: // penthatlon
                break;
            case 2: // biathlon
                break;
            case 3: // games
                break;
            default: // this is not supposed to occur. notify and use default
                $this->myLogger->error("Clasificaciones:: invalid Tipo_Competicion:{$tj} in jornada:{$this->jornada->ID} in Games rounds");
                return parent::sortFinal($final,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8);
        }
    }
}
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
     * Evalua las clasificaciones en funcion de los datos pedidos
     * @param {integer} $rondas bitfield Jornadas::$tipo_ronda
     * @param {array[{integer}]} $idmangas array con los ID's de las mangas a evaluar
     * @param {integer} $mode Modo 0:L 1:M 2:S 3:M+S 4:L+M+S 5:T 6:L+M 7:S+T 8:L+M+S+T
     */
    function clasificacionFinal($rondas,$idmangas,$mode) {
        // evaluate which journey modality we are playing
        // penthatlon
        // biathlon
        // games
    }
}
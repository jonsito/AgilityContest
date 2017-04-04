<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
require_once __DIR__ . '/../server/pdf/fpdm/fdf.php';

$fdf=new FDF();
$fdf->create();
/* templates */
$fdf->set_value("Titulo","Hola mundillo");
$fdf->set_value("Check","Off");
// source file
$fdf->set_file( "Listado_Participantes2.pdf");
/* output fdf */
$fdf->save("Listado_Participantes2.fdf");
/* clean up */
$fdf->close();
?>
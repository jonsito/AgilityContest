<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
require_once __DIR__.'/../server/pdf/ntk_fdf.php';

$fdf=new FDF();
$fdf->ntk_fdf_create();
/* templates */
$fdf->ntk_fdf_set_value("Titulo","Hola mundillo");
$fdf->ntk_fdf_set_value("Check","Off");
// source file
$fdf->ntk_fdf_set_file( "Listado_Participantes2.pdf");
/* output fdf */
$fdf->ntk_fdf_save("Listado_Participantes2.fdf");
/* clean up */
$fdf->ntk_fdf_close();
?>
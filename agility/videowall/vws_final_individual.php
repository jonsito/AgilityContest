<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once(__DIR__."/../server/tools.php");
require_once(__DIR__."/../server/auth/Config.php");
require_once(__DIR__."/../server/auth/AuthManager.php");
$config =Config::getInstance();
$am = new AuthManager("Videowall::combinada");
if ( ! $am->allowed(ENABLE_VIDEOWALL)) { include_once("unregistered.php"); return 0;}

?>
<!--
vws_final_individual.php

Copyright  2013-2016 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 -->
<!--
<div id="vws-panel" style="padding:5px;">
-->
    <div id="vws_header>">
        <form id="vws_hdr_form">
        <?php if ($config->getEnv("vws_uselogo")!=0) {
            // logotipo alargado del evento
            echo '<input type="hidden" id="vws_hdr_logoprueba" name="LogoPrueba" value="/agility/images/agilityawc2016.png"/>';
            echo '<img src="/agility/images/agilityawc2016.png" class="vws_imgpadding" id="vws_hdr_logo" alt="Logo"/>';
            echo '<input type="hidden"      id="vws_hdr_prueba"     name="Prueba" value="Prueba"/>';
            echo '<input type="hidden"      id="vws_hdr_jornada"     name="Jornada" value="Jornada"/>';
        } else {
            // logotipo del organizador. prueba y jornada en texto
            echo '<input type="hidden" id="vws_hdr_logoprueba" name="LogoPrueba" value="/agility/images/logos/agilitycontest.png"/>';
            echo '<img src="/agility/images/logos/agilitycontest.png" class="vws_imgpadding" id="vws_hdr_logo" alt="Logo"/>';
            // nombre de la prueba y jornada
            echo '<input type="text"      id="vws_hdr_prueba"     name="Prueba" value="Prueba"/>';
            echo '<input type="text"      id="vws_hdr_jornada"     name="Jornada" value="Jornada"/>';
        }
        ?>
            <input type="text"      id="vws_hdr_manga"     name="Manga" value="Manga"/>
            <input class="trs" type="text"      id="vws_hdr_trs"     name="TRS" value="Dist/TRS"/>
        </form>
        <span class="vws_theader" id="vws_hdr_calltoring"><?php _e('Call to ring');?> </span>
        <span class="vws_theader" id="vws_hdr_teaminfo"><?php _e("Competitor's data");?> </span>
        <span class="vws_theader" id="vws_hdr_lastround"><?php _e('Round');?> </span>
        <span class="vws_theader" id="vws_hdr_finalscores"><?php _e('Final');?> </span>
    </div>
    
    <div id="vws_llamada">
<?php for($n=0;$n<8;$n++) {
    echo '<form id="vws_call_'.$n.'" class="vws_css_call_'.($n%2).' vws_entry">';
    echo '<input type="text" id="vws_call_Orden_'.$n.'" name="Orden" value="Orden '.$n.'"/>';
    echo '<input type="hidden" id="vws_call_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_call_'.($n%2).' vws_imgpadding" src="/agility/images/logos/agilitycontest.png" id="vws_call_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Perro_'.$n.'"      name="Perro" value="Perro '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Licencia_'.$n.'"   name="Licencia" value="Lic '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_CatGrad_'.$n.'"    name="CatGrad" value="Grad '.$n.'"/>';
    echo '<input type="text"      id="vws_call_Dorsal_'.$n.'"     name="Dorsal" value="Dorsal '.$n.'"/>';
    echo '<input type="text"      id="vws_call_Nombre_'.$n.'"     name="Nombre" value="Nombre '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Celo_'.$n.'"       name="Celo" value="Celo $1"/>';
    echo '<input type="text"      id="vws_call_NombreGuia_'.$n.'" name="NombreGuia" value="Guia '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_NombreClub_'.$n.'" name="NombreClub" value="Club '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_F_'.$n.'"          name="Faltas" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_T_'.$n.'"          name="Tocados" value="Toc '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_FaltasTocados_'.$n.'" name="FaltasTocados" value=F/T $n/>';
    echo '<input type="hidden"    id="vws_call_Rehuses_'.$n.'"    name="Rehuses" value="R '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Puesto_'.$n.'"     name="Puesto" value="P '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Tintermedio_'.$n.'" name="TIntermedio" value="TI '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Tiempo_'.$n.'"     name="Tiempo" value="T '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Eliminado_'.$n.'"  name="Eliminado" value="Elim '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_NoPresentado_'.$n.'" name="NoPresentado" value="NPr '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Pendiente_'.$n.'"  name="Pendiente" value="Pend '.$n.'"/>';
    echo '</form>';
} ?>
    </div>
    
    <div id="vws_results">
<?php for($n=0;$n<10;$n++) {
    echo '<form id="vws_results_'.$n.'" class="vws_css_results_'.($n%2).' vws_entry">';
    echo '<input type="hidden" id="vws_results_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_results_'.($n%2).' vws_imgpadding" src="/agility/images/logos/agilitycontest.png" id="vws_results_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Dorsal_'.$n.'"     name="Dorsal" value="Dorsal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Perro_'.$n.'"      name="Perro" value="Perro '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Nombre_'.$n.'"     name="Nombre" value="Nombre '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Licencia_'.$n.'"      name="Licencia" value="Lic '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="text"      id="vws_results_NombreGuia_'.$n.'" name="NombreGuia" value="Guia '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Equipo_'.$n.'"     name="Equipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_NombreClub_'.$n.'" name="NombreClub" value="Club '.$n.'"/>';
    echo '<!-- data on round 1  (at runtime round1/round2 are selected and/or hidden acording round type -->';
    echo '<input type="hidden"    id="vws_results_F1_'.$n.'"         name="F1" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_R1_'.$n.'"         name="R1" value="Reh '.$n.'"/>';
    echo '<input type="text"      class="lborder" id="vws_results_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_V1_'.$n.'"         name="V1" value="Vel1 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_C1_'.$n.'"         name="C1" value="Cal1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_E1_'.$n.'"         name="E1" value="Elim1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_N1_'.$n.'"         name="N1" value="NoPr1 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Puesto1_'.$n.'"    name="Puesto1" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2  (at runtime round1/round2 are selected and/or hidden acording round type -->';
    echo '<input type="hidden"    id="vws_results_F2_'.$n.'"         name="F2" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_R2_'.$n.'"         name="R2" value="Reh '.$n.'"/>';
    echo '<input type="text"      class="lborder" id="vws_results_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_V2_'.$n.'"         name="V2" value="Vel2 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_C2_'.$n.'"         name="C2" value="Cal2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_E2_'.$n.'"         name="E2" value="Elim2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_N2_'.$n.'"         name="N2" value="NoPr '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Puesto2_'.$n.'"    name="Puesto2" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      class="lborder" id="vws_results_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Calificacion_'.$n.'" name="Calificacion" value="Calif '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
    echo '</form>';

}?>
    </div>
    
    <div id="vws_perro_en_pista">
<?php
    echo '<form id= "vws_current" class="vws_css_current_0 vws_entry">';
    echo '<input type="text" id= "vws_current_Orden" name="Orden" value="Orden"/>';
    echo '<input type="hidden" id= "vws_current_LogoClub"      name="LogoClub" value="Logo"/>';
    echo '<img class="vws_css_current_'.($n%2).' vws_imgpadding" src="/agility/images/logos/getLogo.php?Federation=1&Logo=ES.png" id= "vws_current_Logo" name="Logo" alt="Logo"/>';
    echo '<input type="hidden"    id= "vws_current_Perro"      name="Perro" value="Perro"/>';
    echo '<input type="hidden"    id= "vws_current_Categoria"  name="Categoria" value="Cat"/>';
    echo '<input type="hidden"    id= "vws_current_Grado"      name="Grado" value="Grad"/>';
    echo '<input type="hidden"    id= "vws_current_CatGrad"    name="CatGrad" value="Grad"/>';
    echo '<input type="text"      id= "vws_current_Dorsal"     name="Dorsal" value="Dorsal"/>';
    echo '<input type="text"      id= "vws_current_Nombre"     name="Nombre" value="Nombre"/>';
    echo '<input type="hidden"    id= "vws_current_Celo"       name="Celo" value="Celo $1"/>';
    echo '<input type="text"      id= "vws_current_NombreGuia" name="NombreGuia" value="Guia"/>';
    echo '<input type="hidden"    id= "vws_current_NombreEquipo" name="NombreEquipo" value="Equipo"/>';
    echo '<input type="hidden"    id= "vws_current_NombreClub" name="NombreClub" value="Club"/>';
    echo '<input type="hidden"    id= "vws_current_Faltas"      name="Faltas" value="Flt"/>';
    echo '<input type="hidden"    id= "vws_current_Tocados"     name="Tocados" value="Toc"/>';
    echo '<span id= "vws_current_FaltasTocados">F/T</span>';
    echo '<input type="hidden"      id= "vws_current_Rehuses"    name="Rehuses" value="R"/>';
    echo '<span id= "vws_current_Refusals">F/T</span>';
    echo '<input type="hidden"    id= "vws_current_Tintermedio" name="TIntermedio" value="Tint"/>';
    echo '<input type="hidden"    id= "vws_current_Tiempo"     name="Tiempo" value="Time"/>';
    echo '<span id= "vws_current_Time">Time</span>';
    echo '<input type="hidden"    id= "vws_current_Puesto"     name="Puesto" value="P"/>';
    echo '<input type="hidden"    id= "vws_current_Eliminado"  name="Eliminado" value=""/>';
    echo '<input type="hidden"    id= "vws_current_NoPresentado" name="NoPresentado" value=""/>';
    echo '<input type="hidden"    id= "vws_current_Pendiente"  name="Pendiente" value="Pend"/>';
    echo '<span id="vws_current_Result">Res</span>';
    echo '<span id="vws_current_Active" style="display:none">Active</span>';
    echo '</form>';
?>
    </div>
    
    <div id="vws_sponsors">
        <?php include_once(__DIR__."/../videowall/vws_footer.php");?>
    </div>
    
    <div id="vws_before">
<?php for($n=0;$n<2;$n++) {
    echo '<form id="vws_before_'.$n.'" class="vws_css_results_'.($n%2).' vws_entry">';
    echo '<input type="text"      id="vws_before_Orden_'.$n.'"      name="Orden" value="Orden '.$n.'"/>';
    echo '<input type="hidden" id="vws_before_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_results_'.($n%2).' vws_imgpadding" src="/agility/images/logos/agilitycontest.png" id="vws_before_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Dorsal_'.$n.'"     name="Dorsal" value="Dorsal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Perro_'.$n.'"      name="Perro" value="Perro '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Nombre_'.$n.'"     name="Nombre" value="Nombre '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Licencia_'.$n.'"   name="Licencia" value="Lic '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="text"      id="vws_before_NombreGuia_'.$n.'" name="NombreGuia" value="Guia '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Equipo_'.$n.'"     name="Equipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_NombreClub_'.$n.'" name="NombreClu;outline-width:2pxb" value="Club '.$n.'"/>';
    echo '<!-- data on round 1  (at runtime round1/round2 are selected and/or hidden acording round type  -->';
    echo '<input type="hidden"    id="vws_before_F1_'.$n.'"         name="F1" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_R1_'.$n.'"         name="R1" value="Reh '.$n.'"/>';
    echo '<input type="text"      class="lborder" id="vws_before_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_V1_'.$n.'"         name="V1" value="Vel1 '.$n.'"/>';
    echo '<input type="text"      id="vws_before_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_C1_'.$n.'"         name="C1" value="Cal1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_E1_'.$n.'"         name="E1" value="Elim1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_N1_'.$n.'"         name="N1" value="NoPr1 '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Puesto1_'.$n.'"    name="Puesto1" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2 (at runtime round1/round2 are selected and/or hidden acording round type -->';
    echo '<input type="hidden"    id="vws_before_F2_'.$n.'"         name="F2" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_R2_'.$n.'"         name="R2" value="Reh '.$n.'"/>';
    echo '<input type="text"      class="lborder" id="vws_before_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_V2_'.$n.'"         name="V2" value="Vel2 '.$n.'"/>';
    echo '<input type="text"      id="vws_before_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_C2_'.$n.'"         name="C2" value="Cal2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_E2_'.$n.'"         name="E2" value="Elim2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_N2_'.$n.'"         name="N2" value="NoPr '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Puesto2_'.$n.'"    name="Puesto2" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      class="lborder" id="vws_before_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Calificacion_'.$n.'" name="Calificacion" value="Calif '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
    echo '</form>';
} ?>
    </div>
<!--
</div>
-->
<script type="text/javascript" charset="utf-8">

    var layout= {'rows':142,'cols':247};
    // cabeceras

<?php
    if ($config->getEnv("vws_uselogo")!=0) { // logotipo del evento
        echo 'doLayout(layout,"#vws_hdr_logo",1,0,88,27);';
        echo 'doLayout(layout,"#vws_hdr_manga",101,0,122,9);';
    } else { // logotipo del organizador, prueba y jornada en texto
        echo 'doLayout(layout,"#vws_hdr_logo",1,0,27,27);';
        echo 'doLayout(layout,"#vws_hdr_prueba",28,0,112,9);';
        echo 'doLayout(layout,"#vws_hdr_jornada",28,9,61,9);';
        echo 'doLayout(layout,"#vws_hdr_manga",140,0,82,9);';
    }
?>
    doLayout(layout,"#vws_hdr_trs",222,0,24,9);

    doLayout(layout,"#vws_hdr_calltoring",1,27,87,9);
    doLayout(layout,"#vws_hdr_teaminfo",91,9,83,9);
    doLayout(layout,"#vws_hdr_lastround",174,9,36,9);
    doLayout(layout,"#vws_hdr_finalscores",210,9,36,9);


    // llamada a pista
    for (var n=0;n<8;n++) {
        doLayout(layout,"#vws_call_Orden_"+n,1,37+9*n,10,9);
        doLayout(layout,"#vws_call_Logo_"+n,11,37+9*n,8,9);
        doLayout(layout,"#vws_call_Dorsal_"+n,19,37+9*n,9,9);
        doLayout(layout,"#vws_call_Nombre_"+n,28,37+9*n,18,9);
        doLayout(layout,"#vws_call_NombreGuia_"+n,46,37+9*n,42,9);
    }
    
    // perro en pista
    doLayout(layout,"#vws_current_Orden",       1,     110,10,11);
    doLayout(layout,"#vws_current_Logo",        11,    110,17,11);
    doLayout(layout,"#vws_current_Dorsal",      28,    110,18,11);
    doLayout(layout,"#vws_current_Nombre",      46,    110,36,11);
    doLayout(layout,"#vws_current_NombreGuia",  82,    110,74,11);

    doLayout(layout,"#vws_current_FaltasTocados",156,  110,20,11);
    doLayout(layout,"#vws_current_Refusals",    176,   110,20,11);
    doLayout(layout,"#vws_current_Time",        196,   110,35,11);
    doLayout(layout,"#vws_current_Result",      231,   110,15,11);

    // resultados
    for(n=0;n<10;n++) {
        doLayout(layout,"#vws_results_Logo_"+n,     91,     19+9*n,10,9);
        doLayout(layout,"#vws_results_Dorsal_"+n,   101,     19+9*n,9,9);
        doLayout(layout,"#vws_results_Nombre_"+n,   110,    19+9*n,19,9);
        doLayout(layout,"#vws_results_NombreGuia_"+n,129,   19+9*n,45,9);
        // Agility
        doLayout(layout,"#vws_results_T1_"+n,       174,    19+9*n,14,9);
        doLayout(layout,"#vws_results_P1_"+n,       188,    19+9*n,14,9);
        doLayout(layout,"#vws_results_Puesto1_"+n,  202,    19+9*n,8,9);
        // Jumping
        doLayout(layout,"#vws_results_T2_"+n,       174,    19+9*n,14,9);
        doLayout(layout,"#vws_results_P2_"+n,       188,    19+9*n,14,9);
        doLayout(layout,"#vws_results_Puesto2_"+n,  202,    19+9*n,8,9);
        //final
        doLayout(layout,"#vws_results_Tiempo_"+n,   210,    19+9*n,14,9);
        doLayout(layout,"#vws_results_Penalizacion_"+n,224, 19+9*n,14,9);
        doLayout(layout,"#vws_results_Puesto_"+n,   238,    19+9*n,8,9);
    }
    // ultimos resultados
    for(n=0;n<2;n++) {
        doLayout(layout,"#vws_before_Orden_"+n,    82,     122+9*n,9,9);
        doLayout(layout,"#vws_before_Logo_"+n,     91,     122+9*n,10,9);
        doLayout(layout,"#vws_before_Dorsal_"+n,   101,    122+9*n,9,9);
        doLayout(layout,"#vws_before_Nombre_"+n,   110,    122+9*n,19,9);
        doLayout(layout,"#vws_before_NombreGuia_"+n,129,   122+9*n,45,9);
        // agility
        doLayout(layout,"#vws_before_T1_"+n,       174,    122+9*n,14,9);
        doLayout(layout,"#vws_before_P1_"+n,       188,    122+9*n,14,9);
        doLayout(layout,"#vws_before_Puesto1_"+n,  202,    122+9*n,8,9);
        // Jumping
        doLayout(layout,"#vws_before_T2_"+n,       174,    122+9*n,14,9);
        doLayout(layout,"#vws_before_P2_"+n,       188,    122+9*n,14,9);
        doLayout(layout,"#vws_before_Puesto2_"+n,  202,    122+9*n,8,9);
        // final
        doLayout(layout,"#vws_before_Tiempo_"+n,   210,    122+9*n,14,9);
        doLayout(layout,"#vws_before_Penalizacion_"+n,224, 122+9*n,14,9);
        doLayout(layout,"#vws_before_Puesto_"+n,   238,    122+9*n,8,9);
    }
    // sponsor
    doLayout(layout,"#vws_sponsors",   1,    122,79,18);
</script>
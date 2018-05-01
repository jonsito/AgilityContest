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
vws_final_equipos.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
            echo '<input type="hidden" id="vws_hdr_logoprueba" name="LogoPrueba" value="../images/agilityawc2016.png"/>';
            echo '<img src="../images/agilityawc2016.png" class="vws_imgpadding"  id="vws_hdr_logo" alt="Logo"/>';
            echo '<input type="hidden"      id="vws_hdr_prueba"     name="Prueba" value="Prueba"/>';
            echo '<input type="hidden"      id="vws_hdr_jornada"     name="Jornada" value="Jornada"/>';
        } else {
            // logotipo del organizador. prueba y jornada en texto
            echo '<input type="hidden" id="vws_hdr_logoprueba" name="LogoPrueba" value="../images/logos/agilitycontest.png"/>';
            echo '<img src="../images/logos/agilitycontest.png" class="vws_imgpadding" id="vws_hdr_logo" alt="Logo"/>';
            // nombre de la prueba y jornada
            echo '<input type="text"      id="vws_hdr_prueba"     name="Prueba" value="Prueba Equipos"/>';
            echo '<input type="text"      id="vws_hdr_jornada"     name="Jornada" value="Jornada Equipos"/>';
        }
        ?>
            <input type="text"      id="vws_hdr_manga"     name="Manga" value="Manga"/>
            <input class="trs" type="text"      id="vws_hdr_trs"     name="TRS" value="Dist/TRS"/>
        </form>
        <span class="vws_theader" id="vws_hdr_calltoring"><?php _e('Call to ring');?> </span>
        <span class="vws_theader" id="vws_hdr_teaminfo"><?php _e("Team data");?> </span>
        <span class="vws_theader" id="vws_hdr_lastround"><?php _e('Round');?> </span>
        <span class="vws_theader" id="vws_hdr_finalscores"><?php _e('Final');?> </span>
    </div>
    
    <div id="vws_llamada">
<?php for($n=0;$n<5;$n++) {
    echo '<form id="vws_call_'.$n.'" class="vws_css_call_'.($n%2).' vws_entry">';
    echo '<input type="text" id="vws_call_Orden_'.$n.'" name="Orden" value="Orden '.$n.'"/>';
    echo '<input type="hidden" id="vws_call_LogoTeam_'.$n.'"      name="LogoTeam" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_call_'.($n%2).' vws_imgpadding"  src="../images/logos/agilitycontest.png" id="vws_call_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Equipo_'.$n.'"      name="Equipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="text"      class="left" id="vws_call_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    echo '</form>';
} ?>
    </div>
    
    <div id="vws_results">
<?php for($n=0;$n<7;$n++) {
    echo '<form id="vws_results_'.$n.'" class="vws_css_results_'.($n%2).' vws_entry">';
    echo '<input type="hidden" id="vws_results_LogoTeam_'.$n.'"      name="LogoTeam" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_results_'.($n%2).' vws_imgpadding"  alt="Logo '.$n.'"  id="vws_results_Logo_'.$n.'" name="Logo" src="../images/logos/agilitycontest.png"  />';
    echo '<input type="hidden"    id="vws_results_Categorias_'.$n.'"  name="Categorias" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Equipo_'.$n.'"     name="ID" value="Equipo '.$n.'"/>';
    echo '<input type="text"      class="left" id="vws_results_NombreEquipo_'.$n.'" name="Nombre" value="Equipo '.$n.'"/>';
    echo '<!-- data on round 1 -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_results_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_results_Puesto1_'.$n.'"    name="Puesto1" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2 -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_results_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_results_Puesto2_'.$n.'"    name="Puesto2" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_results_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_results_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
    echo '</form>';

}?>
    </div>
    
    <div id="vws_equipo_en_pista">
<?php
for($n=0;$n<4;$n++) {
    echo '<form id= "vws_current_'.$n.'" class="vws_css_current_'.($n%2).' vws_entry">';
    if ($n==0) {
        echo '<input type="text" class="vws_css_current_'.($n%2).'" id="vws_current_Orden_'.$n.'" name="Orden" value="Orden '.$n.'"/>';
        echo '<img class="vws_css_current_'.($n%2).' vws_imgpadding"  src="../ajax/images/getLogo.php?Federation=1&Logo=ES.png" id="vws_current_Logo_'.$n.'" name="Logo" alt="Logo"/>';
        echo '<input type="hidden"    id= "vws_current_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
        echo '<span id="vws_current_Spare_'.$n.'">&nbsp;</span>';
    } else if ($n==1) {
        echo '<span style="display:none" id="vws_current_Spare_'.$n.'">&nbsp;</span>';
        echo '<input type="text"      id= "vws_current_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    } else {
        echo '<span id="vws_current_Spare_'.$n.'">&nbsp;</span>';
        echo '<input type="hidden"    id= "vws_current_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    }
    echo '<input type="hidden"    id= "vws_current_Logo_'.$n.'"   name="LogoClub" value="Logo '.$n.'"/>';
    echo '<input type="text"      id= "vws_current_Dorsal_'.$n.'"     name="Dorsal" value="Dorsal '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Perro_'.$n.'"      name="Perro" value="Perro '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_CatGrad_'.$n.'"    name="CatGrad" value="Grad '.$n.'"/>';
    echo '<input type="text"      id= "vws_current_Nombre_'.$n.'"     name="Nombre" value="Nombre '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Celo_'.$n.'"       name="Celo" value="Celo '.$n.'"/>';
    echo '<input type="text"      id= "vws_current_NombreGuia_'.$n.'" name="NombreGuia" value="Guia '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_NombreClub_'.$n.'" name="NombreClub" value="Club '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Faltas_'.$n.'"     name="Faltas" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Tocados_'.$n.'"    name="Tocados" value="Toc '.$n.'"/>';
    echo '<span id= "vws_current_FaltasTocados_'.$n.'">F '.$n.'</span>';
    echo '<input type="hidden"      id= "vws_current_Rehuses_'.$n.'"    name="Rehuses" value="R '.$n.'"/>';
    echo '<span id= "vws_current_Refusals_'.$n.'">R '.$n.'</span>';
    echo '<input type="hidden"    id= "vws_current_Tintermedio_'.$n.'" name="TIntermedio" value="Tint '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Tiempo_'.$n.'"     name="Tiempo" value="Time '.$n.'"/>';
    echo '<span id= "vws_current_Time_'.$n.'" >Time '.$n.'</span>';
    echo '<input type="hidden"    id= "vws_current_Puesto_'.$n.'"     name="Puesto" value="P '.$n.'"/>';
    echo '<input type="hidden"    id= "vws_current_Eliminado_'.$n.'"  name="Eliminado" value=""/>';
    echo '<input type="hidden"    id= "vws_current_NoPresentado_'.$n.'" name="NoPresentado" value=""/>';
    echo '<input type="hidden"    id= "vws_current_Pendiente_'.$n.'"  name="Pendiente" value="Pend '.$n.'"/>';
    echo '<span id="vws_current_Result_'.$n.'" >Res '.$n.'</span>';
    echo '<span id="vws_current_Active_'.$n.'" style="padding:0"></span>';
    echo '</form>';
}
?>
    </div>
    
    <div id="vws_sponsors">
        <?php include_once(__DIR__."/../videowall/vws_footer.php");?>
    </div>
    
    <div id="vws_before">
<?php for($n=0;$n<2;$n++) {
    echo '<form id="vws_before_'.$n.'" class="vws_css_results_'.($n%2).' vws_entry">';

    echo '<input type="text"      id="vws_before_Orden_'.$n.'"      name="Orden" value="Orden '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_LogoTeam_'.$n.'"    name="LogoTeam" value="Logo '.$n.'"/>';
    echo '<img class="vws_css_results_'.($n%2).' vws_imgpadding"  alt="Logo '.$n.'"  id="vws_before_Logo_'.$n.'"       name="Logo" src="../images/logos/agilitycontest.png"  />';
    echo '<input type="hidden"    id="vws_before_Categorias_'.$n.'"  name="Categorias" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_before_Equipo_'.$n.'"     name="ID" value="Equipo '.$n.'"/>';
    echo '<input type="text"      class="left" id="vws_before_NombreEquipo_'.$n.'" name="Nombre" value="Equipo '.$n.'"/>';
    echo '<!-- data on round 1 -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_before_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="text"      id="vws_before_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_before_Puesto1_'.$n.'"    name="Puesto1" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2 (hidden in simplified videowall ) -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_before_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="text"      id="vws_before_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_before_Puesto2_'.$n.'"    name="Puesto2" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      style="border-left:2px solid '.$config->getEnv('vws_linecolor').';padding-left:0.5vw" id="vws_before_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_before_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="text"      class="rpadding" id="vws_before_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
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
        echo 'doLayout(layout,"#vws_hdr_logo",1,0,88,26);';
        echo 'doLayout(layout,"#vws_hdr_manga",101,0,122,9);';
    } else { // logotipo del organizador, prueba y jornada en texto
        echo 'doLayout(layout,"#vws_hdr_logo",1,0,27,26);';
        echo 'doLayout(layout,"#vws_hdr_prueba",28,0,112,9);';
        echo 'doLayout(layout,"#vws_hdr_jornada",28,9,63,10);';
        echo 'doLayout(layout,"#vws_hdr_manga",140,0,82,9);';
    }
?>
    doLayout(layout,"#vws_hdr_trs",222,0,24,9); // dist / trs

    doLayout(layout,"#vws_hdr_calltoring",1,27,81,9);
    doLayout(layout,"#vws_hdr_teaminfo",91,9,59,9);
    doLayout(layout,"#vws_hdr_lastround",150,9,48,9);
    doLayout(layout,"#vws_hdr_finalscores",198,9,48,9);


    // llamada a pista
    for (var n=0;n<5;n++) {
        doLayout(layout,"#vws_call_Orden_"+n,1,37+9*n,10,9);
        doLayout(layout,"#vws_call_Logo_"+n,11,37+9*n,15,9);
        doLayout(layout,"#vws_call_NombreEquipo_"+n,26,37+9*n,56,9);
    }
    
    // perros del equipo en pista
    for (n=0;n<4;n++) {
        var y=(n==0)?1:0;
        var dy=((n==0) || (n==3))?1:0;
        if (n==0) { // orden, logo, dorsal
            doLayout(layout,"#vws_current_Orden_0",    1,    82+10*n+y,10,10-dy);
            doLayout(layout,"#vws_current_Logo_0",     11,   82+10*n+y,15,10-dy);
            doLayout(layout,"#vws_current_Spare_0",    26,   82+10*n+y,14,10-dy);
            doLayout(layout,"#vws_current_Dorsal_0",   40,   82+10*n+y,10,10-dy);
        } else if (n==1) { // equipo,dorsal
            doLayout(layout,"#vws_current_NombreEquipo_1", 1,82+10*n+y,39,10-dy);
            doLayout(layout,"#vws_current_Dorsal_1",   40,   82+10*n+y,10,10-dy);
        } else { // dorsal
            doLayout(layout,"#vws_current_Spare_"+n,   1,    82+10*n+y,39,10-dy);
            doLayout(layout,"#vws_current_Dorsal_"+n,  40,   82+10*n+y,10,10-dy);
        }
        doLayout(layout,"#vws_current_Nombre_"+n,      50,   82+10*n+y,40,10-dy);
        doLayout(layout,"#vws_current_NombreGuia_"+n,  90,   82+10*n+y,71,10-dy);
        doLayout(layout,"#vws_current_FaltasTocados_"+n,161, 82+10*n+y,12,10-dy);
        doLayout(layout,"#vws_current_Refusals_"+n,    173,  82+10*n+y,12,10-dy);
        doLayout(layout,"#vws_current_Time_"+n,        185,  82+10*n+y,25,10-dy);
        doLayout(layout,"#vws_current_Result_"+n,      210,  82+10*n+y,25,10-dy);
        doLayout(layout,"#vws_current_Active_"+n,      235,  82+10*n+y,12,10-dy);
    }

    // resultados
    for(n=0;n<7;n++) {
        doLayout(layout,"#vws_results_Logo_"+n,     91,     19+9*n,10,9);
        doLayout(layout,"#vws_results_NombreEquipo_"+n,101, 19+9*n,49,9);
        // ronda agility
        doLayout(layout,"#vws_results_T1_"+n,       150,    19+9*n,20,9);
        doLayout(layout,"#vws_results_P1_"+n,       170,    19+9*n,20,9);
        doLayout(layout,"#vws_results_Puesto1_"+n,  190,    19+9*n,8,9);
        // ronda jumping ( en runtime se hace que solo una de las dos rondas sea visible
        doLayout(layout,"#vws_results_T2_"+n,       150,    19+9*n,20,9);
        doLayout(layout,"#vws_results_P2_"+n,       170,    19+9*n,20,9);
        doLayout(layout,"#vws_results_Puesto2_"+n,  190,    19+9*n,8,9);
        // final
        doLayout(layout,"#vws_results_Tiempo_"+n,   198,    19+9*n,20,9);
        doLayout(layout,"#vws_results_Penalizacion_"+n,218, 19+9*n,20,9);
        doLayout(layout,"#vws_results_Puesto_"+n,   238,    19+9*n,8,9);
    }
    // ultimos resultados
    for(n=0;n<2;n++) {
        doLayout(layout,"#vws_before_Orden_"+n,    82,     122+9*n,9,9);
        doLayout(layout,"#vws_before_Logo_"+n,     91,     122+9*n,10,9);
        doLayout(layout,"#vws_before_NombreEquipo_"+n,101, 122+9*n,49,9);
        // agility
        doLayout(layout,"#vws_before_T1_"+n,       150,    122+9*n,20,9);
        doLayout(layout,"#vws_before_P1_"+n,       170,    122+9*n,20,9);
        doLayout(layout,"#vws_before_Puesto1_"+n,  190,    122+9*n,8,9);
        // jumping
        doLayout(layout,"#vws_before_T2_"+n,       150,    122+9*n,20,9);
        doLayout(layout,"#vws_before_P2_"+n,       170,    122+9*n,20,9);
        doLayout(layout,"#vws_before_Puesto2_"+n,  190,    122+9*n,8,9);
        // final
        doLayout(layout,"#vws_before_Tiempo_"+n,   198,    122+9*n,20,9);
        doLayout(layout,"#vws_before_Penalizacion_"+n,218, 122+9*n,20,9);
        doLayout(layout,"#vws_before_Puesto_"+n,   238,    122+9*n,8,9);
    }
    // sponsor
    doLayout(layout,"#vws_sponsors",   1,    122,79,18);
</script>
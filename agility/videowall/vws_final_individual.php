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
vwc_finales.php

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
Pantalla de de visualizacion combinada llamada/parciales
 prefijos:
  vw_ commun para todos los marcadores
  vwc_ comun para todos los paneles combinados
  vwcf_ id asociados al panel combinado de parciales
  vwcf_ id asociados al panel combinado de finales
-->
<div id="vws-window">
    <div id="vws_cabecera>">
        
    </div>
    
    <div id="vws_llamada">
<?php for($n=0;$n<8;$n++) {
    echo '<form id="vws_call_'.$n.'">';
    echo '<input type="text" id="vws_call_Orden_'.$n.'" name="Orden" value="Orden '.$n.'"/>';
    echo '<input type="hidden" id="vws_call_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img src="/agility/images/logos/null.png" id="vws_call_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
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
    echo '<form id="vws_results_'.$n.'">';
    echo '<input type="hidden" id="vws_results_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img src="/agility/images/logos/null.png" id="vws_results_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
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
    echo '<!-- data on round 1 -->';
    echo '<input type="hidden"    id="vws_results_F1_'.$n.'"         name="F1" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_R1_'.$n.'"         name="R1" value="Reh '.$n.'"/>';
    echo '<input type="text"      id="vws_results_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_V1_'.$n.'"         name="V1" value="Vel1 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_C1_'.$n.'"         name="C1" value="Cal1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_E1_'.$n.'"         name="E1" value="Elim1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_N1_'.$n.'"         name="N1" value="NoPr1 '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Puesto1_'.$n.'"    name="Puesto" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2 -->';
    echo '<input type="hidden"    id="vws_results_F2_'.$n.'"         name="F2" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_R2_'.$n.'"         name="R2" value="Reh '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_V2_'.$n.'"         name="V2" value="Vel2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_C2_'.$n.'"         name="C2" value="Cal2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_E2_'.$n.'"         name="E2" value="Elim2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_N2_'.$n.'"         name="N2" value="NoPr '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Puesto2_'.$n.'"    name="Puesto" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      id="vws_results_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_results_Calificacion_'.$n.'" name="Calificacion" value="Calif '.$n.'"/>';
    echo '<input type="text"      id="vws_results_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
    echo '</form>';

}?>
    </div>
    
    <div id="vws_perro_en_pista">
<?php
    echo '<form id= "vws_current">';
    echo '<input type="text" id= "vws_current_Orden" name="Orden" value="Orden"/>';
    echo '<input type="hidden" id= "vws_current_LogoClub"      name="LogoClub" value="Logo"/>';
    echo '<img src="/agility/images/logos/null.png" id= "vws_current_Logo" name="Logo" alt="Logo"/>';
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
    echo '<input type="hidden"    id= "vws_current_F"          name="Faltas" value="Flt"/>';
    echo '<input type="hidden"    id= "vws_current_T"          name="Tocados" value="Toc"/>';
    echo '<input type="text"      id= "vws_current_FaltasTocados" name="FaltasTocados" value="F/T">';
    echo '<input type="text"      id= "vws_current_Rehuses"    name="Rehuses" value="R"/>';
    echo '<input type="text"      id= "vws_current_Puesto"     name="Puesto" value="P"/>';
    echo '<input type="hidden"    id= "vws_current_Tintermedio" name="TIntermedio" value="Tint"/>';
    echo '<input type="text"      id= "vws_current_Tiempo"     name="Tiempo" value="Time"/>';
    echo '<input type="hidden"    id= "vws_current_Eliminado"  name="Eliminado" value="Elim"/>';
    echo '<input type="hidden"    id= "vws_current_NoPresentado" name="NoPresentado" value="NPr"/>';
    echo '<input type="hidden"    id= "vws_current_Pendiente"  name="Pendiente" value="Pend"/>';
    echo '</form>';
?>
    </div>
    
    <div id="vws_sponsors">
        <?php include_once(__DIR__."/../videowall/vws_footer.php");?>
    </div>
    
    <div id="vws_ultimos">
<?php for($n=0;$n<2;$n++) {
    echo '<form id="vws_ultimos_'.$n.'">';
    echo '<input type="text"      id="vws_ultimos_Orden_'.$n.'"      name="Dorsal" value="Orden '.$n.'"/>';
    echo '<input type="hidden" id="vws_ultimos_LogoClub_'.$n.'"      name="LogoClub" value="Logo '.$n.'"/>';
    echo '<img src="/agility/images/logos/null.png" id="vws_ultimos_Logo_'.$n.'" name="Logo" alt="Logo '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_Dorsal_'.$n.'"     name="Dorsal" value="Dorsal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Perro_'.$n.'"      name="Perro" value="Perro '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_Nombre_'.$n.'"     name="Nombre" value="Nombre '.$n.'"/>';
    echo '<input type="hidden"    id="vws_call_Licencia_'.$n.'"      name="Licencia" value="Lic '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Categoria_'.$n.'"  name="Categoria" value="Cat '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Grado_'.$n.'"      name="Grado" value="Grad '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_NombreGuia_'.$n.'" name="NombreGuia" value="Guia '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Equipo_'.$n.'"     name="Equipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_NombreEquipo_'.$n.'" name="NombreEquipo" value="Equipo '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_NombreClub_'.$n.'" name="NombreClub" value="Club '.$n.'"/>';
    echo '<!-- data on round 1 -->';
    echo '<input type="hidden"    id="vws_ultimos_F1_'.$n.'"         name="F1" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_R1_'.$n.'"         name="R1" value="Reh '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_T1_'.$n.'"         name="T1" value="Time1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_V1_'.$n.'"         name="V1" value="Vel1 '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_P1_'.$n.'"         name="P1" value="Pen1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_C1_'.$n.'"         name="C1" value="Cal1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_E1_'.$n.'"         name="E1" value="Elim1 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_N1_'.$n.'"         name="N1" value="NoPr1 '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_Puesto1_'.$n.'"    name="Puesto" value="Pos '.$n.'"/>';
    echo '<!-- data on round 2 ( in simplified everything is hidden, just show final results -->';
    echo '<input type="hidden"    id="vws_ultimos_F2_'.$n.'"         name="F2" value="Flt '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_R2_'.$n.'"         name="R2" value="Reh '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_T2_'.$n.'"         name="T2" value="Time2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_V2_'.$n.'"         name="V2" value="Vel2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_P2_'.$n.'"         name="P2" value="Pen2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_C2_'.$n.'"         name="C2" value="Cal2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_E2_'.$n.'"         name="E2" value="Elim2 '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_N2_'.$n.'"         name="N2" value="NoPr '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Puesto2_'.$n.'"    name="Puesto" value="Pos '.$n.'"/>';
    echo '<!-- Final data -->';
    echo '<input type="text"      id="vws_ultimos_Tiempo_'.$n.'"       name="Tiempo" value="Tiempo '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_Penalizacion_'.$n.'" name="Penalizacion" value="Penal '.$n.'"/>';
    echo '<input type="hidden"    id="vws_ultimos_Calificacion_'.$n.'" name="Calificacion" value="Calif '.$n.'"/>';
    echo '<input type="text"      id="vws_ultimos_Puesto_'.$n.'"       name="Puesto" value="Pos '.$n.'"/>';
    echo '</form>';
} ?>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    $('#vws-window').window({
        fit:true,
        noheader:true,
        border:false,
        closable:false,
        collapsible:false,
        collapsed:false,
        resizable:true,
        onOpen: function() {
            // startEventMgr();
        }
    });

    var layout= {'rows':140,'cols':245};
    // cabeceras
    // llamada a pista
    for (var n=0;n<8;n++) {
        doLayout(layout,"#vws_call_Orden_"+n,0,37+9*n,9,9);
        doLayout(layout,"#vws_call_LogoClub_"+n,9,37+9*n,8,9);
        doLayout(layout,"#vws_call_Dorsal_"+n,17,37+9*n,9,9);
        doLayout(layout,"#vws_call_Nombre_"+n,26,37+9*n,19,9);
        doLayout(layout,"#vws_call_NombreGuia_"+n,45,37+9*n,42,9);
    }
    // perro en pista

    doLayout(layout,"#vws_current_Orden",       0,     109,10,13);
    doLayout(layout,"#vws_current_Logo",        10,    109,15,13);
    doLayout(layout,"#vws_current_Dorsal",      25,    109,15,13);
    doLayout(layout,"#vws_current_Nombre",      40,    109,35,13);
    doLayout(layout,"#vws_current_NombreGuia",  75,    109,65,13);
    doLayout(layout,"#vws_current_FaltasTocados",140,  109,45,13);
    doLayout(layout,"#vws_current_Rehuses",     185,   109,20,13);
    doLayout(layout,"#vws_current_Puesto",      205,   109,22,13);
    doLayout(layout,"#vws_current_Tiempo",      227,   109,18,13);
    // resultados
    for(n=0;n<10;n++) {
        doLayout(layout,"#vws_results_Logo_"+n,     88,     19+9*n,10,9);
        doLayout(layout,"#vws_results_Dorsal_"+n,   98,     19+9*n,9,9);
        doLayout(layout,"#vws_results_Nombre_"+n,   107,    19+9*n,19,9);
        doLayout(layout,"#vws_results_NombreGuia_"+n,126,   19+9*n,47,9);
        doLayout(layout,"#vws_results_T1_"+n,       173,    19+9*n,14,9);
        doLayout(layout,"#vws_results_P1_"+n,       187,    19+9*n,14,9);
        doLayout(layout,"#vws_results_Puesto1_"+n,  201,    19+9*n,8,9);
        doLayout(layout,"#vws_results_Tiempo_"+n,   209,    19+9*n,14,9);
        doLayout(layout,"#vws_results_Penalizacion_"+n,223, 19+9*n,14,9);
        doLayout(layout,"#vws_results_Puesto_"+n,   237,    19+9*n,8,9);
    }
    // ultimos resultados
    for(n=0;n<2;n++) {
        doLayout(layout,"#vws_ultimos_Orden_"+n,    79,     122+9*n,9,9);
        doLayout(layout,"#vws_ultimos_Logo_"+n,     88,     122+9*n,10,9);
        doLayout(layout,"#vws_ultimos_Dorsal_"+n,   98,     122+9*n,9,9);
        doLayout(layout,"#vws_ultimos_Nombre_"+n,   107,    122+9*n,19,9);
        doLayout(layout,"#vws_ultimos_NombreGuia_"+n,126,   122+9*n,47,9);
        doLayout(layout,"#vws_ultimos_T1_"+n,       173,    122+9*n,14,9);
        doLayout(layout,"#vws_ultimos_P1_"+n,       187,    122+9*n,14,9);
        doLayout(layout,"#vws_ultimos_Puesto1_"+n,  201,    122+9*n,8,9);
        doLayout(layout,"#vws_ultimos_Tiempo_"+n,   209,    122+9*n,14,9);
        doLayout(layout,"#vws_ultimos_Penalizacion_"+n,223, 122+9*n,14,9);
        doLayout(layout,"#vws_ultimos_Puesto_"+n,   237,    122+9*n,8,9);
    }
    // sponsor
    doLayout(layout,"#vws_sponsors",   0,    122,79,18);
</script>
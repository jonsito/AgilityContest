<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
vw_footer.php

Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License as published by the Free Software Foundation;
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program;
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/
/* File used to insert logo, supporters,  head lines and so */

/* el fichero "supporters,csv" tiene el formato CSV: "patrocinador":"logo":"url"[:"categoria"] */
$sponsors_g=array();    $logos_g=array();    $urls_g=array();

$file=fopen(__DIR__ . "/../images/supporters/supporters.csv","r");
if ($file) {
    while (($datos = fgetcsv($file, 0, ':','"')) !== FALSE) {
        $nitems=count($datos);
        if ($nitems<3) continue; // invalid format
        $cat=($nitems==3)?"bronze":strtolower($datos[3]); // "gold","silver","bronze"
        if ($cat=="gold") {
            array_push($sponsors_g,$datos[0]); array_push($logos_g,$datos[1]);  array_push($urls_g,$datos[2]);
        }
    }
    fclose($file); // this also removes temporary file
}
?>
<div id="vw_footer">
    <table style="width:100%;">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td style="text-align:center">
                <a id="url_g0" target="sponsor" href=""> <img id="img_g0" src="../images/logos/null.png" alt="" height="40"/> </a>
            </td>
            <td style="text-align:center">
                <a id="url_g1" target="sponsor" href=""> <img id="img_g1" src="../images/logos/null.png" alt="" height="40"/> </a>
            </td>
            <td style="text-align:center">
                <a id="url_g2" target="sponsor" href=""> <img id="img_g2" src="../images/logos/null.png" alt="" height="40"/> </a>
            </td>
        </tr>
        <!-- ignore bronze sponsors -->
    </table>
</div>
<script type="text/javascript">
    (function() {     // function expression closure to contain variables
        var i = 0;
        var sponsors_g = <?php echo json_encode($sponsors_g);?>;
        var logos_g = <?php echo json_encode($logos_g);?>;
        var urls_g = <?php echo json_encode($urls_g);?>;
        var url_g0=$('#url_g0'); var img_g0=$('#img_g0');
        var url_g1=$('#url_g1'); var img_g1=$('#img_g1');
        var url_g2=$('#url_g2'); var img_g2=$('#img_g2');
        function rotate_sponsors() {
            // set up urls
            url_g0.attr('href',urls_g[(0+i)%urls_g.length]);
            url_g1.attr('href',urls_g[(1+i)%urls_g.length]);
            url_g2.attr('href',urls_g[(2+i)%urls_g.length]);
            // set up images and alternate names
            img_g0.attr('src', "../images/supporters/"+logos_g[(0+i)%logos_g.length]).attr('alt',sponsors_g[(0+i)%sponsors_g.length]);
            img_g1.attr('src', "../images/supporters/"+logos_g[(1+i)%logos_g.length]).attr('alt',sponsors_g[(1+i)%sponsors_g.length]);
            img_g2.attr('src', "../images/supporters/"+logos_g[(2+i)%logos_g.length]).attr('alt',sponsors_g[(2+i)%sponsors_g.length]);
            i++; // next iteration
        }
        setInterval(rotate_sponsors, 5000);
    })();
</script>
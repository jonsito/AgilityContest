<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
vw_footer.php

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
/* File used to insert logo, supporters,  head lines and so */

/* el fichero "supporters,csv" tiene el formato CSV: "patrocinador":"logo":"url"[:"categoria"] */
$sponsors_g=array();    $logos_g=array();    $urls_g=array();
$sponsors_s=array();    $logos_s=array();    $urls_s=array();
$sponsors_b=array();    $logos_b=array();    $urls_b=array();

$file=fopen(__DIR__."/../images/supporters/supporters.csv","r");
if ($file) {
    while (($datos = fgetcsv($file, 0, ':','"')) !== FALSE) {
        $nitems=count($datos);
        if ($nitems<3) continue; // invalid format
        $cat=($nitems==3)?"bronze":strtolower($datos[3]); // "gold","silver","bronze"
        if ($cat=="gold") {
            array_push($sponsors_g,$datos[0]); array_push($logos_g,$datos[1]);  array_push($urls_g,$datos[2]);
        }
        if ($cat=="silver") {
            array_push($sponsors_s,$datos[0]); array_push($logos_s,$datos[1]);  array_push($urls_s,$datos[2]);
        }
        if ($cat=="bronze") {
            array_push($sponsors_b,$datos[0]); array_push($logos_b,$datos[1]);  array_push($urls_b,$datos[2]);
        }
    }
    fclose($file); // this also removes temporary file
    // make sure that we have enought sponsors to fill footer
    while (count($sponsors_g)<3) {$sponsors_g[]="null"; $logos_g[]="null.png"; $urls_g[]="http://www.agilitycontest.es"; }
    while (count($sponsors_s)<5) {$sponsors_s[]="null"; $logos_s[]="null.png"; $urls_s[]="http://www.agilitycontest.es"; }
    while (count($sponsors_b)<9) {$sponsors_b[]="null"; $logos_b[]="null.png"; $urls_b[]="http://www.agilitycontest.es"; }
}
?>
<div id="vw_footer">
    <table style="width:100%;table-layout: fixed">
        <tr>
            <td colspan="2" align="center"> <a id="url_g0" target="sponsor" href=""> <img id="img_g0" src="" alt="" height="60"/> </a> </td>
            <td colspan="2" align="center"> <a id="url_g1" target="sponsor" href=""> <img id="img_g1" src="" alt="" height="60"/> </a> </td>
            <td colspan="2" align="center"> <a id="url_g2" target="sponsor" href=""> <img id="img_g2" src="" alt="" height="60"/> </a> </td>
        </tr>
        <tr>
            <td style="width:57px" align="center"> <a id="url_s0" target="sponsor" href=""> <img id="img_s0" src="" alt="" height="30"/> </a> </td>
            <td style="width:57px" align="center"> <a id="url_s1" target="sponsor" href=""> <img id="img_s1" src="" alt="" height="30"/> </a> </td>
            <td style="width:57px" align="center"> <a id="url_s2" target="sponsor" href=""> <img id="img_s2" src="" alt="" height="30"/> </a> </td>
            <td style="width:57px" align="center"> <a id="url_s3" target="sponsor" href=""> <img id="img_s3" src="" alt="" height="30"/> </a> </td>
            <td style="width:57px" align="center"> <a id="url_s4" target="sponsor" href=""> <img id="img_s4" src="" alt="" height="30"/> </a> </td>
            <td style="width:57px" align="center">
                <!-- El logo de y URL de la aplicación siempre esta presente :-) -->
                <a target="sponsor" href="https://www.github.com/jonsito/AgilityContest">
                    <img id="vw_footer-logoAgilityContest" src="/agility/images/supporters/agilitycontest.png" alt="agilitycontest" height="50"/>
                </a>
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
        var sponsors_s = <?php echo json_encode($sponsors_s);?>;
        var logos_s = <?php echo json_encode($logos_s);?>;
        var urls_s = <?php echo json_encode($urls_s);?>;
        var url_g0=$('#url_g0'); var img_g0=$('#img_g0');
        var url_g1=$('#url_g1'); var img_g1=$('#img_g1');
        var url_g2=$('#url_g2'); var img_g2=$('#img_g2');
        var url_s0=$('#url_s0'); var img_s0=$('#img_s0');
        var url_s1=$('#url_s1'); var img_s1=$('#img_s1');
        var url_s2=$('#url_s2'); var img_s2=$('#img_s2');
        var url_s3=$('#url_s3'); var img_s3=$('#img_s3');
        var url_s4=$('#url_s4'); var img_s4=$('#img_s4');
        function rotate_sponsors() {
            // set up urls
            url_g0.attr('href',urls_g[(0+i)%urls_g.length]);
            url_g1.attr('href',urls_g[(1+i)%urls_g.length]);
            url_g2.attr('href',urls_g[(2+i)%urls_g.length]);
            url_s0.attr('href',urls_s[(0+i)%urls_s.length]);
            url_s1.attr('href',urls_s[(1+i)%urls_s.length]);
            url_s2.attr('href',urls_s[(2+i)%urls_s.length]);
            url_s3.attr('href',urls_s[(3+i)%urls_s.length]);
            url_s4.attr('href',urls_s[(4+i)%urls_s.length]);
            // set up images and alternate names
            img_g0.attr('src', "/agility/images/supporters/"+logos_g[(0+i)%logos_g.length]).attr('alt',sponsors_g[(0+i)%sponsors_g.length]);
            img_g1.attr('src', "/agility/images/supporters/"+logos_g[(1+i)%logos_g.length]).attr('alt',sponsors_g[(1+i)%sponsors_g.length]);
            img_g2.attr('src', "/agility/images/supporters/"+logos_g[(2+i)%logos_g.length]).attr('alt',sponsors_g[(2+i)%sponsors_g.length]);
            img_s0.attr('src', "/agility/images/supporters/"+logos_s[(0+i)%logos_s.length]).attr('alt',sponsors_s[(0+i)%sponsors_s.length]);
            img_s1.attr('src', "/agility/images/supporters/"+logos_s[(1+i)%logos_s.length]).attr('alt',sponsors_s[(1+i)%sponsors_s.length]);
            img_s2.attr('src', "/agility/images/supporters/"+logos_s[(2+i)%logos_s.length]).attr('alt',sponsors_s[(2+i)%sponsors_s.length]);
            img_s3.attr('src', "/agility/images/supporters/"+logos_s[(3+i)%logos_s.length]).attr('alt',sponsors_s[(3+i)%sponsors_s.length]);
            img_s4.attr('src', "/agility/images/supporters/"+logos_s[(4+i)%logos_s.length]).attr('alt',sponsors_s[(4+i)%sponsors_s.length]);
            i++; // next iteration
        }
        setInterval(rotate_sponsors, 5000);
    })();
</script>
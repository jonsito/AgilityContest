<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
vw_footer.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
?>
<div id="vw_footer">
    <span style="float:left">
        <a id="vw_footer-urlFederation" target="fed" href="" style="border:0 none;">
            <img id="vw_footer-logoFederation" src="/agility/images/logos/rsce.png" alt="" width="50"/>
        </a>
        <a id="vw_footer-urlFederation2" target="fed2" href="">
            <img id="vw_footer-logoFederation2" src="/agility/images/logos/fci.png" alt="" width="50"/>
        </a>
        <span style="display:inline-block;padding:12px;font-size:10px;font-style:oblique">
            Powered by AgilityContest-2.0.0 <br/>Copyright &copy; 2013-2015 JAMC
        </span>
    </span>
    <span style="float:right">
        <a  target="canastur" href="http://www.caninaasturiana.es/">
            <img id="vw_footer-caninaAsturias" src="/agility/images/supporters/canina_asturiana.png" alt="canastur" height="50"/>
        </a>
        <a target="oviedo" href="http://www.oviedo.es">
            <img id="vw_footer-aytoOviedo" src="/agility/images/supporters/oviedo.png" alt="oviedo" height="50"/>
        </a>
        <a target="galican" href="http://www.galican.com">
            <img id="vw_footer-logoGalican" src="/agility/images/supporters/galican.png" alt="galican" height="50"/>
        </a>
        <a target="arion" href="http://www.arion-petfood.es">
            <img id="vw_footer-logoArion" src="/agility/images/supporters/arion.png" alt="arion" height="50"/>
        </a>
        <a target="xanastur" href="http://www.xanastur.org">
            <img id="vw_footer-logoGalican" src="/agility/images/supporters/xanastur.png" alt="xanastur" height="50"/>
        </a>
        <a target="acontest" href="https://www.github.com/jonsito/AgilityContest">
            <img id="vw_footer-logoAgilityContest" src="/agility/images/supporters/agilitycontest.png" alt="agilitycontest" height="50"/>
        </a>
    </span>
</div>
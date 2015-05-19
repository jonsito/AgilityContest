<?php
    require_once(__DIR__."/../server/auth/Config.php");
    $config = Config::getInstance();
/*
pb_footer.php

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
<div id="pb_footer">
    <span style="float:left">
        <a id="pb_footer-urlFederation" target="fed" href="" style="border:0 none;">
            <img id="pb_footer-logoFederation" src="/agility/images/logos/rsce.png" alt="" width="75"/>
        </a>
        <a id="pb_footer-urlFederation2" target="fed2" href="">
            <img id="pb_footer-logoFederation2" src="/agility/images/logos/fci.png" alt="" width="75"/>
        </a>
    </span>
    <span style="float:right">
        <a target="galican" href="http://www.galican.com">
            <img id="pb_footer-logoGalican" src="/agility/images/supporters/galican.png" alt="galican" width="75"/>
        </a>
        <a target="xanastur" href="http://www.xanastur.org">
            <img id="pb_footer-logoGalican" src="/agility/images/logos/xanastur.png" alt="xanastur" width="75"/>
        </a>
        <a target="acontest" href="https://www.github.com/jonsito/AgilityContest">
            <img id="pb_footer-logoAgilityContest" src="/agility/images/logos/agilitycontest.png" alt="agilitycontest" width="75"/>
        </a>
    </span>
</div>
<?php

/**
 * Downloader.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 28/12/17
 * Time: 11:21

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

require_once (__DIR__."/../classes/DBObject.php");
require_once (__DIR__."/../../auth/Config.php");

class Downloader {
    protected $myDBObject;
    protected $myConfig;

    function __construct() {
        $this->myDBObject=new DBObject("Downloader");
        $this->myConfig=Config::getInstance();
    }

    /**
     * retrieve from perroguiaclub every item newer than timestamp
     * @param $timestamp
     */
    function getUpdatedEntries($timestamp) {
        // retrieve updated elements from database
        $res=$this->myDBObject->__select(
            "*",
            "PerroGuiaClub",
            "(Licencia != '') AND ( LastModified > '{$timestamp}')"
        );
        if (!$res) throw new Exception ("Downloader::getUpdatedEntries(): {$this->myDBObject->errormsg}");
        return $res;
    }
}
<?php
/**
 * CertManager.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 7/01/18
 * Time: 10:40

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

class CertManager {

    /**
     * Determines if the browser provided a valid SSL client certificate
     * @return boolean True if the client cert is there and is valid
     */
    public function hasValidCert() {
        if (!isset($_SERVER['SSL_CLIENT_M_SERIAL'])
            || !isset($_SERVER['SSL_CLIENT_V_END'])
            || !isset($_SERVER['SSL_CLIENT_VERIFY'])
            || $_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS'
            || !isset($_SERVER['SSL_CLIENT_I_DN'])
        ) {
            return false;
        }
        if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) {
            return false;
        }
        return true;
    }

    // compara el serial number del certificado recibido con
    // la lista de certificados autorizados a acceder a la consola maestra
    // cada linea tiene el formato: SERIALNUMBERINUPPERCASE - Nombre del usuario
    public function checkCertACL() {
        $acl="/etc/AgilityContest/certs.allow";
        if (!file_exists($acl)) return false;
        $a=file($acl,FILE_IGNORE_NEW_LINES);
        $ser=$_SERVER['SSL_CLIENT_M_SERIAL'];
        foreach($a as $sn) {
            if ($ser===substr($sn,0,32)) return true;
        }
        return false;
    }

    public function getCertDN() {
        return $_SERVER['SSL_CLIENT_S_DN'];
    }
}

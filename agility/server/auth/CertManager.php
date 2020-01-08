<?php
/**
 * CertManager.php
 * Created by PhpStorm.
 * User: jantonio
 * Date: 7/01/18
 * Time: 10:40

Copyright  2013-2020 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

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
     * @return string empty on success; error message on cert validation failure
     */
    public function hasValidCert() {
        if (!isset($_SERVER['SSL_CLIENT_M_SERIAL'])) return 'SSL_CLIENT_M_SERIAL not set';
        if (!isset($_SERVER['SSL_CLIENT_V_END'])) return 'SSL_CLIENT_V_END not set';
        if (!isset($_SERVER['SSL_CLIENT_VERIFY'])) return 'SSL_CLIENT_VERIFY not set';
        if ($_SERVER['SSL_CLIENT_VERIFY'] !== 'SUCCESS') return 'VERIFY Failed';
        if (!isset($_SERVER['SSL_CLIENT_I_DN'])) return 'SSL_CLIENT_I_DN not set';
        if ($_SERVER['SSL_CLIENT_V_REMAIN'] <= 0) return 'SSL_CLIENT_V_REMAIN fail';
        return "";
    }

    // compara el serial number del certificado recibido con
    // la lista de certificados autorizados a acceder a la consola maestra
    // cada linea tiene el formato: SERIALNUMBERINUPPERCASE - Nombre del usuario
    public function checkCertACL() {
        $acl="/etc/AgilityContest/certs.allow";
        if (!file_exists($acl)) return "";
        $a=file($acl,FILE_IGNORE_NEW_LINES);
        $ser=$_SERVER['SSL_CLIENT_M_SERIAL'];
        foreach($a as $sn) {
            // on matched serial number, return assigned login
            if ($ser===substr($sn,0,32)) return trim(explode(':',$sn)[1]);
        }
        return "";
    }

    public function getCertCN() {
        return $_SERVER['SSL_CLIENT_S_DN_CN'];
    }
}

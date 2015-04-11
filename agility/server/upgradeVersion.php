<?php

/*
upgradeVersion.php

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

require_once(__DIR__."/database/classes/DBObject.php");

// 1.1.1 - 2015-Mar-30: add Background field to 'Sesiones' table
function addBackgroundField($conn) {
	$sql="ALTER TABLE `Sesiones` ADD `Background` VARCHAR(255) NOT NULL DEFAULT '' AFTER `Tanda`;";
	$conn->query($sql);
	return 0;
}

// 1.1.1 - 2015-Mar-30: add extra federation fields to be used in federation selection
function addRsceFields($conn) {
	$cmds=array(
		// federations: bitmask 1<<federation 0:rsce 1:rfec 2:uca
		"ALTER TABLE `Clubes` ADD `Federations` int(4) NOT NULL DEFAULT 1 AFTER `Logo`;",
		"ALTER TABLE `Jueces` ADD `Federations` int(4) NOT NULL DEFAULT 1 AFTER `Email`;",
		"ALTER TABLE `Guias` ADD `Federation` tinyint(1) NOT NULL DEFAULT 0 AFTER `Club`;",
		"ALTER TABLE `Perros` ADD `Federation` tinyint(1) NOT NULL DEFAULT 0 AFTER `Guia`;"
	);
	foreach ($cmds as $query) {
		$conn->query($query);
	}
	return 0;
}

// 1.2.0 - 2015-Apr-10: need to add trigger permission to correctly make backup for Dorsal setup trigger
function addTriggerPermissions($conn) {
	$sql="GRANT SELECT,INSERT,UPDATE,DELETE,CREATE TEMPORARY TABLES,LOCK TABLES,TRIGGER,EXECUTE,CREATE VIEW,SHOW VIEW,EVENT ON `agility`.* TO 'agility_operator'@'localhost';";
	$conn->query($sql);
	return 0;
}

// 1.2.0 - 2015-Apr-10: as old backups does not preserve triggers, we must ensure they are properly created
function createTrigger($conn) {
	$drop="DROP TRIGGER IF EXISTS `Increase_Dorsal`;";
	$trigger="
		CREATE TRIGGER `Increase_Dorsal` BEFORE INSERT ON `inscripciones`
		FOR EACH ROW BEGIN
			select count(*) into @rows from inscripciones where Prueba = NEW.Prueba;
			if @rows>0 then
				select Dorsal + 1 into @newDorsal from inscripciones where Prueba = NEW.Prueba order by Dorsal desc limit 1;
				set NEW.Dorsal = @newDorsal;
			else
				set NEW.Dorsal = 1;
			end if;
		END";
	$conn->query($drop);
	$conn->query($trigger);
}

$conn = new mysqli("localhost","agility_admin","admin@cachorrera","agility");
if ($conn->connect_error) die("Cannot perform upgrade process: database::dbConnect()");
addBackgroundField($conn);
addRsceFields($conn);
addTriggerPermissions($conn);
createTrigger($conn);
$conn->close();
?>

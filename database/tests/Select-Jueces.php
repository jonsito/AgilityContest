<!--  Lista de jueces en un combobox -->
<?php
require ("../database/DBConnection.php");

function getSelect_Jueces($default) {
	$querystr="SELECT Nombre,Practicas FROM Jueces ORDER BY Nombre ASC";
	// connect DB
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// compose result
	printf ("<select name=\"Jueces\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Nombre"]===$default)?"selected":"";
		printf("<option value=\"%s\" %s>%s %s</td>\n",$row["Nombre"],$selected,$row["Nombre"],($row["Practicas"]==1)?" (p)":"");
	}
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

getSelect_Jueces($_GET["Nombre"]);
?>
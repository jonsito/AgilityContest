<!--  Lista de provincias en un combobox -->
<?php
require ("../database/DBConnection.php");
function getSelect_Provincias($default) {
	$querystr="SELECT Provincia FROM Provincias ORDER BY Provincia ASC";
	// connect DB
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// compose result
	printf ("<select name=\"Provincias\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Provincia"]===$default)?"selected":"";
		printf("<option value=\"%s\" %s>%s</td>",$row["Provincia"],$selected,$row["Provincia"]);
	}
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

getSelect_Provincias($_GET["Provincia"]);
?>
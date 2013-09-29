<!--  Lista de tipos de manga en un combobox -->
<?php
require ("../database/DBConnection.php");
function getSelect_Tipo_Manga($default) {
	$querystr="SELECT * FROM Tipo_Manga ORDER BY Descripcion ASC";
	// connect DB
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// compose response
	printf ("<select name=\"Tipo_Manga\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Tipo=$default)?"selected":"";
		printf("<option value=\"%s\">%s</td>\n",$row["Tipo"],$selected,$row["Descripcion"]);
	}
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

getSelect_Tipo_Manga($_GET["Tipo"]);
?>
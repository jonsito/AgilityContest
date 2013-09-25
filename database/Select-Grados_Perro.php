<!--  Lista de grados de un perro en un combobox -->
<?php
require ("../database/DBConnection.php");

function getSelect_Grados_Perro($default) {
	$querystr="SELECT * FROM Grados_Perro";
	// connect with DB
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// generate select tag
	printf ("<select name=\"Grados_Perro\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Grado"]===$default)?"selected":"";
		printf("<option value=\"%s\" %s>%s</td>\n",$row["Grado"],$selected,$row["Comentarios"]);
	}
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

getSelect_Grados_Perro($_GET["Grado"]);
?>
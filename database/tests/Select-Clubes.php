<!--  Lista de clubes en un combobox -->
<?php
require ("../database/DBConnection.php");

function getSelect_Clubes($default) {
	// Query string
	$querystr="
	SELECT Nombre,Comunidad 
	FROM Clubes,Provincias
	WHERE (Clubes.Provincia=Provincias.Provincia)
	ORDER BY Nombre ASC";
	// connect to DB
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// compose result
	printf ("<select name=\"Clubes\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Nombre"]===$default)?"selected":"";
		$txt="".$row["Nombre"]."&nbsp;&nbsp;---&nbsp;&nbsp;".$row["Comunidad"];
		printf("<option value=\"%s\" %s>%s</option>\n",$row["Nombre"],$selected,$txt);
	}
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

getSelect_Clubes($_GET["Nombre"]);
?>
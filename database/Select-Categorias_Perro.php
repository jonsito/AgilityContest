<!--  Lista de categorias de un perro en un combobox -->
<?php
require ("database/DBConnection.php");

function getSelect_Categorias_Perro($default) {
	$querystr="SELECT * FROM Categorias_Perro";
	// connect database
	$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
	if (!$conn) die("connection error");
	// execute query
	$rs=$conn->query($querystr);
	// compose result
	printf ("<select name=\"Categoria\" class=\"easyui-combobox\">\n");
	while($row = $rs->fetch_array() ){
		$selected=($row["Categoria"]===$default)?"selected":"";
		printf("<option value=\"%s\" %s>%s</td>\n",$row["Categoria"],$selected,$row["Observaciones"]);
	}	
	printf ("</select>\n");
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
}

$categoria=(isset($_GET["Categoria"]))?$_GET["Categoria"]:"";
getSelect_Categorias_Perro($categoria);
?>
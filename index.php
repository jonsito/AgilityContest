<html>
<head>
<title>Test</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="lib/jquery-1.10.2.min.js" type="text/javascript" charset="utf-8" ></script>
</head>
<body>
<table border="1">
<?php
$querystr=
"
SELECT Perros.Dorsal, Perros.Nombre, Perros.Categoria, Perros.Grado, Perros.Guia, Guias.Club 
FROM Perros,Guias
WHERE ( Perros.Guia = Guias.Nombre )
ORDER BY Club, Categoria, Nombre
";

require ("./database/DBConnection.php");
$conn=DBConnection::openConnection("agility_guest","guest@cachorrera");
if (!$conn) die("connection error");
$rs=$conn->query($querystr);
?>
<!--  Cabecera de la tabla -->
<tr>
<?php
	$fields=$rs->fetch_fields();
	foreach($fields as $field) {
		printf("<th>%s</th>",$field->name);
	}
?>
</tr>
<!--  Contenido de la tabla -->
<?php
	while($row = $rs->fetch_array() ){
		printf("<tr>\n");
		foreach($fields as $field) {
			printf("<td>%s</td>",$row[$field->name]);
		}
		printf("</tr>\n");
	}
	// finally close connection
	$rs->free();
	DBConnection::closeConnection($conn);
?>
</table>
</body>
</html>
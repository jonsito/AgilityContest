<html>
<head>
<title>Test</title>
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
$conn=DBConnection::getConnection("agility_guest","guest@cachorrera");
if (!$conn) die("connection error");
$rs=$conn->query($querystr);
?>
<!--  Cabecera de la tabla -->
<th>
<?php
	$fields=$rs->fetch_fields();
	foreach($fields as $field) {
		printf("<td>%s</td>",$field->name);
	}
?>
</th>
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
	DBConnection::removeConnection($conn);
?>
</table>
</body>
</html>
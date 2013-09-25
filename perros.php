<script language="javascript">
        $('#Header_Operation').html('<p>Gesti&oacute;n de Perros</p>');
</script>
<h1>Listado de perros por club y categor&iacute;a </h1>
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
<!--  Declaracion de la tabla -->
<table border="1"  style="width: 100%;" >
<!--  Cabecera de la tabla -->
<thead>
<tr>
<?php
	$fields=$rs->fetch_fields();
	foreach($fields as $field) {
		printf("<th>%s</th>",$field->name);
	}
?>
</tr>
</thead>
<!--  Contenido de la tabla -->
<tbody style="height: 100px; overflow-y: scroll;">
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
</tbody>
</table>
<?php

require_once("logging.php");
require_once("classes/DBObject.php");
require_once("classes/OrdenTandas.php");

function videowall_llamada() {
	$mySession=new DBObject("VideoWall_Llamada");
	$ot=new OrdenTandas("Llamada a pista");
	$result = $ot->getData($p,$j,$a)['rows'];
	$numero=0;
	foreach ($result as $participante) {
		$numero++;
		$logo=$ot->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
		$myLogger->trace("SELECT Logo FROM Clubes,PerroGuiaClub WHERE (Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})");
		if ($logo==="") $logo='rsce.png';
?>
	
	<div id="participante_<?php echo $numero; ?>">
	<table class="llamada">
	<tr>
		<th rowspan="2"><?php echo $numero; ?></th>
		<td rowspan="2"><img src="<?php echo "/agility/images/logos/$logo"?>" alt="<?php echo $logo; ?>" width="50" height="50"/></td>
		<td rowspan="2"><?php echo ''.$participante['Grado'].' - ' .$participante['Categoria']; ?></td>
		<td>Dorsal: <?php echo $participante['Dorsal']?></td>
		<td>Lic.  : <?php echo $participante['Licencia']?></td>
		<td colspan="2" style="text-align:center; font-style:italic; font-size:20px;"><?php echo $participante['Nombre']?></td>
		<td style="text-align:right;"><?php echo ($participante['Celo']==="1")?"Celo":""?></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align:left">Gu&iacute;a: <?php echo $participante['NombreGuia']?></td>
		<td style="text-align:center">-</td>
		<td colspan="2" style="text-align:left">Club: <?php echo $participante['NombreClub']?></td>
	</tr>
	</table>
	<hr />
	</div>
<?php }
}

function videowall_resultados() {
}

function videowall_livestream() {

}

$s = http_request("Sesion","i",0);
$o = http_request("Operation","i",0);

if($o==="Livestream") return videowall_livestream();
if($o==="Llamada") return videowall_llamada();
if($o==="Resultados") return videowall_resultados();

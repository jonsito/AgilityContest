<?php

require_once("logging.php");
require_once(__DIR__."/classes/DBObject.php");
require_once(__DIR__."/classes/OrdenTandas.php");
require_once(__DIR__."/classes/Sesiones.php");

class VideoWall {
	protected $myLogger;
	
	function __construct() {
		$this->myLogger=new Logger("VideoWall.php");
	}
	
	// matrid de modos a evaluar en funcion del tipo de recorrido y de la tanda
	// recorridos:
	// 0: l/m/s separados 1: l/m+s 2: l+m+s conjunto
	// modos:
	// 0:large 1:medium 2:small 3:m+s 4:l+m+s -1:no valido
	public static $modes=array(
			0	=> array(-1,-1,-1, '-- Sin especificar --'),
			// en pre-agility no hay categorias
			1	=> array(-1, -1,  4, 'Pre-Agility 1'), // en pre agility-compiten todos juntos
			2	=> array(-1, -1,  4, 'Pre-Agility 2'),
			3	=> array( 0,  0,  4, 'Agility-1 GI'/* Large */),
			4	=> array( 1,  3,  4, 'Agility-1 GI'/* Medium */),
			5	=> array( 2,  3,  4, 'Agility-1 GI'/* Small */),
			6	=> array( 0,  0,  4, 'Agility-2 GI'/* Large */),
			7	=> array( 1,  3,  4, 'Agility-2 GI'/* Medium */),
			8	=> array( 2,  3,  4, 'Agility-2 GI'/* Small */),
			9	=> array( 0,  0,  4, 'Agility GII'/* Large */),
			10	=> array( 1,  3,  4, 'Agility GII'/* Medium */),
			11	=> array( 2,  3,  4, 'Agility GII'/* Small */),
			12	=> array( 0,  0,  4, 'Agility GIII'/* Large */),
			13	=> array( 1,  3,  4, 'Agility GIII'/* Medium */),
			14	=> array( 2,  3,  4, 'Agility GIII'/* Small */),
			15	=> array( 0,  0,  4, 'Agility Open'/* Large */),
			16	=> array( 1,  3,  4, 'Agility Open'/* Medium */),
			17	=> array( 2,  3,  4, 'Agility Open'/* Small */),
			18	=> array( 0,  0,  4, 'Agility Eq. 3'/* Large */),
			19	=> array(-1,  3,  4, 'Agility Eq. 3'/* Medium */), // en equipos compiten m y s juntos
			20	=> array(-1,  3,  4, 'Agility Eq. 3'/* Small */),
			21	=> array( 0,  0,  4, 'Ag. Equipos 4'/* Large */),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			22	=> array(-1,  3,  4, 'Ag. Equipos 4'/* Med/Small */),
			23	=> array( 0,  0,  4, 'Jumping GII'/* Large */),
			24	=> array( 1,  3,  4, 'Jumping GII'/* Medium */),
			25	=> array( 2,  3,  4, 'Jumping GII'/* Small */),
			26	=> array( 0,  0,  4, 'Jumping GIII'/* Large */),
			27	=> array( 1,  3,  4, 'Jumping GIII'/* Medium */),
			28	=> array( 2,  3,  4, 'Jumping GIII'/* Small */),
			29	=> array( 0,  0,  4, 'Jumping Open'/* Large */),
			30	=> array( 1,  3,  4, 'Jumping Open'/* Medium */),
			31	=> array( 2,  3,  4, 'Jumping Open'/* Small */),
			32	=> array( 0,  0,  4, 'Jumping Eq. 3'/* Large */),
			33	=> array(-1,  3,  4, 'Jumping Eq. 3'/* Medium */),
			34	=> array(-1,  3,  4, 'Jumping Eq. 3'/* Small */),
			// en jornadas por equipos conjunta se mezclan categorias M y S
			35	=> array( 0,  0,  4, 'Jp. Equipos 4'/* Large */),
			36	=> array(-1,  3,  4, 'Jp. Equipos 4'/* Med/Small */),
			// en las rondas KO, los perros compiten todos contra todos
			37	=> array(-1, -1,  4, 'Manga K.O.'),
			38	=> array( 0,  0,  4, 'Manga Especial'/* Large */),
			39	=> array( 1,  3,  4, 'Manga Especial'/* Medium */),
			40	=> array( 2,  3,  4, 'Manga Especial'/* Small */),
	);

	function videowall_llamada($idsesion) {
		$lastTanda="";
		$sesmgr=new Sesiones("VideoWall_Llamada");
		$otmgr=new OrdenTandas("Llamada a pista");
		$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
		$result = $otmgr->getData($mySession->Prueba,$mySession->Jornada,10,$mySession->Tanda)['rows']; // obtiene los 10 primeros perros pendientes
		$numero=0;
		foreach ($result as $participante) {
			if ($lastTanda!==$participante['Tanda']){
				$lastTanda=$participante['Tanda'];
				echo '<div id="tanda_"'.$lastTanda.' class="vwc_tanda"><hr /> ---- '.$lastTanda.' ---- <hr /></div>';
			}
			$numero++;
			$logo=$otmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$participante['Perro']})")['Logo'];
			if ($logo==="") $logo='rsce.png';
			$celo=($participante['Celo']==='1')?'Celo':'';
			$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
			echo '
		<div id="participante_'.$numero.'" style="background:'.$bg.'">
			<table class="vwc_llamada">
			<tr>
				<th rowspan="2">'.$numero.'</th>
				<td rowspan="2"><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
				<td rowspan="2">'.$participante['Grado'].' - '.$participante['Categoria'].'</td>
				<td>Dorsal: '.$participante['Dorsal'].'</td>
				<td>Lic.  : '.$participante['Licencia'].'</td>
				<td colspan="2" style="text-align:center; font-style:italic; font-size:25px;">'.$participante['Nombre'].'</td>
				<td style="text-align:right;">'.$celo.'</td>		
			</tr>
			<tr>
				<td colspan="3" style="text-align:left">Gu&iacute;a: '.$participante['NombreGuia'].'</td>
				<td colspan="2" style="text-align:right">Club: '.$participante['NombreClub'].'</td>
			</tr>
			</table>
		</div>
		';
		}
	}

	function videowall_resultados($idsesion) {
		$sesmgr=new Sesiones("VideoWall_Resultados");
		$mySession=$sesmgr->__getObject("Sesiones",$idsesion);
		$resmgr=new Resultados("videowall_resultados",$mySession->Prueba, $mySession->Manga );
		// obtenemos modo de resultados asociado a la manga
		$myManga=$sesmgr->__getObject("Mangas",$mySession->Manga);
		$mode=VideoWall::$modes[$mySession->Tanda][$myManga->Recorrido];
		$this->myLogger->trace("**** Mode es $mode");
		$result = $resmgr->getResultados($mode);
		$numero=0;
		// cabecera de la tabla
		echo '
			<div id="vwc_tablaResultados">
			<table class="vwc_tresultados">
			<theader>
				<th>Puesto</th>
				<th>&nbsp</th>
				<th colspan="5">Participante</th>
				<th>Flt.</th>
				<th>Toc.</th>
				<th>Reh.</th>
				<th>Tiempo</th>
				<th>Vel.</th>
				<th>Penal.</th>
				<th colspan="2">Calificacion</th>
			</theader>
			<tbody>
			';
		foreach ($result['rows'] as $resultado) {
			error_log(json_encode($resultado));
			$numero++;
			$bg=(($numero%2)!=0)?"#ffffff":"#d0d0d0";
			$logo=$resmgr->__selectAsArray("Logo","Clubes,PerroGuiaClub","(Clubes.ID=PerroGuiaClub.Club) AND (PerroGuiaClub.ID={$resultado['Perro']})")['Logo'];
			if ($logo==="") $logo='rsce.png';
			echo '
				<tr id="Resultado_'.$numero.'" style="background:'.$bg.'">
					<td class="vwc_puesto">'.$resultado['Puesto'].'</td>
					<td><img src="/agility/images/logos/'.$logo.'" alt="'.$logo.'" width="50" height="50"/></td>
					<td colspan="3">
						<table class="vwc_trparticipantes">
							<tr>
								<td>Dorsal:</td><td>'.$resultado['Dorsal'].'</td>
								<td>Lic.:</td><td>'.$resultado['Licencia'].'</td>
								<td>Grado:</td><td>'.$resultado['Grado'].'</td>
								<td>Cat:</td><td>'.$resultado['Categoria'].'</td>
							</tr>
							<tr>
								<td>Guia:</td><td colspan="4">'.$resultado['NombreGuia'].'</td>
								<td>Club:</td><td colspan="2">'.$resultado['NombreClub'].'</td>
							</tr>
						</table>
					</td>
					<td colspan="2" class="vwc_nombre">'.$resultado['Nombre'].'</td>
					<td class="vwc_ftr">'.$resultado['Faltas'].'</td>
					<td class="vwc_ftr">'.$resultado['Tocados'].'</td>
					<td class="vwc_ftr">'.$resultado['Rehuses'].'</td>
					<td class="vwc_rlarge">'.number_format($resultado['Tiempo'],2).'</td>
					<td class="vwc_vel">'.number_format($resultado['Velocidad'],1).'</td>
					<td class="vwc_rlarge">'.number_format($resultado['Penalizacion'],2).'</td>
					<td colspan="2" class="vwc_calif">'.$resultado['Calificacion'].'</td>
				</tr>
			';
		}
		echo '</tbody></table></div>';
	}
	
	function videowall_livestream($sesion) {
		/* recupera los datos de un perro y le añade informacion de celo */
		$celo = http_request("Celo","i",0);
		$id= http_request("Perro","i",0);
		$pmgr= new Dogs("VideoWall_LiveSTream");
		$data=$pmgr->selectByID($id);
		$data["Celo"]=$celo;
		return $data;
	}
} 

$sesion = http_request("Session","i",0);
$operacion = http_request("Operation","s",null);
$vw=new VideoWall();
if($operacion==="livestream") return $vw->videowall_livestream($sesion);
if($operacion==="llamada") return $vw->videowall_llamada($sesion);
if($operacion==="resultados") return $vw->videowall_resultados($sesion);

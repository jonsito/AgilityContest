<?php
header('Set-Cookie: fileDownload=true; path=/');
// mandatory 'header' to be the first element to be echoed to stdout

/**
 * genera un CSV con los datos para las etiquetas
 */

require_once(__DIR__."/../database/tools.php");
require_once(__DIR__."/../database/logging.php");
require_once(__DIR__.'/../database/classes/DBObject.php');
require_once(__DIR__.'/../database/classes/Clubes.php');
require_once(__DIR__.'/../database/classes/Pruebas.php');
require_once(__DIR__.'/../database/classes/Jueces.php');
require_once(__DIR__.'/../database/classes/Jornadas.php');
require_once(__DIR__.'/../database/classes/Mangas.php');
require_once(__DIR__.'/../database/classes/Resultados.php');
require_once(__DIR__.'/../database/classes/Clasificaciones.php');

class CSV  {
	
	public $myLogger;
	protected $prueba;
	protected $club;
	protected $jornada;
	protected $manga1;
	protected $manga2;
	protected $resultados;
	protected $icon;
	
	 /** Constructor
	 * @param {obj} $manga datos de la manga
	 * @param {obj} $resultados resultados asociados a la manga/categoria pedidas
	 * @throws Exception
	 */
	function __construct($prueba,$jornada,$mangas,$resultados) {
		$this->myLogger= new Logger("print_etiquetas_csv");
		$dbobj=new DBObject("print_etiquetas_pdf");
		$this->prueba=$dbobj->__getObject("Pruebas",$prueba);
		$this->club=$dbobj->__getObject("Clubes",$this->prueba->Club);
		$this->jornada=$dbobj->__getObject("Jornadas",$jornada);
		$this->manga1=$dbobj->__getObject("Mangas",$mangas[0]);
		$this->manga2=$dbobj->__getObject("Mangas",$mangas[1]);
		$this->resultados=$resultados;
		// evaluage logo info
		$this->icon="welpe.png";
		if (isset($this->club)) $this->icon=$this->club->Logo;
	}
	
	// No tenemos cabecera: no cabe
	function writeHeader() {
		$str ="Dorsal:Prueba:Fecha:Licencia:Nombre:Guia:Club:Categoria:Grado:";
		$str.="TipoManga1:Penalizacion1:Calificacion1:Puesto1:";
		$str.="TipoManga2:Penalizacion2:Calificacion2:Puesto2";
		$str.="\n";
		return $str;
	}
	
	function writeCell($row) {
		$this->myLogger->trace(json_encode($row));
		$line  ="";
		$line .= $row['Dorsal'].":";
		$line .= $this->prueba->Nombre.":";
		$line .= $this->jornada->Fecha.":";
		$line .= $row['Licencia'].":";
		$line .= $row['Nombre'].":";
		$line .= $row['NombreGuia'].":";
		$line .= $row['NombreClub'].":";
		$line .= $row['Categoria'].":";
		$line .= $row['Grado'].":";
		$line .= Mangas::$tipo_manga[$this->manga1->Tipo][3].":";
		$line .= $row['P1'].":";
		$line .= $row['C1'].":";
		$line .= $row['Puesto1'].":";
		$line .= Mangas::$tipo_manga[$this->manga2->Tipo][3].":";
		$line .= $row['P2'].":";
		$line .= $row['C2'].":";
		$line .= $row['Puesto2'].":";
		$line .= "\n";
		return $line;
	}
	
	function composeTable() {
		$this->myLogger->enter();
		$result=$this->writeHeader();
		$rowcount=0;
		$numrows=16; // 16 etiquetas/pagina
		foreach($this->resultados as $row) {
			$result.=$this->writeCell($row);
			$rowcount++;
		}
		$this->myLogger->leave();
		return $result;
	}
}

try {
	$result=null;
	$mangas=array();
	$prueba=http_request("Prueba","i",0);
	$jornada=http_request("Jornada","i",0);
	$rondas=http_request("Rondas","i","0"); // bitfield of 512:Esp 256:KO 128:Eq4 64:Eq3 32:Opn 16:G3 8:G2 4:G1 2:Pre2 1:Pre1
	$mangas[0]=http_request("Manga1","i",0); // single manga
	$mangas[1]=http_request("Manga2","i",0); // mangas a dos vueltas
	$mangas[2]=http_request("Manga3","i",0);
	$mangas[3]=http_request("Manga4","i",0); // 1,2:GII 3,4:GIII
	$mangas[4]=http_request("Manga5","i",0);
	$mangas[5]=http_request("Manga6","i",0);
	$mangas[6]=http_request("Manga7","i",0);
	$mangas[7]=http_request("Manga8","i",0);
	$mangas[8]=http_request("Manga9","i",0); // mangas 3..9 are used in KO rondas
	$mode=http_request("Mode","i","0"); // 0:Large 1:Medium 2:Small 3:Medium+Small 4:Large+Medium+Small
	$c= new Clasificaciones("print_etiquetas_pdf",$prueba,$jornada);
	$result=$c->clasificacionFinal($rondas,$mangas,$mode);
} catch (Exception $e) {
	do_log($e->getMessage());
	die ($e->getMessage());
}

header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=printEtiquetas_csv.csv");
$csv =new CSV($prueba,$jornada,$mangas,$result['rows']);
echo $csv->composeTable();
?>
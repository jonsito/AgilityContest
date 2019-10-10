<?php
/*
Pruebas.php

Copyright  2013-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/



require_once(__DIR__."/DBObject.php");
require_once(__DIR__ . "/../../modules/Federations.php");
require_once(__DIR__."/Jornadas.php");

class Pruebas extends DBObject {

	function __construct() {
		parent::__construct("Pruebas");
	}

	/**
     * search for duplicate name in pruebas
     * ( this should be solved marking Prueba(Nombre) unique not null, but to maintain compatibility
     *@param {string} $name New Prueba Name
     *@param {$id} 0 on create, PruebaID on update
     *@return 0: no duplicates else number of dups
     */
	protected function checkForDuplicates($name,$id) {
	    $name=$this->conn->real_escape_string($name);
	    $where=($id===0)?"":" AND (ID!={$id})";
	    $res=$this->__selectObject(
	        /* select */ "count(*) AS Items",
            /* from */   "pruebas",
            /* where */  "Nombre='{$name}' {$where}"
        );
	    return $res->Items;
    }

	function insert() {
		$this->myLogger->enter();
        // iniciamos los valores, chequeando su existencia
        // como usamos prepared statements no hace falta escapar la entrada
        // pero habrá que hacerlo a la hora de ver si el nombre esta duplicado
        $nombre =	http_request("Nombre","s",null,false); // not null
        $club =		http_request("Club","i",0);
        $ubicacion=	http_request("Ubicacion","s","",false);
        $triptico =	http_request("Triptico","s","",false);
        $cartel =	http_request("Cartel","s","",false);
        $observaciones = http_request("Observaciones","s","",false);
        $rsce =	http_request("RSCE","i",0);
        $selectiva =	http_request("Selectiva","i",0);
        $cerrada =	http_request("Cerrada","i",0);
        // should be declared, but if not assume defaults
        $openingreg= http_request("OpeningReg","s",date("Y-m-d"));
        $closingreg= http_request("ClosingReg","s",date("Y-m-d",strtotime($openingreg)+86400));
        $this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");
        // make sure that no existing entry with same name
        $dups=$this->checkForDuplicates($nombre,0);
        if ($dups!=0) return $this->error(_("There is already a contest with provided name"));
		// componemos un prepared statement
		$sql ="INSERT INTO pruebas (Nombre,Club,Ubicacion,Triptico,Cartel,Observaciones,RSCE,Selectiva,Cerrada,OpeningReg,ClosingReg)
			   VALUES(?,?,?,?,?,?,?,?,?,?,?)";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param(
		    'sissssiiiss',
            $nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$rsce,$selectiva,$cerrada,$openingreg,$closingreg
        );
		if (!$res) return $this->error($stmt->error);
		
		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		
		// retrieve PruebaID on newly create prueba
		$pruebaid=$this->conn->insert_id;
		$stmt->close();
		
		// create eight journeys per contest
        $today=date("Y-m-d");
		for ($n=1;$n<9;$n++) {
			$sql ="INSERT INTO jornadas (Prueba,Numero,Nombre,Fecha,Hora)
			VALUES ($pruebaid,$n,'-- Sin asignar --','{$today}','08:30:00')";
			$res=$this->query($sql);
			if (!$res) return $this->error($this->conn->error);
			// retrieve ID of inserted jornada
			$jornadaid=$this->conn->insert_id;
			// create default team for each journey
            // as for 4.0.0 and up default team allows every heights
            // notice that "Miembros" is no longer used, just set not null for db integrity
			$str="INSERT INTO equipos (Prueba,Jornada,Nombre,Categorias,Observaciones,Miembros,DefaultTeam)
				VALUES ($pruebaid,$jornadaid,'-- Sin asignar --','XLMST','NO BORRAR: PRUEBA $pruebaid JORNADA $jornadaid - Default Team','BEGIN,END',1 )";
			$res=$this->query($str);
			if (!$res) return $this->error($this->conn->error);
            // retrieve ID of inserted default team and insert into newly created jornada
            // stupid loop, I know, but needed to preserve foreign keys integrity
            $teamid=$this->conn->insert_id;
            $str="UPDATE jornadas SET Default_Team=$teamid WHERE (ID=$jornadaid)";
			$res=$this->query($str);
			if (!$res) return $this->error($this->conn->error);
		}
		// arriving here means everything ok. notify success
		$this->myLogger->leave();
		return "";
	}
	
	function update($pruebaid) {
		$this->myLogger->enter();
		if ($pruebaid<=0) return $this->error("pruebas::update() Invalid Prueba ID:$pruebaid");

        // iniciamos los valores, chequeando su existencia
        $nombre =	http_request("Nombre","s",null,false); // not null
        $id =		$pruebaid;
        $club =		http_request("Club","i",0);
        $ubicacion=	http_request("Ubicacion","s","",false);
        $triptico =	http_request("Triptico","s","",false);
        $cartel =	http_request("Cartel","s","",false);
        $observaciones = http_request("Observaciones","s","",false);
        $rsce =	http_request("RSCE","i",0);
        $selectiva =	http_request("Selectiva","i",0);
        $cerrada =	http_request("Cerrada","i",0);
        // should be declared, but if not assume defaults
        $openingreg= http_request("OpeningReg","s",date("Y-m-d"));
        $closingreg= http_request("ClosingReg","s",date("Y-m-d",strtotime($openingreg)+86400));
        $this->myLogger->debug("Nombre: $nombre Club: $club Ubicacion: $ubicacion Observaciones: $observaciones");

        // make sure that no existing entry with same name
        $dups=$this->checkForDuplicates($nombre,$pruebaid);
        if ($dups!=0) return $this->error(_("There is already a contest with provided name"));

		// componemos un prepared statement
		$sql ="UPDATE pruebas
				SET Nombre=? , Club=? , Ubicacion=? , Triptico=? , Cartel=?, 
				Observaciones=?, RSCE=?, Selectiva=?, Cerrada=?, OpeningReg=?, ClosingReg=?
				WHERE ( ID=? )";
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param(
		    'sissssiiissi',
            $nombre,$club,$ubicacion,$triptico,$cartel,$observaciones,$rsce,$selectiva,$cerrada,$openingreg,$closingreg,$id
        );
		if (!$res) return $this->error($stmt->error);

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		$this->myLogger->leave();
		$stmt->close();
		return "";
	}
	
	/**
	 * Borra una prueba
	 * @param {integer} $id ID de la prueba
	 * @return string
	 */
	function delete($id) {
		$this->myLogger->enter();
		// pruebaID==1 is default prueba, so avoid deletion
		if ($id<=1) return $this->error("pruebas::delete() Invalid Prueba ID:{$id}");
		// Borramos resultados asociados a esta prueba
		$res=$this->__delete("resultados","( Prueba={$id})");
		if (!$res) return $this->error($this->conn->error);
		// Borramos inscripciones de esta prueba
		$res=$this->__delete("inscripciones","( Prueba={$id})");
		if (!$res) return $this->error($this->conn->error);
		// Borramos las jornadas (y mangas) de esta prueba
		$j=new Jornadas("Pruebas.php",$id);
		$j->deleteByPrueba();
		// Borramos tambien las tandas de las jornadas de esta prueba
		$res=$this->__delete("tandas","( Prueba={$id})");
		if (!$res) return $this->error($this->conn->error);
		// finalmente intentamos eliminar la prueba
		$res= $this->__delete("pruebas","(ID={$id})");
		if (!$res) return $this->error($this->conn->error); 
		$this->myLogger->leave();
		return "";
	}
	
	/**
	 * Lista pruebas ordenando por los parametros especificados y con criterios de busqueda
	 * @return null on error, else array in jquery expected format
	 */
	function select() {
		$this->myLogger->enter();
		$sort=getOrderString( //needed to properly handle multisort requests from datagrid
			http_request("sort","s",""),
			http_request("order","s",""),
			"Nombre ASC"
		);
		$search=http_Request("where","s","");
		$closed=http_request("closed","i",0); // si esta declarada, se incluyen las pruebas cerradas
		$page=http_request("page","i",1);
		$rows=http_request("rows","i",50);
		$limit="";
		$where="";
		if ($page!=0 && $rows!=0 ) {
			$offset=($page-1)*$rows;
			$limit="".$offset.",".$rows;
		}
		if ( ($search!=="") && ($closed==0) )
			$where="( (pruebas.Club=clubes.ID) && ( pruebas.Cerrada=0 ) && 	( (pruebas.Nombre LIKE '%$search%') OR ( clubes.Nombre LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) ) ) ";
		if ( ($search!=="") && ($closed!=0) )
			$where="( (pruebas.Club=clubes.ID) && ( (pruebas.Nombre LIKE '%$search%') OR ( clubes.Nombre LIKE '%$search%') OR ( Ubicacion LIKE '%$search%' ) ) )";
		if ( ($search==="") && ($closed==0) )
			$where="( (pruebas.Club=clubes.ID) && ( pruebas.Cerrada=0 ) )";
		if ( ($search==="") && ($closed!=0) )
			$where="(pruebas.Club=clubes.ID)";

		// execute query to retrieve $rows starting at $offset
		$result=$this->__select(
				/* SELECT */ "pruebas.ID AS ID, pruebas.Nombre AS Nombre, pruebas.Club AS Club,clubes.Nombre AS NombreClub, clubes.Logo AS LogoClub,
							pruebas.Ubicacion AS Ubicacion,pruebas.Triptico AS Triptico, pruebas.Cartel AS Cartel,
							pruebas.RSCE AS RSCE, pruebas.Selectiva AS Selectiva, pruebas.OpeningReg AS OpeningReg, pruebas.ClosingReg AS ClosingReg,
							pruebas.Cerrada AS Cerrada, pruebas.Observaciones AS Observaciones",
				/* FROM */ "pruebas,clubes",
				/* WHERE */ $where,
				/* ORDER BY */ $sort,
				/* LIMIT */ $limit
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/** 
	 * lista de pruebas abiertas.
	 * As select but not sort criteria and show only open contests. Used in combogrids
     * @param {object} $am AuthManager object
	 */
	function enumerate($am=null) {
		$this->myLogger->enter();
        // retrieve number of inscriptions for this contest
        if ($am==null) $am=AuthManager::getInstance("Pruebas::enumerate");
        $limit=$am->getUserLimit();
        $inscritos=$this->__select("Prueba, count(*) AS Inscritos","inscripciones","1 GROUP BY Prueba","","");

		// evaluate search criteria for query
		$q=http_request("q","s","");
		$where= "(pruebas.Club=clubes.ID) AND ( pruebas.Cerrada=0 ) ";
		if($q!=="") $where="$where AND ( (pruebas.Nombre LIKE '%$q%' ) OR (clubes.Nombre LIKE '%$q%') OR (pruebas.Observaciones LIKE '%$q%') )";
		// retrieve result from parent __select() call
		$result= $this->__select(
				/* SELECT */ "pruebas.ID AS ID, pruebas.Nombre AS Nombre, pruebas.Club AS Club,
				            clubes.Nombre AS NombreClub, clubes.Logo AS LogoClub,
							pruebas.Ubicacion AS Ubicacion, pruebas.Triptico AS Triptico, pruebas.Cartel AS Cartel, 
							pruebas.RSCE AS RSCE, pruebas.Selectiva AS Selectiva, pruebas.Cerrada AS Cerrada,
							pruebas.OpeningReg AS OpeningReg, pruebas.ClosingReg AS ClosingReg,
							pruebas.Observaciones AS Observaciones, $limit as UserLimit",
				/* FROM */ "pruebas,clubes",
				/* WHERE */ $where,
				/* ORDER BY */ "ID DESC",
				/* LIMIT */ ""
		);
        // parse result and add inscriptions count
        foreach ($result['rows'] as &$item) { // pass by reference
            $item['Inscritos']=0;
            foreach($inscritos['rows'] as $data) { if ($data['Prueba']==$item['ID']) $item['Inscritos']=$data['Inscritos']; }
        }
		// return composed array
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Retrieve data on requested prueba id
	 * @param {integer} $id prueba ID
	 * @return null on error, associative array on success
	 */
	function selectByID($id) {
		$this->myLogger->enter();
		if ($id<=0) return $this->error("prueba:::selectByID() Invalid Prueba ID:$id");

		// make query
		$data= $this->__selectAsArray(
				/* SELECT */ "pruebas.ID AS ID, pruebas.Nombre AS Nombre, pruebas.Club AS Club,clubes.Nombre AS NombreClub, clubes.Logo AS LogoClub,
					pruebas.Ubicacion AS Ubicacion,pruebas.Triptico AS Triptico, pruebas.Cartel AS Cartel,
					pruebas.RSCE AS RSCE, pruebas.Selectiva AS Selectiva, pruebas.OpeningReg AS OpeningReg, pruebas.ClosingReg AS ClosingReg,
					pruebas.Cerrada AS Cerrada, pruebas.Observaciones AS Observaciones",
				/* FROM */ "pruebas,clubes",
				/* WHERE */ "( clubes.ID=pruebas.Club) && ( pruebas.ID=$id )"
		);
		if (!is_array($data))	return $this->error("No Prueba found with ID=$id");
		// fix logo path To be done at client side if required
		// $fed=Federations::getFederation(intval($data['RSCE']));
		// $data['LogoClub']=getIconPath($fed->get('Name'),$data['LogoClub']);
		// if ($fed->isInternational()){	$data['LogoClub']=$fed->get('Logo'); }
		$data['Operation']='update'; // dirty trick to ensure that form operation is fixed
		$this->myLogger->leave();
		return $data;
	}
}

?>
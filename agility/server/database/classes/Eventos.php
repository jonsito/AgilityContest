<?php
/*
Eventos.php

Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once(__DIR__."/../../auth/Config.php");

// How often to poll, in micro-seconds
define('EVENT_POLL_MICROSECONDS', 500000); 
// How long to keep the Long Poll open, in seconds
define('EVENT_TIMEOUT_SECONDS', 30);
// Timeout padding in seconds, to avoid a premature timeout in case the last call in the loop is taking a while
define('EVENT_TIMEOUT_SECONDS_BUFFER', 5);

require_once("DBObject.php");

class Eventos extends DBObject {
	
	static $event_list = array (
		0	=> 'null',			// null event: no action taken
		1	=> 'init',			// operator starts tablet application
		2	=> 'login',			// operador hace login en el sistema
		3	=> 'open',			// operator selects tanda on tablet
		// eventos de crono manual
		4	=> 'salida',		// juez da orden de salida ( crono 15 segundos )
		5	=> 'start',			// Crono manual - value: timestamp
		6	=> 'stop',			// Crono manual - value: timestamp
		// eventos de crono electronico. Siempre llevan Value=timestamp como argumento
		7	=> 'crono_start',	// Arranque Crono electronico
		8	=> 'crono_restart',	// Paso de crono manual a crono electronico
		9	=> 'crono_int',		// Tiempo intermedio Crono electronico
		10	=> 'crono_stop',	// Parada Crono electronico
		11 	=> 'crono_rec',		// Llamada a reconocimiento de pista
		12  => 'crono_dat',     // Envio de Falta/Rehuse/Eliminado desde el crono
		13  => 'crono_reset',	// puesta a cero del contador
		14	=> 'crono_error',	// error en alineamiento de sensores
		// entrada de datos, dato siguiente, cancelar operacion
		15	=> 'llamada',		// operador abre panel de entrada de datos
		16	=> 'datos',			// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		17	=> 'aceptar',		// grabar datos finales
		18	=> 'cancelar',		// restaurar datos originales
        19  => 'info',           // value: message
		// eventos de cambio de camara para videomarcadores
        // el campo data contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
		20	=> 'camera',		// cambio de fuente de streaming
		21	=> 'reconfig'		// se ha cambiado la configuracion en el servidor
	);
	
	protected $sessionID;
	protected $sessionFile;
	protected $myAuth;
	
	/**
	 * Constructor
	 * @param {string} $file caller for this object
	 * @param {integer} $id Session ID
	 * @param {object} $am AuthManager Object
	 * @throws Exception if cannot contact database or invalid Session ID
	 */
	function __construct($file,$id,$am) {
		parent::__construct($file);
		if ( $id<=0 ) {
			$this->errormsg="$file::construct() invalid Session:$id ID";
			throw new Exception($this->errormsg);
		}
		$this->sessionID=$id;
		$this->myAuth=$am;
		$this->sessionFile=__DIR__."/../../../../logs/events.$id";
		// nos aseguramos de quere el fichero de sesion exista
		if ( ! file_exists($this->sessionFile) ) touch($this->sessionFile);
	}
	
	/**
	 * Insert a new event into database
     * @param {array} data dataset of key:value pairs to store in "data" field
	 * @return {string} "" if ok; null on error
	 */
	function putEvent($data) {
		$this->myLogger->enter();
        $cfg=Config::getInstance();
		$sid=$this->sessionID;
		// si el evento es "init" y el flag reset_events está a 1 borramos el historico de eventos antes de reinsertar
        if ( ( intval($cfg->getEnv("reset_events")) == 1 ) && ( ($data['Type']==='init') )) {
            $rs= $this->query("DELETE FROM Eventos WHERE (Session=$sid)");
            if (!$rs) return $this->error($this->conn->error);
            file_put_contents($this->sessionFile,"\n",LOCK_EX); // borra fichero de eventos
        }
		// comprueba los permisos de los diversos eventos antes de aceptarlos:
		switch($data['Type']) {
			case 'null':			// null event: no action taken
			case 'init':			// operator starts tablet application
			case 'login':			// operador hace login en el sistema
			case 'open':			// operator selects tanda on tablet
				break;
			// eventos de crono manual
			case 'salida':			// juez da orden de salida ( crono 15 segundos )
			case 'start':			// Crono manual - value: timestamp
			case 'stop':			// Crono manual - value: timestamp
				break;
			// en crono electronico se pasan dos valores 'Tim' Tiempo a mostrar 'Value': timestamp
			case 'crono_start':		// Arranque Crono electronico
			case 'crono_restart':	// manual to auto transition
			case 'crono_int':		// Tiempo intermedio Crono electronico
			case 'crono_stop':		// Parada Crono electronico
			case 'crono_rec':		// Llamada a reconocimiento de pista
			case 'crono_dat':     	// Envio de Falta/Rehuse/Eliminado desde el crono
            case 'crono_reset':		// puesta a cero del contador
            case 'crono_error':		// error en alineamiento de sensores
				if (!$this->myAuth->allowed(ENABLE_CHRONO)) {
					$this->myLogger->info("Ignore chrono events: licencse forbids");
					return array('errorMsg' => 'Current license does not allow chrono handling');
				} // silently ignore
				break;
			// entrada de datos, dato siguiente, cancelar operacion
			case 'llamada':		// operador abre panel de entrada de datos
			case 'datos':			// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
			case 'aceptar':		// grabar datos finales
			case 'cancelar':		// restaurar datos originales
			case 'info':           // value: message
				break;
			// eventos de cambio de camara para videomarcadores
			// el campo data contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
			case 'camera':		// cambio de fuente de streaming
				if (!$this->myAuth->allowed(ENABLE_VIDEOWALL)) {
					$this->myLogger->info("Ignore camera events: licencse forbids");
					return array('errorMsg' => 'Current license does not allow LiveStream handling');
				} // silently ignore
				break;
			case 'reconfig':	// cambio en la configuracion del servidor
				if (!$this->myAuth->access(PERMS_ADMIN)) {
					$this->myLogger->info("Ignore reconfig events: not enough permissions");
					return array('errorMsg' => 'Only Admin users cand send reconfiguration events');
				}
				break;
			default:
				$this->myLogger->error("Unknown event type:".$data['Type']);
				return "";
		}
        // iniciamos los valores
        // $timestamp= date('Y-m-d G:i:s');
        $timestamp= date('Y-m-d G:i:s',$data['TimeStamp']/1000);
        $source=$data['Source'];
        $type=$data['Type'];
        $evtdata=json_encode($data);

		// prepare statement
		$sql = "INSERT INTO Eventos ( TimeStamp,Session, Source, Type, Data ) VALUES (?,$sid,?,?,?)";
        $this->myLogger->trace("Events::insert() Source:$source Type:$type Data:$evtdata");
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssss',$timestamp,$source,$type,$evtdata);
		if (!$res) return $this->error($this->conn->error);

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($this->conn->error);
		
		// retrieve EventID on newly create event
		$data['TimeStamp']=$timestamp;
		$data['ID']=$this->conn->insert_id;
		$stmt->close();
		
		// and save content to event file
		$flag=$cfg->getEnv("register_events");
		$str=json_encode($data);
		if (boolval($flag)) {
			file_put_contents($this->sessionFile,$str."\n", FILE_APPEND | LOCK_EX);
		} else {
			// as touch() doesn't work if "no_atime" flag is enabled (SSD devices)
			// just overwrite event file with last event
			file_put_contents($this->sessionFile,$str."\n",LOCK_EX);
		}
		// that's all.
		$this->myLogger->leave();
		return ""; 
	}

	/**
	 * send 'reconfig' event to every sessions
	 */
	function reconfigure() {
		$data= array("Type"=>"reconfig", "Source"=>"Console", "ID"=>0, "TimeStamp"=>1000*time(),"Value"=>0);
		return $this->putEvent($data);
	}

    /**
     * (Server side implementation of LongCall ajax)
     * Ask for events
     * If no new events, wait for event available until timeout
     * @see http://www.nolithius.com/game-development/comet-long-polling-with-php-and-jquery
     * @see http://www.abrandao.com/2013/05/11/php-http-long-poll-server-push/
     * @param {array} $data key:value pairs to extract "timestamp" from
     * @return array|null
     */
	function getEvents($data) { 
		$this->myLogger->enter();
		
		// Close the session prematurely to avoid usleep() from locking other requests
		// notice that cannot call http_request after this item
		session_write_close();
		
		// Automatically die after timeout (plus buffer)
		set_time_limit(EVENT_TIMEOUT_SECONDS+EVENT_TIMEOUT_SECONDS_BUFFER);
		
		// retrieve timestamp from file and request
		$current=filemtime($this->sessionFile);
		$last=$data['TimeStamp'];
		$this->myLogger->info("Last timestamp is $last");
		// Counter to manually keep track of time elapsed 
		// (PHP's set_time_limit() is unrealiable while sleeping)
		$counter = 0;
		$res=null;
		
		// Poll for messages and hang if nothing is found, until the timeout is exhausted
		while($counter < EVENT_TIMEOUT_SECONDS )	{
			// $this->myLogger->info("filemtime:$current lastquery:$last" );
			if ( $current > $last ) {
				// new data has arrived: get it
				$res=$this->listEvents($data);
				if ( is_array($res) ) $res['TimeStamp']=$current; // data received: store timestamp in response
				break;
			}
			if ( ($current==$last) && ( $counter<1 ) ){
				
				// poll at least first second to make sure no new data is available
				// new data has arrived: get it
				$res=$this->listEvents($data);
				if ( is_array($res) && ($res['total']!=0) ) {
					$res['TimeStamp']=$current; // data received: store timestamp in response
					break;
				}
			}
			// Otherwise, sleep for the specified time, after which the loop runs again
			usleep(EVENT_POLL_MICROSECONDS);
			// clear stat cache to ask for real mtime
			clearstatcache();
			$current =filemtime($this->sessionFile);
			// Decrement seconds from counter (the interval was set in μs, see above)
			$counter += (EVENT_POLL_MICROSECONDS / 1000000);
		}
		// if no new events (timeout) create an empty result
		if ($res===null) $res=array( 'total'=>0, 'rows'=>array(), 'TimeStamp' => $current );
		$this->myLogger->leave();
		return $res;
	}

    /**
     * As getEvents() but don't wait for new events, just list existing ones
     * @param {array} $data key:value pairs to extract parameters from
     * @return array|null {array} available events for session $data['Session'] with id greater than $data['ID']
     * available events for session $data['Session'] with id greater than $data['ID']
     */
	function listEvents($data) {
		if ($data['Session']<=0) return $this->error("No Session ID specified");
		$this->myLogger->enter();
		$extra="";
		if ($data['Type']!=="") $extra=" AND ( Type = {$data['Type']} )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Eventos",
				/* WHERE */ "( ( Type = 'reconfig' ) OR ( Session = {$data['Session']} ) ) AND ( ID > {$data['ID']} ) $extra",
				/* ORDER BY */ "ID",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Retrieve last "open" event with provided Session ID
	 * Used for clients to retrieve event ID index
	 * SELECT * from Eventos
	 *		WHERE  ( Session = {$data['Session']} ) AND ( Type = 'open' )
	 *		ORDER BY ID DESC LIMIT 1
     * @param {array} $data key:value pairs to extract parameters from
	 * @param {array} $data requested event info
	 * @return {array} data about last "open" event with provided session id
	 */
	function connect($data) {
        $config=Config::getInstance();
        if (intval($config->getEnv('restricted'))!=0) { // in slave config do not allow "connect" operations
            header('HTTP/1.0 403 Forbidden');
            die("You cannot use this server as event source");
        }
		if ($data['Session']<=0) return $this->error("No Session ID specified");
		$this->myLogger->enter();
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Eventos",
				/* WHERE */ "( Session = {$data['Session']} ) AND ( Type = 'open' )",
				/* ORDER BY */ "ID DESC",
				/* LIMIT */ "0,1"
						);
		$this->myLogger->leave();
		return $result;
	}
}
?>
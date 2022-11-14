<?php
/*
Eventos.php

Copyright  2013-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )

This program is free software; you can redistribute it and/or modify it under the terms 
of the GNU General Public License as published by the Free Software Foundation; 
either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; 
if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

require_once("DBObject.php");
require_once(__DIR__."/../../auth/Config.php");
require_once(__DIR__."/Sesiones.php");
require_once(__DIR__."/../../printer/RawPrinter.php");

// How often to poll, in micro-seconds
define('EVENT_POLL_MICROSECONDS', 500000); 
// How long to keep the Long Poll open, in seconds
define('EVENT_TIMEOUT_SECONDS', 30);
// Timeout padding in seconds, to avoid a premature timeout in case the last call in the loop is taking a while
define('EVENT_TIMEOUT_SECONDS_BUFFER', 5);

define('EVTCMD_NULL',0); // nothing; just ping
define('EVTCMD_SWITCH_SCREEN',1); // switch videowall mode
define('EVTCMD_SETFONTFAMILY',2);
define('EVTCMD_NOTUSED3',3); // switch font family ) simplified videowalls )
define('EVTCMD_SETFONTSIZE',4);
define('EVTCMD_OSDSETALPHA',5); // increase/decrease OSD transparency level
define('EVTCMD_OSDSETDELAY',6); // set response time to events ( to sync livestream OSD )
define('EVTCMD_NOTUSED7',7);
define('EVTCMD_MESSAGE',8); // prompt a message dialog on top of screen
define('EVTCMD_ENABLEOSD',9); // enable / disable OnScreenDisplay


class Eventos extends DBObject {
	
	static $event_list = array (
		0	=> 'null',			// null event: no action taken
		1	=> 'init',			// operator starts tablet application
		2	=> 'login',			// operador hace login en el sistema
		3	=> 'open',			// operator selects tanda on tablet
		4	=> 'close',			// no more dogs in tanda
		// eventos de crono manual
		5	=> 'salida',		// juez da orden de salida ( crono 15 segundos )
		6	=> 'start',			// Crono manual - value: timestamp
		7	=> 'stop',			// Crono manual - value: timestamp
		// eventos de crono electronico. Siempre llevan Value=timestamp como argumento
		8	=> 'crono_start',	// Arranque Crono electronico
		9	=> 'crono_restart',	// Paso de crono manual a crono electronico
		10	=> 'crono_int',		// Tiempo intermedio Crono electronico
		11	=> 'crono_stop',	// Parada Crono electronico
		12 	=> 'crono_rec',		// Llamada a reconocimiento de pista
		13  => 'crono_dat',     // Envio de Falta/Rehuse/Eliminado desde el crono
		14  => 'crono_reset',	// puesta a cero del contador
		15	=> 'crono_error',	// error en alineamiento de sensores
        23  => 'crono_ready',   // estado del crono activado/escuchando
		// entrada de datos, dato siguiente, cancelar operacion
		16	=> 'llamada',		// operador abre panel de entrada de datos
		17	=> 'datos',			// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
		18	=> 'aceptar',		// grabar datos finales
		19	=> 'cancelar',		// restaurar datos originales
        20  => 'info',           // value: message
		// eventos de cambio de camara para videomarcadores
        // el campo data contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
		21	=> 'camera',		// cambio de fuente de streaming
		22	=> 'reconfig',		// se ha cambiado la configuracion en el servidor
        24  => 'command',      // control remoto del videomarcador
        25  => 'user'           // evento de usuario generado desde el tablet ( normalmente usado para obs-studio )
	);
	
	protected $sessionID;
	protected $sessionFile;
	protected $myAuth;
    protected $runmode;
	
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
		$this->myConfig=Config::getInstance();
		// nos aseguramos de quere el fichero de sesion exista
		if ( ! file_exists($this->sessionFile) ) touch($this->sessionFile);
        $this->runmode=intval($this->myConfig->getEnv('running_mode'));
	}
	
	/**
	 * Insert a new event into database
     * @param {array} data dataset of key:value pairs to store in "data" field
	 * @return {string} "" if ok; null on error
	 */
	function putEvent(&$data) {
		$this->myLogger->enter();
		$sid=$this->sessionID;
		$onInit=false;
		// si el evento es "init" y el flag reset_events está a 1 borramos el historico de eventos antes de reinsertar
        if ( ( intval($this->myConfig->getEnv("reset_events")) == 1 ) && ( ($data['Type']==='init') )) {
            $rs= $this->__delete("eventos","(Session={$sid})");
            if (!$rs) return $this->error($this->conn->error);
			$onInit=true;
        }
		// comprueba los permisos de los diversos eventos antes de aceptarlos:
		switch($data['Type']) {
			case 'null':			// null event: no action taken
			case 'init':			// operator starts tablet application
			case 'login':			// operador hace login en el sistema
				break;
			case 'info':           // user definded manga: no dogs
			case 'open':			// operator selects tanda on tablet
				$data['NombrePrueba']	= http_request('NombrePrueba',"s","");
				$data['NombreJornada']	= http_request('NombreJornada',"s","");
				$data['NombreManga']	= http_request('NombreManga',"s","");
				$data['NombreRing']		= http_request('NombreRing',"s","");
				// add additional parameters to event data
				break;
			case 'close':			// no more dogs in tanda
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
            case 'crono_ready': // estado activo/escuchando
				if (!$this->myAuth->allowed(ENABLE_CHRONO)) {
					$this->myLogger->info("Ignore chrono events: licencse forbids");
					return array('errorMsg' => 'Current license does not allow chrono handling');
				} // silently ignore
				break;
			// entrada de datos, dato siguiente, cancelar operacion
			case 'llamada':		// operador abre panel de entrada de datos
				// retrieve additional textual data
				$data['Numero']		= http_request('Numero',"i",0);
				$data['Nombre']		= http_request('Nombre',"s","");
                $data['NombreLargo']= http_request('NombreLargo',"s","");
                $data['NombreGuia']	= http_request('NombreGuia',"s","");
                $data['NombreClub']	= http_request('NombreClub',"s","");
                $data['NombreEquipo']=http_request('NombreEquipo',"s","");
                // retrieve federation id from session info
                $res=$this->__select(
                    'Pruebas.RSCE',
                    'Sesiones,Pruebas',
                    "(pruebas.ID = Sesiones.Prueba ) AND  (Sesiones.ID = {$this->sessionID})"
                );
                $fed=($res['total']==0)?0:intval($res['rows'][0]['RSCE']);
                $data['Categoria']	= parseCategory(http_request('Categoria',"s","-"),$fed);
                $data['Grado']		= parseGrade(http_request('Grado',"s","-"));
				break;
			case 'datos':			// actualizar datos (si algun valor es -1 o nulo se debe ignorar)
			case 'aceptar':		// grabar datos finales
			case 'cancelar':		// restaurar datos originales
            case 'user':        // evento definido por el usuario
                break;
			// eventos de cambio de camara para videomarcadores
			// el campo data contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
			case 'camera':		// cambio de fuente de streaming
				if (!$this->myAuth->allowed(ENABLE_VIDEOWALL)) {
					$this->myLogger->info("Ignore camera events: licencse forbids");
					return array('errorMsg' => 'Current license does not allow LiveStream handling');
				} // silently ignore
				break;
			case 'command': // remote control
                // trap switch screen commands to check need for update livestream video info
                if (intval($data['Oper'])===EVTCMD_SWITCH_SCREEN ) {
			        if (!$this->myAuth->allowed(ENABLE_VIDEOWALL) && !$this->myAuth->allowed(ENABLE_LIVESTREAM)) {
                        $this->myLogger->info("Ignore videowall/livestream remote control events: licencse forbids");
                        return array('errorMsg' => 'Current license does not allow VideoWall handling');
                    }
			        $sess=new Sesiones("switch_screen_event");
			        // data: name:sessid:view:mode:playlistidx ... get playlist index
			        $sess->updateVideoInfo($sid,intval( substr(strrchr($data['Value'], ":"), 1 ) ) );
                }
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
        // iniciamos los valores (  Timestamp viene en formato php::time() en segundos
        // el canometro no manda datos de timestamp (==0) por lo que le ponemos la fecha actual
        $timestamp= ($data['TimeStamp']==0)?date('Y-m-d H:i:s'):date('Y-m-d H:i:s',$data['TimeStamp']);
        $source=$data['Source'];
        $type=$data['Type'];
        $evtdata=json_encode($data);

		// prepare statement
		$sql = "INSERT INTO eventos ( TimeStamp,Session, Source, Type, Data ) VALUES (?,$sid,?,?,?)";
        $this->myLogger->trace("Events::insert() Source:$source Type:$type TimeStamp:$timestamp Data:$evtdata");
		$stmt=$this->conn->prepare($sql);
		if (!$stmt) return $this->error($this->conn->error);
		$res=$stmt->bind_param('ssss',$timestamp,$source,$type,$evtdata);
		if (!$res) return $this->error($stmt->error);

		// invocamos la orden SQL y devolvemos el resultado
		$res=$stmt->execute();
		if (!$res) return $this->error($stmt->error);
		
		// retrieve EventID on newly create event
		$data['TimeStamp']=$timestamp;
		$data['ID']=$this->conn->insert_id;
		$stmt->close();
		// if saving event file is requested, handle it
		$flag=$this->myConfig->getEnv("register_events");
		$str=json_encode($data);
		if (boolval($flag)) {
			if ($onInit) file_put_contents($this->sessionFile,$str."\n",LOCK_EX);
			else file_put_contents($this->sessionFile,$str."\n", FILE_APPEND | LOCK_EX);
		} else {
			// as touch() doesn't work if "no_atime" flag is enabled (SSD devices)
			// just overwrite event file with last event
			file_put_contents($this->sessionFile,$str."\n",LOCK_EX);
		}
        // if a remote server to send events is declared, try also to send data
        $revents_url=$this->myConfig->getEnv("remote_events_url");
        if (! is_null($revents_url) &&  trim($revents_url) != "") {
            $this->uploadEvent($revents_url,$str);
        }
		// if raw printer is enabled, send every "accept" events
        $printerName=$this->myConfig->getEnv("event_printer");
        if ( (! is_null($printerName)) &&  (trim($printerName) != "") && ($data['Type']=='aceptar') ) {
			$p=new RawPrinter();
			$p->rawprinter_Print($data);
		}
		// that's all.
		$this->myLogger->leave();
		return ""; 
	}

    /**
     * Send event to server as json data. Ignore response
     * and receive answer
     * @param {string} $host
     * @param {int} $port
     * @throws Exception
     */
    function uploadEvent($url,$data) {
        // first of all, check internet conectivity
        if (isNetworkAlive()<0) {
            throw new Exception("updater::uploadEvent(): No internet access available");
        }
        // do not verify cert on localhost
        $checkcert=true;
        if (strpos($url,"localhost")!=false)  $checkcert=false;
        if (strpos($url,"127.0.0.1")!=false)  $checkcert=false;
        $hdata=array(
            "Operation" => 'putEvent'
        );
        $pdata=array(
            'Data' => $data
        );
        // prepare and execute json request
        $curl = curl_init($url."?".http_build_query($hdata) );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow server redirection
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
        curl_setopt($curl, CURLOPT_POSTREDIR, 1); // do not change from post to get on "301 redirect"
        curl_setopt($curl, CURLOPT_POSTFIELDS, $pdata );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $checkcert); // set to false when using "localhost" url
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 5); // wait 5 secs to attemp connect

        $json_response = @curl_exec($curl); // supress stdout warning
        if ( curl_error($curl) ) {
            $this->myLogger->error("event::uploadEvent() call to URL $url failed: " . curl_error($curl) );
        }
        // close curl stream
        curl_close($curl);
    }

	/**
	 * send 'reconfig' event to every sessions
	 */
	function reconfigure() {
		$data= array("Type"=>"reconfig", "Source"=>"Console", "ID"=>0, "TimeStamp"=>time(),"Value"=>0);
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

        // en sessiones::connect() y en eventos::getEvents()
        // el Canometro de Galican no indica ni Source ni SessionName
        // con lo que hay que componer manualmente los datos que faltan
        if ($data['Source']=="" && $data['SessionName']=="") {
            $data['Name']="CANometro-{$data['Session']}";
            $data['Source']='chrono';
            $data['SessionName']="{$data['Source']}:{$data['Session']}:0:0:{$data['Name']}";
        }
        // refrescamos registro de sesiones abiertas
        if ($data['SessionName']!=="") {
            $ses=new Sesiones("Events::getEvents");
            $ses->testAndSet($data['SessionName']);
        }
		// Close the session prematurely to avoid usleep() from locking other requests
		// notice that cannot call http_request after this item
		session_write_close();
		
		// Automatically die after timeout (plus buffer)
		set_time_limit(EVENT_TIMEOUT_SECONDS+EVENT_TIMEOUT_SECONDS_BUFFER);
		
		// retrieve timestamp from file and request
        // notice that in master server there aren't putEvents() calls and sessionFile wont be updated
        // so need to look for another way of detecting that new events have arrived
        $last=$data['TimeStamp'];
        $current=$last;
        if ($this->runmode !== AC_RUNMODE_MASTER ) $current=filemtime($this->sessionFile);
        $res=null;

		// Counter to manually keep track of time elapsed 
		// (PHP's set_time_limit() is unrealiable while sleeping)
		$counter = 0;
		
		// Poll for messages and hang if nothing is found, until the timeout is exhausted
		while($counter < EVENT_TIMEOUT_SECONDS )	{
            // clear stat cache to ask for real mtime
            clearstatcache();

            // PENDING: this should be reworked to call db only on new data. ¿what about db trigger?
            if ($this->runmode !== AC_RUNMODE_MASTER ) $current =filemtime($this->sessionFile); // get session file mtime
            else $current=$last+1; // dirty trick to force call database every 0.5 seconds.
			// $this->myLogger->info("counter:{$counter} filemtime:{$current} lastquery:{$last}" );

			// compare with received timestamp . Notice that last may be zero ( canometer )
			if ( $current > $last ) {
				// new data has arrived: get it
				$res=$this->listEvents($data);
				if ( is_array($res)  && ($res['total']!=0) ) {
                    // data received: store timestamp in response
				    $res['TimeStamp']=$current;
                    break;
                }
			}
			// no data yet: set last=current to avoid extra db poll
            $last=$current;
			$res=null; // try again
			// sleep for the specified time, after which the loop runs again
            // and decrement wait count from counter (the interval was set in μs, see above)
			usleep(EVENT_POLL_MICROSECONDS);
			$counter += (EVENT_POLL_MICROSECONDS / 1000000);
		}
		// if no new events yet we have timeout, so create an empty result
		if ($res==null) $res=array( 'total'=>0, 'rows'=>array(), 'TimeStamp' => $current );
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
		// $this->myLogger->enter();
		if ($data['Session']<=0) return $this->error("No Session ID specified");
        $ses="";
        if ($data['Session']>1) { // sessionID == 1 means allow events from _any_ source
            $ses="( ( Session = {$data['Session']} ) OR ( Type = 'reconfig' ) ) AND";
        }

        // check for search specific event type
		$extra="";
		if ($data['Type']!=="") $extra=" AND ( Type = '".$data['Type']."' )";

		// perform query
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "eventos",
				/* WHERE */ "$ses ( ID > {$data['ID']} ) $extra",
				/* ORDER BY */ "ID",
				/* LIMIT */ ""
		);
		//$this->myLogger->leave();
		return $result;
	}
	
	/**
	 * Retrieve last "init" event with provided Session ID
	 * Used for clients to retrieve event ID index
	 * SELECT * from Eventos
	 *		WHERE  ( Session = {$data['Session']} ) AND ( Type = 'init' )
	 *		ORDER BY ID DESC LIMIT 1
     * @param {array} $data key:value pairs to extract parameters from
	 * @param {array} $data requested event info
	 * @return {array} data about last "open" event with provided session id
	 */
	function connect($data) {
        // $this->myLogger->enter();

        // in non-standalone nor shared installs do not allow "connect" operations
        if ( ( $this->runmode & AC_RUNMODE_EVTSOURCE) === 0 ) {
            header('HTTP/1.0 403 Forbidden');
            die("You cannot use this server as event source");
        }
        $str="";

        // buscamos los datos de la sesion que pide el connect
        if ($data['Session']<=0) return $this->error("No Session ID specified");
        $sesiones= $this->__select( "*","sesiones","")['rows'];

        $sourceName="(Unknown)";
        $ringName="(Any)";
        $sessionName="";
        // $ipAddress=str_replace(':',';',$_SERVER['REMOTE_ADDR']); // ';' instead ':' for IPv6
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        foreach ($sesiones as $sesion) {
            if ($sesion['ID']!=$data['Session']) continue;
            $ringName=$sesion['Nombre'];
        }

        // en eventos::connect() y en eventos::getEvents()
        // el Canometro de Galican no indica ni Source ni SessionName
        // con lo que hay que componer manualmente los datos que faltan
        if ($data['Source']=="" && $data['SessionName']=="") {
            $data['Name']="CANometro-{$data['Session']}";
            $data['Source']='chrono';
            $data['SessionName']="{$data['Source']}:{$data['Session']}:0:0:{$data['Name']}";
        }
        // if sessionData is provided, extract info on source and session name
        if ($data['SessionName']!=="") {
            // store named sessions into persistent storage
            $ses=new Sesiones("Events::connect");
            $ses->testAndSet($data['SessionName']);
            // and extract info to show in console // source:ringsessid:view:mode:sessionaname
            $sndata=explode(":",$data['SessionName']);
            $sourceName=$sndata[0];
            $sessionName=$sndata[4];
        }

        // ahora buscamos el ultimo evento 'init' de la sesion solicitada
        // o bien si sessionID es 1, de cualquier sesion
        if (intval($data['Session'])>1) $str=" AND ( Session = {$data['Session']} )";
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "eventos",
				/* WHERE */ "( Type = 'init' ) $str",
				/* ORDER BY */ "ID DESC",
				/* LIMIT */ "0,1"
						);
		// $this->myLogger->leave();
        if ($result['total']!=0) { // send connection message to console

            // compose message to be shown on console as timeout:message
            $message="7:Connect From {$sourceName} ({$sessionName})<br/>
                  On {$ringName} (ID {$data['Session']})<br/>
                  IP Address {$ipAddress}";

            // and send to console to be shown
            $this->myLogger->trace($message);
            $data=array (
                // common data for senders and receivers
                'ID'		=>	0, // to be set
                'Session'	=> 	1, // "any"
                'TimeStamp'	=> 	time(), /* date('Y-m-d H:i:s'),*/
                'Type' 		=> 	"command",
                'Source'	=> 	$sourceName,
                'Destination' => 'console',
                'Name'      =>  'Notification', // send event to console
                'SessionName' => "{$sourceName}:1:0:0:Notification",
                // datos identificativos del evento que se envia
                'Oper'      =>  EVTCMD_MESSAGE,
                'Value' =>  $message
            );
            $this->putEvent($data);
        }
		return $result;
	}
}
?>
<?php

// How often to poll, in microseconds (1,000,000 μs equals 1 s)
define('EVENT_POLL_MICROSECONDS', 500000);
// How long to keep the Long Poll open, in seconds
define('EVENT_TIMEOUT_SECONDS', 30);
// Timeout padding in seconds, to avoid a premature timeout in case the last call in the loop is taking a while
define('EVENT_TIMEOUT_SECONDS_BUFFER', 5);

require_once("DBObject.php");

class Eventos extends DBObject {
	
	static $event_list = array (
		0 => 'null',		// null event: no action taken
		1 => 'open',		// operator starts tablet application
		2 => 'falta',		// falta - value:numero de faltas
		3 => 'tocado',		// tocado - value:numero de tocados
		4 => 'rehuse',		// rehuse - value:numero de rehuses
		5 => 'nopresentado',
		6 => 'comienzo',	// operador abre panel de entrada de datos
		7 => 'salida',		// juez da orden de salida ( crono 15 segundos )
		8 => 'cronomanual', // value: timestamp
		9 => 'cronoauto',   // valua: timestamp
		10 => 'fin'			// operador pulsa aceptar o cancelar. 
	);
	
	/**
	 * Insert a new event into database
	 * @return {string} "" if ok; null on error
	 */
	function putEvent($data) {
		$this->myLogger->enter();
		// TODO: write
		$this->myLogger->leave();
		return ""; 
	}
	
	/**
	 * (Server side implementation of LongCall ajax)
	 * Ask for events
	 * If no new events,  for event available
	 */
	function getEvents($data) { 
		$this->myLogger->enter();
		/* FROM: http://www.nolithius.com/game-development/comet-long-polling-with-php-and-jquery */
		
		// Close the session prematurely to avoid usleep() from locking other requests
		// notice that cannot call http_request after this item
		session_write_close();
		// Automatically die after timeout (plus buffer)
		set_time_limit(MESSAGE_TIMEOUT_SECONDS+MESSAGE_TIMEOUT_SECONDS_BUFFER);
		// Counter to manually keep track of time elapsed 
		// (PHP's set_time_limit() is unrealiable while sleeping)
		$counter = MESSAGE_TIMEOUT_SECONDS;
		$res=null;
		// Poll for messages and hang if nothing is found, until the timeout is exhausted
		while($counter > 0)	{
			// Check for new data (not illustrated)
			$res=$this->listEvents($data);
			if ( ! is_array($res) ) break; // error
			if ( $res['total'] !=0 ) break; // data received
			
			// Otherwise, sleep for the specified time, after which the loop runs again
			usleep(MESSAGE_POLL_MICROSECONDS);
			// Decrement seconds from counter (the interval was set in μs, see above)
			$counter -= MESSAGE_POLL_MICROSECONDS / 1000000;
		}
		if ($res===null) $res="Timeout in polling";
		$this->myLogger->leave();
		return $res;
	}
	
	/**
	 * As getEvents() but don't wait for new events, just list existing ones
	 * @param {array} $data requested event info
	 * @return available events for session $data['Session'] with id greater than $data['ID']
	 */
	function listEvents($data) {
		if ($data['Session']<=0) $this->error("No Session ID specified");
		$this->myLogger->enter();
		$result=$this->__select(
				/* SELECT */ "*",
				/* FROM */ "Eventos",
				/* WHERE */ "( Session = {$data['Session']} ) AND ( ID > {$data['ID']} )",
				/* ORDER BY */ "ID",
				/* LIMIT */ ""
		);
		$this->myLogger->leave();
		return $result;
	}
}
?>
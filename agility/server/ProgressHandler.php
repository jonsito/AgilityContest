<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 17/10/18
 * Time: 12:19
 */
require_once(__DIR__."/logging.php");
define("PROGRESS_DIR",__DIR__."/../../logs/");

class ProgressHandler {

    private $myLogger;
    private $progressfile;
    private $closeOnDone;

    private function __construct($name,$suffix) {
        // default closeOnDone is false
        $this->closeOnDone=false;
        $this->myLogger=new Logger("Progress_{$name}-${suffix}");
        // if directory does not exist create
        if (!is_dir(PROGRESS_DIR)) @mkdir(PROGRESS_DIR);
        $this->progressfile=PROGRESS_DIR."/{$name}_{$suffix}.log";
        // if file does not exist create with default contents
        if (!file_exists($this->progressfile)) {
            @file_put_contents($this->progressfile,_("Waiting for progress info...")."\n");
        }
    }

    static function getHandler($name,$sufix) {
        return new ProgressHandler($name,$sufix);
    }

    function setCloseOnDone($flag) { $this->closeOnDone=$flag; }

    function getData() {
        $lines=file($this->progressfile,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return json_encode( array( 'operation'=>'progress','success'=>'fail', 'status' => "Error reading progress: {$this->progressfile}" ) );
        }
        $result=strval($lines[count($lines)-1]);
        if ( ("Done."===$result) && ($this->closeOnDone===true) ) {
            $this->myLogger->trace("Process Completed: closing progress handler");
            $this->closeHandler();
        }
        return json_encode( array( 'operation'=>'progress','success'=>'ok', 'status' => $result ) );
    }

    public function putData($str,$reset=false){
        $f=fopen($this->progressfile,($reset===true)?"w":"a"); // open for append-only
        if (!$f) {
            $this->myLogger->error("fopen() cannot open/create file: $this->progressfile");
            return;
        }
        @fwrite($f,"$str\n");
        @fclose($f);
    }

    function closeHandler() {
        @unlink($this->progressfile);
    }

    function resetHandler() {
        $this->putData(_("Waiting for progress info..."),true); // force start new file
    }
}
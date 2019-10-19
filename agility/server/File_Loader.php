<?php
/**
 * Plugin Name: DBI File Uploader
 * Description: Upload large files using the JavaScript FileReader API
 * Author: Delicious Brains Inc
 * Version: 1.0
 * Author URI: http://deliciousbrains.com
 */

require_once (__DIR__."/auth/Config.php");
require_once (__DIR__."/logging.php");

define('UPLOAD_DIR',__DIR__."/../../logs/uploads");
define('DOWNLOAD_DIR',__DIR__."/../../logs/downloads");

class File_Loader {

    protected $myData;
    protected $myLogger;
    protected $myConfig;
    protected $progressFile;

    /**
     * File_Loader constructor.
     * @param $data
     * @throws Exception when file name is not specified
     */
    public function __construct($data) {
        if (!file_exists(UPLOAD_DIR) && !is_dir(UPLOAD_DIR)) {
            @mkdir(UPLOAD_DIR);
        }        if (!file_exists(DOWNLOAD_DIR) && !is_dir(DOWNLOAD_DIR)) {
            @mkdir(DOWNLOAD_DIR);
        }
        if($data['file']==="") throw new Exception("FileUploader: not filename especified");
        if($data['data']==="") throw new Exception("FileUploader: empty data received");

        $this->myConfig=Config::getInstance();
        $l=$this->myConfig->getEnv("debug_level");
        $this->myLogger= new Logger("File_Loader( {$data['file']} )",$l);
        $this->myData=$data;
        $this->progressFile=DOWNLOAD_DIR."/docsync_{$data['suffix']}.log";
    }

    public function reportProgress($str) {
        $f=fopen($this->progressFile,"a"); // open for append-only
        if (!$f) { $this->myLogger->error("fopen() cannot open file: ".$this->progressFile); return;}
        fwrite($f,"$str\n");
        fclose($f);
    }

    public function abortUpload() {
        // erase file and return ok
        $this->myLogger->info("Request for cancel file upload {$this->myData['file']}");
        @unlink (UPLOAD_DIR . "/{$this->myData['file']}");
        return ""; // mark return ok anyway
    }

    /**
     * Retrieve 'data' block number 'chunk' from client and append to 'file'
     * @param {array} obj 'file','data','chunk'
     * @return {string} "" on success; else error message
     */
    public function fileUpload() {
        $file_path     = UPLOAD_DIR . "/{$this->myData['file']}";
        $file_data     = $this->decode_chunk( $this->myData['data'] );
        if ( false === $file_data )
            return "Error on upload chunk {$this->myData['chunk']} of file '{$this->myData['file']}'";
        // append received data to uploading file. On first chunk, create file instead of append
        $this->myLogger->trace("upload file {$this->myData['file']} chunk {$this->myData['chunk']}");
        @file_put_contents( $file_path, $file_data, ($this->myData['chunk']==0)?0:FILE_APPEND );
        // mark return ok
        return "";
    }

    // data comes in base64encoded format. Extract data from chunk block
    public function decode_chunk( $data ) {
        $data = explode( ';base64,', $data );
        if ( ! is_array( $data ) || ! isset( $data[1] ) ) return false;
        $data = base64_decode( $data[1] );
        if ( ! $data ) return false;
        return $data;
    }

    private function sendLocalFile($file) {
        // when $myData['data'] is false (0) do not send anything, just store
        if ( $this->myData['data']==0 ) {
            if ( strpos($file,"DocumentNotAvailable.pdf")!==FALSE) {
                $this->reportProgress("File: {$this->myData['file']} : Not available");
            } else {
                $this->reportProgress("Get file {$this->myData['file']} : Success");
            }
            return;
        }
        // try to force browser to direct open on new window without download
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.basename($file).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));
        header('Accept-Ranges: bytes');
        header('Expires: 0');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        set_time_limit(0);
        readfile($file);
    }

    /**
     * check for file in cache.
     * if not exists, get it from server
     * and send to browser
     */
    public function fileDownload() {
        // comprobamos si hay conexion a internet
        $have_internet=(isNetworkAlive()>=0)?true:false;
        // componemos nombres y url's
        $local = DOWNLOAD_DIR.DIRECTORY_SEPARATOR.$this->myData['file'];
        $temp  = DOWNLOAD_DIR.DIRECTORY_SEPARATOR."tmp_{$this->myData['file']}";
        $remote= "https://www.agilitycontest.es/downloads/{$this->myData['file']}";
        if ( (!file_exists($local)) && (!$have_internet) ) {
            // si el fichero no existe y no hay internet se manda pagina de error al navegador
            $this->myLogger->trace("fileDownload({$this->myData['file']}): no file, no internet");
            $local=__DIR__."/../console/doc/DocumentNotAvailable.pdf";
            $this->sendLocalFile($local);
            return "";
        }
        if ( file_exists($local) && (!$have_internet) ) {
            // si el fichero existe y no hay internet se manda al navegador
            $this->myLogger->trace("fileDownload({$this->myData['file']}): file exists, no internet");
            $this->sendLocalFile($local);
            return "";
        }
        if ( (!file_exists($local)) && $have_internet) {
            // si el fichero no existe y hay internet se descarga y envia al navegador
            $this->myLogger->trace("fileDownload({$this->myData['file']}): no file, internet available");
            $data=retrieveFileFromURL($remote);
            if ($data===FALSE) $local=__DIR__."/../console/doc/DocumentNotAvailable.pdf";
            else {
                $res=file_put_contents($temp,$data);
                if ($res===FALSE) $local=__DIR__."/../console/doc/DocumentNotAvailable.pdf";
                else @rename($temp,$local);
            }
            $this->sendLocalFile($local);
            return "";
        }
        // si el fichero existe y hay internet se comparan las fechas de las dos versiones
        $this->myLogger->trace("fileDownload({$this->myData['file']}): file exists, internet available");

        $curl = curl_init($remote);
        curl_setopt($curl, CURLOPT_URL, $remote); // redundant, just for yes the flies
        curl_setopt($curl, CURLOPT_NOBODY, true); //don't fetch the actual page, you only want headers
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //stop it from outputting stuff to stdout
        curl_setopt($curl, CURLOPT_FILETIME, true); // ask server to retrieve the modification date
        curl_setopt($curl, CURLOPT_HEADER, true); // ask for complete headers
        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . "/../../config/cacert.pem");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); // tell curl to allow redirects up to 5 jumps
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // try to fix some slowness issues in windozes
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5); // wait 5 seconds for connection
        $result = curl_exec($curl);
        if ($result === false) {
            $this->myLogger->error("fileDownload({$this->myData['file']}): Cannot retrieve remote modification time");
            $this->sendLocalFile($local);
            curl_close($curl);
            return "";
        }
        $ltime = filemtime($local);
        $rtime = curl_getinfo($curl, CURLINFO_FILETIME);
        curl_close($curl);
        $this->myLogger->info("fileDownload({$this->myData['file']}): local:{$ltime} remote:{$rtime}");
        if ($ltime<$rtime) {  // remote file is newer: download
            $this->myLogger->info("fileDownload({$this->myData['file']}): updating local copy");
            $data=retrieveFileFromURL($remote);
            if ($data!==FALSE) {
                $res=file_put_contents($temp,$data);
                if ($res!==FALSE) @rename($temp,$local);
            }
        }
        $this->sendLocalFile($local);
        return "";
    }

    function downloadDocumentation() {
        // check internet conectivity
        if ( isNetworkAlive()<0 ) {
            $this->reportProgress("No internet conection");
            $this->reportProgress("Error");
        }

        // retrieve from www.agilitycontest.es list of files in download directory
        $this->reportProgress("Retrieve list of documentation files");
        $ch=curl_init();
        curl_setopt_array($ch,
            array(
                CURLOPT_URL=>'http://www.agilitycontest.es/downloads/',
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_FOLLOWLOCATION=>true,
                CURLOPT_CAINFO => __DIR__ . "/../../config/cacert.pem",
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_CONNECTTIMEOUT =>5
            )
        );
        $domd=new DOMDocument();
        $domd->loadHTML(curl_exec($ch));
        $document_list=array();
        // compose list of pdf files
        foreach($domd->getElementsByTagName("a") as $file){
            $fname=trim($file->textContent);
            if (! preg_match('/^ac_.*\.pdf$/i', $fname)) continue; // not a pdf documentation file
            $document_list[]=$fname;
        }
        curl_close($ch);

        // iterate over retrieved list and download to local server
        $this->myData['data']=0; // disable send file back to browser
        foreach($document_list as $item) {
            $this->myData['file']=$item;
            $this->reportProgress("Trying to Get: {$item}");
            $this->fileDownload();
        }
        $this->reportProgress("Done.");
        return "";
    }
}

?>

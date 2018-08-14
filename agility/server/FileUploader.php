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

class File_Uploader {

    protected $myData;
    protected $myLogger;
    protected $myConfig;

    /**
     * File_Uploader constructor.
     * @param $data
     * @throws Exception when file name is not specified
     */
    public function __construct($data) {
        if (!file_exists(UPLOAD_DIR) && !is_dir(UPLOAD_DIR)) {
            @mkdir(UPLOAD_DIR);
        }
        if($data['file']==="") throw new Exception("FileUploader: not filename especified");
        if($data['data']==="") throw new Exception("FileUploader: empty data received");

        $this->myConfig=Config::getInstance();
        $l=$this->myConfig->getEnv("debug_level");
        $this->myLogger= new Logger("File_Uploader( {$data['file']} )",$l);
        $this->myData=$data;
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

}

?>

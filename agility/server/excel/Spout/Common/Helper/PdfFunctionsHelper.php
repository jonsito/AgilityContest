<?php

namespace Box\Spout\Common\Helper;

/**
 * Class PdfFunctionsHelper
 * This class wraps global functions to facilitate testing
 * JAMC: use to bypass system functions to allow "reading" text from PDF files
 * @codeCoverageIgnore
 *
 * @package Box\Spout\Common\Helper
 */
class PdfFunctionsHelper extends GlobalFunctionsHelper
{
    private $lines;
    private $command;
    private $index=0;

    function __construct() {
        $lines=array();
        $osName = strtoupper(PHP_OS);
        $command="";
        switch ($osName) {
            case 'WINDOWS':
            case 'WIN32':
            case 'WINNT':
                $command = __DIR__ . "/../../../../pdf/bin/pdftotext.exe";
                break;
            case 'LINUX':
                $command = __DIR__ . "/../../../../pdf/bin/pdftotext.linux";
                break;
            case 'DARWIN':
                $command = __DIR__ . "/../../../../pdf/bin/pdftotext.mac";
                break;
            default: break;
        }
    }

    /**
     * Wrapper around global function fopen()
     * @see fopen()
     *
     * @param string $fileName
     * @param string $mode
     * @return resource|bool
     */
    public function fopen($fileName, $mode)
    {
        $result=0;
        if ($mode!=='r') return false; // cannot handle oder open options
        if (!file_exists($this->command)) return false; // pdftotext command not found
        $cmd="{$this->command} {$fileName} -";
        $res=exec($cmd,$this->lines,$result);
        if ($result!==0) return false;
        return $this->lines;
    }

    /**
     * Wrapper around global function fgets()
     * @see fgets()
     *
     * @param resource $handle
     * @param int|void $length
     * @return string
     */
    public function fgets($handle, $length = null)
    {
        if($this->index>=count($this->lines)) return false;
        $str=substr($this->lines[$this->index],$length);
        $this->index++;
        return $str;
    }

    /**
     * Wrapper around global function fseek()
     * @see fseek()
     *
     * @param resource $handle
     * @param int $offset
     * @return int
     */
    public function fseek($handle, $offset)
    {
        $this->index=$offset;
        if ($this->index>=count($this->lines)) return -1;
        return 0;
    }

    /**
     * Wrapper around global function fclose()
     * @see fclose()
     *
     * @param resource $handle
     * @return bool
     */
    public function fclose($handle)
    {
        $this->lines=array();
        $this->index=0;
        return true;
    }

    /**
     * Wrapper around global function rewind()
     * @see rewind()
     *
     * @param resource $handle
     * @return bool
     */
    public function rewind($handle)
    {
        $this->index=0;
    }

    /**
     * Wrapper around global function feof()
     * @see feof()
     *
     * @param resource
     * @return bool
     */
    public function feof($handle)
    {
        return ($this->index>=count($this->lines));
    }

}

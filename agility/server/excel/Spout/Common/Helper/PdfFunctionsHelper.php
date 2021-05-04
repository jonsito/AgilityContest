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
        $command="";
        switch (strtoupper(PHP_OS)) {
            case 'WINDOWS':
            case 'WIN32':
            case 'WINNT': $command = __DIR__ . "/../../../../pdf/bin/pdftotext.exe"; break;
            case 'LINUX': $command = __DIR__ . "/../../../../pdf/bin/pdftotext.linux"; break;
            case 'DARWIN': $command = __DIR__ . "/../../../../pdf/bin/pdftotext.mac"; break;
            default: break;
        }
        $result = 0;
        $lines = array();
        if ($mode !== 'r') return false; // cannot handle oder open options
        if (!file_exists($command)) return false; // pdftotext command not found
        $cmd = "{$command} -nopgbrk -layout '{$fileName}' -";
        exec($cmd, $lines, $result);
        if ($result !== 0) return false;
        $handler = fopen("php://memory", "w+");
        foreach ($lines as $line) {
            if (strpos($line, "NombreLargo") !== false) fputs($handler, trim($line).PHP_EOL);
        }
        rewind($handler);
        return $handler;
    }
}

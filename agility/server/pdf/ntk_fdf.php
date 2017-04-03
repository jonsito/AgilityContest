<?php

define('ntk_FDFValue', 0);
define('ntk_FDFStatus', 1);
define('ntk_FDFFile', 2);
define('ntk_FDFID',    3);
define('ntk_FDFFf',    5);
define('ntk_FDFSetFf', 6);
define('ntk_FDFClearFf', 7);
define('ntk_FDFFlags', 8);
define('ntk_FDFSetF', 9);
define('ntk_FDFClrF',    10);
define('ntk_FDFAP',    11);
define('ntk_FDFAS',    12);
define('ntk_FDFAction',    13);
define('ntk_FDFAA',    14);
define('ntk_FDFAPRef', 15);
define('ntk_FDFIF',    16);
define('ntk_FDFEnter', 0);
define('ntk_FDFExit',    1);
define('ntk_FDFDown',    2);
define('ntk_FDFUp',    3);
define('ntk_FDFFormat',    4);
define('ntk_FDFValidate',    5);
define('ntk_FDFKeystroke', 6);
define('ntk_FDFCalculate', 7);
define('ntk_FDFNormalAP',    1);
define('ntk_FDFRolloverAP',    2);
define('ntk_FDFDownAP',    3);

/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 3/04/17
 * Time: 14:22
 */
class FDF {
    /*
     * FROM: http://php.inf.duf.hu/manual/es/ref.fdf.php
     * Use these functions instead if you want to create an FDF file without installing the FDF toolkit.
     * You would use it the same way as the fdf_* functions. BTW, I only wrote the basic library functions for creating FDFs.
     */
    protected $header;
    protected $trailer;
    protected $file;
    protected $target;
    protected $values=array();
    protected $docscripts=array();
    protected $fieldscripts=array();

    function __construct() {

    }

    function ntk_fdf_header() {
        header('Content-type: application/vnd.fdf');
    }

    function ntk_fdf_create() {
        $this->header = "%FDF-1.2\n%����\n1 0 obj \n<< /FDF ";
        $this->trailer = ">>\nendobj\ntrailer\n<<\n/Root 1 0 R \n\n>>\n%%EOF";
    }

    function ntk_fdf_close() {
        // unset($fdf);
    }

    function ntk_fdf_set_file($pdfFile) {
        $this->file = $pdfFile;
    }

    function ntk_fdf_set_target_frame($target) {
        $this->target = $target;
    }

    function ntk_fdf_set_value( $fieldName, $fieldValue) {
        $this->values = array_merge($this->values, array($fieldName => $fieldValue));
    }

    function ntk_fdf_add_doc_javascript( $scriptName, $script) {
        $this->docscripts = array_merge($this->docscripts, array($scriptName => $script));
    }

    function ntk_fdf_set_javascript_action( $fieldName, $trigger, $script) {
        $this->fieldscripts = array_merge($this->fieldscripts, array($fieldName => array($script, $trigger)));
    }

    function ntk_fdf_save( $fdfFile = null) {
        $search = array('\\', '(', ')');
        $replace = array('\\\\', '\(', '\)');
        $fdfStr = $this->header;
        $fdfStr.= "<< ";
        if(isset($this->file)) {
            $fdfStr.= "/F (".$this->file.") ";
        }
        if(isset($this->target)) {
            $fdfStr.= "/Target (".$this->target.") ";
        }
        if(isset($this->docscripts)) {
            $fdfStr.= "/JavaScript << /Doc [\n";
            // populate the doc level javascripts
            foreach($this->docscripts as $key => $value) {
                $fdfStr.= "(".str_replace($search, $replace, $key).")(".str_replace($search, $replace, $value).")";
            }
            $fdfStr.= "\n] >>\n";
        }
        if(isset($this->values) || isset($this->fieldscripts)) {
            // field level
            $fdfStr.= "/Fields [\n";
            if(isset($this->fieldscripts)) {
                // populate the field level javascripts
                foreach($this->fieldscripts as $key => $val) {
                    $fdfStr .= "<< /A << /S /JavaScript /JS (".str_replace($search, $replace, $val[0]).") >> /T (".str_replace($search, $replace, $key).") >>\n";
                }
            }
            if(isset($this->values)) {
                // populate the fields
                foreach($this->values as $key => $value) {
                    $fdfStr .= "<< /V (".str_replace($search, $replace, $value).") /T (".str_replace($search, $replace, $key).") >>\n";
                }
            }
            $fdfStr.= "]\n";
        }
        $fdfStr.= ">>";
        $fdfStr.= $this->trailer;
        if($fdfFile) {
            $handle = fopen($fdfFile, 'w');
            fwrite($handle, $fdfStr);
            fclose($handle);
        } else {
            echo $fdfStr;
        }
    }
}
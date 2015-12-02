<?php
/**
 * Created by PhpStorm.
 * User: jantonio
 * Date: 30/11/15
 * Time: 13:42
 */
require_once __DIR__.'/Spout/Autoloader/autoload.php';
require_once __DIR__.'/../auth/Config.php';
require_once __DIR__.'/../auth/AuthManager.php';
require_once __DIR__.'/../../modules/Federations.php';

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;

class XLSX_Writer {
    protected $myConfig;
    protected $myLogger;
    protected $myWriter;
    protected $myFile;
    protected $myTitle;
    protected $prueba=null;
    protected $jornada=null;

    protected $titleStyle;
    protected $rowHeaderStyle;

    function __construct($file,$title) {
        $this->myFile=$file;
        $this->myTitle=$title;
        date_default_timezone_set('Europe/Madrid');
        $this->myConfig=Config::getInstance();
        $this->myLogger= new Logger($file,$this->myConfig->getEnv("debug_level"));
        $this->myWriter=WriterFactory::create(Type::XLSX);
        $this->titleStyle=(new StyleBuilder())
           ->setFontBold()
           ->setFontItalic()
           ->setFontSize(15)
           ->setFontColor(substr($this->myConfig->getEnv('pdf_hdrfg1'),1))
           ->build();
        $this->rowHeaderStyle=(new StyleBuilder())
            ->setFontBold()
            ->setFontItalic()
            ->setFontSize(12)
            ->setFontColor(substr($this->myConfig->getEnv('pdf_hdrfg2'),1))
            ->build();
    }

    function open() {
        $this->myWriter->openToBrowser($this->myFile);
    }

    function createInfoPage($federation=-1) {
        $infopage=$this->myWriter->getCurrentSheet();
        $infopage->setName(_utf("Information"));

        // titulo
        $this->myWriter->addRowsWithStyle([[$this->myTitle],[""]], $this->titleStyle);

        // en caso de estar definido, informacion de Prueba, jornada, y en su caso federacion
        if ($this->prueba != null )
            $this->myWriter->addRowWithStyle([ _utf("Contest").":",$this->prueba->Nombre], $this->rowHeaderStyle);
        if ($this->jornada != null )
            $this->myWriter->addRowWithStyle([ _utf("Journey").":",$this->jornada->Nombre], $this->rowHeaderStyle);
        if ($federation>=0) {
            $fed=Federations::getFederation($federation);
            $this->myWriter->addRowWithStyle([ _utf("Federation").":",$fed->get('Name')], $this->rowHeaderStyle);
        }

        // informacion de la aplicacion
        $this->myWriter->addRows(
            [
                [ "" ],
                [ _utf("Program info") ],
                [ "Application: ",  $this->myConfig->getEnv("program_name") ],
                [ "Version:",      $this->myConfig->getEnv("version_name") ],
                [ "Revision:",     $this->myConfig->getEnv("version_date") ]
            ]
        );

        // informacion de licencia
        $am=new AuthManager($this->myFile);
        $ainfo=$am->getRegistrationInfo();
        $this->myWriter->addRows(
          [
              [ "" ],
              [ _utf("License Info") ],
              [ "Serial Number:",   $ainfo["Serial"] ],
              [ "User:",            $ainfo["User"]   ],
              [ "Club:",            $ainfo["Club"]   ]
          ]
        );
    }

    function close() {
        $this->myWriter->close();
    }
}
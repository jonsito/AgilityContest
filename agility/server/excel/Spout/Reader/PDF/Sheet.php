<?php

namespace Box\Spout\Reader\PDF;

use Box\Spout\Reader\SheetInterface;

/**
 * Class Sheet
 *
 * @package Box\Spout\Reader\PDF
 */
class Sheet implements SheetInterface
{
    /** @var \Box\Spout\Reader\PDF\RowIterator To iterate over the PDF's rows */
    protected $rowIterator;

    /**
     * @param resource $filePointer Pointer to the PDF "file" ( really an string array ) to read
     * @param \Box\Spout\Reader\PDF\ReaderOptions $options
     * @param \Box\Spout\Common\Helper\GlobalFunctionsHelper $globalFunctionsHelper
     */
    public function __construct($filePointer, $options, $globalFunctionsHelper)
    {
        $this->rowIterator = new RowIterator($filePointer, $options, $globalFunctionsHelper);
    }

    /**
     * @api
     * @return \Box\Spout\Reader\PDF\RowIterator
     */
    public function getRowIterator()
    {
        return $this->rowIterator;
    }

    /**
     * @api
     * @return int Index of the sheet
     */
    public function getIndex()
    {
        return 0;
    }

    /**
     * @api
     * @return string Name of the sheet - empty string since PDF does not support that
     */
    public function getName()
    {
        return '';
    }

    /**
     * @api
     * @return bool Always TRUE as there is only one sheet
     */
    public function isActive()
    {
        return true;
    }
}

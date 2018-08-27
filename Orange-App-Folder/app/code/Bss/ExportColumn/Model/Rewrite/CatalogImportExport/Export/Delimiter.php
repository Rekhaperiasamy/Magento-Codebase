<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ExportColumn
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ExportColumn\Model\Rewrite\CatalogImportExport\Export;

class Delimiter extends \Magento\ImportExport\Model\Export\Adapter\Csv
{
	   protected $_delimiter = ';';
     public function setHeaderCols(array $headerColumns)
    {
        if (null !== $this->_headerCols) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The header column names are already set.'));
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
            $this->_fileHandler->writeCsv(array_keys($this->_headerCols), $this->_delimiter, $this->_enclosure);
        }
        return $this;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws \Exception
     * @return $this
     */
    public function writeRow(array $rowData)
    {
        if (null === $this->_headerCols) {
            $this->setHeaderCols(array_keys($rowData));
        }
        $this->_fileHandler->writeCsv(
            array_merge($this->_headerCols, array_intersect_key($rowData, $this->_headerCols)),
            $this->_delimiter,
            $this->_enclosure
        );
        return $this;
    }
}

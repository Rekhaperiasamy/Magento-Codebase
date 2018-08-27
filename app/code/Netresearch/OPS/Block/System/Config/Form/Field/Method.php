<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2016 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Block\System\Config\Form\Field;

/**
 * Method.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

class Method extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Method constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->addColumn('title', [
            'label' => __('Title'),
            'style' => 'width:80px',
            'class' => 'required-entry'
        ]);
        $this->addColumn('pm', [
            'label' => 'PM',
            'style' => 'width:80px',
            'class' => 'required-entry'
        ]);
        $this->addColumn('brand', [
            'label' => 'BRAND',
            'style' => 'width:80px',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Method');
    }
}

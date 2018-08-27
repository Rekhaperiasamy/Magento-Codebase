<?php

namespace Netresearch\OPS\Block\System\Config\Form\Field;

/**
 * Netresearch OPS
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
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2012 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Netresearch
 *
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 */

class Brand extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->addColumn('brand', [
            'label' => __('Brand'),
            'style' => 'width:120px',
        ]);
        $this->addColumn('value', [
            'label' => __('Title'),
            'style' => 'width:120px',
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Brand');
    }
}

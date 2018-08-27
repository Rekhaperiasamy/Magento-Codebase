<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
/**
 * Widget Instance Category Id Options
 */
namespace Dilmah\TeaFaq\Model\ResourceModel\Options;

use Magento\Widget\Model\Widget\Instance as WigetInstance;

class CategoryId implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Declaration
     * @var WigetInstance
     */
    protected $_resourceModel;

    /**
     * Constructor
     * @param \Dilmah\TeaFaq\Model\ResourceModel\Category\Collection $widgetResourceModel
     */
    public function __construct(\Dilmah\TeaFaq\Model\ResourceModel\Category\Collection $widgetResourceModel)
    {
        $this->_resourceModel = $widgetResourceModel;
    }

    /**
     * Option Array
     * @return []
     */
    public function toOptionArray()
    {
        return $this->_resourceModel->toOptionHash();
    }
}

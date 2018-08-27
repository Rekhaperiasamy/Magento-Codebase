<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Dilmah_TeaFaq
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\TeaFaq\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Category
 *
 * @package Dilmah\TeaFaq\Model
 */
class Item extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dilmah\TeaFaq\Model\ResourceModel\Item');
    }
}

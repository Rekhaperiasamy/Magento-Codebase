<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Model\ResourceModel\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * Define resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dilmah\Payments\Model\Item', 'Dilmah\Payments\Model\ResourceModel\Item');
    }
}

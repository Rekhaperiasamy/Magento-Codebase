<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Afeature
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Afeature\Model\ResourceModel\Item;

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
        $this->_init('Dilmah\Afeature\Model\Item', 'Dilmah\Afeature\Model\ResourceModel\Item');
    }
}

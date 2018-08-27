<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Payments
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Payments\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Item.
 */
class Item extends AbstractModel
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dilmah\Payments\Model\ResourceModel\Item');
    }
}

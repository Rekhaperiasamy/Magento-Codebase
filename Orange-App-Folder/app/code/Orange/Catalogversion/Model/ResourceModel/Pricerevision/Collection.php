<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Catalogversion\Model\ResourceModel\Pricerevision;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Catalogversions Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
	    $this->_init('Orange\Catalogversion\Model\Pricerevision', 'Orange\Catalogversion\Model\ResourceModel\Pricerevision');
		
    }
}

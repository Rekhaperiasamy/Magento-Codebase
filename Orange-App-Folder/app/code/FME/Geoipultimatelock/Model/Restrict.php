<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Model;

use Magento\Framework\Model\AbstractModel;

class Restrict extends AbstractModel
{
    /*     * #@+
     * Page's Statuses
     */

    const STATUS_BLOCKED = 1;
    const STATUS_UNBLOCKED = 0;

    protected function _construct()
    {
        $this->_init('FME\Geoipultimatelock\Model\ResourceModel\Restrict');
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return array(self::STATUS_BLOCKED => __('Blocked'), self::STATUS_UNBLOCKED => __('Unblocked'));
    }
}

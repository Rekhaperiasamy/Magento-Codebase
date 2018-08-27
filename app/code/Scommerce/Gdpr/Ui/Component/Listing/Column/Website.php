<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Ui\Component\Listing\Column;

/**
 * Class Website
 * @package Scommerce\Gdpr\Ui\Component\Listing\Column
 */
class Website extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        return $this->systemStore->getWebsiteName($item['website_id']);
    }
}

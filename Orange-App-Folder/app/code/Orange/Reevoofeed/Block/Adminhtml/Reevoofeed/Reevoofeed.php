<?php
/**
 * Copyright © 2015 Orange . All rights reserved.
 */
namespace Orange\Reevoofeed\Block\Adminhtml\Reevoofeed;
class Reevoofeed extends \Magento\Backend\Block\Template
{

	public function getSaveUrl()
    {
        return $this->getUrl('seo/Reevoo/Feed');
    }
}

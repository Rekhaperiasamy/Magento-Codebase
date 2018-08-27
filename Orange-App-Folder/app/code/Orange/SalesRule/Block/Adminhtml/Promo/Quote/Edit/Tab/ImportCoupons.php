<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab;

/**
 * "Import Coupons Codes" Tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ImportCoupons extends \Magento\Framework\View\Element\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Import Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Import Coupon Codes');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCanSHow($canShow)
    {
        $this->_data['config']['canShow'] = $canShow;
    }
	
	public function getImportCouponUrl()
    {
        return $this->getUrl('orange_sales_rule/*/importcoupon');
    }
	
	public function getRuleId()
	{
		return $this->getRequest()->getParam('id');
	}
}

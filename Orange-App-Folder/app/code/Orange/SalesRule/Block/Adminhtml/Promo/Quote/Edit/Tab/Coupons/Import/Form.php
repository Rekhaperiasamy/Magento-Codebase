<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Coupons\Import;

/**
 * Coupons generation parameters form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Sales rule coupon
     *
     * @var \Magento\SalesRule\Helper\Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\SalesRule\Helper\Coupon $salesRuleCoupon,
        array $data = []
    ) {		
        $this->_salesRuleCoupon = $salesRuleCoupon;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare coupon codes generation parameters form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
		/** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create( [
        'data' => [
                   'id' => 'import_edit_form',
                   'action' => '/test',
                   'method' => 'post',
                   'enctype' => 'multipart/form-data'
                  ]
        ]);
		$fieldset = $form->addFieldset('import_information_fieldset', []);
        $fieldset->addClass('ignore-validate');
		$couponHelper = $this->_salesRuleCoupon;
		$model = $this->_coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
        $ruleId = $model->getId();
		$form->setHtmlIdPrefix('importcoupons_');

        $gridBlock = $this->getLayout()->getBlock('promo_quote_edit_tab_coupons_grid');
        $gridBlockJsObject = '';
        if ($gridBlock) {
            $gridBlockJsObject = $gridBlock->getJsObjectName();
        }
		
        $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id', 'value' => $ruleId]);
		$fieldset->addField(
            'coupon_delete',
            'radios',
            [
                'name' => 'coupon_delete',
                'label' => __('Delete Existing Coupons'),
                'title' => __('Delete Existing Coupons'),                
                'values' => [
                                ['value' =>1, 'label' => __('Yes')],
                                ['value' => 0, 'label' => __('No')]
                            ],                
            ]
        );
		$fieldset->addField(
            'select_csv',
            'file',
            [
                'label' => __('Select CSV File'),
				'title' => __('Select CSV File'),
                'name' => 'select_csv'                
            ]
        );
		$idPrefix = $form->getHtmlIdPrefix();
        $importUrl = $this->getImportCouponUrl();
		$fieldset->addField(
            'import_button',
            'note',
            [
                'text' => $this->getButtonHtml(
                    __('Import'),
                    "importCouponCodes('{$idPrefix}' ,'{$importUrl}', '{$gridBlockJsObject}')",
                    'import'
                )
            ]
        );
		$this->setForm($form);
		$this->_eventManager->dispatch(
            'adminhtml_promo_quote_edit_tab_coupons_form_prepare_form',
            ['form' => $form]
        );
		return parent::_prepareForm();
	}
	
	 /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getImportCouponUrl()
    {
        return $this->getUrl('orange_sales_rule/*/importcoupon');
    }
}
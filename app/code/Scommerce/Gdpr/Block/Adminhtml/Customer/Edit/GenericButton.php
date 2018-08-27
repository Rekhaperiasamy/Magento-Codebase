<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Block\Adminhtml\Customer\Edit;

/**
 * Class GenericButton
 * @package Scommerce\Gdpr\Block\Adminhtml\Customer\Edit
 */
abstract class GenericButton extends \Magento\Customer\Block\Adminhtml\Edit\GenericButton
    implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        parent::__construct($context, $registry);
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        if (! $this->helper->isEnabled()) {
            return [];
        }
        $id = $this->getCustomerId();
        if (! $id) {
            return [];
        }
        $action = sprintf('scommerce_gdpr/customer/%s', $this->getAction());
        $url = $this->getUrl($action, compact('id'));
        return [
            'label' => $this->getLabel(),
            'class' => 'delete',
            'data_attribute' => compact('url'),
            'on_click' => sprintf("deleteConfirm('%s', '%s')", __('Are you sure you want to do this?'), $url),
            'sort_order' => 100,
        ];
    }

    /**
     * @return string
     */
    abstract protected function getLabel();

    /**
     * @return string
     */
    abstract protected function getAction();
}

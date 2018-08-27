<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit;

/**
 * Class Tabs
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('csblock_tabs');
        $this->setDestElementId('edit_form');

        /* @var $model \Aheadworks\Csblock\Model\Csblock */
        $model = $this->_coreRegistry->registry('aw_csblock_model');
        if ($model && $model->getId()) {
            $this->setTitle(sprintf("%s \"%s\"", __('Edit Block'), $model->getName()));
        } else {
            $this->setTitle(__('New Block'));
        }
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'general',
            [
                'label' => __('General'),
                'content' => $this->getLayout()
                        ->createBlock('Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\General')
                        ->initForm()
                        ->toHtml(),
                'active'  => true
            ]
        );

        $this->addTab(
            'content',
            [
                'label' => __('Content'),
                'content' =>
                    $this->getLayout()
                        ->createBlock('Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\Content')
                        ->initForm()
                        ->toHtml(),
            ]
        );

        $this->addTab(
            'schedule',
            [
                'label' => __('Schedule'),
                'content' =>
                    $this->getLayout()
                        ->createBlock('Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\Schedule')
                        ->initForm()
                        ->toHtml(),
            ]
        );
        return parent::_beforeToHtml();
    }
}

<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab;

/**
 * Class Content
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab
 */
class Content extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_contentRenderer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\Content\Renderer $contentRenderer,
        array $data = []
    ) {
        $this->_contentRenderer = $contentRenderer;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Aheadworks\Csblock\Model\Csblock */
        $model = $this->_coreRegistry->registry('aw_csblock_model');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('csblock_');

        $fieldset = $form->addFieldset(
            'content_fieldset',
            [
                'legend' => __('Content'),
            ]
        );

        $fieldset->addField(
            'content',
            'text',
            [
                'name' => 'title',
            ]
        )
            ->setBlockModel($model)
            ->setRenderer($this->_contentRenderer);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function initForm()
    {
        $this->_prepareForm();
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Content');
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
}

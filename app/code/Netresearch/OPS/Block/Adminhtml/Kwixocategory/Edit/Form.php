<?php

namespace Netresearch\OPS\Block\Adminhtml\Kwixocategory\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    protected $oPSKwixoCategoryMappingFactory;

    /**
     * @var \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory
     */
    protected $oPSSourceKwixoProductCategoriesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Netresearch\OPS\Model\Kwixo\Category\MappingFactory $oPSKwixoCategoryMappingFactory
     * @param \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory $oPSSourceKwixoProductCategoriesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Netresearch\OPS\Model\Kwixo\Category\MappingFactory $oPSKwixoCategoryMappingFactory,
        \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory $oPSSourceKwixoProductCategoriesFactory,
        array $data = []
    ) {
    
        parent::__construct($context, $registry, $formFactory, $data);
        $this->oPSKwixoCategoryMappingFactory = $oPSKwixoCategoryMappingFactory;
        $this->oPSSourceKwixoProductCategoriesFactory = $oPSSourceKwixoProductCategoriesFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setUseContainer(true);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $categoryId = (int) $this->getRequest()->getParam('id');
        if ($categoryId <= 0) {
            return parent::_prepareForm();
        }
        $kwixoCategoryMapping = $this->oPSKwixoCategoryMappingFactory->create()->loadByCategoryId($categoryId);
        $storeId = (int) $this->getRequest()->getParam('store');

        $fieldset = $form->addFieldset('ops_form', ['legend' => __('Categories configuration')]);

        $fieldset->addField('storeId', 'hidden', [
                                                      'required' => true,
                                                      'name' => 'storeId',
                                                      'value' => $storeId,
                                                 ]);

        $fieldset->addField('id', 'hidden', [
                                                 'required' => false,
                                                 'name' => 'id',
                                                 'value' => $kwixoCategoryMapping->getId(),
                                            ]);
        $fieldset->addField('category_id', 'hidden', [
                                                 'required' => true,
                                                 'name' => 'category_id',
                                                 'value' => $categoryId,
                                            ]);

        $kwixoProductCategories = $this->oPSSourceKwixoProductCategoriesFactory->create()->toOptionArray();

        $fieldset->addField('kwixoCategory_id', 'select', [
                                                          'label' => __('Kwixo category'),
                                                          'class' => 'required-entry',
                                                          'required' => true,
                                                          'name' => 'kwixoCategory_id',
                                                          'value' => $kwixoCategoryMapping->getKwixoCategoryId(),
                                                          'values' => $kwixoProductCategories
                                                     ]);

        $fieldset->addField('applysubcat', 'checkbox', [
                                                            'label' => __('Apply to sub-categories'),
                                                            'name' => 'applysubcat'
                                                       ]);

        $form->setUseContainer($this->getUseContainer());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

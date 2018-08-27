<?php
namespace Orange\Errormessage\Block\Adminhtml\Errormessage\Edit\Tab;
class ErrormessageConfiguration extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('errormessage_errormessage');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $groupOptions = $objectManager->get('\Magento\Customer\Model\Customer\Attribute\Source\Group')->getAllOptions();
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Click and Reserve Error Management')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
		 $fieldset->addField(
                'store_id', 'select', array(
            'name' => 'store_id',
            'label' => __('Language'),
            'title' => __('Store_id'),
            'values' => $this->_systemStore->getStoreValuesForForm(false, true),
            'required' => true,
                )
        );
		$fieldset->addField(
            'code',
            'text',
            array(
                'name' => 'code',
                'label' => __('Error Code'),
                'title' => __('code'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'message',
            'textarea',
            array(
                'name' => 'message',
                'label' => __('Error Message'),
                'title' => __('message'),
                /*'required' => true,*/
            )
        );
    
       /* $fieldset->addField(
                'customer_group', 'select', array(
            'name' => 'customer_group',
            'label' => __('Customer Group'),
            'title' => __('Customer Group'),
            'values' => $groupOptions,
            'required' => true,
                )
        );
     $fieldset->addField(
                'html_content', 'editor', array(
            'name' => 'html_content',
            'label' => __('Promo Description'),
            'title' => __('Content'),
            'style' => 'height:20em',
            'required' => true,
            'config' => $this->_wysiwygConfig->getConfig()
                )
        );*/
		/*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Click and Reserve');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Click and Reserve');
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
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}

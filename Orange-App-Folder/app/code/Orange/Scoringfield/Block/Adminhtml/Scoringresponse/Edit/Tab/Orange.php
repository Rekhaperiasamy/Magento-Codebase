<?php

namespace Orange\Scoringfield\Block\Adminhtml\Scoringresponse\Edit\Tab;
use Magento\Framework\App\ObjectManager;
class Orange extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore,\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_coreRegistry = $registry;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
        /* @var $model \Magento\Cms\Model\Page */
		$objectManager = ObjectManager::getInstance();
		$id = $this->getRequest()->getParam('id');
        $model = $objectManager->create('Orange\Scoringfield\Model\Scoringresponse')->load($id);
        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Content Mapping')));

         if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

        $fieldset->addField(
                'ws_response_content', 'text', array(
            'name' => 'ws_response_content',
            'label' => __('WS Response'),
            'title' => __('Webservice Response'),
            'required' => true
                )
        );
		$fieldset->addField('content_fr', 'editor',
			array(
				'name' => 'content_fr',
				'label' => __('Content For FR'),
				'title' => __('Content For FR'),
				'style' => 'height:20em',
				'required' => true,
				'config' => $this->_wysiwygConfig->getConfig()
            )
        );
        $fieldset->addField('content_nl', 'editor',
			array(
				'name' => 'content_nl',
				'label' => __('Content For NL'),
				'title' => __('Content For NL'),
				'style' => 'height:20em',
				'required' => true,
				'config' => $this->_wysiwygConfig->getConfig()
            )
        );
        /* {{CedAddFormField}} */
		
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __('Scoring Webservice');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __('Orangeq');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed($resourceId);
    }

}

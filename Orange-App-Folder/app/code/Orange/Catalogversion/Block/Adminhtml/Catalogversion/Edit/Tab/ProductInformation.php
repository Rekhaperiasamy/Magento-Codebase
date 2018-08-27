<?php
namespace Orange\Catalogversion\Block\Adminhtml\Catalogversion\Edit\Tab;
class ProductInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
	 
	    $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {  
	    //die('sadsadasds');
		$model = $this->_coreRegistry->registry('catalogversion_catalogversion');
	    $isElementDisabled = false;
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset =$form->addFieldset('base_fieldset', array('legend' => __('')));
        $custom_attribues_unserialize= array();
		$res = $model->getCustomAttribueInfo();
		$ProductID= $model->getProductid();
		$sku= $model->getSku();
		$ProductName=$model->getName();
		$status=$model->getStatus();
		$quanty=$model->getQuantity();
		$price=$model->getPrice();
		$stock=$model->getStock();
		$categories=$model->getCategories();
		$visibility=$model->getVisibility();
		$urlkey=$model->getUrlkey();
		$metatitle=$model->getMetatitle();
		$metakeyword=$model->getMetakeyword();
		$metadescription=$model->getMetadescription();
		$description=$model->getDescription();
		$shortdescription=$model->getShortdescription();
		$custom_attribues_unserialize=unserialize($res);
		if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
		
	       echo '<div>';
			echo '<strong><label>ProductId</label></strong> : ';
			echo '<label>'.$ProductID.'</label><br><br>' ;
			echo '<strong><label>SKU</label></strong> : ';
			echo '<label>'.$sku.'</label><br><br>' ;
			echo '<strong><label>ProductName</label></strong> : ';
			echo '<label>'.$ProductName.'</label><br><br>';
			echo '<strong><label>Status</label></strong> : ';
			echo '<label>'.$status.'</label><br><br>';
			echo '<strong><label>Quantity</label></strong> : ';
			echo '<label>'.$quanty.'</label><br><br>';
			echo '<strong><label>Price</label></strong> : ';
			echo '<label>'.$price.'</label><br><br>';
			echo '<strong><label>stock</label></strong> : ';
			echo '<label>'.$stock.'</label><br><br>';
			echo '<strong><label>categories</label></strong> : ';
			echo '<label>'.$categories.'</label><br><br>';
			echo '<strong><label>visibility</label></strong> : ';
			echo '<label>'.$visibility.'</label><br><br>';
			echo '<strong><label>Related Products</label></strong> : ';
			echo "<br><br>";
 			echo '<strong><label>Description</label></strong> : ';
			echo '<label>'.$description.'</label><br><br>';
			echo '<strong><label>ShortDescription</label></strong> : ';
			echo '<label>'.$shortdescription.'</label><br><br>';
			echo '<strong><label>URLkey</label></strong> : ';
			echo '<label>'.$urlkey.'</label><br><br>';
			echo '<strong><label>MetaTitle</label></strong> : ';
			echo '<label>'.$metatitle.'</label><br><br>';
			echo '<strong><label>MetaKeyword</label></strong> : ';
			echo '<label>'.$metakeyword.'</label><br><br>';
			echo '<strong><label>MetaDescription</label></strong> : ';
			echo '<label>'.$metadescription.'</label><br>';			
			echo '</div>';
		    foreach($custom_attribues_unserialize as $key => $val){
			  echo '<br/>';
			  echo '<div>';
			  echo '<strong><label>'.$key.'</label></strong> : ';
			  echo '<label>'.$val.'</label>';
			 echo '</div>'; 
			} 
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
        return __('Product Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
       // return __('Product Information');
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

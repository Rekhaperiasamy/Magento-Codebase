<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block;

/**
 * Class Csblock
 * @package Aheadworks\Csblock\Block
 */
class Csblock extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file in theme.
     * @var string
     */
    protected $_template = 'Aheadworks_Csblock::block.phtml';

    protected $_blockPosition = null;
    protected $_blockType = null;
    protected $_rules = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    protected $_csblockCollectionFactory;

    protected $_csblockFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Aheadworks\Csblock\Model\ResourceModel\Csblock\CollectionFactory $csblockCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Aheadworks\Csblock\Model\ResourceModel\Csblock\CollectionFactory $csblockCollectionFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Aheadworks\Csblock\Model\CsblockFactory $csblockFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_csblockCollectionFactory = $csblockCollectionFactory;
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_csblockFactory = $csblockFactory;
        $this->_customerSession = $customerSession;
        $this->_formKey = $formKey;
        $this->_dateTime = $dateTime;
    }

    /**
     * @return int|null
     */
    private function __getBlockPosition()
    {
        if ($this->_blockPosition === null) {
          /**  if (false !== strpos($this->getNameInLayout(), 'menu_top')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::MENU_TOP;
            }
            if (false !== strpos($this->getNameInLayout(), 'menu_bottom')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::MENU_BOTTOM;
            } **/
            if (false !== strpos($this->getNameInLayout(), 'content_top')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::CONTENT_TOP;
            }
			if (false !== strpos($this->getNameInLayout(), 'before_tab')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::BEFORE_TAB;
            }
			if (false !== strpos($this->getNameInLayout(), 'product_detail')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::PRODUCT_DETAIL;
            }
          /**  if (false !== strpos($this->getNameInLayout(), 'sidebar_top')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::SIDEBAR_TOP;
            }
            if (false !== strpos($this->getNameInLayout(), 'sidebar_bottom')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::SIDEBAR_BOTTOM;
            } **/
            if (false !== strpos($this->getNameInLayout(), 'page_bottom')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::PAGE_BOTTOM;
            }
			if (false !== strpos($this->getNameInLayout(), 'page_accordion')) {
                $this->_blockPosition = \Aheadworks\Csblock\Model\Source\Position::PAGE_ACCORDION;
            }
        }
        return $this->_blockPosition;
    }

    /**
     * @return int|null
     */
    private function __getBlockType()
    {
		
        if ($this->_blockType === null) {
            if (false !== strpos($this->getNameInLayout(), 'csblock_product')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::PRODUCT_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'csblock_category')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::CATEGORY_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'csblock_category')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::CATEGORY_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'csblock_cart')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::SHOPPINGCART_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'csblock_home')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::HOME_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'csblock_checkout')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::CHECKOUT_PAGE;
            }
			if (false !== strpos($this->getNameInLayout(), 'csblock_intermediate')) {
                $this->_blockType = \Aheadworks\Csblock\Model\Source\PageType::INTERMEDIATE_PAGE;
            }
			
        }
        return $this->_blockType;
    }

    /**
     * @return bool|null
     */
    private function __canShow($csblock)
    {
        $result = true;
        switch ($csblock->getPageType()) {
            case \Aheadworks\Csblock\Model\Source\PageType::PRODUCT_PAGE:
                $result = false;
                $currentProductId = $this->getProductId();
                if (null === $currentProductId) {
                    return $result;
                }
                $csblockModel = $this->_csblockFactory->create();
                $csblockModel->load($csblock->getId());
                $conditions = $csblockModel->getRuleModel()->getConditions();
                if (isset($conditions)) {
                    $match = $csblockModel->getRuleModel()->getMatchingProductIds();
                    if (in_array($currentProductId, $match)) {
                        $result = true;
                    }
                }
                break;
            case \Aheadworks\Csblock\Model\Source\PageType::CATEGORY_PAGE:
                $result = false;
                $currentCategoryId = $this->__getCurrentCategoryId();
                if ($currentCategoryId && in_array($currentCategoryId, explode(',', $csblock->getCategoryIds()))) {
                    $result = true;
                }
                break;
			/* case \Aheadworks\Csblock\Model\Source\PageType::INTERMEDIATE_PAGE:
                $result = false;
                $currentCategoryId = $this->__getCurrentCategoryId();
                if ($currentCategoryId && in_array($currentCategoryId, explode(',', $csblock->getCategoryIds()))) {
                    $result = true;
                }
                break; */
        }
        return $result;
    }

    public function getBlockContentHtml()
    {
        $gmtTimestamp = strtotime($this->_dateTime->gmtDate()) + $this->_dateTime->getGmtOffset();
        $currentDate = date('Y-m-d', $gmtTimestamp);
        $currentTime = date('H,i,s', $gmtTimestamp);

        $csblockCollection = $this->_csblockCollectionFactory->create();
        $csblockCollection
            ->addCustomerGroupFilter($this->_customerSession->getCustomerGroupId())
            ->addPositionFilter($this->__getBlockPosition())
            ->addPageTypeFilter($this->__getBlockType())
            ->addDateFilter($currentDate)
            ->addTimeFilter($currentTime)
            ->addPatternFilter($gmtTimestamp)
            ->addStatusEnabledFilter();

        $html = '';
		
        foreach ($csblockCollection->getItems() as $csblock) {
			 if ($this->__canShow($csblock)) {
				$html .= $this->__getStaticBlockHtml($csblock->getId());
            }
        }

        return $html;
    }

    private function __getStaticBlockHtml($csblockIds)
    {
        $contentCollection = $this->_contentCollectionFactory->create();
        $contentCollection
            ->addBlockIdFilter($csblockIds)
            ->addStoreFilter($this->_storeManager->getStore()->getId());
        ;
        $html = '';
        foreach ($contentCollection->getItems() as $content) {
            $html .= $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($content->getStaticBlockId())->toHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }

    /**
     * @return mixed
     */
    private function getProductId()
    {
        return $this->_request->getParam('id', null);
    }

    /**
     * @return mixed
     */
    private function __getCurrentCategoryId()
    {
        return $this->_request->getParam('id', null);
    }

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }
}
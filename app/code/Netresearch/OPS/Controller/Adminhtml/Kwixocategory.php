<?php

namespace Netresearch\OPS\Controller\Adminhtml;

/**
 * @package   OPS
 * @copyright 2013 Netresearch
 * @author    Michael Lühr <michael.luehr@netresearch.de>
 * @license   OSL 3.0
 */
abstract class Kwixocategory extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $catalogCategoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Netresearch\OPS\Helper\Kwixo
     */
    protected $oPSKwixoHelper;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    protected $oPSKwixoCategoryMappingFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Netresearch\OPS\Helper\Kwixo $oPSKwixoHelper,
        \Netresearch\OPS\Model\Kwixo\Category\MappingFactory $oPSKwixoCategoryMappingFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->catalogCategoryFactory = $catalogCategoryFactory;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->backendAuthSession = $backendAuthSession;
        $this->jsonEncoder = $jsonEncoder;
        $this->oPSKwixoHelper = $oPSKwixoHelper;
        $this->oPSKwixoCategoryMappingFactory = $oPSKwixoCategoryMappingFactory;
        $this->pageFactory = $pageFactory;
    }

    protected function _initCategory($getRootInstead = false)
    {
        $categoryId = (int)$this->getRequest()->getParam('id', false);
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $category = $this->catalogCategoryFactory->create();
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = $this->storeManager->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    } else {
                        return false;
                    }
                }
            }
        }

        $this->registry->register('category', $category);
        $this->registry->register('current_category', $category);

        return $category;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog:categories');
    }
}

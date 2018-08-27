<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml;

/**
 * Simple google shopping backend controller
 */
abstract class Feeds extends \Magento\Backend\App\Action
{

    public $coreRegistry = null;
    public $coreHelper = null;
    public $sgsHelper = null;
    public $resultForwardFactory = null;
    public $resultRedirectFactory = null;
    public $resultRawFactory = null;
    public $directoryRead = null;
    public $sgsModel = null;
    public $attributeRepository = null;
    public $parserHelper = null;
    public $resultPageFactory = null;
    public $cacheManager = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Model\Context $contextModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Wyomind\Core\Helper\Data $coreHelper
     * @param \Wyomind\SimpleGoogleShopping\Helper\Data $sgsHelper
     * @param \Wyomind\SimpleGoogleShopping\Model\Feeds $sgsModel
     * @param \Magento\Framework\Model\Context $context_
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead
     * @param \Wyomind\SimpleGoogleShopping\Helper\Parser $parserHelper
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Model\Context $contextModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Wyomind\Core\Helper\Data $coreHelper,
        \Wyomind\SimpleGoogleShopping\Helper\Data $sgsHelper,
        \Wyomind\SimpleGoogleShopping\Model\Feeds $sgsModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $directoryRead,
        \Wyomind\SimpleGoogleShopping\Helper\Parser $parserHelper,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreHelper = $coreHelper;
        $this->cacheManager = $contextModel->getCacheManager();
        $this->sgsHelper = $sgsHelper;
        $this->sgsModel = $sgsModel;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultRawFactory = $resultRawFactory;
        $this->parserHelper = $parserHelper;
        $this->attributeRepository = $attributeRepository;
        $root = $directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
        if (file_exists($root."/app/code/Wyomind/SimpleGoogleShopping")) {
            $this->directoryRead = $directoryRead->create($root."/app/code/Wyomind/SimpleGoogleShopping");
        } elseif (file_exists($root."/vendor/wyomind/simplegoogleshopping")) {
            $this->directoryRead = $directoryRead->create($root."/vendor/wyomind/simplegoogleshopping");
        }
        parent::__construct($context);
    }

    /**
     * Does the menu is allowed
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_SimpleGoogleShopping::main');
    }

    /**
     * execute action
     */
    abstract public function execute();
}

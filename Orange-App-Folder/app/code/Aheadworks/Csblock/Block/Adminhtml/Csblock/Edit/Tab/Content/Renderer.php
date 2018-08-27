<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\Content;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Class Renderer
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab\Content
 */
class Renderer extends \Magento\Backend\Block\Template implements RendererInterface
{
    /**
     * @var null|\Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $stores = null;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected $element;

    protected $_contentCollectionFactory;
    protected $_csblockModel = null;
    protected $_contentCollection = [];

    protected $_staticBlockCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Csblock::csblock/edit/tab/content/renderer.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $staticBlockCollectionFactory,
        \Aheadworks\Csblock\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        array $data = []
    ) {
        $this->_contentCollectionFactory = $contentCollectionFactory;
        $this->_staticBlockCollectionFactory = $staticBlockCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Render form element as HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        $this->_csblockModel = $element->getBlockModel();
        return $this->toHtml();
    }

    /**
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    public function getStores()
    {
        if ($this->stores === null) {
            $this->stores = $this->_storeManager->getStores(true);
            ksort($this->stores);
        }
        return $this->stores;
    }

    public function getContentCollection()
    {
        if (!$this->_contentCollection && $this->_csblockModel && $this->_csblockModel->getId()) {
            $collection = $this->_contentCollectionFactory->create();
            $collection->addBlockIdFilter($this->_csblockModel->getId());
            $this->_contentCollection = $collection;
        }
        return $this->_contentCollection;
    }

    public function getStoreOptionsHtml($selectedStoreId = null)
    {
        $storeCollection = $this->getStores();
        $html = '';
        foreach ($storeCollection as $store) {
            $selected = '';
            if ($selectedStoreId === $store->getId()) {
                $selected = 'selected';
            }
            $storeName = $store->getName();
            if ($store->getId() === '0') {
                $storeName = __('All Store Views');
            }
            $html .= "<option value='{$store->getId()}' {$selected}>{$storeName}</option>";
        }

        return $html;
    }

    public function getStaticBlockOptionsHtml($selectedStaticBlockId = null)
    {
        $html = '';
        $collection =  $this->_staticBlockCollectionFactory->create();
        foreach ($collection->getData() as $block) {
            $selected = '';
            if ($selectedStaticBlockId === $block['block_id']) {
                $selected = 'selected';
            }
            $id = $block['block_id'];
            $title = $block['title'];
            $html .= "<option value='{$id}' {$selected}>{$title}</option>";
        }

        return $html;
    }

    public function getLastId()
    {
        $collection = $this->getContentCollection();
        $result = 0;
        if (!empty($collection)) {
            $result = $collection->getMaxId();
        }
        return $result;
    }

    public function getEditStaticBlockUrl()
    {
        return $this->getUrl('cms/block/edit');
    }
}

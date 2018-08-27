<?php
/**
 * Netresearch OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Block\Adminhtml\Kwixocategory;

class CategoryTree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * @var \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory
     */
    protected $oPSSourceKwixoProductCategoriesFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory $oPSSourceKwixoProductCategoriesFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $categoryTree,
            $registry,
            $categoryFactory,
            $jsonEncoder,
            $resourceHelper,
            $backendSession,
            $data
        );
        $this->oPSSourceKwixoProductCategoriesFactory = $oPSSourceKwixoProductCategoriesFactory;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/categoriestree.phtml');
        $this->setUseAjax(true);
        $this->_withProductCount = false;
    }

    public function getSwitchTreeUrl()
    {
        return $this->getUrl(
            "*/*/tree",
            [
                '_current' => true,
                'store' => null,
                '_query' => false,
                'id' => null,
                'parent' => null
            ]
        );
    }

    public function getNodesUrl()
    {
        return $this->getUrl('adminhtml/catalog_category/jsonTree');
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', ['_current' => true, '_query' => false, 'id' => null, 'parent' => null]);
    }

    protected function _getNodeJson($node, $level = 0)
    {
        $item = [];
        $item['text'] = $this->buildNodeName($node);

        $item['id'] = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['store'] = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;
        if ((int) $node->getChildrenCount() > 0) {
            $item['children'] = [];
        }
        $isParent = $this->_isParentSelectedCategory($node);
        if ($node->hasChildren()) {
            $item['children'] = [];
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }
        return $item;
    }

    protected function _getProductTypeLabel($productTypeId)
    {
        $res = '';
        $types = $this->oPSSourceKwixoProductCategoriesFactory->create()->toOptionArray();
        foreach ($types as $data) {
            if ($data['value'] == $productTypeId) {
                $res = $data['label'];
                break;
            }
        }
        return $res;
    }

    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        return $result;
    }
}

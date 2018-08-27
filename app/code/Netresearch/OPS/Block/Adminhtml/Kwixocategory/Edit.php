<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Adminhtml\Kwixocategory;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $catalogCategoryFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->catalogCategoryFactory = $catalogCategoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_kwixocategory';
        $this->_blockGroup = 'Netresearch_OPS';
        $this->_mode = 'edit';
        $this->buttonList->update('delete', 'label', __('Delete'));
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('save', 'url', $this->getUrl('*/*/save'));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($categoryId <= 0) {
            return __('Categories configuration');
        }
        $category = $this->catalogCategoryFactory->create()->load($categoryId);
        return __('Categorie\'s %1 configuration', $category->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}

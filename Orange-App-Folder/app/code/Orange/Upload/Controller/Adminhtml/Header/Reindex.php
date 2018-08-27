<?php
namespace Orange\Upload\Controller\Adminhtml\Header;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Reindex extends \Magento\Framework\App\Action\Action {

    public function execute() {
        exec('php bin/magento indexer:reindex');
        $this->execundex();
        $this->messageManager->addSuccess('Reindex Done successfully.');
        $this->_redirect('*/*/subsidyprice');
    }

    public function execundex() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $indexerFactory = $objectManager->get('Magento\Indexer\Model\IndexerFactory');
        $indexerIds = array('catalogrule_rule', 'catalog_category_product', 'catalog_product_category', 'catalog_product_price', 'catalog_product_attribute',
            'cataloginventory_stock', 'catalogrule_product', 'catalogsearch_fulltext', 'design_config_grid', 'customer_grid', 'salesrule_rule',
            'targetrule_product_rule', 'targetrule_rule_product');
        foreach ($indexerIds as $indexerId) {
            $indexer = $indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    }

}
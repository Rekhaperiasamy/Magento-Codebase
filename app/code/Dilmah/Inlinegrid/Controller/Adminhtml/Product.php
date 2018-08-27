<?php
namespace Dilmah\Inlinegrid\Controller\Adminhtml;
use Magento\Backend\App\Action;
abstract class Product extends \Magento\Backend\App\Action {
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
}

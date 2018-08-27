<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Anonymize orders
 *
 * Class Anonimize
 * @package Scommerce\Gdpr\Controller\Adminhtml\Order
 */
class Anonymize extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    const ADMIN_RESOURCE = 'Scommerce_Gdpr::config';

    /** @var \Scommerce\Gdpr\Model\Service\Anonymize\Sale */
    private $sale;

    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Scommerce\Gdpr\Model\Service\Anonymize\Sale $sale
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Scommerce\Gdpr\Model\Service\Anonymize\Sale $sale,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->sale = $sale;
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function massAction(AbstractCollection $collection)
    {
        if (! $this->helper->isEnabled()) {
            return $this->_redirect('admin/dashboard/index');
        }
        foreach ($collection->getItems() as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $this->sale->order($order);
        }
        $this->messageManager->addSuccessMessage(__('All selected orders have been successfully anonymised.'));
        return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
    }
}

<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Group extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $_customerGroupFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_customerGroupFactory = $groupFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['customer_groups'] = $this->_prepareContent($item['customer_groups']);
        }

        return $dataSource;
    }

    protected function _prepareContent($customerGroup)
    {
        $content = [];
        $customerGroup = explode(',', $customerGroup);
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }
        foreach ($customerGroup as $groupId) {
            $groupModel = $this->_customerGroupFactory->create();
            $groupModel->load($groupId);
            if (null === $groupModel->getId()) {
                continue;
            }
            $content[] = $groupModel->getCode();
        }

        return implode(', ', $content);
    }
}
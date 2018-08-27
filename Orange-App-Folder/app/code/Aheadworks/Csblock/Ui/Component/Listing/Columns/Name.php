<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Name extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $_filterManager;

    public function __construct(
        \Magento\Framework\Filter\FilterManager $filterManager,
        ContextInterface $context, UiComponentFactory $uiComponentFactory,
        array $components = [], array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_filterManager = $filterManager;
    }

    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['name'] = $this->_getLink($item['id'], $item['name']);
        }

        return $dataSource;
    }

    protected function _getLink($entityId, $name)
    {
        $url = $this->context->getUrl('csblock_admin/csblock/edit', ['id' => $entityId]);
        return '<a href="' . $url . '" target="_blank" onclick="setLocation(this.href)">' . $name . '</a>';
    }
}
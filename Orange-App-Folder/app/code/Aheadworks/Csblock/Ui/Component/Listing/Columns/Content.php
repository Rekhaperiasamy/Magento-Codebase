<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Content extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $_staticBlockFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Cms\Model\BlockFactory $staticBlockFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_staticBlockFactory = $staticBlockFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as & $item) {
            $item['static_block_id'] = $this->_prepareContent($item['static_block_id']);
        }

        return $dataSource;
    }

    protected function _prepareContent($staticBlocks)
    {
        $content = [];
        $staticBlocks = explode(',', $staticBlocks);
        if (!is_array($staticBlocks)) {
            $staticBlocks = [$staticBlocks];
        }
        foreach ($staticBlocks as $staticBlockId) {
            $staticBlockModel = $this->_staticBlockFactory->create();
            $staticBlockModel->load($staticBlockId);
            if (null === $staticBlockModel->getId()) {
                continue;
            }
            $content[] = $staticBlockModel->getTitle();
        }

        return implode(', ', $content);
    }
}
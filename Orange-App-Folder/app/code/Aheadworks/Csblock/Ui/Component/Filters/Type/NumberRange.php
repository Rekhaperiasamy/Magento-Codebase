<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Ui\Component\Filters\Type;

class NumberRange extends \Magento\Ui\Component\Filters\Type\Range
{
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (isset($value['from']) && 0 < $value['from']) {
                parent::applyFilter();
                return;
            }
            if (isset($value['to']) && 0 > $value['to']) {
                parent::applyFilter();
                return;
            }
            if (isset($value['from'])) {
                $this->applyMyFilterByType('gteq', $value['from']);
            }

            if (isset($value['to'])) {
                $this->applyMyFilterByType('lteq', $value['to']);
            }
        }
    }

    protected function applyMyFilterByType($type, $value)
    {

        if (strlen($value) > 0) {
            $filter = $this->filterBuilder->setConditionType('use_undocumented_feature')
                ->setField('main_table.' . $this->getName())
                ->setValue([
                    [$type => $value],
                    ['null' => $value]
                ])
                ->create()
            ;
            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }

    protected function applyFilterByType($type, $value)
    {
        if (!empty($value) && $value !== '0') {
            $filter = $this->filterBuilder->setConditionType($type)
                ->setField('main_table.' .$this->getName())
                ->setValue($value)
                ->create();

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }
}
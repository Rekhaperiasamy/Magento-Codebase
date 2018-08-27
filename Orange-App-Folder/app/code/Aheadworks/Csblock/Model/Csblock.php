<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model;

/**
 * Class Csblock
 * @package Aheadworks\Csblock\Model
 */
class Csblock extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Magento\Rule\Model\AbstractModel
     */
    private $_rule = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Aheadworks\Csblock\Model\ResourceModel\Csblock $resource
     * @param \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Aheadworks\Csblock\Model\ResourceModel\Csblock $resource = null,
        \Aheadworks\Csblock\Model\ResourceModel\Csblock\Collection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    protected function _construct()
    {
        $this->_init('Aheadworks\Csblock\Model\ResourceModel\Csblock');
    }


    /*
     * @return \Aheadworks\Csblock\Model\Rule\Product
     */
    public function getRuleModel()
    {
        if (null === $this->_rule) {
           $ruleModel = \Magento\Framework\App\ObjectManager::getInstance()
               ->create('Aheadworks\Csblock\Model\Rule\Product');
            $this->_rule = $ruleModel;
        }
        return $this->_rule;
    }

    /*
     * Set up prepared conditions to model
     *
     * @param array $data
     * @param array $allowedKeys
     */
    public function loadPost($data, $allowedKeys)
    {
        if (empty($data)) {
            $this->setData('csblock_conditions', '');
            return $this;
        }
        $conditions = $this->_convertFlatToRecursive($data, $allowedKeys);
        $this->setData('csblock_conditions', $conditions['csblock'][1]);

        return $this;
    }


    /*
     * @param array $data
     * @param array $allowedKeys
     * @return array
     */
    protected function _convertFlatToRecursive(array $data, $allowedKeys = [])
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $arr;

                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = & $node[$key][$path[$i]];

                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                /**
                 * Convert dates into \DateTime
                 */
                if (in_array($key, ['from_date', 'to_date']) && $value) {
                    $value = new \DateTime($value);
                }
                $this->setData($key, $value);
            }
        }
        return $arr;
    }
}
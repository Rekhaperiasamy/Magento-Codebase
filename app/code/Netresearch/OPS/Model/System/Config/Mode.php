<?php
/**
 * Mode.php
 * @author  paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Netresearch\OPS\Model\System\Config;

class Mode extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $configValueFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configValueFactory = $configValueFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if ($this->getValue() != \Netresearch\OPS\Model\Source\Mode::CUSTOM && $this->isValueChanged()) {
            foreach ($this->getUrlPaths() as $path) {
                $default = $this->configValueFactory->create()->load('default/' . $path, 'path')->getValue();
                $newValue = preg_replace('/\/ncol\/\w+/', '/ncol/'.$this->getValue(), $default);
                $this->configValueFactory->create()->load($path, 'path')
                    ->setValue($newValue)
                    ->setPath($path)
                    ->setScope($this->getScope())
                    ->setScopeId($this->getScopeId())
                    ->save();
            }
        }

        return parent::afterSave();
    }

    protected function getUrlPaths()
    {
        return [
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'ops_gateway',
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'ops_alias_gateway',
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'frontend_gateway',
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'directlink_gateway',
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'directlink_gateway_order',
            \Netresearch\OPS\Model\Config::OPS_PAYMENT_PATH.'directlink_maintenance_api'
        ];
    }
}

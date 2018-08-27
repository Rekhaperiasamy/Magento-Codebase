<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Helper;

/**
 * Class Serializer
 * @package Scommerce\Gdpr\Helper
 */
class Serializer
{
    /* @var \Magento\Framework\ObjectManagerInterface */
    protected $objectManager;

    /* @var \Magento\Framework\App\ProductMetadata */
    protected $productMetadata;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ProductMetadata $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Serialize data depending Magento version
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->is22Version() ? $this->getSerializer()->serialize($data) : serialize($data);
    }

    /**
     * Unserialize data depending Magento version
     *
     * @param string $data
     * @return mixed
     */
    public function unserialize($data)
    {
        return $this->is22Version() ? $this->getSerializer()->unserialize($data) : unserialize($data);
    }

    /**
     * Return true if Magento version >= 2.2.0
     *
     * @return bool
     */
    protected function is22Version()
    {
        return $this->productMetadata->getVersion() >= '2.2.0';
    }

    /**
     * Returns serializer instance for Magento version 2.2
     *
     * @return \Magento\Framework\Serialize\Serializer\Json
     * @since 2.2.0
     */
    protected function getSerializer()
    {
        return $this->objectManager->get('Magento\Framework\Serialize\Serializer\Json');
    }

}

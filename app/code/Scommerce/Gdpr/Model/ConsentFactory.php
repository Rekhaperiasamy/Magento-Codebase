<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Model;

/**
 * Class ConsentFactory
 * @package Scommerce\Gdpr\Model
 */
class ConsentFactory
{
    /* @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /* @var string Instance name to create */
    private $instanceName;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Scommerce\Gdpr\Api\Data\ConsentInterface'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * @param array $data
     * @return \Scommerce\Gdpr\Api\Data\ConsentInterface|\Scommerce\Gdpr\Model\Data\Consent
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}

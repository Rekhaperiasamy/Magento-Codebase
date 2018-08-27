<?php
/**
 * Netresearch_OPS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @copyright Copyright (c) 2017 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

/**
 * Config.php
 *
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Block\Form;

use Magento\Framework\View\Element\Template;
use Netresearch\OPS\Model\OpsCcConfigProvider;
use Netresearch\OPS\Model\OpsConfigProvider;

class Config extends Template
{
    /**
     * @var OpsCcConfigProvider
     */
    private $ccConfigProvider;

    /**
     * @var OpsConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        OpsCcConfigProvider $ccConfigProvider,
        OpsConfigProvider $opsConfigProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ccConfigProvider = $ccConfigProvider;
        $this->configProvider = $opsConfigProvider;
    }


    public function getPaymentConfig()
    {
        return json_encode(
            array_merge($this->configProvider->getConfig()['payment'], $this->ccConfigProvider->getConfig()['payment'])
        );
    }
}

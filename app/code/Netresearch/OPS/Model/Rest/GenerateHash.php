<?php
/**
 * Axalta_
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
 * GenerateHash.php
 *
 * @package   Netresearch_OPS
 * @author    Paul Siedler <paul.siedler@netresearch.de>
 */

namespace Netresearch\OPS\Model\Rest;

use Netresearch\OPS\Api\HashInterface;
use Netresearch\OPS\Helper\Payment;
use Netresearch\OPS\Model\Config;

class GenerateHash implements HashInterface
{
    /** @var  Config */
    private $config;

    /** @var  Payment */
    private $paymentHelper;

    private $keysToFilter = ['form_key', 'isAjax', 'key', 'formKey'];

    public function __construct(
        Config $config,
        Payment $paymentHelper
    ) {
        $this->config = $config;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @inheritdoc
     */
    public function getShaSign(...$args)
    {
        $storeId = array_key_exists('storeId', $args) ? $args['storeId'] : null;
        $params = $this->cleanParamKeys($args);
        $secret = $this->config->getShaOutCode($storeId);
        $shaSign = $this->paymentHelper->getSHASign($this->cleanParamKeys($args), $secret, $storeId);

        return ['hash' => $shaSign];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function cleanParamKeys($params)
    {
        $data = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->keysToFilter)) {
                continue;
            }
            $data[str_replace('_', '.', $key)] = $value;
        }

        return $data;
    }
}

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
 * @copyright Copyright (c) 2016 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

namespace Netresearch\OPS\Block\Info;

/**
 * Flex.php
 *
 * @category Payment
 * @package  Netresearch_OPS
 * @author   Paul Siedler <paul.siedler@netresearch.de>
 */

class Flex extends \Netresearch\OPS\Block\Info\Redirect
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/info/flex.phtml');
    }

    /**
     * @return string
     */
    public function getFlexTitle()
    {
        return $this->getMethod()->getInfoInstance()->getAdditionalInformation(
            \Netresearch\OPS\Model\Payment\Flex::INFO_KEY_TITLE
        );
    }
}

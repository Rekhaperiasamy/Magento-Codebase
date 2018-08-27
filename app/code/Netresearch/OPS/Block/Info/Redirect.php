<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Netresearch\OPS\Block\Info;

/**
 * OPS payment information block
 */
class Redirect extends \Magento\Payment\Block\Info
{
    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * Cc constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netresearch\OPS\Helper\Data $oPSHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->oPSHelper = $oPSHelper;
    }

    /**
     * Init ops payment information block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Netresearch_OPS::ops/info/redirect.phtml');
    }

    /**
     * @return \Netresearch\OPS\Helper\Data
     */
    public function getOPSHelper()
    {
        return $this->oPSHelper;
    }
}

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

namespace Netresearch\OPS\Model\Source\Cc;

/**
 * OPS credit card types
 */
class AliasInterfaceEnabledTypes
{
    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    public function __construct(\Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory)
    {
        $this->oPSConfigFactory = $oPSConfigFactory;
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $types = array_intersect(
            $this->oPSConfigFactory->create()->getAllCcTypes(),
            $this->getAliasInterfaceCompatibleTypes()
        );
        foreach ($types as $type) {
            $options[] = [
                'value' => $type,
                'label' => __($type)
            ];
        }
        return $options;
    }

    public function getAliasInterfaceCompatibleTypes()
    {
        return [
            'American Express',
            'Diners Club',
            'MaestroUK',
            'MasterCard',
            'VISA',
        ];
    }
}

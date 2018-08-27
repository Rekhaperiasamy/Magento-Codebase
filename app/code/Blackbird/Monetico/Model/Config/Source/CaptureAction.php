<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Model\Config\Source;

use Blackbird\Monetico\Model\Method\Onetime;

/**
 * @todo next release (deferral payment)
 */
class CaptureAction implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Onetime::CAPTURE_ON_INVOICE,
                'label' => __('Invoice'),
            ],
            [
                'value' => Onetime::CAPTURE_ON_SHIPMENT,
                'label' => __('Shipment'),
            ],
        ];
    }
}

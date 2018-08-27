<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Acart\Ui\Component\Listing\Column\History\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\Acart\Model\History as History;
/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    public function toArray()
    {
        return [

        ];
    }

    public function toOptionArray()
    {
        return array(
            array(
                'value' => History::STATUS_PROCESSING,
                'label' => __("Not sent")
            ),
            array(
                'value' => History::STATUS_SENT,
                'label' => __("Sent")
            ),
            array(
                'value' => History::STATUS_CANCEL_EVENT,
                'label' => __("Cancel Condition")
            ),
            array(
                'value' => History::STATUS_BLACKLIST,
                'label' => __("Blacklist")
            ),
            array(
                'value' => History::STATUS_ADMIN,
                'label' => __("Canceled by the admin")
            ),
            array(
                'value' => History::STATUS_NOT_NEWSLETTER_SUBSCRIBER,
                'label' => __("Customer is Not Newsletter Subscriber")
            ),
        );
    }
}

<?php

/**
 * Class Rule
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */

namespace FME\Geoipultimatelock\Controller\Adminhtml;

abstract class Rule extends \Magento\Backend\App\Action
{

    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
    }
}

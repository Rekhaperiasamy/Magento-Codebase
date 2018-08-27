<?php
/**
 * Copyright © 2015 Orange . All rights reserved.
 * 
 */
namespace Orange\Webform\Block\Index;

/**
 * Abstract product block context
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mnp extends \Magento\Framework\View\Element\Template
{
	public function getsuccessTxt()
    {
        return __('Your form submited successfully');
    }
}

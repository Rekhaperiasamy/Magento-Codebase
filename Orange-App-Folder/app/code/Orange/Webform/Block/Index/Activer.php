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
class Activer extends \Magento\Framework\View\Element\Template
{
	public function getsuccessTxt()
    {
        return __('Your form submited successfully');
    }
}

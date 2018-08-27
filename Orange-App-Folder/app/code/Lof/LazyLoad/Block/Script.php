<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_LazyLoad
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\LazyLoad\Block;

class Script extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Lof\LazyLoad\Helper\Data
	 */
	protected $_helper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context 
	 * @param \Lof\LazyLoad\Helper\Data                        $helper  
	 * @param array                                            $data    
	 */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Lof\LazyLoad\Helper\Data $helper,
    	array $data = []
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
    }

    public function _toHtml() {
    	if (!$this->_helper->getConfig('general/enable')) {
    		return;
    	}
        if (!$this->_helper->isEnable()) {
            return;
        }
        return parent::_toHtml();
    }

}
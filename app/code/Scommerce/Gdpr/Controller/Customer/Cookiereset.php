<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Controller\Customer;

/**
 * Cookie Reset action from frontend
 *
 * Class Cookie Reset
 * @package Scommerce\Gdpr\Controller\Customer
 */
class Cookiereset extends \Magento\Framework\App\Action\Action
{
	/**
	* @var \Magento\Framework\Stdlib\CookieManagerInterface
	*/
	protected $_cookieManager;

	/**
	* @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
	*/
	protected $_cookieMetadataFactory;
	
	/**
	* @var \Magento\Framework\Session\SessionManagerInterface;
	*/
	protected $_sessionManager;
	
	
    /** @var \Scommerce\Gdpr\Helper\Data */
    private $helper;
	
	/** @var \Scommerce\Gdpr\Model\Service\QuoteReset */
    private $_quoteReset;
	
	/** @var \Scommerce\Gdpr\Model\Service\AnonymiseOrders */
    private $_anonymiseOrders;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
	 * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
	 * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
	 * @param \Scommerce\Gdpr\Model\Service\QuoteReset $quoteReset
	 * @param \Scommerce\Gdpr\Model\Service\AnonymiseOrders $anonymiseOrders
     * @param \Scommerce\Gdpr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
		\Magento\Framework\Session\SessionManagerInterface $sessionManager,
		\Scommerce\Gdpr\Model\Service\QuoteReset $quoteReset,
		\Scommerce\Gdpr\Model\Service\AnonymiseOrders $anonymiseOrders,
        \Scommerce\Gdpr\Helper\Data $helper
    ) {
		$this->_cookieManager = $cookieManager;
		$this->_cookieMetadataFactory = $cookieMetadataFactory;
		$this->_sessionManager = $sessionManager;
		$this->helper = $helper;
		$this->_quoteReset = $quoteReset;
		$this->_anonymiseOrders = $anonymiseOrders;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
		//$this->_quoteReset->execute();
		
		//$this->_anonymiseOrders->execute();
		
		$cookieMetadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()
			->setDomain($this->_sessionManager->getCookieDomain())
			->setPath($this->_sessionManager->getCookiePath());
		
		$this->_cookieManager->deleteCookie($this->helper->getCookieClosedKey(),$cookieMetadata);
        $this->_redirect($this->_redirect->getRefererUrl());
		return;
    }
}
